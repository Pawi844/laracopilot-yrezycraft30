@extends('layouts.admin')
@section('title','Hotspot — ' . $router->name)
@section('page-title','Hotspot Management')
@section('page-subtitle', $router->name . ' · ' . count($active) . ' active')
@section('content')
@include('admin.mikrotik._nav')

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
    <!-- Active -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center space-x-2">
            <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
            <h3 class="text-gray-800 font-bold text-sm">Active Sessions ({{ count($active) }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead style="background:#f8fafc"><tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">User</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">IP</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">MAC</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Uptime</th>
                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500">Action</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($active as $c)
                    <tr class="hover:bg-orange-50/30">
                        <td class="px-4 py-2 text-orange-700 font-mono text-xs font-bold">{{ $c['user'] ?? '-' }}</td>
                        <td class="px-4 py-2 text-gray-600 font-mono text-xs">{{ $c['address'] ?? '-' }}</td>
                        <td class="px-4 py-2 text-gray-400 font-mono text-xs">{{ $c['mac-address'] ?? '-' }}</td>
                        <td class="px-4 py-2 text-gray-500 text-xs">{{ $c['uptime'] ?? '-' }}</td>
                        <td class="px-4 py-2 text-right">
                            <form action="{{ route('admin.mikrotik.hotspot.disconnect', $router->id) }}" method="POST" class="inline">
                                @csrf <input type="hidden" name="username" value="{{ $c['user'] ?? '' }}">
                                <button onclick="return confirm('Disconnect?')" class="text-red-500 hover:text-red-700 text-xs"><i class="fas fa-times-circle"></i> Kick</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-8 text-center text-gray-400 text-xs">No active hotspot users</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Hotspot Users List -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-4 py-3 border-b border-gray-100"><h3 class="text-gray-800 font-bold text-sm">Hotspot Users ({{ count($users) }})</h3></div>
        <div class="overflow-y-auto" style="max-height:320px">
            <table class="w-full">
                <thead style="background:#f8fafc"><tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Username</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Profile</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Limit</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $u)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-gray-800 font-mono text-xs font-semibold">{{ $u['name'] ?? '-' }}</td>
                        <td class="px-4 py-2 text-gray-500 text-xs">{{ $u['profile'] ?? 'default' }}</td>
                        <td class="px-4 py-2 text-gray-400 text-xs">{{ $u['limit-bytes-total'] ?? 'unlimited' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="py-6 text-center text-gray-400 text-xs">No hotspot users</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Profiles -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm">
    <div class="px-4 py-3 border-b border-gray-100"><h3 class="text-gray-800 font-bold text-sm">Hotspot Profiles ({{ count($profiles) }})</h3></div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 p-4">
        @forelse($profiles as $p)
        <div class="bg-orange-50 border border-orange-100 rounded-xl p-3">
            <p class="text-orange-700 font-bold text-sm">{{ $p['name'] ?? '-' }}</p>
            <p class="text-gray-500 text-xs mt-1">Rate: {{ $p['rate-limit'] ?? 'unlimited' }}</p>
            <p class="text-gray-400 text-xs">Session: {{ $p['session-timeout'] ?? '0' }}</p>
        </div>
        @empty
        <p class="text-gray-400 text-sm col-span-4">No profiles configured</p>
        @endforelse
    </div>
</div>
@endsection
