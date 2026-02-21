<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\RadiusSession;

class SessionController extends Controller {
    private function auth() { if (!session('admin_logged_in')) return redirect()->route('admin.login'); }

    public function index() {
        $this->auth();
        $sessions = RadiusSession::with('client')
            ->orderBy('start_time','desc')
            ->paginate(25);
        $activeSessions = RadiusSession::where('status','active')->count();
        $totalToday = RadiusSession::whereDate('start_time', today())->count();
        return view('admin.sessions.index', compact('sessions','activeSessions','totalToday'));
    }

    public function live() {
        $this->auth();
        $sessions = RadiusSession::with('client')
            ->where('status','active')
            ->orderBy('start_time','desc')
            ->get();
        $totalOnline = $sessions->count();
        $totalDownload = $sessions->sum('bytes_in');
        $totalUpload = $sessions->sum('bytes_out');
        return view('admin.sessions.live', compact('sessions','totalOnline','totalDownload','totalUpload'));
    }

    public function destroy($id) {
        $this->auth();
        $session = RadiusSession::findOrFail($id);
        $session->update(['status'=>'disconnect','stop_time'=>now(),'terminate_cause'=>'Admin-Reset']);
        return back()->with('success', 'Session terminated.');
    }
}