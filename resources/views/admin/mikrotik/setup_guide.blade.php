@extends('layouts.admin')
@section('title','MikroTik Setup Guide')
@section('page-title','MikroTik Configuration Guide')
@section('page-subtitle','Step-by-step setup to connect MikroTik with MtaaKonnect ISP System')

@section('content')
<div class="max-w-5xl space-y-6">

    <!-- Alert Banner -->
    <div class="rounded-xl p-4 flex items-start space-x-3" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
        <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
            <i class="fas fa-router text-white"></i>
        </div>
        <div>
            <h2 class="text-white font-black text-lg">MikroTik → MtaaKonnect Integration</h2>
            <p class="text-blue-200 text-sm mt-1">Follow these steps in order. All commands are for MikroTik RouterOS (via Winbox, WebFig, or SSH terminal). Your MtaaKonnect server IP is the machine running this application.</p>
        </div>
    </div>

    <!-- Step Navigator -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Jump to Section</p>
        <div class="flex flex-wrap gap-2">
            @foreach([
                ['#step1','1. Enable API','fa-plug'],
                ['#step2','2. RADIUS Server','fa-satellite'],
                ['#step3','3. PPPoE RADIUS','fa-plug'],
                ['#step4','4. Hotspot RADIUS','fa-wifi'],
                ['#step5','5. NAS in System','fa-server'],
                ['#step6','6. Firewall','fa-shield-alt'],
                ['#step7','7. IP Pools','fa-list-ol'],
                ['#step8','8. PPPoE Server','fa-network-wired'],
                ['#step9','9. Hotspot Server','fa-broadcast-tower'],
                ['#step10','10. Test & Verify','fa-check-circle'],
            ] as $nav)
            <a href="{{ $nav[0] }}" class="flex items-center space-x-1.5 bg-gray-50 hover:bg-orange-50 border border-gray-200 hover:border-orange-300 rounded-lg px-3 py-1.5 text-xs font-semibold text-gray-600 hover:text-orange-600 transition-all">
                <i class="fas {{ $nav[2] }} text-orange-400"></i>
                <span>{{ $nav[1] }}</span>
            </a>
            @endforeach
        </div>
    </div>

    <!-- STEP 1 -->
    <div id="step1" class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 flex items-center space-x-3" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
            <span class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white font-black text-sm">1</span>
            <div>
                <h3 class="text-white font-bold">Enable MikroTik API Service</h3>
                <p class="text-blue-200 text-xs">Required for MtaaKonnect to communicate with the router</p>
            </div>
        </div>
        <div class="p-5 space-y-4">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 flex items-start space-x-2">
                <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5"></i>
                <p class="text-yellow-800 text-sm">The API service on MikroTik is <strong>disabled by default</strong>. You must enable it first. Default API port is <strong>8728</strong> (unencrypted) or <strong>8729</strong> (SSL).</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm font-semibold mb-2">Via Terminal / SSH:</p>
                <div class="bg-gray-900 rounded-xl p-4 font-mono text-sm space-y-1">
                    <p class="text-green-400"># Enable the API service</p>
                    <p class="text-white">/ip service set api disabled=no</p>
                    <p class="text-white">/ip service set api address=<span class="text-yellow-300">YOUR_SERVER_IP/32</span></p>
                    <br>
                    <p class="text-green-400"># Verify it is running</p>
                    <p class="text-white">/ip service print</p>
                </div>
            </div>
            <div>
                <p class="text-gray-600 text-sm font-semibold mb-2">Via Winbox:</p>
                <ol class="list-decimal ml-5 text-sm text-gray-600 space-y-1">
                    <li>Open <strong>IP → Services</strong></li>
                    <li>Double-click <strong>api</strong></li>
                    <li>Uncheck <strong>Disabled</strong></li>
                    <li>Set <strong>Available From</strong> to your MtaaKonnect server IP</li>
                    <li>Click <strong>OK</strong></li>
                </ol>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <p class="text-blue-800 text-xs font-semibold">📋 What to enter in MtaaKonnect Router settings:</p>
                <div class="grid grid-cols-2 gap-2 mt-2">
                    <div class="bg-white rounded-lg p-2"><p class="text-gray-400 text-xs">IP Address</p><p class="text-blue-700 font-mono font-bold text-sm">Your MikroTik's LAN/WAN IP</p></div>
                    <div class="bg-white rounded-lg p-2"><p class="text-gray-400 text-xs">API Port</p><p class="text-blue-700 font-mono font-bold text-sm">8728</p></div>
                    <div class="bg-white rounded-lg p-2"><p class="text-gray-400 text-xs">Username</p><p class="text-blue-700 font-mono font-bold text-sm">admin (or your admin user)</p></div>
                    <div class="bg-white rounded-lg p-2"><p class="text-gray-400 text-xs">Password</p><p class="text-blue-700 font-mono font-bold text-sm">Your admin password</p></div>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 2 -->
    <div id="step2" class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 flex items-center space-x-3" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
            <span class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white font-black text-sm">2</span>
            <div>
                <h3 class="text-white font-bold">Add RADIUS Server on MikroTik</h3>
                <p class="text-blue-200 text-xs">Point MikroTik to your MtaaKonnect server for authentication</p>
            </div>
        </div>
        <div class="p-5 space-y-4">
            <p class="text-gray-600 text-sm">MikroTik must know where to send authentication requests. You point it to this server (running MtaaKonnect / FreeRADIUS).</p>
            <div class="bg-gray-900 rounded-xl p-4 font-mono text-sm space-y-1">
                <p class="text-green-400"># Add RADIUS server (replace values with your actual IPs)</p>
                <p class="text-white">/radius add <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">service=ppp,hotspot,login <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">address=<span class="text-yellow-300">YOUR_MTAAKONNECT_SERVER_IP</span> <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">secret=<span class="text-yellow-300">YOUR_RADIUS_SECRET</span> <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">authentication-port=1812 <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">accounting-port=1813 <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">timeout=3000</p>
                <br>
                <p class="text-green-400"># Verify</p>
                <p class="text-white">/radius print</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-4">
                    <p class="text-orange-700 font-bold text-xs mb-1">service=</p>
                    <p class="text-gray-700 text-xs"><code class="bg-white px-1 rounded">ppp</code> — for PPPoE clients<br><code class="bg-white px-1 rounded">hotspot</code> — for WiFi hotspot<br><code class="bg-white px-1 rounded">login</code> — for Winbox login (optional)</p>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <p class="text-blue-700 font-bold text-xs mb-1">address=</p>
                    <p class="text-gray-700 text-xs">The <strong>IP address of your MtaaKonnect/FreeRADIUS server</strong>. Must be reachable from the MikroTik.</p>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                    <p class="text-green-700 font-bold text-xs mb-1">secret=</p>
                    <p class="text-gray-700 text-xs">A shared secret key. Must match exactly what you configure in FreeRADIUS <code>clients.conf</code> and in MtaaKonnect NAS settings.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 3 -->
    <div id="step3" class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 flex items-center space-x-3" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
            <span class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white font-black text-sm">3</span>
            <div>
                <h3 class="text-white font-bold">Enable RADIUS for PPPoE</h3>
                <p class="text-blue-200 text-xs">Make PPPoE clients authenticate via RADIUS instead of local secrets</p>
            </div>
        </div>
        <div class="p-5 space-y-4">
            <div class="bg-gray-900 rounded-xl p-4 font-mono text-sm space-y-1">
                <p class="text-green-400"># Enable RADIUS authentication for PPP (PPPoE)</p>
                <p class="text-white">/ppp aaa set use-radius=yes accounting=yes</p>
                <br>
                <p class="text-green-400"># Create PPPoE Server profile (linked to RADIUS)</p>
                <p class="text-white">/ppp profile add <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">name=<span class="text-yellow-300">mtaakonnect-pppoe</span> <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">use-compression=no <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">use-encryption=no <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">local-address=<span class="text-yellow-300">10.10.10.1</span> <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">remote-address=<span class="text-yellow-300">pppoe-pool</span> <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">dns-server=8.8.8.8,8.8.4.4</p>
                <br>
                <p class="text-green-400"># Create PPPoE Server on the WAN-facing interface</p>
                <p class="text-white">/interface pppoe-server server add <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">service-name=<span class="text-yellow-300">MtaaKonnect</span> <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">interface=<span class="text-yellow-300">ether1</span> <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">default-profile=<span class="text-yellow-300">mtaakonnect-pppoe</span> <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">authentication=chap,mschap1,mschap2 <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">one-session-per-host=yes <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">max-sessions=0 <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">disabled=no</p>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <p class="text-blue-800 text-xs"><i class="fas fa-info-circle mr-1"></i><strong>interface=ether1</strong> — Replace with the interface your client ONTs/CPEs connect to (e.g. <code>ether2</code>, <code>bridge-clients</code>, <code>VLAN100</code>).</p>
            </div>
            <div>
                <p class="text-gray-700 text-sm font-semibold mb-2">RADIUS Attributes for Speed Control (sent from FreeRADIUS):</p>
                <div class="bg-gray-900 rounded-xl p-4 font-mono text-xs space-y-1">
                    <p class="text-green-400"># FreeRADIUS users file example</p>
                    <p class="text-yellow-300">john_doe</p>
                    <p class="text-white pl-4">Cleartext-Password := "password123"</p>
                    <p class="text-white pl-4">Mikrotik-Rate-Limit = "10M/5M",</p>
                    <p class="text-white pl-4">Framed-Pool = "pppoe-pool",</p>
                    <p class="text-white pl-4">Session-Timeout = 0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 4 -->
    <div id="step4" class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 flex items-center space-x-3" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
            <span class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white font-black text-sm">4</span>
            <div>
                <h3 class="text-white font-bold">Enable RADIUS for Hotspot</h3>
                <p class="text-blue-200 text-xs">Authenticate WiFi hotspot users via RADIUS</p>
            </div>
        </div>
        <div class="p-5 space-y-4">
            <div class="bg-gray-900 rounded-xl p-4 font-mono text-sm space-y-1">
                <p class="text-green-400"># Create Hotspot Server Profile with RADIUS enabled</p>
                <p class="text-white">/ip hotspot profile add <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">name=<span class="text-yellow-300">mtaakonnect-hotspot</span> <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">hotspot-address=<span class="text-yellow-300">192.168.10.1</span> <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">dns-name=<span class="text-yellow-300">hotspot.mtaakonnect.co.ke</span> <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">use-radius=yes <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">radius-accounting=yes <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">radius-interim-update=1m <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">login-by=cookie,mac,http-chap</p>
                <br>
                <p class="text-green-400"># Create Hotspot Server on the WiFi/LAN interface</p>
                <p class="text-white">/ip hotspot add <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">name=<span class="text-yellow-300">MtaaKonnect-Hotspot</span> <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">interface=<span class="text-yellow-300">wlan1</span> <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">profile=<span class="text-yellow-300">mtaakonnect-hotspot</span> <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">address-pool=<span class="text-yellow-300">hotspot-pool</span> <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">idle-timeout=5m <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">keepalive-timeout=none <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">disabled=no</p>
            </div>
        </div>
    </div>

    <!-- STEP 5 -->
    <div id="step5" class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 flex items-center space-x-3" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
            <span class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white font-black text-sm">5</span>
            <div>
                <h3 class="text-white font-bold">Configure NAS in MtaaKonnect</h3>
                <p class="text-blue-200 text-xs">Register the MikroTik as a NAS (Network Access Server) in this system</p>
            </div>
        </div>
        <div class="p-5 space-y-4">
            <p class="text-gray-600 text-sm">In MtaaKonnect, go to <strong>NAS Servers → Add NAS</strong> and fill in:</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="space-y-2">
                    @foreach([
                        ['Name','MikroTik-Nairobi-01 (descriptive name)'],
                        ['Short Name','nas-nbi-01'],
                        ['Type','mikrotik'],
                        ['IP Addresses','All MikroTik IPs e.g. 102.68.4.5, 192.168.1.1'],
                        ['RADIUS Secret','Same secret you put on MikroTik in Step 2'],
                        ['Community','public'],
                        ['Status','Active'],
                    ] as $row)
                    <div class="bg-gray-50 rounded-lg px-4 py-2.5 flex justify-between items-center">
                        <span class="text-gray-500 text-xs font-semibold">{{ $row[0] }}</span>
                        <span class="text-blue-700 text-xs font-mono">{{ $row[1] }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-4">
                    <p class="text-orange-700 font-bold text-sm mb-2"><i class="fas fa-key mr-1"></i>The RADIUS Secret</p>
                    <p class="text-gray-600 text-xs leading-relaxed">The <strong>RADIUS Secret</strong> is a password shared between MikroTik and FreeRADIUS. It must be <strong>identical</strong> in:</p>
                    <ul class="text-gray-600 text-xs mt-2 space-y-1 list-disc ml-4">
                        <li>MikroTik <code>/radius</code> → secret field</li>
                        <li>FreeRADIUS <code>/etc/freeradius/3.0/clients.conf</code></li>
                        <li>MtaaKonnect NAS Server → Secret field</li>
                    </ul>
                    <div class="mt-3 bg-white rounded-lg p-2">
                        <p class="text-gray-400 text-xs mb-1">Example secret:</p>
                        <p class="text-orange-700 font-mono font-bold">MtaaKonnect@2025!</p>
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.nas.create') }}" class="inline-flex items-center space-x-2 text-white px-5 py-2.5 rounded-xl text-sm font-bold" style="background:linear-gradient(90deg,#f97316,#ea580c)">
                <i class="fas fa-plus"></i><span>Go to Add NAS</span>
            </a>
        </div>
    </div>

    <!-- STEP 6 -->
    <div id="step6" class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 flex items-center space-x-3" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
            <span class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white font-black text-sm">6</span>
            <div>
                <h3 class="text-white font-bold">Firewall Rules</h3>
                <p class="text-blue-200 text-xs">Allow RADIUS traffic and API access</p>
            </div>
        </div>
        <div class="p-5 space-y-4">
            <div class="bg-gray-900 rounded-xl p-4 font-mono text-sm space-y-1">
                <p class="text-green-400"># Allow RADIUS authentication from this MikroTik to MtaaKonnect server</p>
                <p class="text-white">/ip firewall filter add <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">chain=output <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">dst-address=<span class="text-yellow-300">YOUR_MTAAKONNECT_SERVER_IP</span> <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">dst-port=1812,1813 <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">protocol=udp <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">action=accept <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">comment="Allow RADIUS to MtaaKonnect"</p>
                <br>
                <p class="text-green-400"># Allow API access from MtaaKonnect server to MikroTik</p>
                <p class="text-white">/ip firewall filter add <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">chain=input <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">src-address=<span class="text-yellow-300">YOUR_MTAAKONNECT_SERVER_IP</span> <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">dst-port=8728 <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">protocol=tcp <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">action=accept <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">comment="Allow MtaaKonnect API"</p>
                <br>
                <p class="text-green-400"># NAT masquerade for client internet access</p>
                <p class="text-white">/ip firewall nat add <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">chain=srcnat <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">out-interface=<span class="text-yellow-300">ether1</span> <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">action=masquerade <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">comment="Masquerade outbound traffic"</p>
            </div>
        </div>
    </div>

    <!-- STEP 7 -->
    <div id="step7" class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 flex items-center space-x-3" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
            <span class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white font-black text-sm">7</span>
            <div>
                <h3 class="text-white font-bold">Create IP Pools</h3>
                <p class="text-blue-200 text-xs">IP address ranges assigned to PPPoE and Hotspot clients</p>
            </div>
        </div>
        <div class="p-5 space-y-4">
            <div class="bg-gray-900 rounded-xl p-4 font-mono text-sm space-y-1">
                <p class="text-green-400"># Pool for PPPoE clients</p>
                <p class="text-white">/ip pool add <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">name=<span class="text-yellow-300">pppoe-pool</span> <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">ranges=<span class="text-yellow-300">10.10.10.10-10.10.10.254</span></p>
                <br>
                <p class="text-green-400"># Pool for Hotspot clients</p>
                <p class="text-white">/ip pool add <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">name=<span class="text-yellow-300">hotspot-pool</span> <span class="text-yellow-300">\</span></p>
                <p class="text-white pl-4">ranges=<span class="text-yellow-300">192.168.10.10-192.168.10.254</span></p>
                <br>
                <p class="text-green-400"># Verify pools</p>
                <p class="text-white">/ip pool print</p>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <p class="text-blue-800 text-xs"><i class="fas fa-info-circle mr-1"></i>The pool name (e.g. <code>pppoe-pool</code>) must match the <strong>Address Pool</strong> field in your MtaaKonnect PPPoE Plan settings and the PPP Profile's remote-address.</p>
            </div>
        </div>
    </div>

    <!-- STEP 8 -->
    <div id="step8" class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 flex items-center space-x-3" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
            <span class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white font-black text-sm">8</span>
            <div>
                <h3 class="text-white font-bold">FreeRADIUS Configuration on Server</h3>
                <p class="text-blue-200 text-xs">Configure FreeRADIUS to accept MikroTik and serve clients</p>
            </div>
        </div>
        <div class="p-5 space-y-4">
            <p class="text-gray-600 text-sm">On your MtaaKonnect server (Linux), install and configure FreeRADIUS:</p>
            <div class="bg-gray-900 rounded-xl p-4 font-mono text-sm space-y-1">
                <p class="text-green-400"># Install FreeRADIUS + MySQL support</p>
                <p class="text-white">sudo apt update</p>
                <p class="text-white">sudo apt install freeradius freeradius-mysql freeradius-utils -y</p>
                <br>
                <p class="text-green-400"># Edit clients.conf — add MikroTik as a client</p>
                <p class="text-white">sudo nano /etc/freeradius/3.0/clients.conf</p>
                <br>
                <p class="text-green-400"># Add this block for your MikroTik:</p>
                <p class="text-white">client <span class="text-yellow-300">mikrotik_nairobi</span> {</p>
                <p class="text-white pl-4">ipaddr = <span class="text-yellow-300">YOUR_MIKROTIK_IP</span></p>
                <p class="text-white pl-4">secret = <span class="text-yellow-300">YOUR_RADIUS_SECRET</span></p>
                <p class="text-white pl-4">shortname = <span class="text-yellow-300">mikrotik-nbi</span></p>
                <p class="text-white pl-4">nas-type = <span class="text-yellow-300">other</span></p>
                <p class="text-white">}</p>
                <br>
                <p class="text-green-400"># Test and start FreeRADIUS</p>
                <p class="text-white">sudo freeradius -X  <span class="text-gray-400"># debug mode to see errors</span></p>
                <p class="text-white">sudo systemctl enable freeradius</p>
                <p class="text-white">sudo systemctl start freeradius</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-gray-700 font-bold text-xs mb-2">Important FreeRADIUS Files:</p>
                    <div class="space-y-1.5">
                        @foreach([
                            ['/etc/freeradius/3.0/clients.conf','Register MikroTik NAS clients'],
                            ['/etc/freeradius/3.0/users','Local user definitions'],
                            ['/etc/freeradius/3.0/mods-enabled/sql','Database connection'],
                            ['/etc/freeradius/3.0/sites-enabled/default','Auth/Acct pipeline'],
                        ] as $file)
                        <div class="bg-white rounded-lg p-2">
                            <p class="text-blue-700 font-mono text-xs">{{ $file[0] }}</p>
                            <p class="text-gray-400 text-xs">{{ $file[1] }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-gray-700 font-bold text-xs mb-2">RADIUS Database Tables (MySQL):</p>
                    <div class="space-y-1.5">
                        @foreach([
                            ['radcheck','Username + password storage'],
                            ['radreply','Attributes sent back (rate-limit, IP)'],
                            ['radusergroup','User group assignments'],
                            ['radgroupcheck','Group-level checks'],
                            ['radgroupreply','Group-level replies (speed limits)'],
                            ['radacct','Accounting / session logs'],
                        ] as $table)
                        <div class="flex justify-between bg-white rounded-lg px-3 py-1.5">
                            <span class="text-orange-700 font-mono text-xs font-bold">{{ $table[0] }}</span>
                            <span class="text-gray-500 text-xs">{{ $table[1] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 9 -->
    <div id="step9" class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 flex items-center space-x-3" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
            <span class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white font-black text-sm">9</span>
            <div>
                <h3 class="text-white font-bold">Speed Limiting via RADIUS Attributes</h3>
                <p class="text-blue-200 text-xs">Control per-client bandwidth using RADIUS reply attributes</p>
            </div>
        </div>
        <div class="p-5 space-y-4">
            <p class="text-gray-600 text-sm">FreeRADIUS sends these attributes back to MikroTik to set speed limits automatically per client plan:</p>
            <div class="bg-gray-900 rounded-xl p-4 font-mono text-sm space-y-1">
                <p class="text-green-400"># Insert into radreply table (for specific user)</p>
                <p class="text-white">INSERT INTO radreply (username, attribute, op, value) VALUES</p>
                <p class="text-white">(<span class="text-yellow-300">'john_doe'</span>, <span class="text-yellow-300">'Mikrotik-Rate-Limit'</span>, <span class="text-yellow-300">'='</span>, <span class="text-yellow-300">'10M/5M'</span>),</p>
                <p class="text-white">(<span class="text-yellow-300">'john_doe'</span>, <span class="text-yellow-300">'Framed-Pool'</span>, <span class="text-yellow-300">'='</span>, <span class="text-yellow-300">'pppoe-pool'</span>),</p>
                <p class="text-white">(<span class="text-yellow-300">'john_doe'</span>, <span class="text-yellow-300">'Session-Timeout'</span>, <span class="text-yellow-300">'='</span>, <span class="text-yellow-300">'0'</span>);</p>
                <br>
                <p class="text-green-400"># Or use Group-based speed (recommended for plans)</p>
                <p class="text-green-400"># radgroupreply — set for entire plan group</p>
                <p class="text-white">INSERT INTO radgroupreply (groupname, attribute, op, value) VALUES</p>
                <p class="text-white">(<span class="text-yellow-300">'plan-10mbps'</span>, <span class="text-yellow-300">'Mikrotik-Rate-Limit'</span>, <span class="text-yellow-300">'='</span>, <span class="text-yellow-300">'10M/5M'</span>),</p>
                <p class="text-white">(<span class="text-yellow-300">'plan-10mbps'</span>, <span class="text-yellow-300">'Framed-Pool'</span>, <span class="text-yellow-300">'='</span>, <span class="text-yellow-300">'pppoe-pool'</span>);</p>
                <br>
                <p class="text-green-400"># Assign user to group</p>
                <p class="text-white">INSERT INTO radusergroup (username, groupname, priority) VALUES</p>
                <p class="text-white">(<span class="text-yellow-300">'john_doe'</span>, <span class="text-yellow-300">'plan-10mbps'</span>, <span class="text-yellow-300">1</span>);</p>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <p class="text-green-700 font-bold text-sm mb-2"><i class="fas fa-lightbulb mr-1"></i>Rate Limit Format</p>
                <div class="grid grid-cols-2 gap-2">
                    @foreach([
                        ['10M/5M','10 Mbps down / 5 Mbps up'],
                        ['2M/1M','2 Mbps down / 1 Mbps up'],
                        ['20M/10M 30M/15M 2M/1M 20M/10M 10/10','With burst: maxLimit burstLimit threshold'],
                        ['512k/256k','512 Kbps down / 256 Kbps up'],
                    ] as $r)
                    <div class="bg-white rounded-lg p-2">
                        <p class="text-orange-700 font-mono font-bold text-xs">{{ $r[0] }}</p>
                        <p class="text-gray-500 text-xs">{{ $r[1] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 10 -->
    <div id="step10" class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 flex items-center space-x-3" style="background:linear-gradient(90deg,#059669,#047857)">
            <span class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-green-700 font-black text-sm">10</span>
            <div>
                <h3 class="text-white font-bold">Test & Verify the Connection</h3>
                <p class="text-green-100 text-xs">Confirm everything works end-to-end</p>
            </div>
        </div>
        <div class="p-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-700 font-bold text-sm mb-2">Test RADIUS Authentication:</p>
                    <div class="bg-gray-900 rounded-xl p-4 font-mono text-sm space-y-1">
                        <p class="text-green-400"># From the FreeRADIUS server, test a user</p>
                        <p class="text-white">radtest <span class="text-yellow-300">john_doe</span> <span class="text-yellow-300">password123</span> <span class="text-yellow-300">localhost</span> 0 <span class="text-yellow-300">testing123</span></p>
                        <br>
                        <p class="text-green-400"># Expected: Access-Accept</p>
                        <p class="text-green-300">Received Access-Accept Id 0 from 127.0.0.1:1812</p>
                    </div>
                </div>
                <div>
                    <p class="text-gray-700 font-bold text-sm mb-2">Test MikroTik API (from MtaaKonnect):</p>
                    <ol class="text-sm text-gray-600 space-y-2">
                        <li class="flex items-start space-x-2"><span class="w-5 h-5 bg-orange-100 text-orange-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">1</span><span>Go to <strong>MikroTik Panel</strong> in sidebar</span></li>
                        <li class="flex items-start space-x-2"><span class="w-5 h-5 bg-orange-100 text-orange-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">2</span><span>Click <strong>Connect</strong> on your router</span></li>
                        <li class="flex items-start space-x-2"><span class="w-5 h-5 bg-orange-100 text-orange-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">3</span><span>If dashboard loads with CPU/Memory → <strong>API is working</strong></span></li>
                        <li class="flex items-start space-x-2"><span class="w-5 h-5 bg-orange-100 text-orange-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">4</span><span>Go to <strong>RADIUS tab</strong> → Push RADIUS config</span></li>
                        <li class="flex items-start space-x-2"><span class="w-5 h-5 bg-orange-100 text-orange-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">5</span><span>Go to <strong>PPPoE tab</strong> → Add a test secret</span></li>
                        <li class="flex items-start space-x-2"><span class="w-5 h-5 bg-orange-100 text-orange-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">6</span><span>Connect a PPPoE client with those credentials</span></li>
                    </ol>
                </div>
            </div>

            <!-- Troubleshooting -->
            <div>
                <p class="text-gray-700 font-bold text-sm mb-2"><i class="fas fa-wrench text-orange-500 mr-1"></i>Common Issues & Fixes:</p>
                <div class="space-y-2">
                    @foreach([
                        ['Cannot connect to MikroTik API','Check port 8728 is open. Run: /ip service set api disabled=no. Verify your server IP is whitelisted in /ip service set api address=.'],
                        ['Access-Reject from RADIUS','Wrong password in radcheck table, or wrong secret in clients.conf vs MikroTik /radius.'],
                        ['Client connects but no internet','Check NAT masquerade rule on WAN interface. Verify routing.'],
                        ['Speed limit not applied','Mikrotik-Rate-Limit attribute not in radreply or use-radius=yes not set in PPP AAA.'],
                        ['Hotspot users not authenticated','Ensure use-radius=yes in hotspot server profile. Check FreeRADIUS is running.'],
                        ['Sessions not appearing in live view','RADIUS accounting not enabled. Add accounting=yes to /ppp aaa and radius-accounting=yes to hotspot profile.'],
                    ] as $issue)
                    <div class="bg-red-50 border border-red-100 rounded-lg px-4 py-3">
                        <p class="text-red-700 font-semibold text-xs"><i class="fas fa-times-circle mr-1"></i>{{ $issue[0] }}</p>
                        <p class="text-gray-600 text-xs mt-0.5">{{ $issue[1] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Reference Card -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h3 class="text-gray-800 font-bold text-sm mb-4"><i class="fas fa-clipboard-list text-orange-500 mr-2"></i>Quick Reference — Ports & Addresses</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach([
                ['RADIUS Auth','UDP 1812','MikroTik → This Server'],
                ['RADIUS Acct','UDP 1813','MikroTik → This Server'],
                ['MikroTik API','TCP 8728','This Server → MikroTik'],
                ['MikroTik API-SSL','TCP 8729','This Server → MikroTik (optional)'],
                ['PPPoE','Layer 2','Client → MikroTik'],
                ['Winbox','TCP 8291','Admin → MikroTik'],
                ['SSH','TCP 22','Admin → MikroTik'],
                ['WebFig','TCP 80/443','Admin → MikroTik'],
            ] as $port)
            <div class="bg-gray-50 rounded-xl p-3">
                <p class="text-gray-800 font-bold text-xs">{{ $port[0] }}</p>
                <p class="text-orange-600 font-mono font-bold text-sm">{{ $port[1] }}</p>
                <p class="text-gray-400 text-xs">{{ $port[2] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-wrap gap-3 pb-6">
        <a href="{{ route('admin.mikrotik.select') }}" class="flex items-center space-x-2 text-white px-5 py-2.5 rounded-xl text-sm font-bold" style="background:linear-gradient(90deg,#f97316,#ea580c)">
            <i class="fas fa-router"></i><span>Open MikroTik Panel</span>
        </a>
        <a href="{{ route('admin.nas.create') }}" class="flex items-center space-x-2 text-white px-5 py-2.5 rounded-xl text-sm font-bold bg-blue-700 hover:bg-blue-800">
            <i class="fas fa-server"></i><span>Add NAS Server</span>
        </a>
        <a href="{{ route('admin.routers.create') }}" class="flex items-center space-x-2 text-white px-5 py-2.5 rounded-xl text-sm font-bold bg-slate-700 hover:bg-slate-800">
            <i class="fas fa-network-wired"></i><span>Add Router</span>
        </a>
        <a href="{{ route('admin.plans.create') }}" class="flex items-center space-x-2 text-white px-5 py-2.5 rounded-xl text-sm font-bold bg-green-700 hover:bg-green-800">
            <i class="fas fa-plug"></i><span>Create PPPoE Plan</span>
        </a>
    </div>

</div>
@endsection
