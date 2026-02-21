<?php
namespace App\Http\Controllers\Portal;
use App\Http\Controllers\Controller;
use App\Models\IspClient;
use App\Models\ClientInvoice;
use App\Models\Plan;
use App\Models\Tr069Device;
use App\Models\RadiusTrafficLog;
use App\Models\RadiusSession;
use App\Models\SystemSetting;
use App\Services\MikrotikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PortalController extends Controller {
    private function client(): ?IspClient {
        $id = session('portal_client_id');
        return $id ? IspClient::with(['plan','nas','fat','sessions'])->find($id) : null;
    }
    private function guard() {
        if (!session('portal_client_id')) return redirect()->route('portal.login');
        return null;
    }

    public function dashboard() {
        if ($r = $this->guard()) return $r;
        $client  = $this->client();
        $invoice = ClientInvoice::where('client_id',$client->id)->latest()->first();
        $session = RadiusSession::where('username',$client->username)->where('status','active')->latest()->first();
        $graphData = RadiusTrafficLog::forGraph($client->id, 20);
        $mpesa   = ['shortcode'=>SystemSetting::get('mpesa','shortcode',''),'type'=>SystemSetting::get('mpesa','type','paybill')];
        return view('portal.dashboard', compact('client','invoice','session','graphData','mpesa'));
    }

    public function bills() {
        if ($r = $this->guard()) return $r;
        $client   = $this->client();
        $invoices = ClientInvoice::where('client_id',$client->id)->latest()->paginate(15);
        return view('portal.bills', compact('client','invoices'));
    }

    public function utilization() {
        if ($r = $this->guard()) return $r;
        $client    = $this->client();
        $graphData = RadiusTrafficLog::forGraph($client->id, 60);
        $sessions  = RadiusSession::where('username',$client->username)->latest()->take(10)->get();
        return view('portal.utilization', compact('client','graphData','sessions'));
    }

    public function changePlan() {
        if ($r = $this->guard()) return $r;
        $client = $this->client();
        $plans  = Plan::where('active',true)->where('connection_type',$client->connection_type)->orderBy('price')->get();
        return view('portal.change_plan', compact('client','plans'));
    }

    public function submitChangePlan(Request $req) {
        if ($r = $this->guard()) return $r;
        $req->validate(['plan_id'=>'required|exists:plans,id']);
        $client = $this->client();
        $newPlan = Plan::findOrFail($req->plan_id);
        $client->update(['plan_id'=>$req->plan_id]);
        return redirect()->route('portal.dashboard')->with('success','Plan changed to '.$newPlan->name.'. It will take effect on next renewal.');
    }

    public function devices() {
        if ($r = $this->guard()) return $r;
        $client  = $this->client();
        $devices = Tr069Device::where('client_id',$client->id)->get();
        return view('portal.devices', compact('client','devices'));
    }

    public function changeWifiPassword(Request $req) {
        if ($r = $this->guard()) return $r;
        $req->validate(['ssid'=>'nullable|max:32','wifi_password'=>'required|min:8']);
        $client  = $this->client();
        $device  = Tr069Device::where('client_id',$client->id)->first();
        if ($device) {
            $device->update(['wlan_ssid'=>$req->ssid??$device->wlan_ssid]);
            // In production: push via TR-069 ACS
        }
        return back()->with('success','WiFi settings change request submitted. May take up to 5 minutes to apply.');
    }

    public function profile() {
        if ($r = $this->guard()) return $r;
        $client = $this->client();
        return view('portal.profile', compact('client'));
    }

    public function updateProfile(Request $req) {
        if ($r = $this->guard()) return $r;
        $client = $this->client();
        $req->validate(['phone'=>'nullable|max:20','email'=>'nullable|email']);
        $updateData = ['phone'=>$req->phone,'email'=>$req->email];
        if ($req->filled('new_password')) {
            if (!Hash::check($req->current_password,$client->portal_password??$client->password)) {
                return back()->withErrors(['current_password'=>'Current password is incorrect.']);
            }
            $updateData['portal_password'] = Hash::make($req->new_password);
        }
        $client->update($updateData);
        return back()->with('success','Profile updated!');
    }

    public function topup() {
        if ($r = $this->guard()) return $r;
        $client = $this->client();
        $mpesa  = ['shortcode'=>SystemSetting::get('mpesa','shortcode',''),'type'=>SystemSetting::get('mpesa','type','paybill')];
        return view('portal.topup', compact('client','mpesa'));
    }

    // AJAX: live traffic for portal
    public function liveTraffic() {
        if (!session('portal_client_id')) return response()->json([],401);
        $client  = IspClient::find(session('portal_client_id'));
        $data    = RadiusTrafficLog::forGraph($client->id,20);
        $session = RadiusSession::where('username',$client->username)->where('status','active')->latest()->first();
        return response()->json(['graph'=>$data,'session'=>$session]);
    }
}