<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Router;
use App\Models\SystemSetting;
use App\Models\IspClient;
use Illuminate\Http\Request;

class HotspotController extends Controller {
    private function auth() { if (!session('admin_logged_in')) return redirect()->route('admin.login'); return null; }

    public function index(Request $req) {
        if ($r = $this->auth()) return $r;
        // paginate() gives ->total() and ->links() — NOT get()
        $plans   = Plan::paginate(20)->withQueryString();
        $routers = Router::orderBy('name')->get();
        $stats   = [
            'active_users' => IspClient::where('connection_type','hotspot')->where('status','active')->count(),
            'total_plans'  => Plan::count(),
            'routers'      => $routers->count(),
        ];
        $settings = [
            'captive_title'   => SystemSetting::get('hotspot','captive_title','Free WiFi'),
            'captive_bg'      => SystemSetting::get('hotspot','captive_bg','#1e3a5f'),
            'captive_logo'    => SystemSetting::get('hotspot','captive_logo',''),
            'captive_message' => SystemSetting::get('hotspot','captive_message','Welcome! Please log in to access the internet.'),
            'session_timeout' => SystemSetting::get('hotspot','session_timeout','86400'),
            'idle_timeout'    => SystemSetting::get('hotspot','idle_timeout','3600'),
        ];
        return view('admin.hotspot.index', compact('plans','routers','stats','settings'));
    }

    public function captivePage() {
        if ($r = $this->auth()) return $r;
        $settings = [
            'captive_title'   => SystemSetting::get('hotspot','captive_title','Free WiFi — Connect'),
            'captive_bg'      => SystemSetting::get('hotspot','captive_bg','#1e3a5f'),
            'captive_logo'    => SystemSetting::get('hotspot','captive_logo',''),
            'captive_message' => SystemSetting::get('hotspot','captive_message','Welcome! Log in to get online.'),
            'session_timeout' => SystemSetting::get('hotspot','session_timeout','86400'),
            'idle_timeout'    => SystemSetting::get('hotspot','idle_timeout','3600'),
        ];
        return view('admin.hotspot.captive_settings', compact('settings'));
    }

    public function saveCaptive(Request $req) {
        if ($r = $this->auth()) return $r;
        $req->validate([
            'captive_title'   => 'required|max:100',
            'captive_bg'      => 'nullable|max:20',
            'captive_logo'    => 'nullable|max:500',
            'captive_message' => 'nullable|max:500',
            'session_timeout' => 'nullable|integer',
            'idle_timeout'    => 'nullable|integer',
        ]);
        foreach ($req->except(['_token','_method']) as $key => $value) {
            SystemSetting::updateOrCreate(
                ['group'=>'hotspot','key'=>$key],
                ['value'=>$value,'label'=>ucfirst(str_replace('_',' ',$key)),'type'=>'text']
            );
        }
        return back()->with('success','Captive portal settings saved!');
    }

    public function previewCaptive() {
        $settings = [
            'title'   => SystemSetting::get('hotspot','captive_title','Free WiFi'),
            'bg'      => SystemSetting::get('hotspot','captive_bg','#1e3a5f'),
            'logo'    => SystemSetting::get('hotspot','captive_logo',''),
            'message' => SystemSetting::get('hotspot','captive_message','Welcome! Please log in to access the internet.'),
            'company' => SystemSetting::get('general','company_name','ISP'),
        ];
        return view('hotspot.captive', compact('settings'));
    }

    public function create() {
        if ($r = $this->auth()) return $r;
        return view('admin.hotspot.create');
    }

    public function store(Request $req) {
        if ($r = $this->auth()) return $r;
        return redirect()->route('admin.hotspot.index')->with('success','Hotspot plan added.');
    }

    public function edit($id) {
        if ($r = $this->auth()) return $r;
        $plan = Plan::findOrFail($id);
        return view('admin.hotspot.edit', compact('plan'));
    }

    public function update(Request $req, $id) {
        if ($r = $this->auth()) return $r;
        return redirect()->route('admin.hotspot.index')->with('success','Plan updated.');
    }

    public function destroy($id) {
        if ($r = $this->auth()) return $r;
        Plan::findOrFail($id)->delete();
        return redirect()->route('admin.hotspot.index')->with('success','Plan deleted.');
    }
}