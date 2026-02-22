<?php
namespace App\Services;

use App\Models\Router;
use Illuminate\Support\Facades\Log;

class MikrotikService
{
    private ?Router $router;
    private mixed $socket = null;
    private bool $connected = false;

    public function __construct(?Router $router = null)
    {
        $this->router = $router;
    }

    // ── Static helpers (usable without instantiation) ──────────────────────────

    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        if ($bytes <= 0) return '0 B';
        $units = ['B','KB','MB','GB','TB','PB'];
        $i     = (int) floor(log($bytes, 1024));
        $i     = min($i, count($units) - 1);
        return round($bytes / pow(1024, $i), $precision) . ' ' . $units[$i];
    }

    public static function formatSpeed(int $bitsPerSecond): string
    {
        if ($bitsPerSecond <= 0) return '0 bps';
        $units = ['bps','Kbps','Mbps','Gbps'];
        $i     = (int) floor(log($bitsPerSecond, 1000));
        $i     = min($i, count($units) - 1);
        return round($bitsPerSecond / pow(1000, $i), 2) . ' ' . $units[$i];
    }

    public static function formatUptime(string $uptime): string
    {
        // MikroTik format: 1w2d3h4m5s or 3h4m5s etc.
        if (empty($uptime)) return '—';
        $uptime = preg_replace_callback(
            '/(?:(\d+)w)?(?:(\d+)d)?(?:(\d+)h)?(?:(\d+)m)?(?:(\d+)s)?/',
            function ($m) {
                $parts = [];
                if (!empty($m[1])) $parts[] = $m[1].'w';
                if (!empty($m[2])) $parts[] = $m[2].'d';
                if (!empty($m[3])) $parts[] = $m[3].'h';
                if (!empty($m[4])) $parts[] = $m[4].'m';
                if (!empty($m[5])) $parts[] = $m[5].'s';
                return implode(' ', $parts);
            },
            $uptime
        );
        return trim($uptime) ?: $uptime;
    }

    // ── Connection ─────────────────────────────────────────────────────────────

    public function connect(): bool
    {
        if (!$this->router) return false;

        $host     = ($this->router->use_ovpn && $this->router->ovpn_gateway)
                        ? $this->router->ovpn_gateway
                        : $this->router->ip_address;
        $port     = (int) ($this->router->api_port ?? 8728);
        $username = $this->router->username ?? 'admin';
        $password = $this->router->password ?? '';

        try {
            $this->socket = @fsockopen($host, $port, $errno, $errstr, 5);
            if (!$this->socket) {
                Log::warning("[MikroTik] Cannot connect to {$host}:{$port} — {$errstr} ({$errno})");
                return false;
            }
            stream_set_timeout($this->socket, 5);

            $this->write(['/login', '=name='.$username, '=password='.$password]);
            $result = $this->read();

            if (isset($result[0]) && str_starts_with($result[0], '!done')) {
                $this->connected = true;
                return true;
            }

            // Fallback: two-step MD5 challenge (older RouterOS)
            foreach ($result as $line) {
                if (str_starts_with($line, '=ret=')) {
                    $challenge = substr($line, 5);
                    $hash      = '00'.md5(chr(0).$password.pack('H*', $challenge));
                    $this->write(['/login', '=name='.$username, '=response='.$hash]);
                    $result2 = $this->read();
                    if (isset($result2[0]) && str_starts_with($result2[0], '!done')) {
                        $this->connected = true;
                        return true;
                    }
                }
            }

            Log::warning("[MikroTik] Login failed for {$host}");
            $this->disconnect();
            return false;

        } catch (\Throwable $e) {
            Log::error('[MikroTik] connect(): '.$e->getMessage());
            return false;
        }
    }

    public function testConnection(): bool
    {
        $ok = $this->connect();
        $this->disconnect();
        return $ok;
    }

    public function disconnect(): void
    {
        if ($this->socket) {
            try { @fclose($this->socket); } catch (\Throwable $e) {}
            $this->socket    = null;
            $this->connected = false;
        }
    }

    public function isConnected(): bool
    {
        return $this->connected;
    }

    // ── Low-level API ──────────────────────────────────────────────────────────

    private function encodeLength(int $len): string
    {
        if ($len < 0x80)       return chr($len);
        if ($len < 0x4000)     return chr(($len >> 8) | 0x80) . chr($len & 0xFF);
        if ($len < 0x200000)   return chr(($len >> 16) | 0xC0) . chr(($len >> 8) & 0xFF) . chr($len & 0xFF);
        if ($len < 0x10000000) return chr(($len >> 24) | 0xE0) . chr(($len >> 16) & 0xFF) . chr(($len >> 8) & 0xFF) . chr($len & 0xFF);
        return chr(0xF0) . chr(($len >> 24) & 0xFF) . chr(($len >> 16) & 0xFF) . chr(($len >> 8) & 0xFF) . chr($len & 0xFF);
    }

    private function decodeLength(): int
    {
        if (!$this->socket) return 0;
        $b = ord(fread($this->socket, 1));
        if ($b < 0x80)  return $b;
        if ($b < 0xC0)  return (($b & ~0x80) << 8)  | ord(fread($this->socket, 1));
        if ($b < 0xE0)  return (($b & ~0xC0) << 16) | (ord(fread($this->socket, 1)) << 8)  | ord(fread($this->socket, 1));
        if ($b < 0xF0)  return (($b & ~0xE0) << 24) | (ord(fread($this->socket, 1)) << 16) | (ord(fread($this->socket, 1)) << 8) | ord(fread($this->socket, 1));
        return (ord(fread($this->socket, 1)) << 24) | (ord(fread($this->socket, 1)) << 16) | (ord(fread($this->socket, 1)) << 8) | ord(fread($this->socket, 1));
    }

    public function write(array $words): void
    {
        if (!$this->socket) return;
        foreach ($words as $word) {
            $len = strlen($word);
            fwrite($this->socket, $this->encodeLength($len) . $word);
        }
        fwrite($this->socket, chr(0));
    }

    public function read(): array
    {
        if (!$this->socket) return [];
        $result = [];
        while (true) {
            $len = $this->decodeLength();
            if ($len === 0) break;
            $result[] = fread($this->socket, $len);
        }
        return $result;
    }

    public function query(array $words): array
    {
        if (!$this->socket && !$this->connect()) return [];
        try {
            $this->write($words);
            $raw     = [];
            $current = [];
            while (true) {
                $len = $this->decodeLength();
                if ($len === 0) {
                    if (!empty($current)) { $raw[] = $current; $current = []; }
                    $tlen = $this->decodeLength();
                    if ($tlen > 0) {
                        $type = fread($this->socket, $tlen);
                        if ($type === '!done' || $type === '!trap') break;
                        if ($type === '!re') continue;
                        $current[] = $type;
                    } else {
                        break;
                    }
                } else {
                    $word = fread($this->socket, $len);
                    if ($word === '!done') break;
                    if ($word === '!trap') { $this->read(); break; }
                    if ($word === '!re')   continue;
                    $current[] = $word;
                }
            }
            return array_map(function ($words) {
                $row = [];
                foreach ($words as $w) {
                    if (str_starts_with($w, '=')) {
                        $parts = explode('=', ltrim($w, '='), 2);
                        $row[$parts[0]] = $parts[1] ?? '';
                    }
                }
                return $row;
            }, $raw);
        } catch (\Throwable $e) {
            Log::error('[MikroTik] query(): '.$e->getMessage());
            return [];
        }
    }

    // ── High-level helpers ────────────────────────────────────────────────────

    public function getSystemResource(): array
    {
        $res = $this->query(['/system/resource/print']);
        return $res[0] ?? [];
    }

    public function getSystemIdentity(): string
    {
        $res = $this->query(['/system/identity/print']);
        return $res[0]['name'] ?? ($this->router?->name ?? 'Unknown');
    }

    public function getInterfaces(): array
    {
        return $this->query(['/interface/print']);
    }

    public function getIpAddresses(): array
    {
        return $this->query(['/ip/address/print']);
    }

    public function getActiveConnections(): array
    {
        return $this->query(['/ppp/active/print']);
    }

    public function getPppoeSecrets(): array
    {
        return $this->query(['/ppp/secret/print']);
    }

    public function getHotspotActive(): array
    {
        return $this->query(['/ip/hotspot/active/print']);
    }

    public function getHotspotUsers(): array
    {
        return $this->query(['/ip/hotspot/user/print']);
    }

    public function getQueues(): array
    {
        return $this->query(['/queue/simple/print']);
    }

    public function getFirewallFilter(): array
    {
        return $this->query(['/ip/firewall/filter/print']);
    }

    public function getDhcpLeases(): array
    {
        return $this->query(['/ip/dhcp-server/lease/print']);
    }

    public function getWirelessInterfaces(): array
    {
        return $this->query(['/interface/wireless/print']);
    }

    public function getWirelessClients(): array
    {
        return $this->query(['/interface/wireless/registration-table/print']);
    }

    public function getRadiusServers(): array
    {
        return $this->query(['/radius/print']);
    }

    public function addPppoeSecret(string $name, string $password, string $profile = 'default'): bool
    {
        try {
            $this->write(['/ppp/secret/add', '=name='.$name, '=password='.$password, '=profile='.$profile, '=service=pppoe']);
            $r = $this->read();
            return isset($r[0]) && str_starts_with($r[0], '!done');
        } catch (\Throwable $e) { return false; }
    }

    public function deletePppoeSecret(string $id): bool
    {
        try {
            $this->write(['/ppp/secret/remove', '=.id='.$id]);
            $r = $this->read();
            return isset($r[0]) && str_starts_with($r[0], '!done');
        } catch (\Throwable $e) { return false; }
    }

    public function disconnectPppoe(string $id): bool
    {
        try {
            $this->write(['/ppp/active/remove', '=.id='.$id]);
            $r = $this->read();
            return isset($r[0]) && str_starts_with($r[0], '!done');
        } catch (\Throwable $e) { return false; }
    }

    public function disconnectHotspot(string $id): bool
    {
        try {
            $this->write(['/ip/hotspot/active/remove', '=.id='.$id]);
            $r = $this->read();
            return isset($r[0]) && str_starts_with($r[0], '!done');
        } catch (\Throwable $e) { return false; }
    }

    public function pushRadiusConfig(string $radiusIp, string $secret, int $port = 1812): bool
    {
        try {
            $existing = $this->query(['/radius/print']);
            foreach ($existing as $e) {
                if (isset($e['.id'])) {
                    $this->write(['/radius/remove', '=.id='.$e['.id']]);
                    $this->read();
                }
            }
            $this->write(['/radius/add', '=service=ppp,hotspot', '=address='.$radiusIp, '=secret='.$secret, '=authentication-port='.$port, '=accounting-port='.($port + 1)]);
            $r = $this->read();
            return isset($r[0]) && str_starts_with($r[0], '!done');
        } catch (\Throwable $e) { return false; }
    }

    public function generateOvpnConfig(Router $router): string
    {
        $host = $router->ovpn_gateway ?: $router->ip_address;
        return "client\ndev tun\nproto tcp\nremote {$host} 1194\nresolv-retry infinite\nnobind\npersist-key\npersist-tun\nauth-user-pass\nverb 3\n# Username: ".($router->ovpn_username ?: 'your_username')."\n# Generated by ISP Manager for router: {$router->name}\n";
    }
}