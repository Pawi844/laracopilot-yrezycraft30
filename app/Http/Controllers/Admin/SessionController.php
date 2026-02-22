<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Router;
use App\Models\IspClient;
use App\Models\RadiusSession;
use App\Services\MikrotikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SessionController extends Controller {
    private function auth() { if (!session('admin_logged_in')) return redirect()->route('admin.login'); return null; }

    /**
     * Live sessions — polled directly from MikroTik via RouterOS API
     */
    public function index(Request $req) {
        if ($r = $this->auth()) return $r;
        $routers = Router::orderBy('name')->get();
        $selectedRouter = null;
        $sessions = collect();
        $hotspot  = collect();
        $error    = null;
        $source   = 'mikrotik'; // mikrotik or radius

        $routerId = $req->router_id ?? $routers->first()?->id;

        if ($routerId) {
            $selectedRouter = Router::find($routerId);
            if ($selectedRouter) {
                try {
                    $svc = new MikrotikService($selectedRouter);
                    // PPPoE active connections
                    $raw = $svc->getActiveConnections();
                    foreach ($raw as $s) {
                        $sessions->push((object)[
                            'username'      => $s['name'] ?? '—',
                            'ip_address'    => $s['address'] ?? '—',
                            'uptime'        => $s['uptime'] ?? '—',
                            'service'       => $s['service'] ?? 'pppoe',
                            'caller_id'     => $s['caller-id'] ?? '—',
                            'encoding'      => $s['encoding'] ?? '—',
                            'id'            => $s['.id'] ?? null,
                        ]);
                    }
                    // Hotspot active
                    $rawHs = $svc->getHotspotActive();
                    foreach ($rawHs as $h) {
                        $hotspot->push((object)[
                            'username'   => $h['user'] ?? '—',
                            'ip_address' => $h['address'] ?? '—',
                            'mac'        => $h['mac-address'] ?? '—',
                            'uptime'     => $h['uptime'] ?? '—',
                            'idle_time'  => $h['idle-time'] ?? '—',
                            'id'         => $h['.id'] ?? null,
                        ]);
                    }
                } catch (\Exception $e) {
                    $error = 'Cannot reach router: '.$e->getMessage();
                    Log::error('[Sessions] '.$e->getMessage());
                }
            }
        }

        // Fallback: RADIUS sessions from DB if MikroTik unreachable or no router
        $radiusSessions = RadiusSession::with('client')
            ->whereNull('acctstoptime')
            ->latest('acctstarttime')
            ->take(100)
            ->get();

        $stats = [
            'pppoe'   => $sessions->count(),
            'hotspot' => $hotspot->count(),
            'radius'  => $radiusSessions->count(),
        ];

        return view('admin.sessions.index', compact(
            'routers','selectedRouter','sessions','hotspot','radiusSessions','error','stats','routerId'
        ));
    }

    /**
     * AJAX endpoint — returns fresh live session JSON for auto-refresh
     */
    public function live(Request $req) {
        if (!session('admin_logged_in')) return response()->json([],401);
        $router = Router::find($req->router_id ?? Router::first()?->id);
        if (!$router) return response()->json(['sessions'=>[],'hotspot'=>[],'error'=>'No router']);
        try {
            $svc      = new MikrotikService($router);
            $sessions = $svc->getActiveConnections();
            $hotspot  = $svc->getHotspotActive();
            return response()->json(['sessions'=>$sessions,'hotspot'=>$hotspot,'error'=>null,'time'=>now()->format('H:i:s')]);
        } catch (\Exception $e) {
            return response()->json(['sessions'=>[],'hotspot'=>[],'error'=>$e->getMessage()]);
        }
    }

    public function destroy(Request $req, $id) {
        if ($r = $this->auth()) return $r;
        $router = Router::find($req->router_id ?? Router::first()?->id);
        if ($router) {
            $svc = new MikrotikService($router);
            if ($req->type === 'hotspot') {
                $svc->query(['/ip/hotspot/active/remove','=.id='.$id]);
            } else {
                $svc->query(['/ppp/active/remove','=.id='.$id]);
            }
        }
        return back()->with('success','Session disconnected.');
    }
}