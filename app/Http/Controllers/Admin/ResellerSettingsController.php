<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Reseller;
use App\Models\ResellerSetting;
use App\Models\NotificationTemplate;
use Illuminate\Http\Request;

class ResellerSettingsController extends Controller {
    private function auth() {
        if (!session('admin_logged_in') && !session('reseller_logged_in')) return redirect()->route('admin.login');
        return null;
    }

    public function index($resellerId) {
        if ($r = $this->auth()) return $r;
        $reseller = Reseller::findOrFail($resellerId);
        $settings = ResellerSetting::where('reseller_id', $resellerId)->get()->groupBy('group');
        return view('admin.resellers.settings', compact('reseller','settings'));
    }

    public function update(Request $request, $resellerId) {
        if ($r = $this->auth()) return $r;
        $reseller = Reseller::findOrFail($resellerId);
        foreach ($request->except(['_token','_method']) as $key => $value) {
            [$group, $k] = explode('__', $key, 2) + [null, null];
            if ($group && $k) {
                ResellerSetting::updateOrCreate(
                    ['reseller_id'=>$resellerId,'group'=>$group,'key'=>$k],
                    ['value'=>$value]
                );
            }
        }
        return back()->with('success', 'Reseller settings saved!');
    }

    public function templates($resellerId) {
        if ($r = $this->auth()) return $r;
        $reseller  = Reseller::findOrFail($resellerId);
        $templates = NotificationTemplate::where('reseller_id',$resellerId)->orderBy('event')->get();
        $events    = NotificationTemplate::events();
        return view('admin.resellers.templates', compact('reseller','templates','events'));
    }

    public function storeTemplate(Request $request, $resellerId) {
        if ($r = $this->auth()) return $r;
        $request->validate(['event'=>'required','channel'=>'required|in:sms,email,whatsapp','body'=>'required']);
        NotificationTemplate::create([
            'event'       => $request->event,
            'channel'     => $request->channel,
            'subject'     => $request->subject,
            'body'        => $request->body,
            'active'      => $request->has('active') ? 1 : 0,
            'reseller_id' => $resellerId,
        ]);
        return back()->with('success', 'Template created!');
    }

    public function destroyTemplate(Request $request, $resellerId, $templateId) {
        if ($r = $this->auth()) return $r;
        NotificationTemplate::where('id',$templateId)->where('reseller_id',$resellerId)->delete();
        return back()->with('success', 'Template deleted.');
    }
}