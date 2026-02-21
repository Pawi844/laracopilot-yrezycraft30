<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\NotificationTemplate;
use Illuminate\Http\Request;

class SettingsController extends Controller {
    private function auth() {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        return null;
    }

    // ─── Main Settings Hub ────────────────────────────────────────────
    public function index() {
        if ($r = $this->auth()) return $r;
        return view('admin.settings.index');
    }

    // ─── Group Settings (mpesa / sms / whatsapp / mail / general / billing) ─
    public function group(string $group) {
        if ($r = $this->auth()) return $r;
        $settings = SystemSetting::where('group', $group)->orderBy('sort_order')->get();
        return view('admin.settings.group', compact('settings', 'group'));
    }

    public function updateGroup(Request $request, string $group) {
        if ($r = $this->auth()) return $r;
        $settings = SystemSetting::where('group', $group)->get();
        foreach ($settings as $setting) {
            $val = $request->input($setting->key);
            if ($setting->type === 'toggle') {
                $val = $request->has($setting->key) ? '1' : '0';
            }
            if ($setting->type === 'password' && empty($val)) continue; // don't wipe existing secrets
            SystemSetting::set($group, $setting->key, $val ?? '');
        }
        // If mail settings changed — update runtime config
        if ($group === 'mail') $this->applyMailConfig();
        return back()->with('success', ucfirst($group) . ' settings saved!');
    }

    private function applyMailConfig(): void {
        config([
            'mail.mailers.smtp.host'       => SystemSetting::get('mail','host'),
            'mail.mailers.smtp.port'       => SystemSetting::get('mail','port'),
            'mail.mailers.smtp.encryption' => SystemSetting::get('mail','encryption'),
            'mail.mailers.smtp.username'   => SystemSetting::get('mail','username'),
            'mail.mailers.smtp.password'   => SystemSetting::get('mail','password'),
            'mail.from.address'            => SystemSetting::get('mail','from_address'),
            'mail.from.name'               => SystemSetting::get('mail','from_name'),
        ]);
    }

    public function testMail(Request $request) {
        if ($r = $this->auth()) return $r;
        $request->validate(['to' => 'required|email']);
        try {
            $this->applyMailConfig();
            \Mail::raw('Test email from MtaaKonnect ISP System. Configuration is working!', function($msg) use ($request) {
                $msg->to($request->to)->subject('MtaaKonnect — Mail Test');
            });
            return back()->with('success', 'Test email sent to ' . $request->to);
        } catch (\Exception $e) {
            return back()->with('error', 'Mail failed: ' . $e->getMessage());
        }
    }

    // ─── Notification Templates ───────────────────────────────────────
    public function templates() {
        if ($r = $this->auth()) return $r;
        $templates = NotificationTemplate::whereNull('reseller_id')->orderBy('event')->orderBy('channel')->get();
        $events    = NotificationTemplate::events();
        return view('admin.settings.templates', compact('templates','events'));
    }

    public function editTemplate($id) {
        if ($r = $this->auth()) return $r;
        $template = NotificationTemplate::findOrFail($id);
        $events   = NotificationTemplate::events();
        $vars     = ['{name}','{username}','{password}','{plan}','{expiry}','{days_left}','{amount}','{reference}','{company}','{support_phone}','{paybill_no}','{paybill_type}','{ip}'];
        return view('admin.settings.template_edit', compact('template','events','vars'));
    }

    public function updateTemplate(Request $request, $id) {
        if ($r = $this->auth()) return $r;
        $template = NotificationTemplate::findOrFail($id);
        $template->update([
            'subject' => $request->subject,
            'body'    => $request->body,
            'active'  => $request->has('active') ? 1 : 0,
        ]);
        return redirect()->route('admin.settings.templates')->with('success', 'Template updated!');
    }

    public function createTemplate() {
        if ($r = $this->auth()) return $r;
        $events = NotificationTemplate::events();
        $vars   = ['{name}','{username}','{password}','{plan}','{expiry}','{days_left}','{amount}','{reference}','{company}','{support_phone}','{paybill_no}','{paybill_type}','{ip}'];
        return view('admin.settings.template_create', compact('events','vars'));
    }

    public function storeTemplate(Request $request) {
        if ($r = $this->auth()) return $r;
        $request->validate(['event'=>'required','channel'=>'required|in:sms,email,whatsapp','body'=>'required']);
        NotificationTemplate::create([
            'event'   => $request->event,
            'channel' => $request->channel,
            'subject' => $request->subject,
            'body'    => $request->body,
            'active'  => $request->has('active') ? 1 : 0,
            'reseller_id' => null,
        ]);
        return redirect()->route('admin.settings.templates')->with('success', 'Template created!');
    }

    public function destroyTemplate($id) {
        if ($r = $this->auth()) return $r;
        NotificationTemplate::findOrFail($id)->delete();
        return back()->with('success', 'Template deleted.');
    }
}