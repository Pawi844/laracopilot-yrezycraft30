<?php
namespace App\Services;

use App\Models\Router;
use App\Models\MikrotikCache;
use App\Models\RadiusSession;
use App\Models\IspClient;
use Illuminate\Support\Facades\Log;

class MikrotikService
{
    private Router $router;
    private $socket = null;
    private bool $connected = false;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    // ─── Low-level RouterOS API ───────────────────────────────────────────

    public function connect(): bool
    {
        try {
            $this->socket = @fsockopen(
                $this->router->ip_address,
                $this->router->api_port ?? 8728,
                $errno, $errstr, 5
            );
            if (!$this->socket) {
                Log::warning("MikroTik connect failed: {$this->router->ip_address} — $errstr");
                return false;
            }
            stream_set_timeout($this->socket, 8);
            $this->connected = true;
            return $this->login($this->router->username, $this->router->password);
        } catch (\Exception $e) {
            Log::error('MikroTik connect exception: ' . $e->getMessage());
            return false;
        }
    }

    public function disconnect(): void
    {
        if ($this->socket) {
            fclose($this->socket);
            $this->socket = null;
            $this->connected = false;
        }
    }

    private function login(string $user, string $pass): bool
    {
        $response = $this->communicate(['/login', '=name=' . $user, '=password=' . $pass]);
        if (isset($response[0]) && $response[0] === '!done') return true;
        // MD5 challenge login (RouterOS < 6.43)
        if (isset($response[0]) && $response[0] === '!done' && isset($response[1])) {
            preg_match('/ret=([0-9a-f]+)/', implode('', $response), $m);
            if ($m) {
                $hash = md5(chr(0) . $pass . pack('H*', $m[1]));
                $r2 = $this->communicate(['/login', '=name=' . $user, '=response=00' . $hash]);
                return isset($r2[0]) && $r2[0] === '!done';
            }
        }
        return isset($response[0]) && $response[0] === '!done';
    }

    private function writeWord(string $word): void
    {
        $len = strlen($word);
        if ($len < 0x80) fwrite($this->socket, chr($len));
        elseif ($len < 0x4000) fwrite($this->socket, chr(($len >> 8) | 0x80) . chr($len & 0xff));
        else fwrite($this->socket, chr(($len >> 16) | 0xc0) . chr(($len >> 8) & 0xff) . chr($len & 0xff));
        fwrite($this->socket, $word);
    }

    private function readWord(): string
    {
        $byte = ord(fread($this->socket, 1));
        if ($byte & 0x80) {
            if (($byte & 0xc0) === 0x80) {
                $len = (($byte & 0x3f) << 8) | ord(fread($this->socket, 1));
            } else {
                $len = (($byte & 0x3f) << 16) | (ord(fread($this->socket, 1)) << 8) | ord(fread($this->socket, 1));
            }
        } else {
            $len = $byte;
        }
        if ($len === 0) return '';
        $word = '';
        while (strlen($word) < $len) {
            $word .= fread($this->socket, $len - strlen($word));
        }
        return $word;
    }

    public function communicate(array $words): array
    {
        if (!$this->socket) return ['!fatal', 'Not connected'];
        foreach ($words as $word) $this->writeWord($word);
        $this->writeWord('');
        $response = [];
        while (true) {
            $word = $this->readWord();
            if ($word === '') continue;
            $response[] = $word;
            if ($word === '!done' || $word === '!fatal' || $word === '!trap') {
                // Read remaining words in this reply
                while (($w = $this->readWord()) !== '') $response[] = $w;
                break;
            }
        }
        return $response;
    }

    private function apiQuery(array $command): array
    {
        if (!$this->connected && !$this->connect()) return [];
        $raw = $this->communicate($command);
        return $this->parseResponse($raw);
    }

    private function parseResponse(array $raw): array
    {
        $result = [];
        $current = [];
        foreach ($raw as $word) {
            if ($word === '!re') {
                if ($current) $result[] = $current;
                $current = [];
            } elseif ($word === '!done') {
                if ($current) $result[] = $current;
                break;
            } elseif (str_starts_with($word, '=')) {
                $parts = explode('=', ltrim($word, '='), 2);
                $current[$parts[0]] = $parts[1] ?? '';
            }
        }
        return $result;
    }

    // ─── Cached Data Fetcher ──────────────────────────────────────────────

    private function cached(string $type, array $command, int $ttlMinutes = 2): array
    {
        $cache = MikrotikCache::firstOrNew(['router_id' => $this->router->id, 'data_type' => $type]);
        if (!$cache->exists || $cache->isStale($ttlMinutes)) {
            $data = $this->apiQuery($command);
            $cache->fill(['data' => json_encode($data), 'cached_at' => now()])->save();
            return $data;
        }
        return $cache->data_array;
    }

    // ─── Public API Methods ───────────────────────────────────────────────

    public function getResources(): array
    {
        $r = $this->apiQuery(['/system/resource/print']);
        return $r[0] ?? [];
    }

    public function getIdentity(): array
    {
        $r = $this->apiQuery(['/system/identity/print']);
        return $r[0] ?? [];
    }

    public function getInterfaces(): array
    {
        return $this->cached('interfaces', ['/interface/print'], 1);
    }

    public function getIpAddresses(): array
    {
        return $this->cached('ip_addresses', ['/ip/address/print'], 1);
    }

    public function getRoutes(): array
    {
        return $this->cached('routes', ['/ip/route/print'], 3);
    }

    public function getIpPools(): array
    {
        return $this->cached('ip_pools', ['/ip/pool/print'], 5);
    }

    public function getPppoeActive(): array
    {
        return $this->apiQuery(['/ppp/active/print']); // Always live
    }

    public function getHotspotActive(): array
    {
        return $this->apiQuery(['/ip/hotspot/active/print']); // Always live
    }

    public function getPppoeProfiles(): array
    {
        return $this->cached('pppoe_profiles', ['/ppp/profile/print'], 10);
    }

    public function getHotspotProfiles(): array
    {
        return $this->cached('hotspot_profiles', ['/ip/hotspot/profile/print'], 10);
    }

    public function getHotspotUsers(): array
    {
        return $this->cached('hotspot_users', ['/ip/hotspot/user/print'], 5);
    }

    public function getPppoeSecrets(): array
    {
        return $this->cached('pppoe_secrets', ['/ppp/secret/print'], 5);
    }

    public function getFirewallFilter(): array
    {
        return $this->cached('firewall_filter', ['/ip/firewall/filter/print'], 10);
    }

    public function getFirewallNat(): array
    {
        return $this->cached('firewall_nat', ['/ip/firewall/nat/print'], 10);
    }

    public function getFirewallMangle(): array
    {
        return $this->cached('firewall_mangle', ['/ip/firewall/mangle/print'], 10);
    }

    public function getQueueSimple(): array
    {
        return $this->cached('queue_simple', ['/queue/simple/print'], 5);
    }

    public function getQueueTree(): array
    {
        return $this->cached('queue_tree', ['/queue/tree/print'], 5);
    }

    public function getRadiusServers(): array
    {
        return $this->cached('radius_servers', ['/radius/print'], 10);
    }

    public function getArpTable(): array
    {
        return $this->apiQuery(['/ip/arp/print']);
    }

    public function getDhcpLeases(): array
    {
        return $this->apiQuery(['/ip/dhcp-server/lease/print']);
    }

    public function getBridges(): array
    {
        return $this->cached('bridges', ['/interface/bridge/print'], 10);
    }

    public function getWireless(): array
    {
        return $this->cached('wireless', ['/interface/wireless/print'], 5);
    }

    public function getWirelessRegistrations(): array
    {
        return $this->apiQuery(['/interface/wireless/registration-table/print']);
    }

    public function getLogs(int $limit = 50): array
    {
        return $this->apiQuery(['/log/print', '=count-only=']);
    }

    // ─── Write Operations ─────────────────────────────────────────────────

    public function addPppoeSecret(string $name, string $password, string $profile = 'default', string $service = 'pppoe'): bool
    {
        $r = $this->communicate(['/ppp/secret/add', '=name=' . $name, '=password=' . $password, '=profile=' . $profile, '=service=' . $service]);
        return in_array('!done', $r);
    }

    public function removePppoeSecret(string $name): bool
    {
        // Find .id first
        $secrets = $this->apiQuery(['/ppp/secret/print', '?name=' . $name]);
        if (empty($secrets)) return false;
        $r = $this->communicate(['/ppp/secret/remove', '=.id=' . $secrets[0]['.id']]);
        return in_array('!done', $r);
    }

    public function disconnectPppoeUser(string $name): bool
    {
        $active = $this->apiQuery(['/ppp/active/print', '?name=' . $name]);
        if (empty($active)) return false;
        $r = $this->communicate(['/ppp/active/remove', '=.id=' . $active[0]['.id']]);
        return in_array('!done', $r);
    }

    public function addHotspotUser(string $name, string $password, string $profile = 'default'): bool
    {
        $r = $this->communicate(['/ip/hotspot/user/add', '=name=' . $name, '=password=' . $password, '=profile=' . $profile]);
        return in_array('!done', $r);
    }

    public function disconnectHotspotUser(string $name): bool
    {
        $active = $this->apiQuery(['/ip/hotspot/active/print', '?user=' . $name]);
        if (empty($active)) return false;
        $r = $this->communicate(['/ip/hotspot/active/remove', '=.id=' . $active[0]['.id']]);
        return in_array('!done', $r);
    }

    public function addQueue(string $name, string $target, string $maxLimit, string $burstLimit = ''): bool
    {
        $cmd = ['/queue/simple/add', '=name=' . $name, '=target=' . $target, '=max-limit=' . $maxLimit];
        if ($burstLimit) $cmd[] = '=burst-limit=' . $burstLimit;
        return in_array('!done', $this->communicate($cmd));
    }

    public function reboot(): bool
    {
        $r = $this->communicate(['/system/reboot']);
        return in_array('!done', $r);
    }

    public function setRadiusConfig(string $nasIp, string $secret, string $authPort = '1812', string $acctPort = '1813'): bool
    {
        // Add RADIUS server on MikroTik pointing back to this system
        $r = $this->communicate([
            '/radius/add',
            '=service=ppp,hotspot',
            '=address=' . $nasIp,
            '=secret=' . $secret,
            '=authentication-port=' . $authPort,
            '=accounting-port=' . $acctPort,
        ]);
        return in_array('!done', $r);
    }

    // ─── RADIUS Sync ──────────────────────────────────────────────────────

    public function syncRadiusUsers(): array
    {
        $clients = IspClient::with('plan')
            ->where('status', 'active')
            ->where('router_id', $this->router->id)
            ->get();

        $synced = 0;
        $failed = 0;

        foreach ($clients as $client) {
            $password = $client->plain_password ?? 'changeme';
            $profile  = $client->plan->profile_name ?? 'default';

            if ($client->connection_type === 'pppoe') {
                $ok = $this->addPppoeSecret($client->username, $password, $profile);
            } elseif ($client->connection_type === 'hotspot') {
                $ok = $this->addHotspotUser($client->username, $password, $profile);
            } else {
                $ok = true;
            }

            $ok ? $synced++ : $failed++;
        }

        return ['synced' => $synced, 'failed' => $failed, 'total' => $clients->count()];
    }

    // ─── Helper ───────────────────────────────────────────────────────────

    public static function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)    return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)       return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    public static function uptimeHuman(string $uptime): string
    {
        // e.g. "10d2h35m12s" → human readable
        return str_replace(['d','h','m','s'], ['d ', 'h ', 'm ', 's'], $uptime);
    }
}