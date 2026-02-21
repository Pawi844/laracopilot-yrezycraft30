@extends('layouts.admin')
@section('title','Dashboard')
@section('page-title','ISP Dashboard')
@section('page-subtitle','Real-time overview of your network')

@section('content')
<!-- KPI Row -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <p class="text-gray-500 text-xs font-medium">Total Clients</p>
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#eff6ff"><i class="fas fa-users text-blue-600 text-sm"></i></div>
        </div>
        <p class="text-2xl font-black text-gray-800">{{ number_format($totalClients) }}</p>
        <p class="text-xs text-green-600 mt-1 font-medium">{{ $activeClients }} active</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <p class="text-gray-500 text-xs font-medium">Online Now</p>
            <span class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse"></span>
        </div>
        <p class="text-2xl font-black text-green-600">{{ number_format($onlineClients) }}</p>
        <p class="text-xs text-gray-400 mt-1">Live sessions</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <p class="text-gray-500 text-xs font-medium">Expired</p>
            <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-red-50"><i class="fas fa-times-circle text-red-500 text-sm"></i></div>
        </div>
        <p class="text-2xl font-black text-red-500">{{ number_format($expiredClients) }}</p>
        <p class="text-xs text-orange-500 mt-1">{{ $suspendedClients }} suspended</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <p class="text-gray-500 text-xs font-medium">NAS Servers</p>
            <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-50"><i class="fas fa-server text-blue-600 text-sm"></i></div>
        </div>
        <p class="text-2xl font-black text-blue-700">{{ $totalNas }}</p>
        <p class="text-xs text-green-600 mt-1">{{ $activeNas }} active</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <p class="text-gray-500 text-xs font-medium">Routers</p>
            <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange-50"><i class="fas fa-network-wired text-orange-500 text-sm"></i></div>
        </div>
        <p class="text-2xl font-black text-orange-600">{{ $totalRouters }}</p>
        <p class="text-xs text-green-600 mt-1">{{ $onlineRouters }} online</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <p class="text-gray-500 text-xs font-medium">TR-069</p>
            <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-purple-50"><i class="fas fa-router text-purple-600 text-sm"></i></div>
        </div>
        <p class="text-2xl font-black text-purple-700">{{ $tr069Total }}</p>
        <p class="text-xs text-green-600 mt-1">{{ $tr069Online }} online</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    <!-- Live Sessions -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-5 py-3.5 border-b border-gray-100 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                <h2 class="text-gray-800 font-bold text-sm">Live Active Sessions</h2>
            </div>
            <a href="{{ route('admin.sessions.live') }}" class="text-orange-500 text-xs hover:underline font-semibold">View All →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead style="background:#f8fafc">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Username</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">IP Address</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">↓ Down</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">↑ Up</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Duration</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentSessions as $s)
                    <tr class="hover:bg-orange-50/40 transition-colors">
                        <td class="px-4 py-2.5 text-blue-700 font-mono text-xs font-bold">{{ $s->username }}</td>
                        <td class="px-4 py-2.5 text-gray-600 text-xs font-mono">{{ $s->framed_ip ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-green-600 text-xs font-semibold">{{ $s->bytes_in_human }}</td>
                        <td class="px-4 py-2.5 text-blue-500 text-xs font-semibold">{{ $s->bytes_out_human }}</td>
                        <td class="px-4 py-2.5 text-gray-700 font-mono text-xs">{{ $s->session_time_human }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400 text-sm"><i class="fas fa-satellite-dish text-2xl mb-2 block text-gray-300"></i>No active sessions</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Panel -->
    <div class="space-y-4">
        <!-- Offline Alerts -->
        <div class="bg-white rounded-xl border border-red-100 shadow-sm">
            <div class="px-4 py-3 border-b border-red-100 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-exclamation-triangle text-red-500 text-sm"></i>
                    <h3 class="text-gray-800 font-bold text-sm">Offline 12h+</h3>
                </div>
                <span class="bg-red-100 text-red-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $offlineAlerts->count() }}</span>
            </div>
            <div class="p-3 space-y-2">
                @forelse($offlineAlerts->take(5) as $alert)
                <div class="flex items-center justify-between bg-red-50 rounded-lg px-3 py-2">
                    <div>
                        <p class="text-gray-800 text-xs font-semibold">{{ $alert->client->full_name ?? 'Unknown' }}</p>
                        <p class="text-red-500 text-xs">{{ $alert->offline_hours }}h offline</p>
                    </div>
                    <a href="{{ route('admin.notifications.index') }}" class="text-xs text-orange-500 hover:underline font-medium">Notify</a>
                </div>
                @empty
                <p class="text-gray-400 text-xs text-center py-4">🎉 No offline alerts</p>
                @endforelse
            </div>
        </div>

        <!-- Notification Stats -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <h3 class="text-gray-800 font-bold text-sm mb-3">Notifications Sent</h3>
            <div class="space-y-2.5">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-2"><div class="w-7 h-7 bg-green-100 rounded-lg flex items-center justify-center"><i class="fas fa-sms text-green-600 text-xs"></i></div><span class="text-gray-600 text-xs">SMS</span></div>
                    <span class="text-gray-800 font-bold text-sm">{{ number_format($notifStats['sms']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-2"><div class="w-7 h-7 bg-green-100 rounded-lg flex items-center justify-center"><i class="fab fa-whatsapp text-green-600 text-xs"></i></div><span class="text-gray-600 text-xs">WhatsApp</span></div>
                    <span class="text-gray-800 font-bold text-sm">{{ number_format($notifStats['whatsapp']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-2"><div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center"><i class="fas fa-envelope text-blue-600 text-xs"></i></div><span class="text-gray-600 text-xs">Email</span></div>
                    <span class="text-gray-800 font-bold text-sm">{{ number_format($notifStats['email']) }}</span>
                </div>
            </div>
            <a href="{{ route('admin.notifications.index') }}" class="mt-3 block text-center text-xs text-orange-500 hover:underline font-semibold">Send Notification →</a>
        </div>

        <!-- Resellers -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <h3 class="text-gray-800 font-bold text-sm mb-3"><i class="fas fa-building text-blue-600 mr-1"></i>Resellers</h3>
            <div class="grid grid-cols-3 gap-2 text-center">
                <div><p class="text-xl font-black text-gray-800">{{ $totalResellers }}</p><p class="text-gray-400 text-xs">Total</p></div>
                <div><p class="text-xl font-black text-green-600">{{ $activeResellers }}</p><p class="text-gray-400 text-xs">Active</p></div>
                <div><p class="text-xl font-black text-red-500">{{ $totalResellers - $activeResellers }}</p><p class="text-gray-400 text-xs">Inactive</p></div>
            </div>
            <a href="{{ route('admin.resellers.index') }}" class="mt-3 block text-center text-xs text-orange-500 hover:underline font-semibold">Manage Resellers →</a>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
    <h3 class="text-gray-800 font-bold text-sm mb-4">Quick Actions</h3>
    <div class="grid grid-cols-4 md:grid-cols-8 gap-3">
        @foreach([
            ['url' => route('admin.clients.create'), 'icon' => 'fa-user-plus', 'label' => 'Add Client', 'color' => '#eff6ff', 'icolor' => '#2563eb'],
            ['url' => route('admin.nas.create'), 'icon' => 'fa-server', 'label' => 'Add NAS', 'color' => '#fff7ed', 'icolor' => '#ea580c'],
            ['url' => route('admin.routers.create'), 'icon' => 'fa-network-wired', 'label' => 'Add Router', 'color' => '#f5f3ff', 'icolor' => '#7c3aed'],
            ['url' => route('admin.plans.create'), 'icon' => 'fa-plug', 'label' => 'PPPoE Plan', 'color' => '#f0fdf4', 'icolor' => '#16a34a'],
            ['url' => route('admin.hotspot.create'), 'icon' => 'fa-wifi', 'label' => 'Hotspot Plan', 'color' => '#fff7ed', 'icolor' => '#f97316'],
            ['url' => route('admin.sessions.live'), 'icon' => 'fa-satellite-dish', 'label' => 'Live Sessions', 'color' => '#fef2f2', 'icolor' => '#dc2626'],
            ['url' => route('admin.notifications.index'), 'icon' => 'fa-bell', 'label' => 'Notify', 'color' => '#fefce8', 'icolor' => '#ca8a04'],
            ['url' => route('admin.resellers.create'), 'icon' => 'fa-building', 'label' => 'Add Reseller', 'color' => '#f0f9ff', 'icolor' => '#0284c7'],
        ] as $action)
        <a href="{{ $action['url'] }}" class="flex flex-col items-center p-3 rounded-xl hover:shadow-md transition-all" style="background:{{ $action['color'] }}">
            <i class="fas {{ $action['icon'] }} text-lg mb-1.5" style="color:{{ $action['icolor'] }}"></i>
            <span class="text-gray-700 text-xs text-center font-medium leading-tight">{{ $action['label'] }}</span>
        </a>
        @endforeach
    </div>
</div>
@endsection
