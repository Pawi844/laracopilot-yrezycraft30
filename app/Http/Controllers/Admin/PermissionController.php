<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminPermission;
use Illuminate\Http\Request;

class PermissionController extends Controller {
    private function superOnly() {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        if (session('admin_role') !== 'admin') return back()->with('error','Only admins can manage permissions.');
        return null;
    }

    public function index() {
        if ($r = $this->superOnly()) return $r;
        $users = User::where('role','!=','admin')->orderBy('name')->get();
        $allPerms = AdminPermission::allPermissions();
        return view('admin.permissions.index', compact('users','allPerms'));
    }

    public function show($userId) {
        if ($r = $this->superOnly()) return $r;
        $user     = User::findOrFail($userId);
        $allPerms = AdminPermission::allPermissions();
        $granted  = AdminPermission::where('user_id',$userId)->where('granted',true)->pluck('permission')->toArray();
        return view('admin.permissions.show', compact('user','allPerms','granted'));
    }

    public function update(Request $req, $userId) {
        if ($r = $this->superOnly()) return $r;
        $user = User::findOrFail($userId);
        $allPerms = array_keys(AdminPermission::allPermissions());
        $grantedBy = session('admin_user_id');

        // Remove all existing permissions for user
        AdminPermission::where('user_id',$userId)->delete();

        // Re-grant checked ones
        $checked = $req->input('permissions',[]);
        foreach ($allPerms as $perm) {
            AdminPermission::create([
                'user_id'    => $userId,
                'permission' => $perm,
                'granted'    => in_array($perm,$checked),
                'granted_by' => $grantedBy,
            ]);
        }
        return back()->with('success','Permissions updated for '.$user->name);
    }
}