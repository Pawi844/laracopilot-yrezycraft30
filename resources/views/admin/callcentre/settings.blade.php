@extends('layouts.admin')
@section('title','Call Centre — 3CX / VoIP Settings')
@section('page-title','Call Centre Integration')
@section('page-subtitle','Configure 3CX, FreePBX, or any SIP/VoIP system')
@section('content')
<div class="max-w-3xl space-y-5">
    <!-- 3CX Quick Setup -->
    <div class="bg-white rounded-xl border border-blue-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4" style="background:linear-gradient(90deg,#1e40af,#1d4ed8)">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <i class="fas fa-headset text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-white font-black">3CX Integration</h2>
                    <p class="text-blue-200 text-xs">Connect your 3CX PBX system for call routing and logging</p>
                </div>
            </div>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                <div class="bg-blue-50 rounded-xl p-4">
                    <p class="text-blue-700 font-bold text-sm mb-2"><i class="fas fa-globe mr-1"></i>3CX Web Client URL</p>
                    <p class="text-gray-600 text-xs">Point to your 3CX web interface, e.g.:<br><code class="bg-gray-100 px-1 rounded font-mono">https://pbx.yourisp.com:5001</code></p>
                </div>
                <div class="bg-green-50 rounded-xl p-4">
                    <p class="text-green-700 font-bold text-sm mb-2"><i class="fas fa-phone-alt mr-1"></i>Call Routing</p>
                    <p class="text-gray-600 text-xs">3CX routes inbound calls to agents. This system logs calls manually or via webhook.</p>
                </div>
            </div>
            <form action="{{ route('admin.settings.update','callcentre') }}" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">VoIP / 3CX Panel URL</label>
                        <input type="url" name="voip_url" value="{{ \App\Models\SystemSetting::get('callcentre','voip_url','') }}" placeholder="https://pbx.yourisp.com:5001" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        <p class="text-gray-400 text-xs mt-1">This link appears as a button in the Call Centre — opens your 3CX/PBX web panel in a new tab</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">VoIP Username / Extension</label>
                        <input type="text" name="voip_username" value="{{ \App\Models\SystemSetting::get('callcentre','voip_username','') }}" placeholder="admin or 100" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">VoIP Password</label>
                        <input type="password" name="voip_password" placeholder="{{ \App\Models\SystemSetting::get('callcentre','voip_password') ? '●●●●●●●●' : 'Enter password' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Provider / System</label>
                        <input type="text" name="provider" value="{{ \App\Models\SystemSetting::get('callcentre','provider','3CX') }}" placeholder="3CX, FreePBX, Asterisk, Twilio" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">SIP Server / Domain</label>
                        <input type="text" name="sip_server" value="{{ \App\Models\SystemSetting::get('callcentre','sip_server','') }}" placeholder="sip.yourisp.com" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="hidden" name="recording_enabled" value="0">
                        <input type="checkbox" name="recording_enabled" value="1" {{ \App\Models\SystemSetting::get('callcentre','recording_enabled')==='1'?'checked':'' }} class="accent-blue-500 w-4 h-4">
                        <span class="text-gray-700 text-sm">Enable call recording links</span>
                    </label>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold"><i class="fas fa-save mr-1"></i>Save 3CX Settings</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3CX Webhook -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h3 class="text-gray-800 font-bold mb-3"><i class="fas fa-code text-purple-500 mr-2"></i>3CX Webhook Integration</h3>
        <p class="text-gray-500 text-sm mb-3">Configure 3CX to POST call events to this URL — auto-logs calls:</p>
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 font-mono text-xs text-blue-700 break-all">
            {{ url('/api/callcentre/webhook') }}
        </div>
        <div class="mt-3 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
            <p class="text-yellow-800 text-xs font-semibold mb-1">3CX Setup Steps:</p>
            <ol class="text-yellow-700 text-xs space-y-1 list-decimal ml-4">
                <li>In 3CX Admin → Settings → Call Reporting → Webhook URL → paste the URL above</li>
                <li>Set Method: POST, Format: JSON</li>
                <li>Events: Call Answered, Call Ended, Missed Call</li>
                <li>Calls will auto-appear in Call Centre log below</li>
            </ol>
        </div>
    </div>

    <!-- OVPN for MikroTik -->
    <div class="bg-white rounded-xl border border-orange-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4" style="background:linear-gradient(90deg,#ea580c,#c2410c)">
            <div class="flex items-center space-x-3">
                <i class="fas fa-shield-alt text-white text-xl"></i>
                <div>
                    <h2 class="text-white font-black">OpenVPN — MikroTik (No Public IP)</h2>
                    <p class="text-orange-100 text-xs">For routers behind NAT / no public IP — connect via VPN tunnel</p>
                </div>
            </div>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4 text-sm">
                <div class="bg-orange-50 rounded-xl p-3 text-center">
                    <i class="fas fa-server text-orange-600 text-2xl mb-1 block"></i>
                    <p class="font-bold text-gray-700">1. VPN Server</p>
                    <p class="text-gray-500 text-xs">Run OpenVPN server on your ISP management host</p>
                </div>
                <div class="bg-orange-50 rounded-xl p-3 text-center">
                    <i class="fas fa-router text-orange-600 text-2xl mb-1 block"></i>
                    <p class="font-bold text-gray-700">2. MikroTik Client</p>
                    <p class="text-gray-500 text-xs">MikroTik connects to VPN as OVPN client — gets tunnel IP</p>
                </div>
                <div class="bg-orange-50 rounded-xl p-3 text-center">
                    <i class="fas fa-plug text-orange-600 text-2xl mb-1 block"></i>
                    <p class="font-bold text-gray-700">3. API via Tunnel</p>
                    <p class="text-gray-500 text-xs">This system uses the tunnel IP to reach the router API</p>
                </div>
            </div>
            <form action="{{ route('admin.settings.update','ovpn') }}" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">OpenVPN Server IP / Domain</label>
                        <input type="text" name="server_ip" value="{{ \App\Models\SystemSetting::get('ovpn','server_ip','') }}" placeholder="vpn.yourisp.com or 41.80.x.x" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Port</label>
                        <input type="number" name="server_port" value="{{ \App\Models\SystemSetting::get('ovpn','server_port','1194') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Protocol</label>
                        <select name="protocol" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                            <option value="tcp" {{ \App\Models\SystemSetting::get('ovpn','protocol')==='tcp'?'selected':'' }}>TCP (recommended for MikroTik)</option>
                            <option value="udp" {{ \App\Models\SystemSetting::get('ovpn','protocol')==='udp'?'selected':'' }}>UDP</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">CA Certificate (paste PEM)</label>
                        <textarea name="ca_cert" rows="4" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="-----BEGIN CERTIFICATE-----
...
-----END CERTIFICATE-----">{{ \App\Models\SystemSetting::get('ovpn','ca_cert','') }}</textarea>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-xl text-sm font-semibold"><i class="fas fa-save mr-1"></i>Save OpenVPN Settings</button>
                </div>
            </form>
            <!-- Router OVPN list -->
            <div class="mt-5 border-t border-gray-100 pt-4">
                <h4 class="text-gray-700 font-bold text-sm mb-3">Router OpenVPN Status</h4>
                @php $routers = \App\Models\Router::all(); @endphp
                @if($routers->count())
                <div class="space-y-2">
                @foreach($routers as $r)
                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3">
                    <div>
                        <p class="font-semibold text-sm text-gray-800">{{ $r->name }}</p>
                        <p class="text-gray-400 text-xs">Public IP: {{ $r->ip_address }} · Tunnel: {{ $r->ovpn_gateway ?: 'Not set' }}</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        @if($r->use_ovpn)
                        <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full font-semibold"><i class="fas fa-shield-alt mr-1"></i>OVPN On</span>
                        @else
                        <span class="bg-gray-100 text-gray-500 text-xs px-2 py-0.5 rounded-full">OVPN Off</span>
                        @endif
                        <a href="{{ route('admin.routers.edit',$r->id) }}" class="text-orange-500 hover:text-orange-700 text-xs font-semibold">Configure</a>
                        @if($r->use_ovpn)
                        <a href="{{ route('admin.routers.ovpn_config',$r->id) }}" class="text-blue-500 hover:text-blue-700 text-xs font-semibold"><i class="fas fa-download mr-0.5"></i>.ovpn</a>
                        @endif
                    </div>
                </div>
                @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
