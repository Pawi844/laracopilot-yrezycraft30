<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\IspClient;
use App\Models\Plan;
use App\Models\Nas;
use App\Models\FatNode;
use App\Models\Router;
use App\Models\Reseller;
use App\Models\RadiusSession;
use App\Models\RadiusTrafficLog;
use App\Services\MikrotikService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ClientController extends Controller {
    private function auth() {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        return null;
    }

    public function index(Request $req) {
        if ($r = $this->auth()) return $r;
        $query = IspClient::with(['plan','nas','fat'])->latest();
        // Global search
        if ($req->q) {
            $q = $req->q;
            $query->where(function($query) use ($q) {
                $query->where('username','like',"%{$q}%")
                      ->orWhere('first_name','like',"%{$q}%")
                      ->orWhere('last_name','like',"%{$q}%")
                      ->orWhere('phone','like',"%{$q}%")
                      ->orWhere('email','like',"%{$q}%")
                      ->orWhere('static_ip','like',"%{$q}%")
                      ->orWhere('mac_address','like',"%{$q}%")
                      ->orWhereHas('fat',fn($f)=>$f->where('code','like',"%{$q}%"));
            });
        }
        if ($req->status)  $query->where('status',$req->status);
        if ($req->plan_id) $query->where('plan_id',$req->plan_id);
        if ($req->fat_id)  $query->where('fat_node_id',$req->fat_id);
        if ($req->type)    $query->where('connection_type',$req->type);

        $clients  = $query->paginate(25)->withQueryString();
        $plans    = Plan::where('active',true)->get();
        $fatNodes = FatNode::orderBy('name')->get();
        $stats = [
            'total'    => IspClient::count(),
            'active'   => IspClient::where('status','active')->count(),
            'expired'  => IspClient::where('status','expired')->count(),
            'suspended'=> IspClient::where('status','suspended')->count(),
        ];
        return view('admin.clients.index', compact('clients','plans','fatNodes','stats'));
    }

    public function create() {
        if ($r = $this->auth()) return $r;
        $plans    = Plan::where('active',true)->orderBy('name')->get();
        $nas      = Nas::orderBy('shortname')->get();
        $fatNodes = FatNode::where('status','active')->orderBy('name')->get();
        $routers  = Router::orderBy('name')->get();
        $resellers= Reseller::where('status','active')->get();
        return view('admin.clients.create', compact('plans','nas','fatNodes','routers','resellers'));
    }

    public function store(Request $req) {
        if ($r = $this->auth()) return $r;
        $v = $req->validate([
            'username'        => 'required|unique:clients|max:100|regex:/^[a-zA-Z0-9._@-]+$/',
            'password'        => 'required|min:4',
            'first_name'      => 'required|max:100',
            'last_name'       => 'required|max:100',
            'phone'           => 'nullable|max:20',
            'email'           => 'nullable|email|max:150',
            'connection_type' => 'required|in:pppoe,hotspot,static,dhcp',
            'plan_id'         => 'nullable|exists:plans,id',
            'nas_id'          => 'nullable|exists:nas,id',
            'fat_node_id'     => 'nullable|exists:fat_nodes,id',
            'static_ip'       => 'nullable|ip',
            'mac_address'     => 'nullable|max:17',
            'expiry_date'     => 'nullable|date',
            'status'          => 'required|in:active,inactive,suspended,expired,pending',
        ]);
        $v['password'] = bcrypt($req->password);
        $client = IspClient::create($v);

        // Update FAT used count
        if ($client->fat_node_id) {
            $client->fat->recalculateUsed();
        }

        (new NotificationService)->notifyClient($client,'welcome');
        return redirect()->route('admin.clients.show',$client->id)->with('success','Client created successfully!');
    }

    public function show($id) {
        if ($r = $this->auth()) return $r;
        $client   = IspClient::with(['plan','nas','fat','router','devices','invoices','tickets.technician'])->findOrFail($id);
        $sessions = RadiusSession::where('username',$client->username)->latest()->take(5)->get();
        $graphData= RadiusTrafficLog::forGraph($client->id, 20);
        return view('admin.clients.show', compact('client','sessions','graphData'));
    }

    public function edit($id) {
        if ($r = $this->auth()) return $r;
        $client   = IspClient::findOrFail($id);
        $plans    = Plan::where('active',true)->orderBy('name')->get();
        $nas      = Nas::orderBy('shortname')->get();
        $fatNodes = FatNode::orderBy('name')->get();
        $routers  = Router::orderBy('name')->get();
        $resellers= Reseller::where('status','active')->get();
        return view('admin.clients.edit', compact('client','plans','nas','fatNodes','routers','resellers'));
    }

    public function update(Request $req, $id) {
        if ($r = $this->auth()) return $r;
        $client = IspClient::findOrFail($id);
        $v = $req->validate([
            'username'        => 'required|max:100|unique:clients,username,'.$id,
            'first_name'      => 'required|max:100',
            'last_name'       => 'required|max:100',
            'phone'           => 'nullable|max:20',
            'email'           => 'nullable|email|max:150',
            'connection_type' => 'required|in:pppoe,hotspot,static,dhcp',
            'plan_id'         => 'nullable|exists:plans,id',
            'nas_id'          => 'nullable|exists:nas,id',
            'fat_node_id'     => 'nullable|exists:fat_nodes,id',
            'static_ip'       => 'nullable',
            'mac_address'     => 'nullable|max:17',
            'expiry_date'     => 'nullable|date',
            'status'          => 'required|in:active,inactive,suspended,expired,pending',
        ]);
        if ($req->filled('password')) $v['password'] = bcrypt($req->password);
        $oldFat = $client->fat_node_id;
        $client->update($v);
        // Recalc both old and new FAT
        if ($oldFat && $oldFat != $client->fat_node_id) {
            FatNode::find($oldFat)?->recalculateUsed();
        }
        if ($client->fat_node_id) $client->fat->recalculateUsed();
        return redirect()->route('admin.clients.show',$id)->with('success','Client updated!');
    }

    public function destroy($id) {
        if ($r = $this->auth()) return $r;
        $client = IspClient::findOrFail($id);
        $fatId  = $client->fat_node_id;
        $client->delete();
        if ($fatId) FatNode::find($fatId)?->recalculateUsed();
        return redirect()->route('admin.clients.index')->with('success','Client deleted.');
    }

    public function disconnect($id) {
        if ($r = $this->auth()) return $r;
        $client = IspClient::with('nas')->findOrFail($id);
        try {
            (new MikrotikService($client->nas))->disconnectUser($client->username);
        } catch(\Exception $e) {}
        return back()->with('success','Disconnect command sent.');
    }

    public function reconnect($id) {
        if ($r = $this->auth()) return $r;
        $client = IspClient::findOrFail($id);
        $client->update(['status'=>'active']);
        return back()->with('success','Client re-activated.');
    }

    public function trafficData($id) {
        if (!session('admin_logged_in')) return response()->json([],401);
        $client = IspClient::findOrFail($id);
        return response()->json(RadiusTrafficLog::forGraph($client->id,20));
    }
}