<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Tr069Device;
use App\Models\IspClient;
use App\Models\FatNode;
use App\Models\Router;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Tr069Controller extends Controller {
    private function auth() { if (!session('admin_logged_in')) return redirect()->route('admin.login'); return null; }

    public function index(Request $req) {
        if ($r = $this->auth()) return $r;
        $query = Tr069Device::with(['client','fatNode','router'])->latest();
        if ($req->search) $query->where(function($q) use ($req) {
            $q->where('serial_number','like','%'.$req->search.'%')
              ->orWhere('mac_address','like','%'.$req->search.'%')
              ->orWhereHas('client',fn($c)=>$c->where('username','like','%'.$req->search.'%'));
        });
        if ($req->status) $query->where('onu_status',$req->status);
        $devices    = $query->paginate(20)->withQueryString();
        $globalAcs  = SystemSetting::get('tr069','acs_url','');
        return view('admin.tr069.index', compact('devices','globalAcs'));
    }

    public function create() {
        if ($r = $this->auth()) return $r;
        $clients  = IspClient::select('id','first_name','last_name','username')->orderBy('username')->get();
        $fatNodes = FatNode::orderBy('name')->get();
        $routers  = Router::orderBy('name')->get();
        $globalAcsUrl  = SystemSetting::get('tr069','acs_url',config('app.url').'/tr069/acs');
        $globalAcsUser = SystemSetting::get('tr069','acs_username','acs_user');
        return view('admin.tr069.create', compact('clients','fatNodes','routers','globalAcsUrl','globalAcsUser'));
    }

    public function store(Request $req) {
        if ($r = $this->auth()) return $r;
        $v = $req->validate([
            'serial_number'               => 'required|unique:tr069_devices|max:100',
            'mac_address'                 => 'nullable|max:50',
            'model'                       => 'nullable|max:100',
            'client_id'                   => 'nullable|exists:clients,id',
            'fat_node_id'                 => 'nullable|exists:fat_nodes,id',
            'router_id'                   => 'nullable|exists:routers,id',
            'acs_url'                     => 'nullable|max:500',
            'acs_username'                => 'nullable|max:100',
            'acs_password'                => 'nullable|max:100',
            'connection_request_url'      => 'nullable|max:500',
            'connection_request_username' => 'nullable|max:100',
            'connection_request_password' => 'nullable|max:100',
            'internet_username'           => 'nullable|max:100',
            'internet_password'           => 'nullable|max:100',
            'wlan_ssid'                   => 'nullable|max:32',
            'wlan_password'               => 'nullable|max:64',
        ]);
        if (empty($v['acs_url']))      $v['acs_url']      = SystemSetting::get('tr069','acs_url','');
        if (empty($v['acs_username'])) $v['acs_username'] = SystemSetting::get('tr069','acs_username','');
        if (empty($v['acs_password'])) $v['acs_password'] = SystemSetting::get('tr069','acs_password','');
        // Auto-fill internet credentials from client if not provided
        if (empty($v['internet_username']) && !empty($v['client_id'])) {
            $c = IspClient::find($v['client_id']);
            if ($c) $v['internet_username'] = $c->username;
        }
        Tr069Device::create($v);
        return redirect()->route('admin.tr069.index')->with('success','ONU device registered!');
    }

    public function show($id) {
        if ($r = $this->auth()) return $r;
        $device = Tr069Device::with(['client','fatNode','router'])->findOrFail($id);
        $globalAcsUrl = SystemSetting::get('tr069','acs_url',config('app.url').'/tr069/acs');
        return view('admin.tr069.show', compact('device','globalAcsUrl'));
    }

    public function edit($id) {
        if ($r = $this->auth()) return $r;
        $device   = Tr069Device::findOrFail($id);
        $clients  = IspClient::select('id','first_name','last_name','username')->orderBy('username')->get();
        $fatNodes = FatNode::orderBy('name')->get();
        $routers  = Router::orderBy('name')->get();
        $globalAcsUrl  = SystemSetting::get('tr069','acs_url',config('app.url').'/tr069/acs');
        $globalAcsUser = SystemSetting::get('tr069','acs_username','acs_user');
        return view('admin.tr069.edit', compact('device','clients','fatNodes','routers','globalAcsUrl','globalAcsUser'));
    }

    public function update(Request $req, $id) {
        if ($r = $this->auth()) return $r;
        $device = Tr069Device::findOrFail($id);
        $v = $req->validate([
            'serial_number'               => 'required|max:100|unique:tr069_devices,serial_number,'.$id,
            'mac_address'                 => 'nullable|max:50',
            'model'                       => 'nullable|max:100',
            'client_id'                   => 'nullable|exists:clients,id',
            'fat_node_id'                 => 'nullable|exists:fat_nodes,id',
            'acs_url'                     => 'nullable|max:500',
            'acs_username'                => 'nullable|max:100',
            'acs_password'                => 'nullable|max:100',
            'connection_request_url'      => 'nullable|max:500',
            'connection_request_username' => 'nullable|max:100',
            'connection_request_password' => 'nullable|max:100',
            'internet_username'           => 'nullable|max:100',
            'internet_password'           => 'nullable|max:100',
            'wlan_ssid'                   => 'nullable|max:32',
            'wlan_password'               => 'nullable|max:64',
        ]);
        $device->update($v);
        return redirect()->route('admin.tr069.show',$id)->with('success','Device updated!');
    }

    public function destroy($id) {
        if ($r = $this->auth()) return $r;
        Tr069Device::findOrFail($id)->delete();
        return redirect()->route('admin.tr069.index')->with('success','Device removed.');
    }

    public function reboot($id) {
        if ($r = $this->auth()) return $r;
        $device = Tr069Device::findOrFail($id);
        $this->acsCall($device,'reboot');
        return back()->with('success','Reboot command sent to '.$device->serial_number);
    }

    public function refreshFromAcs($id) {
        if ($r = $this->auth()) return $r;
        $device = Tr069Device::findOrFail($id);
        $this->acsCall($device,'refresh');
        return back()->with('success','Refresh requested from ACS.');
    }

    public function pushInternetSettings($id) {
        if ($r = $this->auth()) return $r;
        $device = Tr069Device::with('client')->findOrFail($id);
        $pushed = $this->acsCall($device,'set_internet');
        return back()->with($pushed ? 'success' : 'error', $pushed ? 'Internet settings pushed to ONU!' : 'Failed — check ACS URL and connection.');
    }

    public function acsGuide() {
        if ($r = $this->auth()) return $r;
        return view('admin.tr069.acs-guide');
    }

    /**
     * ACS Endpoint — ONUs call this URL on boot / periodic inform
     * A full CWMP implementation requires a dedicated ACS server (GenieACS, FreeACS).
     * This endpoint acknowledges the inform and logs the device.
     */
    public function acsEndpoint(Request $req) {
        Log::info('[TR-069 ACS] Inform received', ['ip'=>$req->ip(),'body'=>substr($req->getContent(),0,500)]);
        // Parse basic serial number from SOAP if possible
        $body   = $req->getContent();
        $serial = '';
        if (preg_match('/<SerialNumber>(.*?)<\/SerialNumber>/', $body, $m)) $serial = $m[1];
        if ($serial) {
            $dev = Tr069Device::where('serial_number',$serial)->first();
            if ($dev) $dev->update(['last_seen'=>now(),'onu_status'=>'online']);
            else Tr069Device::create(['serial_number'=>$serial,'onu_status'=>'online','last_seen'=>now()]);
        }
        // Return CWMP InformResponse
        return response(
            '<?xml version="1.0" encoding="UTF-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:cwmp="urn:dslforum-org:cwmp-1-0"><soap:Body><cwmp:InformResponse><MaxEnvelopes>1</MaxEnvelopes></cwmp:InformResponse></soap:Body></soap:Envelope>',
            200, ['Content-Type'=>'text/xml']
        );
    }

    public function connectionRequest(Request $req) {
        Log::info('[TR-069 CR]', $req->all());
        return response()->json(['ok'=>true]);
    }

    private function acsCall(Tr069Device $device, string $action): bool {
        $url  = $device->connection_request_url ?? SystemSetting::get('tr069','acs_url','');
        $user = $device->connection_request_username ?? SystemSetting::get('tr069','acs_username','');
        $pass = $device->connection_request_password ?? SystemSetting::get('tr069','acs_password','');
        if (!$url) return false;
        try {
            $resp = Http::withBasicAuth($user,$pass)->timeout(8)->post($url,['action'=>$action,'serial'=>$device->serial_number]);
            return $resp->successful();
        } catch(\Exception $e) {
            Log::warning('[TR-069] ACS call failed: '.$e->getMessage());
            return false;
        }
    }
}