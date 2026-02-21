<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Router;
use App\Models\MikrotikCache;
use App\Services\MikrotikService;
use Illuminate\Http\Request;

class MikrotikController extends Controller
{
    private function auth()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        return null;
    }

    private function getService(Router $router): ?MikrotikService
    {
        $svc = new MikrotikService($router);
        if (!$svc->connect()) return null;
        return $svc;
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

    public function dashboard(Request $request, $routerId)
    {
        if ($r = $this->auth()) return $r;
        $router = Router::findOrFail($routerId);
        $svc = $this->getService($router);
        $connected = $svc !== null;
        $resources = $identity = [];
        $pppoeActive = $hotspotActive = $interfaces = $ipAddresses = [];
        if ($svc) {
            $resources     = $svc->getResources();
            $identity      = $svc->getIdentity();
            $pppoeActive   = $svc->getPppoeActive();
            $hotspotActive = $svc->getHotspotActive();
            $interfaces    = $svc->getInterfaces();
            $ipAddresses   = $svc->getIpAddresses();
            $router->update(['status' => 'online', 'last_sync' => now()]);
            $svc->disconnect();
        } else {
            $router->update(['status' => 'offline']);
        }
        return view('admin.mikrotik.dashboard', compact(
            'router','connected','resources','identity',
            'pppoeActive','hotspotActive','interfaces','ipAddresses'
        ));
    }

    public function interfaces($routerId)
    {
        if ($r = $this->auth()) return $r;
        $router = Router::findOrFail($routerId);
        $svc = $this->getService($router);
        $interfaces = $svc ? $svc->getInterfaces() : [];
        $ipAddresses = $svc ? $svc->getIpAddresses() : [];
        if ($svc) $svc->disconnect();
        return view('admin.mikrotik.interfaces', compact('router','interfaces','ipAddresses'));
    }

    public function pppoe($routerId)
    {
        if ($r = $this->auth()) return $r;
        $router = Router::findOrFail($routerId);
        $svc = $this->getService($router);
        $active   = $svc ? $svc->getPppoeActive() : [];
        $secrets  = $svc ? $svc->getPppoeSecrets() : [];
        $profiles = $svc ? $svc->getPppoeProfiles() : [];
        if ($svc) $svc->disconnect();
        return view('admin.mikrotik.pppoe', compact('router','active','secrets','profiles'));
    }

    public function disconnectPppoe(Request $request, $routerId)
    {
        if ($r = $this->auth()) return $r;
        $router = Router::findOrFail($routerId);
        $svc = $this->getService($router);
        if ($svc) { $svc->disconnectPppoeUser($request->username); $svc->disconnect(); }
        return back()->with('success', 'PPPoE user ' . $request->username . ' disconnected.');
    }

    public function addPppoeSecret(Request $request, $routerId)
    {
        if ($r = $this->auth()) return $r;
        $request->validate(['name'=>'required','password'=>'required','profile'=>'nullable']);
        $router = Router::findOrFail($routerId);
        $svc = $this->getService($router);
        $ok = false;
        if ($svc) {
            $ok = $svc->addPppoeSecret($request->name, $request->password, $request->profile ?? 'default');
            $svc->disconnect();
        }
        return back()->with($ok ? 'success' : 'error', $ok ? 'PPPoE secret added: '.$request->name : 'Failed to add secret.');
    }

    public function deletePppoeSecret(Request $request, $routerId)
    {
        if ($r = $this->auth()) return $r;
        $router = Router::findOrFail($routerId);
        $svc = $this->getService($router);
        $ok = false;
        if ($svc) { $ok = $svc->removePppoeSecret($request->name); $svc->disconnect(); }
        return back()->with($ok ? 'success' : 'error', $ok ? 'Secret removed.' : 'Failed.');
    }

    public function hotspot($routerId)
    {
        if ($r = $this->auth()) return $r;
        $router = Router::findOrFail($routerId);
        $svc = $this->getService($router);
        $active   = $svc ? $svc->getHotspotActive() : [];
        $users    = $svc ? $svc->getHotspotUsers() : [];
        $profiles = $svc ? $svc->getHotspotProfiles() : [];
        if ($svc) $svc->disconnect();
        return view('admin.mikrotik.hotspot', compact('router','active','users','profiles'));
    }

    public function disconnectHotspot(Request $request, $routerId)
    {
        if ($r = $this->auth()) return $r;
        $router = Router::findOrFail($routerId);
        $svc = $this->getService($router);
        if ($svc) { $svc->disconnectHotspotUser($request->username); $svc->disconnect(); }
        return back()->with('success', 'Hotspot user disconnected.');
    }

    public function ipPools($routerId)
    {
        if ($r = $this->auth()) return $r;
        $router = Router::findOrFail($routerId);
        $svc = $this->getService($router);
        $pools = $svc ? $svc->getIpPools() : [];
        if ($svc) $svc->disconnect();
        return view('admin.mikrotik.ip_pools', compact('router','pools'));
    }

    public function queues($routerId)
    {
        if ($r = $this->auth()) return $r;
        $router = Router::findOrFail($routerId);
        $svc = $this->getService($router);
        $simple = $svc ? $svc->getQueueSimple() : [];
        $tree   = $svc ? $svc->getQueueTree() : [];
        if ($svc) $svc->disconnect();
        return view('admin.mikrotik.queues', compact('router','simple','tree'));
    }

    public function firewall($routerId)
    {
        if ($r = $this->auth()) return $r;
        $router = Router::findOrFail($routerId);
        $svc = $this->getService($router);
        $filter = $svc ? $svc->getFirewallFilter() : [];
        $nat    = $svc ? $svc->getFirewallNat() : [];
        $mangle = $svc ? $svc->getFirewallMangle() : [];
        if ($svc) $svc->disconnect();
        return view('admin.mikrotik.firewall', compact('router','filter','nat','mangle'));
    }

    public function dhcp($routerId)
    {
        if ($r = $this->auth()) return $r;
        $router = Router::findOrFail($routerId);
        $svc = $this->getService($router);
        $leases = $svc ? $svc->getDhcpLeases() : [];
        $arp    = $svc ? $svc->getArpTable() : [];
        if ($svc) $svc->disconnect();
        return view('admin.mikrotik.dhcp', compact('router','leases','arp'));
    }

    public function routes($routerId)
    {
        if ($r = $this->auth()) return $r;
        $router = Router::findOrFail($routerId);
        $svc = $this->getService($router);
        $routes = $svc ? $svc->getRoutes() : [];
        if ($svc) $svc->disconnect();
        return view('admin.mikrotik.routes', compact('router','routes'));
    }

    public function wireless($routerId)
    {
        if ($r = $this->auth()) return $r;
        $router = Router::findOrFail($routerId);
        $svc = $this->getService($router);
        $wireless      = $svc ? $svc->getWireless() : [];
        $registrations = $svc ? $svc->getWirelessRegistrations() : [];
        if ($svc) $svc->disconnect();
        return view('admin.mikrotik.wireless', compact('router','wireless','registrations'));
    }

    public function radius($routerId)
    {
        if ($r = $this->auth()) return $r;
        $router = Router::findOrFail($routerId);
        $svc = $this->getService($router);
        $radiusServers = $svc ? $svc->getRadiusServers() : [];
        if ($svc) $svc->disconnect();
        return view('admin.mikrotik.radius', compact('router','radiusServers'));
    }

    public function pushRadiusConfig(Request $request, $routerId)
    {
        if ($r = $this->auth()) return $r;
        $request->validate(['nas_ip'=>'required','secret'=>'required']);
        $router = Router::findOrFail($routerId);
        $svc = $this->getService($router);
        $ok = false;
        if ($svc) {
            $ok = $svc->setRadiusConfig($request->nas_ip, $request->secret, $request->auth_port ?? '1812', $request->acct_port ?? '1813');
            $svc->disconnect();
        }
        return back()->with($ok ? 'success' : 'error', $ok ? 'RADIUS server pushed to MikroTik!' : 'Failed to push RADIUS config.');
    }

    public function syncUsers($routerId)
    {
        if ($r = $this->auth()) return $r;
        $router = Router::findOrFail($routerId);
        $svc = $this->getService($router);
        $result = ['synced'=>0,'failed'=>0,'total'=>0];
        if ($svc) { $result = $svc->syncRadiusUsers(); $svc->disconnect(); }
        return back()->with('success', "Sync complete: {$result['synced']} synced, {$result['failed']} failed of {$result['total']} clients.");
    }
}