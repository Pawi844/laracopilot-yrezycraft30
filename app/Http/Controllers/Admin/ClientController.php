<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\IspClient;
use App\Models\Plan;
use App\Models\Nas;
use App\Models\Router;
use App\Models\Reseller;
use App\Models\RadiusTrafficLog;
use App\Models\RadiusSession;
use Illuminate\Http\Request;

class ClientController extends Controller {
    private function authCheck() {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        return null;
    }

    public function index(Request $request) {
        if ($r = $this->authCheck()) return $r;
        $query = IspClient::with(['plan','nas'])->latest();
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('username','like','%'.$request->search.'%')
                  ->orWhere('first_name','like','%'.$request->search.'%')
                  ->orWhere('last_name','like','%'.$request->search.'%')
                  ->orWhere('phone','like','%'.$request->search.'%');
            });
        }
        if ($request->status)  $query->where('status',  $request->status);
        if ($request->type)    $query->where('connection_type', $request->type);
        $clients = $query->paginate(25)->withQueryString();
        return view('admin.clients.index', compact('clients'));
    }

    public function create() {
        if ($r = $this->authCheck()) return $r;
        $plans     = Plan::where('active',true)->orderBy('name')->get();
        $nas       = Nas::where('status','active')->get();
        $routers   = Router::orderBy('name')->get();
        $resellers = Reseller::where('status','active')->get();
        return view('admin.clients.create', compact('plans','nas','routers','resellers'));
    }

    public function store(Request $request) {
        if ($r = $this->authCheck()) return $r;
        $v = $request->validate([
            'username'        => 'required|unique:clients|max:100',
            'password'        => 'required|min:4',
            'first_name'      => 'required|max:100',
            'last_name'       => 'required|max:100',
            'email'           => 'nullable|email',
            'phone'           => 'nullable|max:20',
            'connection_type' => 'required|in:pppoe,hotspot,static',
            'status'          => 'required|in:active,pending,inactive,suspended',
            'plan_id'         => 'nullable|exists:plans,id',
            'nas_id'          => 'nullable|exists:nas,id',
            'router_id'       => 'nullable|exists:routers,id',
            'expiry_date'     => 'nullable|date',
        ]);
        IspClient::create($v);
        return redirect()->route('admin.clients.index')->with('success', 'Client created!');
    }

    public function show($id) {
        if ($r = $this->authCheck()) return $r;
        $client = IspClient::with(['plan','nas','sessions','notificationLogs'])->findOrFail($id);
        // Traffic graph data — last 30 polls
        $graphData     = RadiusTrafficLog::forGraph($id, 30);
        $currentSession = RadiusSession::where('username', $client->username)
            ->where('status','active')->latest()->first();
        $totalIn  = $client->sessions->sum('bytes_in');
        $totalOut = $client->sessions->sum('bytes_out');
        return view('admin.clients.show', compact('client','graphData','currentSession','totalIn','totalOut'));
    }

    public function edit($id) {
        if ($r = $this->authCheck()) return $r;
        $client    = IspClient::findOrFail($id);
        $plans     = Plan::where('active',true)->orderBy('name')->get();
        $nas       = Nas::where('status','active')->get();
        $routers   = Router::orderBy('name')->get();
        $resellers = Reseller::where('status','active')->get();
        return view('admin.clients.edit', compact('client','plans','nas','routers','resellers'));
    }

    public function update(Request $request, $id) {
        if ($r = $this->authCheck()) return $r;
        $client = IspClient::findOrFail($id);
        $v = $request->validate([
            'first_name'      => 'required|max:100',
            'last_name'       => 'required|max:100',
            'email'           => 'nullable|email',
            'phone'           => 'nullable|max:20',
            'connection_type' => 'required|in:pppoe,hotspot,static',
            'status'          => 'required',
            'plan_id'         => 'nullable|exists:plans,id',
            'nas_id'          => 'nullable|exists:nas,id',
            'static_ip'       => 'nullable|ip',
            'expiry_date'     => 'nullable|date',
            'notify_sms'      => 'nullable',
            'notify_email'    => 'nullable',
            'notify_whatsapp' => 'nullable',
        ]);
        if ($request->filled('password')) $v['password'] = $request->password;
        $v['notify_sms']      = $request->has('notify_sms')      ? 1 : 0;
        $v['notify_email']    = $request->has('notify_email')    ? 1 : 0;
        $v['notify_whatsapp'] = $request->has('notify_whatsapp') ? 1 : 0;
        $client->update($v);
        return redirect()->route('admin.clients.show', $id)->with('success', 'Client updated!');
    }

    public function destroy($id) {
        if ($r = $this->authCheck()) return $r;
        IspClient::findOrFail($id)->delete();
        return redirect()->route('admin.clients.index')->with('success', 'Client deleted.');
    }

    public function disconnect($id) {
        if ($r = $this->authCheck()) return $r;
        $client = IspClient::findOrFail($id);
        RadiusSession::where('username',$client->username)->where('status','active')->update(['status'=>'closed']);
        return back()->with('success', $client->username . ' disconnected.');
    }

    public function reconnect($id) {
        if ($r = $this->authCheck()) return $r;
        $client = IspClient::findOrFail($id);
        if ($client->status === 'suspended') $client->update(['status'=>'active']);
        return back()->with('success', $client->username . ' account reactivated.');
    }

    // AJAX endpoint for live graph updates
    public function trafficData($id) {
        $client = IspClient::findOrFail($id);
        $data   = RadiusTrafficLog::forGraph($id, 20);
        $session = RadiusSession::where('username',$client->username)->where('status','active')->latest()->first();
        return response()->json([
            'graph'   => $data,
            'session' => $session ? [
                'bytes_in'      => $session->bytes_in ?? 0,
                'bytes_out'     => $session->bytes_out ?? 0,
                'session_time'  => $session->session_time ?? '-',
                'framed_ip'     => $session->framed_ip ?? '-',
                'status'        => $session->status ?? 'unknown',
            ] : null,
        ]);
    }
}