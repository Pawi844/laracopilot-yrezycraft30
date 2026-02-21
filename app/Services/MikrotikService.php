<?php
namespace App\Services;
use App\Models\Nas;
use App\Models\Router;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class MikrotikService {
    private ?object $nas;
    private ?object $router;
    private string $ip;
    private int    $port;
    private string $user;
    private string $pass;
    private bool   $useOvpn;
    private string $ovpnGateway;
    private $api = null;

    public function __construct($nasOrRouter = null) {
        if ($nasOrRouter instanceof Router) {
            $this->router  = $nasOrRouter;
            $this->nas     = null;
            $this->ip      = $nasOrRouter->ip_address;
            $this->port    = (int)($nasOrRouter->api_port ?? 8728);
            $this->user    = $nasOrRouter->api_username ?? 'admin';
            $this->pass    = $nasOrRouter->api_password ?? '';
            $this->useOvpn = (bool)($nasOrRouter->use_ovpn ?? false);
            $this->ovpnGateway = $nasOrRouter->ovpn_gateway ?? '';
        } elseif ($nasOrRouter instanceof Nas) {
            $this->nas     = $nasOrRouter;
            $this->router  = null;
            $this->ip      = $nasOrRouter->nasname;
            $this->port    = 8728;
            $this->user    = $nasOrRouter->api_username ?? 'admin';
            $this->pass    = $nasOrRouter->api_password ?? '';
            $this->useOvpn = (bool)($nasOrRouter->use_ovpn ?? false);
            $this->ovpnGateway = $nasOrRouter->ovpn_gateway ?? '';
        } else {
            $this->ip   = '';
            $this->port = 8728;
            $this->user = 'admin';
            $this->pass = '';
            $this->useOvpn = false;
            $this->ovpnGateway = '';
        }
    }

    /**
     * Determine the effective IP to connect to.
     * If OpenVPN is enabled, we connect via the OVPN tunnel gateway IP
     * (the private IP assigned to this router's OVPN client).
     */
    private function effectiveIp(): string {
        if ($this->useOvpn && $this->ovpnGateway) {
            return $this->ovpnGateway; // e.g. 10.8.0.x — tunnel IP
        }
        return $this->ip;
    }

    /**
     * Connect to MikroTik RouterOS API
     */
    private function connect(): bool {
        if ($this->api) return true;
        $ip   = $this->effectiveIp();
        $port = $this->port;
        if (!$ip) return false;
        try {
            // Attempt TCP connection to RouterOS API port
            $sock = @fsockopen($ip, $port, $errno, $errstr, 5);
            if (!$sock) {
                Log::warning("[MikroTik] Cannot connect to {$ip}:{$port} — {$errstr}");
                return false;
            }
            // Simple RouterOS API login sequence
            $this->api = $sock;
            $this->apiLogin();
            return true;
        } catch (\Exception $e) {
            Log::error('[MikroTik] Connect error: '.$e->getMessage());
            return false;
        }
    }

    private function apiLogin(): void {
        $this->apiWrite([
            '/login',
            '=name='.$this->user,
            '=password='.$this->pass,
        ]);
        $this->apiRead();
    }

    private function apiWrite(array $sentence): void {
        if (!$this->api) return;
        foreach ($sentence as $word) {
            $len  = strlen($word);
            $lenB = '';
            if ($len < 0x80) {
                $lenB = chr($len);
            } elseif ($len < 0x4000) {
                $len  |= 0x8000;
                $lenB  = chr(($len >> 8) & 0xFF).chr($len & 0xFF);
            } else {
                $len  |= 0xC00000;
                $lenB  = chr(($len >> 16) & 0xFF).chr(($len >> 8) & 0xFF).chr($len & 0xFF);
            }
            fwrite($this->api, $lenB.$word);
        }
        fwrite($this->api, chr(0)); // end of sentence
    }

    private function apiRead(): array {
        if (!$this->api) return [];
        $r = [];
        while (true) {
            $lenB = ord(fread($this->api, 1));
            if ($lenB & 0x80) {
                if ($lenB & 0x40) {
                    $lenB  = ($lenB & 0x3F) << 16;
                    $lenB |= ord(fread($this->api,1)) << 8;
                    $lenB |= ord(fread($this->api,1));
                } else {
                    $lenB  = ($lenB & 0x7F) << 8;
                    $lenB |= ord(fread($this->api,1));
                }
            }
            if ($lenB == 0) break;
            $w = '';
            while (strlen($w) < $lenB) $w .= fread($this->api, $lenB - strlen($w));
            $r[] = $w;
        }
        return $r;
    }

    public function query(array $command): array {
        if (!$this->connect()) return [];
        try {
            $this->apiWrite($command);
            $raw  = $this->apiRead();
            $rows = [];
            $row  = [];
            foreach ($raw as $word) {
                if (strpos($word,'!re') === 0) {
                    if ($row) { $rows[] = $row; $row = []; }
                } elseif (strpos($word,'=') === 0) {
                    [$k,$v]   = explode('=', substr($word,1), 2) + ['',''];
                    $row[$k]  = $v;
                } elseif (strpos($word,'!done') === 0) {
                    if ($row) $rows[] = $row;
                    break;
                }
            }
            return $rows;
        } catch (\Exception $e) {
            Log::error('[MikroTik] Query error: '.$e->getMessage());
            return [];
        }
    }

    public function disconnectUser(string $username): bool {
        $sessions = $this->query(['/ppp/active/print','?name='.$username]);
        foreach ($sessions as $s) {
            if (isset($s['.id'])) {
                $this->query(['/ppp/active/remove','=.id='.$s['.id']]);
            }
        }
        return true;
    }

    public function getActiveConnections(): array  { return $this->query(['/ppp/active/print']); }
    public function getPppoeSecrets(): array        { return $this->query(['/ppp/secret/print']); }
    public function getInterfaces(): array          { return $this->query(['/interface/print']); }
    public function getQueues(): array              { return $this->query(['/queue/simple/print']); }
    public function getDhcpLeases(): array          { return $this->query(['/ip/dhcp-server/lease/print']); }
    public function getWireless(): array            { return $this->query(['/interface/wireless/print']); }
    public function getFirewallRules(): array       { return $this->query(['/ip/firewall/filter/print']); }
    public function getRoutes(): array              { return $this->query(['/ip/route/print']); }
    public function getIpPools(): array             { return $this->query(['/ip/pool/print']); }
    public function getHotspotUsers(): array        { return $this->query(['/ip/hotspot/user/print']); }
    public function getHotspotActive(): array       { return $this->query(['/ip/hotspot/active/print']); }
    public function getSystemResource(): array      { $r=$this->query(['/system/resource/print']); return $r[0]??[]; }
    public function getRadiusConfig(): array        { $r=$this->query(['/radius/print']); return $r[0]??[]; }
    public function getOpenVpnInterfaces(): array   { return $this->query(['/interface/ovpn-client/print']); }

    public function addPppoeSecret(string $user, string $pass, string $profile='default', string $ip=''): bool {
        $cmd = ['/ppp/secret/add','=name='.$user,'=password='.$pass,'=service=pppoe','=profile='.$profile];
        if ($ip) $cmd[] = '=remote-address='.$ip;
        $this->query($cmd);
        return true;
    }

    public function removePppoeSecret(string $user): bool {
        $secrets = $this->query(['/ppp/secret/print','?name='.$user]);
        foreach ($secrets as $s) {
            if (isset($s['.id'])) $this->query(['/ppp/secret/remove','=.id='.$s['.id']]);
        }
        return true;
    }

    public function disconnectHotspot(string $mac): bool {
        $active = $this->query(['/ip/hotspot/active/print','?mac-address='.$mac]);
        foreach ($active as $a) {
            if (isset($a['.id'])) $this->query(['/ip/hotspot/active/remove','=.id='.$a['.id']]);
        }
        return true;
    }

    public function pushRadiusConfig(array $config): bool {
        $this->query(['/radius/set','=0','=address='.$config['address'],'=secret='.$config['secret'],'=service=ppp,hotspot','=authentication-port='.$config['auth_port'],'=accounting-port='.$config['acct_port']]);
        return true;
    }

    public function testConnection(): bool {
        try {
            $r = $this->getSystemResource();
            return !empty($r);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Generate OpenVPN client config (.ovpn) for a MikroTik router.
     * This file is downloaded and imported into MikroTik via /certificate import
     * or added manually as an OVPN client interface.
     */
    public function generateOvpnConfig(Router $router): string {
        $serverIp   = SystemSetting::get('ovpn','server_ip','');
        $serverPort = SystemSetting::get('ovpn','server_port','1194');
        $proto      = SystemSetting::get('ovpn','protocol','tcp');
        $ca         = SystemSetting::get('ovpn','ca_cert','');
        $certBlock  = $ca ? "<ca>\n{$ca}\n</ca>" : '# Paste CA cert here';
        return "client\ndev tun\nproto {$proto}\nremote {$serverIp} {$serverPort}\nresolv-retry infinite\nnobind\npersist-key\npersist-tun\n{$certBlock}\nverb 3\n# Router: {$router->name}\n# Connect on MikroTik: /interface ovpn-client add name=ovpn-isp connect-to={$serverIp} port={$serverPort} user={$router->ovpn_username} password={$router->ovpn_password}\n";
    }

    public function __destruct() {
        if ($this->api && is_resource($this->api)) @fclose($this->api);
    }
}