<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Router;
use App\Models\Nas;
use App\Models\Reseller;
use App\Services\MikrotikService;
use Illuminate\Http\Request;

class RouterController extends Controller {
    private function auth() { if (!session('admin_logged_in')) return redirect()->route('admin.login'); return null; }

    public function index(Request $req) {
        if ($r = $this->auth()) return $r;
        $query = Router::with(['reseller']);
        if ($req->search) {
            $q = $req->search;
            $query->where(function($qb) use ($q) {
                $qb->where('name','like',"%{$q}%")->orWhere('ip_address','like',"%{$q}%");
            });
        }
        $routers = $query->latest()->paginate(20)->withQueryString();
        return view('admin.routers.index', compact('routers'));
    }

    public function create() {
        if ($r = $this->auth()) return $r;
        $resellers = Reseller::orderBy('company_name')->get();
        $nas       = Nas::orderBy('shortname')->get();
        return view('admin.routers.create', compact('resellers','nas'));
    }

    public function store(Request $req) {
        if ($r = $this->auth()) return $r;
        $v = $req->validate([
            'name'          => 'required|max:100',
            'ip_address'    => 'required|max:100',
            'username'      => 'required|max:100',
            'password'      => 'required|max:100',
            'api_port'      => 'nullable|integer',
            'model'         => 'nullable|max:100',
            'use_ovpn'      => 'nullable',
            'ovpn_gateway'  => 'nullable|max:50',
            'ovpn_username' => 'nullable|max:100',
            'ovpn_password' => 'nullable|max:100',
            'reseller_id'   => 'nullable|exists:resellers,id',
            'nas_id'        => 'nullable|exists:nas,id',
        ]);
        $v['use_ovpn'] = $req->has('use_ovpn');
        Router::create($v);
        return redirect()->route('admin.routers.index')->with('success','Router added successfully!');
    }

    public function edit($id) {
        if ($r = $this->auth()) return $r;
        $router    = Router::findOrFail($id);
        $resellers = Reseller::orderBy('company_name')->get();
        $nas       = Nas::orderBy('shortname')->get();
        return view('admin.routers.edit', compact('router','resellers','nas'));
    }

    public function update(Request $req, $id) {
        if ($r = $this->auth()) return $r;
        $router = Router::findOrFail($id);
        $v = $req->validate([
            'name'          => 'required|max:100',
            'ip_address'    => 'required|max:100',
            'username'      => 'required|max:100',
            'password'      => 'nullable|max:100',
            'api_port'      => 'nullable|integer',
            'model'         => 'nullable|max:100',
            'use_ovpn'      => 'nullable',
            'ovpn_gateway'  => 'nullable|max:50',
            'ovpn_username' => 'nullable|max:100',
            'ovpn_password' => 'nullable|max:100',
            'nas_id'        => 'nullable|exists:nas,id',
        ]);
        $v['use_ovpn'] = $req->has('use_ovpn');
        // Don't overwrite password if left blank on edit
        if (empty($v['password'])) unset($v['password']);
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
        try {
            $ok = (new MikrotikService($router))->testConnection();
            return back()->with($ok ? 'success' : 'error', $ok ? '✅ Connected to '.$router->name.' via API!' : '❌ Cannot connect — check IP, username, password, API port, and that /ip service enable api is set on MikroTik.');
        } catch (\Exception $e) {
            return back()->with('error','❌ '.$e->getMessage());
        }
    }

    public function downloadOvpnConfig($id) {
        if ($r = $this->auth()) return $r;
        $router = Router::findOrFail($id);
        $config = view('admin.routers.ovpn_template', compact('router'))->render();
        return response($config, 200, [
            'Content-Type'        => 'application/x-openvpn-profile',
            'Content-Disposition' => 'attachment; filename="'.str_replace(' ','_',$router->name).'.ovpn"',
        ]);
    }
}