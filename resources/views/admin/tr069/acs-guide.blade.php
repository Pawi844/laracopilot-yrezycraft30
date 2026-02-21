@extends('layouts.admin')
@section('title','TR-069 / EPON ACS Setup')
@section('page-title','TR-069 / EPON ACS Configuration Guide')
@section('page-subtitle','What to enter in your ONU/router for TR-069 provisioning')
@section('content')
@php
$acsUrl  = \App\Models\SystemSetting::get('tr069','acs_url','');
$acsUser = \App\Models\SystemSetting::get('tr069','acs_username','');
$acsPass = \App\Models\SystemSetting::get('tr069','acs_password','');
$appUrl  = config('app.url');
// Auto-derive a default ACS URL if not set
if (!$acsUrl) $acsUrl = $appUrl.'/tr069/acs';
@endphp

<div class="max-w-3xl space-y-5">
    <!-- What is TR-069 -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h3 class="text-gray-800 font-bold mb-2"><i class="fas fa-info-circle text-blue-500 mr-2"></i>What is TR-069 / EPON?</h3>
        <p class="text-gray-600 text-sm">TR-069 (CWMP) is a protocol that allows this ISP management system to remotely configure, provision, and monitor ONUs/routers. EPON uses the same ACS (Auto Configuration Server) concept. The ONU calls home to this system's ACS endpoint on boot and periodically.</p>
    </div>

    <!-- Credentials Box -->
    <div class="bg-white rounded-xl border border-orange-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4" style="background:linear-gradient(90deg,#ea580c,#c2410c)">
            <h2 class="text-white font-black text-lg"><i class="fas fa-key mr-2"></i>Enter These in Your ONU / Router</h2>
            <p class="text-orange-100 text-sm">Copy these settings into the TR-069/CWMP section of each ONU</p>
        </div>
        <div class="p-5 space-y-4">
            @foreach([
                ['ACS URL (CWMP Server URL)', $acsUrl ?: $appUrl.'/tr069/acs', 'fa-globe', 'blue', 'This is the main URL the ONU connects to for provisioning'],
                ['ACS Username', $acsUser ?: 'acs_user', 'fa-user', 'green', 'Username the ONU uses to authenticate with the ACS'],
                ['ACS Password', $acsPass ?: 'acs_password_here', 'fa-lock', 'purple', 'Password the ONU uses — set this in Settings → TR-069'],
                ['Connection Request URL', $appUrl.'/tr069/connection-request', 'fa-arrows-alt-h', 'orange', 'URL this system uses to push commands TO the ONU (optional)'],
                ['Connection Request Username', 'cr_user', 'fa-user-shield', 'teal', 'For this system to initiate connection to ONU'],
                ['Connection Request Password', 'cr_password', 'fa-shield-alt', 'red', 'Match with what you set per-device in TR-069 → Device → Edit'],
            ] as [$label, $value, $icon, $color, $desc])
            <div class="bg-gray-50 rounded-xl p-4">
                <div class="flex items-start justify-between flex-wrap gap-2">
                    <div>
                        <p class="text-gray-700 font-bold text-sm flex items-center"><i class="fas {{ $icon }} text-{{ $color }}-500 mr-2 w-4"></i>{{ $label }}</p>
                        <p class="text-gray-400 text-xs mt-0.5">{{ $desc }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <code class="bg-white border border-gray-200 rounded-lg px-3 py-1.5 text-sm font-mono text-gray-800 break-all">{{ $value }}</code>
                        <button onclick="navigator.clipboard.writeText('{{ $value }}');this.innerHTML='<i class=\'fas fa-check\'></i>';setTimeout(()=>this.innerHTML='<i class=\'fas fa-copy\'></i>',1500)" class="w-8 h-8 flex items-center justify-center bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-600 text-xs">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Per-Brand Setup -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h3 class="text-gray-800 font-bold mb-4"><i class="fas fa-router text-orange-500 mr-2"></i>Brand-Specific Setup Locations</h3>
        <div class="space-y-4">
            @foreach([
                ['Tenda / Hioso ONU', 'fa-wifi', 'orange',
                    'Administration → TR-069 / CWMP → Enable TR-069 → Enter ACS URL, Username, Password → Save & Reboot',
                    'Some Tenda ONUs: Advanced → CWMP Settings'],
                ['Visol ONU', 'fa-broadcast-tower', 'blue',
                    'Management → TR-069 → ACS URL → ACS Username → ACS Password → Periodic Inform Interval: 300 → Enable → Apply',
                    'Also check: System → Remote Management → CWMP'],
                ['Huawei ONU (HG8xxx)', 'fa-cube', 'red',
                    'Advanced → ITMS (TR-069) → ACS URL → Username → Password → Inform Enable → Period: 300s',
                    'Default web UI: 192.168.100.1 — user: telecomadmin / pass: admintelecom'],
                ['Generic/Unknown ONU', 'fa-question-circle', 'gray',
                    'Look for: CWMP / TR-069 / Auto Config / Remote Management → Fill ACS URL and credentials',
                    'Periodic Inform: Enable, Interval: 300 seconds'],
            ] as [$brand, $icon, $color, $steps, $note])
            <div class="border border-gray-100 rounded-xl p-4">
                <p class="text-gray-800 font-bold text-sm mb-2"><i class="fas {{ $icon }} text-{{ $color }}-500 mr-2"></i>{{ $brand }}</p>
                <p class="text-gray-600 text-sm bg-gray-50 rounded-lg px-3 py-2 font-mono text-xs">{{ $steps }}</p>
                <p class="text-gray-400 text-xs mt-1.5"><i class="fas fa-lightbulb text-yellow-400 mr-1"></i>{{ $note }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Internet (WAN/PPPoE) Credentials via TR-069 -->
    <div class="bg-white rounded-xl border border-green-200 shadow-sm p-5">
        <h3 class="text-gray-800 font-bold mb-3"><i class="fas fa-globe text-green-500 mr-2"></i>Internet (PPPoE/WAN) Credentials Pushed via TR-069</h3>
        <p class="text-gray-600 text-sm mb-3">Once the ONU is registered in TR-069 Devices, go to the device record, set the Internet Username and Password, then click <strong>"Push Internet Settings"</strong> to provision the WAN/PPPoE credentials remotely.</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div class="bg-green-50 rounded-xl p-3">
                <p class="font-bold text-green-700"><i class="fas fa-user mr-1"></i>Internet Username</p>
                <p class="text-green-600 text-xs mt-1">The PPPoE username for this client (usually their ISP username, e.g. john@isp.com or their RADIUS username)</p>
            </div>
            <div class="bg-green-50 rounded-xl p-3">
                <p class="font-bold text-green-700"><i class="fas fa-key mr-1"></i>Internet Password</p>
                <p class="text-green-600 text-xs mt-1">The PPPoE password for this client — matches their RADIUS/PPPoE secret</p>
            </div>
        </div>
        <div class="mt-3 bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs text-blue-700">
            <strong>TR-069 Parameter Path (PPPoE):</strong><br>
            <code>InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANPPPConnection.1.Username</code><br>
            <code>InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANPPPConnection.1.Password</code><br><br>
            This is automatically sent by the "Push Internet Settings" button using the ACS connection request.
        </div>
    </div>

    <!-- Save Settings -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h3 class="text-gray-800 font-bold mb-3"><i class="fas fa-cog text-orange-500 mr-2"></i>Update Global ACS Settings</h3>
        <form action="{{ route('admin.settings.update','tr069') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @csrf @method('PUT')
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">ACS URL</label>
                <input type="text" name="acs_url" value="{{ $acsUrl }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">ACS Username</label>
                <input type="text" name="acs_username" value="{{ $acsUser }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">ACS Password</label>
                <input type="password" name="acs_password" placeholder="{{ $acsPass ? '●●●●●●●●' : 'Set password' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div class="sm:col-span-3 text-right">
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-xl text-sm font-semibold"><i class="fas fa-save mr-1"></i>Save ACS Settings</button>
            </div>
        </form>
    </div>
</div>
@endsection
