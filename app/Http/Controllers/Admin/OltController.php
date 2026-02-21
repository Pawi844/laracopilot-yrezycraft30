<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\OltDevice;
use App\Models\OltPort;
use App\Models\FatNode;
use App\Models\Router;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OltController extends Controller {
    private function auth() { if (!session('admin_logged_in')) return redirect()->route('admin.login'); return null; }

    public function index() {
        if ($r = $this->auth()) return $r;
        $olts = OltDevice::with(['router','ports'])->latest()->get();
        $stats = [
            'total'   => $olts->count(),
            'online'  => $olts->where('status','online')->count(),
            'offline' => $olts->where('status','offline')->count(),
            'ports_full' => OltPort::where('onu_count','>=', \DB::raw('max_onu'))->count(),
        ];
        return view('admin.olt.index', compact('olts','stats'));
    }

    public function create() {
        if ($r = $this->auth()) return $r;
        $routers  = Router::orderBy('name')->get();
        $fatNodes = FatNode::orderBy('name')->get();
        return view('admin.olt.create', compact('routers','fatNodes'));
    }

    public function store(Request $req) {
        if ($r = $this->auth()) return $r;
        $v = $req->validate([
            'name'            => 'required|max:100',
            'brand'           => 'required|in:Huawei,ZTE,Calix,Nokia,Other',
            'model'           => 'nullable|max:100',
            'ip_address'      => 'required|max:50',
            'snmp_community'  => 'nullable|max:50',
            'ssh_username'    => 'nullable|max:100',
            'ssh_password'    => 'nullable|max:100',
            'ssh_port'        => 'nullable|integer',
            'total_ports'     => 'required|integer|min:1',
            'router_id'       => 'nullable|exists:routers,id',
            'location'        => 'nullable|max:255',
        ]);
        $olt = OltDevice::create($v);
        // Auto-create port records
        for ($i = 1; $i <= $olt->total_ports; $i++) {
            OltPort::create([
                'olt_device_id' => $olt->id,
                'port_number'   => $i,
                'port_name'     => 'GPON 0/'.$i,
                'max_onu'       => 128,
            ]);
        }
        return redirect()->route('admin.olt.show',$olt->id)->with('success','OLT added with '.$olt->total_ports.' ports!');
    }

    public function show($id) {
        if ($r = $this->auth()) return $r;
        $olt      = OltDevice::with(['router','ports.fatNode'])->findOrFail($id);
        $fatNodes = FatNode::orderBy('name')->get();
        return view('admin.olt.show', compact('olt','fatNodes'));
    }

    public function edit($id) {
        if ($r = $this->auth()) return $r;
        $olt     = OltDevice::findOrFail($id);
        $routers = Router::orderBy('name')->get();
        return view('admin.olt.edit', compact('olt','routers'));
    }

    public function update(Request $req, $id) {
        if ($r = $this->auth()) return $r;
        $olt = OltDevice::findOrFail($id);
        $v   = $req->validate([
            'name'          => 'required|max:100',
            'brand'         => 'required',
            'model'         => 'nullable|max:100',
            'ip_address'    => 'required|max:50',
            'snmp_community'=> 'nullable|max:50',
            'ssh_username'  => 'nullable|max:100',
            'ssh_password'  => 'nullable|max:100',
            'ssh_port'      => 'nullable|integer',
            'total_ports'   => 'required|integer',
            'router_id'     => 'nullable|exists:routers,id',
            'location'      => 'nullable|max:255',
        ]);
        $olt->update($v);
        return redirect()->route('admin.olt.show',$id)->with('success','OLT updated!');
    }

    public function destroy($id) {
        if ($r = $this->auth()) return $r;
        OltDevice::findOrFail($id)->delete();
        return redirect()->route('admin.olt.index')->with('success','OLT deleted.');
    }

    public function updatePort(Request $req, $oltId, $portId) {
        if ($r = $this->auth()) return $r;
        $port = OltPort::where('olt_device_id',$oltId)->findOrFail($portId);
        $port->update($req->only(['onu_count','max_onu','onu_status','signal_level','fat_node_id','port_name','notes']));
        return back()->with('success','Port updated!');
    }

    public function pollOlt($id) {
        if ($r = $this->auth()) return $r;
        $olt = OltDevice::with('ports')->findOrFail($id);
        // SNMP poll to check OLT status
        try {
            // Try basic ICMP/HTTP reach check
            $resp = Http::timeout(5)->get('http://'.$olt->ip_address);
            $olt->update(['status'=>'online','last_polled_at'=>now()]);
        } catch (\Exception $e) {
            $olt->update(['status'=>'offline','last_polled_at'=>now()]);
        }
        return back()->with('success','OLT polled at '.now()->format('H:i:s'));
    }
}