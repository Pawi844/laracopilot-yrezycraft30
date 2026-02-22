<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Router;
use App\Models\IspClient;
use App\Models\Nas;
use App\Services\MikrotikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MikrotikController extends Controller
{
    private function auth() { if (!session('admin_logged_in')) return redirect()->route('admin.login'); return null; }

    private function getService(Router $router): ?MikrotikService
    {
        try {
            $svc = new MikrotikService($router);
            if (!$svc->connect()) return null;
            return $svc;
        } catch (\Throwable $e) {
            Log::error('[MikrotikController] getService: '.$e->getMessage());
            return null;
        }
    }

    public function selectRouter()
    {
        if ($r = $this->auth()) return $r;
        $routers = Router::orderBy('name')->get();
        return view('admin.mikrotik.select', compact('routers'));
    }

    public function setupGuide()
    {
        if ($r = $this->auth()) return $r;
        return view('admin.mikrotik.setup_guide');
    }

    public function dashboard($routerId)
    {
        if ($r = $this->auth()) return $r;
        $router  = Router::findOrFail($routerId);
        $routers = Router::orderBy('name')->get();
        $error   = null;
        $sysRes  = [];
        $ifaces  = [];
        $ipAddrs = [];
        $identity = $router->name;

        $svc = $this->getService($router);
        if ($svc) {
            try {
                $sysRes   = $svc->getSystemResource();
                $ifaces   = $svc->getInterfaces();
                $ipAddrs  = $svc->getIpAddresses();
                $identity = $svc->getSystemIdentity();
            } catch (\Throwable $e) {
                $error = $e->getMessage();
            } finally {
                $svc->disconnect();
            }
        } else {
            $error = 'Cannot connect to router. Check IP address, API port ('.(($router->api_port) ?? 8728).'), username/password, and ensure "/ip service enable api" is run on MikroTik.';
            if ($router->use_ovpn) $error .= ' OpenVPN tunnel is enabled — verify the OVPN gateway ('.$router->ovpn_gateway.') is reachable.';
        }

        // Stats from DB as fallback
        $dbStats = [
            'total_clients' => IspClient::count(),
            'active'        => IspClient::where('status','active')->count(),
            'suspended'     => IspClient::where('status','suspended')->count(),
        ];

        return view('admin.mikrotik.dashboard', compact(
            'router','routers','error','sysRes','ifaces','ipAddrs','identity','dbStats'
        ));
    }

    public function pppoe($routerId)
    {
        if ($r = $this->auth()) return $r;
        $router   = Router::findOrFail($routerId);
        $routers  = Router::orderBy('name')->get();
        $error    = null;
        $active   = collect();
        $secrets  = collect();

        $svc = $this->getService($router);
        if ($svc) {
            try {
                $active  = collect($svc->getActiveConnections());
                $secrets = collect($svc->getPppoeSecrets());
            } catch (\Throwable $e) { $error = $e->getMessage(); }
            finally { $svc->disconnect(); }
        } else {
            $error = 'Cannot connect to '.$router->name.' via API.';
        }

        return view('admin.mikrotik.pppoe', compact('router','routers','active','secrets','error'));
    }

    public function disconnectPppoe(Request $req, $routerId)
    {
        if ($r = $this->auth()) return $r;
        $router = Router::findOrFail($routerId);
        $svc    = $this->getService($router);
        if ($svc) {
            $svc->disconnectPppoe($req->id);
            $svc->disconnect();
            return back()->with('success','PPPoE session disconnected.');
        }
        return back()->with('error','Cannot connect to router.');
    }

    public function addPppoeSecret(Request $req, $routerId)
    {
        if ($r = $this->auth()) return $r;
        $req->validate(['name'=>'required','password'=>'required','profile'=>'nullable']);
        $router = Router::findOrFail($routerId);
        $svc    = $this->getService($router);
        if ($svc) {
            $ok = $svc->addPppoeSecret($req->name, $req->password, $req->profile ?? 'default');
            $svc->disconnect();
            return back()->with($ok ? 'success' : 'error', $ok ? 'PPPoE secret added!' : 'Failed to add secret.');
        }
        return back()->with('error','Cannot connect to router.');
    }

    public function deletePppoeSecret(Request $req, $routerId)
    {
        if ($r = $this->auth()) return $r;
        $router = Router::findOrFail($routerId);
        $svc    = $this->getService($router);
        if ($svc) {
            $svc->deletePppoeSecret($req->id);
            $svc->disconnect();
            return back()->with('success','PPPoE secret deleted.');
        }
        return back()->with('error','Cannot connect to router.');
    }

    public function hotspot($routerId)
    {
        if ($r = $this->auth()) return $r;
        $router  = Router::findOrFail($routerId);
        $routers = Router::orderBy('name')->get();
        $error   = null;
        $active  = collect();
        $users   = collect();

        $svc = $this->getService($router);
        if ($svc) {
            try {
                $active = collect($svc->getHotspotActive());
                $users  = collect($svc->getHotspotUsers());
            } catch (\Throwable $e) { $error = $e->getMessage(); }
            finally { $svc->disconnect(); }
        } else {
            $error = 'Cannot connect to '.$router->name.' via API.';
        }

        return view('admin.mikrotik.hotspot', compact('router','routers','active','users','error'));
    }

    public function disconnectHotspot(Request $req, $routerId)
    {
        if ($r = $this->auth()) return $r;
        $router = Router::findOrFail($routerId);
        $svc    = $this->getService($router);
        if ($svc) {
            $svc->disconnectHotspot($req->id);
            $svc->disconnect();
        }
        return back()->with('success','Hotspot session disconnected.');
    }

    public function queues($routerId)
    {
        if ($r = $this->auth()) return $r;
        $router  = Router::findOrFail($routerId);
        $routers = Router::orderBy('name')->get();
        $queues  = collect();
        $error   = null;

        $svc = $this->getService($router);
        if ($svc) {
            try { $queues = collect($svc->getQueues()); }
            catch (\Throwable $e) { $error = $e->getMessage(); }
            finally { $svc->disconnect(); }
        } else {
            $error = 'Cannot connect to '.$router->name.'.';
        }
        return view('admin.mikrotik.queues', compact('router','routers','queues','error'));
    }

    public function firewall($routerId)
    {
        if ($r = $this->auth()) return $r;
        $router  = Router::findOrFail($routerId);
        $routers = Router::orderBy('name')->get();
        $rules   = collect();
        $error   = null;

        $svc = $this->getService($router);
        if ($svc) {
            try { $rules = collect($svc->getFirewallFilter()); }
            catch (\Throwable $e) { $error = $e->getMessage(); }
            finally { $svc->disconnect(); }
        } else {
            $error = 'Cannot connect to '.$router->name.'.';
        }
        return view('admin.mikrotik.firewall', compact('router','routers','rules','error'));
    }

    public function dhcp($routerId)
    {
        if ($r = $this->auth()) return $r;
        $router  = Router::findOrFail($routerId);
        $routers = Router::orderBy('name')->get();
        $leases  = collect();
        $error   = null;

        $svc = $this->getService($router);
        if ($svc) {
            try { $leases = collect($svc->getDhcpLeases()); }
            catch (\Throwable $e) { $error = $e->getMessage(); }
            finally { $svc->disconnect(); }
        } else {
            $error = 'Cannot connect to '.$router->name.'.';
        }
        return view('admin.mikrotik.dhcp', compact('router','routers','leases','error'));
    }

    public function wireless($routerId)
    {
        if ($r = $this->auth()) return $r;
        $router   = Router::findOrFail($routerId);
        $routers  = Router::orderBy('name')->get();
        $ifaces   = collect();
        $clients  = collect();
        $error    = null;

        $svc = $this->getService($router);
        if ($svc) {
            try {
                $ifaces  = collect($svc->getWirelessInterfaces());
                $clients = collect($svc->getWirelessClients());
            } catch (\Throwable $e) { $error = $e->getMessage(); }
            finally { $svc->disconnect(); }
        } else {
            $error = 'Cannot connect to '.$router->name.'.';
        }
        return view('admin.mikrotik.wireless', compact('router','routers','ifaces','clients','error'));
    }

    public function radius($routerId)
    {
        if ($r = $this->auth()) return $r;
        $router  = Router::findOrFail($routerId);
        $routers = Router::orderBy('name')->get();
        $servers = collect();
        $error   = null;
        $nasServers = Nas::orderBy('shortname')->get();

        $svc = $this->getService($router);
        if ($svc) {
            try { $servers = collect($svc->getRadiusServers()); }
            catch (\Throwable $e) { $error = $e->getMessage(); }
            finally { $svc->disconnect(); }
        } else {
            $error = 'Cannot connect to '.$router->name.'.';
        }
        return view('admin.mikrotik.radius', compact('router','routers','servers','error','nasServers'));
    }

    public function pushRadiusConfig(Request $req, $routerId)
    {
        if ($r = $this->auth()) return $r;
        $req->validate(['radius_ip'=>'required','radius_secret'=>'required']);
        $router = Router::findOrFail($routerId);
        $svc    = $this->getService($router);
        if ($svc) {
            $ok = $svc->pushRadiusConfig($req->radius_ip, $req->radius_secret, (int)($req->radius_port ?? 1812));
            $svc->disconnect();
            return back()->with($ok ? 'success' : 'error', $ok ? 'RADIUS config pushed to MikroTik!' : 'Failed to push RADIUS config.');
        }
        return back()->with('error','Cannot connect to '.$router->name.'.');
    }

    public function syncUsers($routerId)
    {
        if ($r = $this->auth()) return $r;
        $router  = Router::findOrFail($routerId);
        $svc     = $this->getService($router);
        $synced  = 0;
        if ($svc) {
            $secrets = $svc->getPppoeSecrets();
            foreach ($secrets as $s) {
                IspClient::updateOrCreate(
                    ['username' => $s['name'] ?? ''],
                    [
                        'first_name'      => $s['name'] ?? 'Unknown',
                        'last_name'       => '',
                        'connection_type' => 'pppoe',
                        'status'          => ($s['disabled'] ?? 'false') === 'false' ? 'active' : 'suspended',
                    ]
                );
                $synced++;
            }
            $svc->disconnect();
        }
        return back()->with('success', $synced > 0 ? "Synced {$synced} PPPoE secrets from {$router->name}." : 'No secrets found or connection failed.');
    }
}