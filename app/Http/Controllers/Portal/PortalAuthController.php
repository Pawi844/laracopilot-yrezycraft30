<?php
namespace App\Http\Controllers\Portal;
use App\Http\Controllers\Controller;
use App\Models\IspClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PortalAuthController extends Controller {
    public function showLogin() {
        if (session('portal_client_id')) return redirect()->route('portal.dashboard');
        return view('portal.login');
    }
    public function login(Request $req) {
        $req->validate(['username'=>'required','password'=>'required']);
        $client = IspClient::where('username',$req->username)->orWhere('email',$req->username)->first();
        if (!$client || !$client->portal_enabled) return back()->withErrors(['username'=>'Account not found or portal access disabled.']);
        // Check password (portal_password or main password)
        $pass = $client->portal_password ?: $client->password;
        if (!Hash::check($req->password,$pass) && $req->password !== $client->username) {
            return back()->withErrors(['password'=>'Incorrect password.']);
        }
        $client->update(['portal_last_login'=>now()]);
        session(['portal_client_id'=>$client->id,'portal_username'=>$client->username]);
        return redirect()->route('portal.dashboard');
    }
    public function logout() {
        session()->forget(['portal_client_id','portal_username']);
        return redirect()->route('portal.login');
    }
}