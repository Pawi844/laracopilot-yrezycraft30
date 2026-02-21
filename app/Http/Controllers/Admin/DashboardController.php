<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\IspClient;
use App\Models\RadiusSession;
use App\Models\Nas;
use App\Models\Router;
use App\Models\IspPlan;
use App\Models\Tr069Device;
use App\Models\NotificationLog;
use App\Models\Reseller;
use App\Models\Operator;
use App\Models\OfflineAlert;

class DashboardController extends Controller {
    public function index() {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');

        $resellerId = session('admin_reseller_id');
        $query = fn($model) => $resellerId ? $model::where('reseller_id', $resellerId) : $model::query();

        $totalClients = $query(IspClient::class)->count();
        $activeClients = $query(IspClient::class)->where('status', 'active')->count();
        $onlineClients = RadiusSession::where('status', 'active')->count();
        $expiredClients = $query(IspClient::class)->where('status', 'expired')->count();
        $suspendedClients = $query(IspClient::class)->where('status', 'suspended')->count();

        $totalNas = $query(Nas::class)->count();
        $activeNas = $query(Nas::class)->where('status', 'active')->count();
        $totalRouters = $query(Router::class)->count();
        $onlineRouters = $query(Router::class)->where('status', 'online')->count();

        $totalPlans = $query(IspPlan::class)->count();
        $tr069Online = $query(Tr069Device::class)->where('status', 'online')->count();
        $tr069Total = $query(Tr069Device::class)->count();

        $offlineAlerts = OfflineAlert::with('client')
            ->where('notified', false)
            ->orderBy('offline_hours', 'desc')
            ->limit(5)->get();

        $recentSessions = RadiusSession::with('client')
            ->where('status', 'active')
            ->orderBy('start_time', 'desc')
            ->limit(10)->get();

        $totalResellers = Reseller::count();
        $activeResellers = Reseller::where('status', 'active')->count();

        // Monthly revenue SQLite-compatible
        $monthlyRevenue = RadiusSession::selectRaw("CAST(strftime('%m', created_at) AS INTEGER) as month, COUNT(*) as sessions")
            ->whereRaw("strftime('%Y', created_at) = ?", [now()->year])
            ->groupByRaw("strftime('%m', created_at)")
            ->orderByRaw("strftime('%m', created_at)")
            ->get();

        $notifStats = [
            'sms' => NotificationLog::where('channel', 'sms')->count(),
            'whatsapp' => NotificationLog::where('channel', 'whatsapp')->count(),
            'email' => NotificationLog::where('channel', 'email')->count(),
        ];

        return view('admin.dashboard', compact(
            'totalClients','activeClients','onlineClients','expiredClients','suspendedClients',
            'totalNas','activeNas','totalRouters','onlineRouters',
            'totalPlans','tr069Online','tr069Total',
            'offlineAlerts','recentSessions','totalResellers','activeResellers',
            'monthlyRevenue','notifStats'
        ));
    }
}