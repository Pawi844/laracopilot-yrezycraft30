<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\NotificationSchedule;
use App\Models\NotificationTemplate;
use Illuminate\Http\Request;

class NotificationScheduleController extends Controller {
    private function auth() { if (!session('admin_logged_in')) return redirect()->route('admin.login'); return null; }

    public function index() {
        if ($r = $this->auth()) return $r;
        $schedules = NotificationSchedule::orderBy('event')->get();
        $events    = NotificationSchedule::eventOptions();
        $templates = NotificationTemplate::whereNull('reseller_id')->get()->groupBy('event');
        return view('admin.settings.notification_schedule', compact('schedules','events','templates'));
    }

    public function store(Request $req) {
        if ($r = $this->auth()) return $r;
        $req->validate([
            'event'       =>'required',
            'channel'     =>'required|in:sms,email,whatsapp',
            'timing'      =>'required|in:immediate,days_before,days_after,on_day',
            'days_offset' =>'required_unless:timing,immediate|integer|min:0|max:90',
            'send_at_time'=>'required',
        ]);
        NotificationSchedule::create($req->only(['event','channel','timing','days_offset','send_at_time','active']));
        return back()->with('success','Schedule created!');
    }

    public function update(Request $req, $id) {
        if ($r = $this->auth()) return $r;
        $schedule = NotificationSchedule::findOrFail($id);
        $schedule->update([
            'active'     => $req->has('active') ? 1 : 0,
            'days_offset'=> $req->days_offset ?? $schedule->days_offset,
            'send_at_time'=> $req->send_at_time ?? $schedule->send_at_time,
        ]);
        return back()->with('success','Schedule updated!');
    }

    public function destroy($id) {
        if ($r = $this->auth()) return $r;
        NotificationSchedule::findOrFail($id)->delete();
        return back()->with('success','Schedule removed.');
    }
}