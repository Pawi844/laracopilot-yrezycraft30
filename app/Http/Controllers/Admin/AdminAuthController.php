<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller {
    public function showLogin() {
        if (session('admin_logged_in')) return redirect()->route('admin.dashboard');
        return view('admin.login');
    }

    public function login(Request $request) {
        $request->validate(['email' => 'required|email', 'password' => 'required']);

        // Check DB operators first
        $operator = Operator::where('email', $request->email)->where('active', true)->first();
        if ($operator && Hash::check($request->password, $operator->password)) {
            session([
                'admin_logged_in' => true,
                'admin_user' => $operator->name,
                'admin_email' => $operator->email,
                'admin_role' => $operator->role,
                'admin_operator_id' => $operator->id,
                'admin_reseller_id' => $operator->reseller_id,
                'admin_permissions' => $operator->permissions ?? []
            ]);
            $operator->update(['last_login' => now()]);
            return redirect()->route('admin.dashboard');
        }

        // Fallback hardcoded superadmin
        $hardcoded = [
            'admin@mtaakonnect.co.ke' => ['password' => 'admin123', 'name' => 'Super Admin', 'role' => 'superadmin'],
            'manager@mtaakonnect.co.ke' => ['password' => 'manager123', 'name' => 'Manager', 'role' => 'admin'],
            'support@mtaakonnect.co.ke' => ['password' => 'support123', 'name' => 'Support', 'role' => 'support'],
        ];

        if (isset($hardcoded[$request->email]) && $hardcoded[$request->email]['password'] === $request->password) {
            $u = $hardcoded[$request->email];
            session(['admin_logged_in' => true, 'admin_user' => $u['name'], 'admin_email' => $request->email, 'admin_role' => $u['role'], 'admin_permissions' => []]);
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
    }

    public function logout() {
        session()->flush();
        return redirect()->route('admin.login');
    }
}