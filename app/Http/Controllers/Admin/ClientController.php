<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\IspClient;
use App\Models\IspPlan;
use App\Models\Router;
use App\Models\NetworkZone;
use App\Models\FatNode;
use App\Models\ClientInvoice;
use App\Models\RadiusSession;
use App\Models\CallLog;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    private function auth() { if (!session('admin_logged_in')) return redirect()->route('admin.login'); return null; }

    public function index(Request $req)
    {
        if ($r = $this->auth()) return $r;

        $query = IspClient::with(['plan','router','zone']);

        if ($req->search) {
            $q = $req->search;
            $query->where(function($qb) use ($q) {
                $qb->where('first_name','like',"%{$q}%")
                   ->orWhere('last_name','like',"%{$q}%")
                   ->orWhere('username','like',"%{$q}%")
                   ->orWhere('email','like',"%{$q}%")
                   ->orWhere('phone','like',"%{$q}%");
            });
        }
        if ($req->status)          $query->where('status', $req->status);
        if ($req->connection_type) $query->where('connection_type', $req->connection_type);
        if ($req->zone_id)         $query->where('zone_id', $req->zone_id);
        if ($req->plan_id)         $query->where('plan_id', $req->plan_id);

        $clients  = $query->latest()->paginate(20)->withQueryString();
        $zones    = NetworkZone::orderBy('name')->get();
        $plans    = IspPlan::orderBy('name')->get();
        $routers  = Router::orderBy('name')->get();
        $fatNodes = FatNode::orderBy('name')->get();

        $stats = [
            'total'     => IspClient::count(),
            'active'    => IspClient::where('status','active')->count(),
            'suspended' => IspClient::where('status','suspended')->count(),
            'expired'   => IspClient::where('status','expired')->count(),
        ];

        return view('admin.clients.index', compact(
            'clients','zones','plans','routers','fatNodes','stats'
        ));
    }

    public function show($id)
    {
        if ($r = $this->auth()) return $r;
        $client = IspClient::with(['plan','router','zone'])->findOrFail($id);

        $invoices = ClientInvoice::where('client_id', $id)->latest()->limit(10)->get();

        $sessions = collect();
        $totalIn  = 0;
        $totalOut = 0;
        try {
            $sessions = RadiusSession::where('username', $client->username)->latest('start_time')->limit(20)->get();
            $totalIn  = (int) $sessions->sum('bytes_in');
            $totalOut = (int) $sessions->sum('bytes_out');
        } catch (\Throwable $e) {}

        $callLogs = collect();
        try { $callLogs = CallLog::where('client_id', $id)->latest()->limit(10)->get(); } catch (\Throwable $e) {}

        $tickets = collect();
        try { $tickets = SupportTicket::where('client_id', $id)->latest()->limit(5)->get(); } catch (\Throwable $e) {}

        return view('admin.clients.show', compact(
            'client','invoices','sessions','totalIn','totalOut','callLogs','tickets'
        ));
    }

    public function create()
    {
        if ($r = $this->auth()) return $r;
        $plans    = IspPlan::where('active', true)->orderBy('name')->get();
        $routers  = Router::orderBy('name')->get();
        $zones    = NetworkZone::orderBy('name')->get();
        $fatNodes = FatNode::orderBy('name')->get();
        return view('admin.clients.create', compact('plans','routers','zones','fatNodes'));
    }

    public function store(Request $req)
    {
        if ($r = $this->auth()) return $r;
        $v = $req->validate([
            'first_name'      => 'required|max:100',
            'last_name'       => 'nullable|max:100',
            'username'        => 'required|unique:isp_clients,username|max:100',
            'password'        => 'required|min:4|max:100',
            'email'           => 'nullable|email|max:150',
            'phone'           => 'nullable|max:30',
            'connection_type' => 'required|in:pppoe,hotspot,static',
            'plan_id'         => 'nullable|exists:isp_plans,id',
            'router_id'       => 'nullable|exists:routers,id',
            'zone_id'         => 'nullable|exists:network_zones,id',
            'ip_address'      => 'nullable|max:50',
            'mac_address'     => 'nullable|max:50',
            'expiry_date'     => 'nullable|date',
            'status'          => 'required|in:active,suspended,expired,pending',
            'address'         => 'nullable|max:300',
            'notes'           => 'nullable|max:1000',
        ]);
        IspClient::create($v);
        return redirect()->route('admin.clients.index')->with('success','Client created successfully!');
    }

    public function edit($id)
    {
        if ($r = $this->auth()) return $r;
        $client   = IspClient::findOrFail($id);
        $plans    = IspPlan::where('active', true)->orderBy('name')->get();
        $routers  = Router::orderBy('name')->get();
        $zones    = NetworkZone::orderBy('name')->get();
        $fatNodes = FatNode::orderBy('name')->get();
        return view('admin.clients.edit', compact('client','plans','routers','zones','fatNodes'));
    }

    public function update(Request $req, $id)
    {
        if ($r = $this->auth()) return $r;
        $client = IspClient::findOrFail($id);
        $v = $req->validate([
            'first_name'      => 'required|max:100',
            'last_name'       => 'nullable|max:100',
            'username'        => 'required|max:100|unique:isp_clients,username,'.$id,
            'password'        => 'nullable|min:4|max:100',
            'email'           => 'nullable|email|max:150',
            'phone'           => 'nullable|max:30',
            'connection_type' => 'required|in:pppoe,hotspot,static',
            'plan_id'         => 'nullable|exists:isp_plans,id',
            'router_id'       => 'nullable|exists:routers,id',
            'zone_id'         => 'nullable|exists:network_zones,id',
            'ip_address'      => 'nullable|max:50',
            'mac_address'     => 'nullable|max:50',
            'expiry_date'     => 'nullable|date',
            'status'          => 'required|in:active,suspended,expired,pending',
            'address'         => 'nullable|max:300',
            'notes'           => 'nullable|max:1000',
        ]);
        if (empty($v['password'])) unset($v['password']);
        $client->update($v);
        return redirect()->route('admin.clients.show', $id)->with('success','Client updated!');
    }

    public function destroy($id)
    {
        if ($r = $this->auth()) return $r;
        IspClient::findOrFail($id)->delete();
        return redirect()->route('admin.clients.index')->with('success','Client deleted.');
    }

    public function suspend($id)
    {
        if ($r = $this->auth()) return $r;
        IspClient::findOrFail($id)->update(['status' => 'suspended']);
        return back()->with('success','Client suspended.');
    }

    public function activate($id)
    {
        if ($r = $this->auth()) return $r;
        IspClient::findOrFail($id)->update(['status' => 'active']);
        return back()->with('success','Client activated.');
    }

    public function renew(Request $req, $id)
    {
        if ($r = $this->auth()) return $r;
        $req->validate(['expiry_date' => 'required|date']);
        IspClient::findOrFail($id)->update([
            'expiry_date' => $req->expiry_date,
            'status'      => 'active',
        ]);
        return back()->with('success','Client renewed until '.$req->expiry_date.'.');
    }
}