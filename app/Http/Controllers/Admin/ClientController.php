<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\IspClient;
use App\Models\IspPlan;
use App\Models\Nas;
use App\Models\Router;
use App\Models\Reseller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller {
    private function auth() { if (!session('admin_logged_in')) return redirect()->route('admin.login'); }

    public function index(Request $request) {
        $this->auth();
        $query = IspClient::with(['plan','nas','router','reseller']);
        if ($request->status) $query->where('status', $request->status);
        if ($request->type) $query->where('connection_type', $request->type);
        if ($request->search) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('username','like',"%$s%")->orWhere('first_name','like',"%$s%")->orWhere('last_name','like',"%$s%")->orWhere('phone','like',"%$s%")->orWhere('email','like',"%$s%"));
        }
        $clients = $query->orderBy('created_at','desc')->paginate(25);
        return view('admin.clients.index', compact('clients'));
    }

    public function create() {
        $this->auth();
        $plans = IspPlan::where('active',true)->get();
        $nas = Nas::where('status','active')->get();
        $routers = Router::where('status','online')->get();
        $resellers = Reseller::where('status','active')->get();
        return view('admin.clients.create', compact('plans','nas','routers','resellers'));
    }

    public function store(Request $request) {
        $this->auth();
        $validated = $request->validate([
            'username' => 'required|unique:clients|max:100',
            'password' => 'required|min:4',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'id_number' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'county' => 'nullable|string|max:100',
            'plan_id' => 'nullable|exists:isp_plans,id',
            'nas_id' => 'nullable|exists:nas,id',
            'router_id' => 'nullable|exists:routers,id',
            'reseller_id' => 'nullable|exists:resellers,id',
            'static_ip' => 'nullable|string|max:50',
            'mac_address' => 'nullable|string|max:20',
            'connection_type' => 'required|in:pppoe,hotspot,static',
            'status' => 'required|in:active,inactive,suspended,expired',
            'expiry_date' => 'nullable|date'
        ]);
        $validated['password'] = Hash::make($request->password);
        $validated['notify_sms'] = $request->has('notify_sms');
        $validated['notify_email'] = $request->has('notify_email');
        $validated['notify_whatsapp'] = $request->has('notify_whatsapp');
        IspClient::create($validated);
        return redirect()->route('admin.clients.index')->with('success', 'Client created successfully!');
    }

    public function show($id) {
        $this->auth();
        $client = IspClient::with(['plan','nas','router','reseller','sessions' => fn($q) => $q->orderBy('start_time','desc')->limit(20),'tr069Device','notificationLogs' => fn($q) => $q->orderBy('created_at','desc')->limit(10)])->findOrFail($id);
        return view('admin.clients.show', compact('client'));
    }

    public function edit($id) {
        $this->auth();
        $client = IspClient::findOrFail($id);
        $plans = IspPlan::where('active',true)->get();
        $nas = Nas::where('status','active')->get();
        $routers = Router::all();
        $resellers = Reseller::where('status','active')->get();
        return view('admin.clients.edit', compact('client','plans','nas','routers','resellers'));
    }

    public function update(Request $request, $id) {
        $this->auth();
        $client = IspClient::findOrFail($id);
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'plan_id' => 'nullable|exists:isp_plans,id',
            'nas_id' => 'nullable|exists:nas,id',
            'router_id' => 'nullable|exists:routers,id',
            'static_ip' => 'nullable|string|max:50',
            'connection_type' => 'required|in:pppoe,hotspot,static',
            'status' => 'required|in:active,inactive,suspended,expired',
            'expiry_date' => 'nullable|date'
        ]);
        if ($request->filled('password')) $validated['password'] = Hash::make($request->password);
        $validated['notify_sms'] = $request->has('notify_sms');
        $validated['notify_email'] = $request->has('notify_email');
        $validated['notify_whatsapp'] = $request->has('notify_whatsapp');
        $client->update($validated);
        return redirect()->route('admin.clients.index')->with('success', 'Client updated!');
    }

    public function destroy($id) {
        $this->auth();
        IspClient::findOrFail($id)->delete();
        return redirect()->route('admin.clients.index')->with('success', 'Client deleted!');
    }

    public function disconnect($id) {
        $this->auth();
        $client = IspClient::findOrFail($id);
        // Mark active sessions as disconnected
        $client->sessions()->where('status','active')->update(['status'=>'disconnect','stop_time'=>now()]);
        return back()->with('success', 'Client ' . $client->username . ' disconnected from network.');
    }

    public function reconnect($id) {
        $this->auth();
        $client = IspClient::findOrFail($id);
        $client->update(['status' => 'active']);
        return back()->with('success', 'Client ' . $client->username . ' reconnected.');
    }
}