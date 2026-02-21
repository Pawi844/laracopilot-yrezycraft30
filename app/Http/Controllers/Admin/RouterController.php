<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Router;
use App\Models\Reseller;
use App\Services\MikrotikService;
use Illuminate\Http\Request;

class RouterController extends Controller {
    private function auth() { if (!session('admin_logged_in')) return redirect()->route('admin.login'); return null; }

    public function index(Request $req) {
        if ($r = $this->auth()) return $r;
        $routers = Router::with('reseller')->latest()->paginate(20);
        return view('admin.routers.index', compact('routers'));
    }

    public function create() {
        if ($r = $this->auth()) return $r;
        $resellers = Reseller::where('status','active')->get();
        return view('admin.routers.create', compact('resellers'));
    }

    public function store(Request $req) {
        if ($r = $this->auth()) return $r;
        $v = $req->validate([
            'name'         => 'required|max:100',
            'ip_address'   => 'required|max:100',
            'api_port'     => 'nullable|integer',
            'api_username' => 'nullable|max:100',
            'api_password' => 'nullable|max:100',
            'use_ovpn'     => 'nullable|boolean',
            'ovpn_gateway' => 'nullable|max:50',
            'ovpn_username'=> 'nullable|max:100',
            'ovpn_password'=> 'nullable|max:100',
            'reseller_id'  => 'nullable|exists:resellers,id',
        ]);
        $v['use_ovpn'] = $req->has('use_ovpn');
        Router::create($v);
        return redirect()->route('admin.routers.index')->with('success','Router added!');
    }

    public function edit($id) {
        if ($r = $this->auth()) return $r;
        $router    = Router::findOrFail($id);
        $resellers = Reseller::where('status','active')->get();
        return view('admin.routers.edit', compact('router','resellers'));
    }

    public function update(Request $req, $id) {
        if ($r = $this->auth()) return $r;
        $router = Router::findOrFail($id);
        $v = $req->validate([
            'name'         => 'required|max:100',
            'ip_address'   => 'required|max:100',
            'api_port'     => 'nullable|integer',
            'api_username' => 'nullable|max:100',
            'api_password' => 'nullable|max:100',
            'use_ovpn'     => 'nullable|boolean',
            'ovpn_gateway' => 'nullable|max:50',
            'ovpn_username'=> 'nullable|max:100',
            'ovpn_password'=> 'nullable|max:100',
        ]);
        $v['use_ovpn'] = $req->has('use_ovpn');
        $router->update($v);
        return redirect()->route('admin.routers.index')->with('success','Router updated!');
    }

    public function destroy($id) {
        if ($r = $this->auth()) return $r;
        Router::findOrFail($id)->delete();
        return redirect()->route('admin.routers.index')->with('success','Router deleted.');
    }

    public function sync($id) {
        if ($r = $this->auth()) return $r;
        $router = Router::findOrFail($id);
        $ok = (new MikrotikService($router))->testConnection();
        return back()->with($ok ? 'success' : 'error', $ok ? 'Connected to '.$router->name.' successfully!' : 'Cannot connect to '.$router->name.' — check IP/credentials/tunnel.');
    }

    public function downloadOvpnConfig($id) {
        if ($r = $this->auth()) return $r;
        $router = Router::findOrFail($id);
        $config = (new MikrotikService)->generateOvpnConfig($router);
        return response($config,200,[
            'Content-Type'        => 'application/x-openvpn-profile',
            'Content-Disposition' => 'attachment; filename="'.str_replace(' ','_',$router->name).'.ovpn"',
        ]);
    }
}