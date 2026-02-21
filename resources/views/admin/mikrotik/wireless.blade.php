@extends('layouts.admin')
@section('title','Wireless — ' . $router->name)
@section('page-title','Wireless Interfaces')
@section('page-subtitle', $router->name)
@section('content')
@include('admin.mikrotik._nav')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-4 py-3 border-b border-gray-100"><h3 class="text-gray-800 font-bold text-sm">Wireless Interfaces</h3></div>
        @forelse($wireless as $w)
        <div class="px-4 py-3 border-b border-gray-50">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-800 font-bold text-sm">{{ $w['name'] ?? '-' }}</p>
                    <p class="text-blue-600 text-xs">SSID: {{ $w['ssid'] ?? 'N/A' }}</p>
                    <p class="text-gray-400 text-xs">Band: {{ $w['band'] ?? '-' }} · Channel: {{ $w['frequency'] ?? '-' }} MHz</p>
                    <p class="text-gray-400 text-xs">Mode: {{ $w['mode'] ?? '-' }}</p>
                </div>
                <span class="px-2 py-0.5 rounded-full text-xs {{ (isset($w['disabled']) && $w['disabled'] === 'true') ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">{{ (isset($w['disabled']) && $w['disabled'] === 'true') ? 'Disabled' : 'Running' }}</span>
            </div>
        </div>
        @empty
        <p class="px-4 py-8 text-center text-gray-400 text-sm">No wireless interfaces (router may not have WiFi)</p>
        @endforelse
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-4 py-3 border-b border-gray-100"><h3 class="text-gray-800 font-bold text-sm">Wireless Clients ({{ count($registrations) }})</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead style="background:#f8fafc"><tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">MAC</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Interface</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Signal</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Uptime</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($registrations as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 font-mono text-xs text-gray-700">{{ $r['mac-address'] ?? '-' }}</td>
                        <td class="px-4 py-2 text-xs text-gray-500">{{ $r['interface'] ?? '-' }}</td>
                        <td class="px-4 py-2 text-xs {{ (int)($r['signal-strength'] ?? 0) > -60 ? 'text-green-600' : 'text-red-500' }}">{{ $r['signal-strength'] ?? '-' }} dBm</td>
                        <td class="px-4 py-2 text-xs text-gray-500">{{ $r['uptime'] ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-6 text-center text-gray-400 text-xs">No wireless clients</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
