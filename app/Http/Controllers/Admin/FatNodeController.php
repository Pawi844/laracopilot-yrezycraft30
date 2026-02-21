<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\FatNode;
use App\Models\Router;
use App\Models\User;
use App\Models\Reseller;
use App\Models\AdminPermission;
use Illuminate\Http\Request;

class FatNodeController extends Controller {
    private function auth() {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $userId = session('admin_user_id');
        if ($userId && !AdminPermission::userHas($userId,'fat.manage') && session('admin_role') !== 'admin') {
            return back()->with('error','You do not have permission to manage FAT nodes.');
        }
        return null;
    }

    public function index(Request $req) {
        if ($r = $this->auth()) return $r;
        $query = FatNode::with(['router','technician'])->withCount('clients');
        if ($req->search) $query->where('name','like','%'.$req->search.'%')->orWhere('code','like','%'.$req->search.'%');
        if ($req->status) $query->where('status',$req->status);
        $nodes = $query->paginate(20)->withQueryString();
        return view('admin.fat.index', compact('nodes'));
    }

    public function create() {
        if ($r = $this->auth()) return $r;
        $routers    = Router::orderBy('name')->get();
        $technicians= User::where('role','technician')->orWhere('role','operator')->get();
        $resellers  = Reseller::where('status','active')->get();
        return view('admin.fat.create', compact('routers','technicians','resellers'));
    }

    public function store(Request $req) {
        if ($r = $this->auth()) return $r;
        $v = $req->validate([
            'name'          =>'required|max:100',
            'code'          =>'required|unique:fat_nodes|max:30|regex:/^[A-Za-z0-9_-]+$/',
            'location'      =>'nullable|max:255',
            'latitude'      =>'nullable|numeric',
            'longitude'     =>'nullable|numeric',
            'max_onu'       =>'required|integer|min:1|max:256',
            'router_id'     =>'nullable|exists:routers,id',
            'technician_id' =>'nullable|exists:users,id',
            'reseller_id'   =>'nullable|exists:resellers,id',
            'olt_port'      =>'nullable|max:50',
            'splitter_type' =>'nullable|max:20',
            'notes'         =>'nullable|max:500',
        ]);
        FatNode::create($v);
        return redirect()->route('admin.fat.index')->with('success','FAT node created!');
    }

    public function show($id) {
        if ($r = $this->auth()) return $r;
        $node = FatNode::with(['router','technician','clients.plan','devices'])->withCount('clients')->findOrFail($id);
        return view('admin.fat.show', compact('node'));
    }

    public function edit($id) {
        if ($r = $this->auth()) return $r;
        $node        = FatNode::findOrFail($id);
        $routers     = Router::orderBy('name')->get();
        $technicians = User::where('role','technician')->orWhere('role','operator')->get();
        $resellers   = Reseller::where('status','active')->get();
        return view('admin.fat.edit', compact('node','routers','technicians','resellers'));
    }

    public function update(Request $req, $id) {
        if ($r = $this->auth()) return $r;
        $node = FatNode::findOrFail($id);
        $v = $req->validate([
            'name'         =>'required|max:100',
            'location'     =>'nullable|max:255',
            'max_onu'      =>'required|integer|min:1',
            'router_id'    =>'nullable|exists:routers,id',
            'technician_id'=>'nullable|exists:users,id',
            'olt_port'     =>'nullable|max:50',
            'splitter_type'=>'nullable|max:20',
            'notes'        =>'nullable|max:500',
        ]);
        $node->update($v);
        $node->recalculateUsed();
        return redirect()->route('admin.fat.show',$id)->with('success','FAT node updated!');
    }

    public function destroy($id) {
        if ($r = $this->auth()) return $r;
        $node = FatNode::findOrFail($id);
        if ($node->clients_count > 0) return back()->with('error','Cannot delete FAT node with '.$node->clients_count.' clients assigned.');
        $node->delete();
        return redirect()->route('admin.fat.index')->with('success','FAT node deleted.');
    }
}