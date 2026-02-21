@extends('layouts.admin')
@section('title','Dashboard - MtaaKonnect ISP')
@section('page-title','ISP Dashboard')

@section('content')
<!-- Top KPIs -->
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 mb-5">
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
        <p class="text-slate-400 text-xs mb-1">Total Clients</p>
        <p class="text-2xl font-black text-white">{{ number_format($totalClients) }}</p>
        <p class="text-green-400 text-xs mt-1">{{ $activeClients }} active</p>
    </div>
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-1">
            <p class="text-slate-400 text-xs">Online Now</p>
            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
        </div>
        <p class="text-2xl font-black text-green-400">{{ number_format($onlineClients) }}</p>
        <p class="text-slate-500 text-xs mt-1">Live sessions</p>
    </div>
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
        <p class="text-slate-400 text-xs mb-1">Expired</p>
        <p class="text-2xl font-black text-red-400">{{ number_format($expiredClients) }}</p>
        <p class="text-slate-500 text-xs mt-1">{{ $suspendedClients }} suspended</p>
    </div>
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
        <p class="text-slate-400 text-xs mb-1">NAS Servers</p>
        <p class="text-2xl font-black text-sky-400">{{ $totalNas }}</p>
        <p class="text-green-400 text-xs mt-1">{{ $activeNas }} active</p>
    </div>
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
        <p class="text-slate-400 text-xs mb-1">MikroTik</p>
        <p class="text-2xl font-black text-purple-400">{{ $totalRouters }}</p>
        <p class="text-green-400 text-xs mt-1">{{ $onlineRouters }} online</p>
    </div>
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
        <p class="text-slate-400 text-xs mb-1">TR-069</p>
        <p class="text-2xl font-black text-orange-400">{{ $tr069Total }}</p>
        <p class="text-green-400 text-xs mt-1">{{ $tr069Online }} online</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-5">
    <!-- Live Sessions Table -->
    <div class="lg:col-span-2 bg-slate-800 border border-slate-700 rounded-xl">
        <div class="px-5 py-3 border-b border-slate-700 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                <h2 class="text-white font-bold text-sm">Live Active Sessions</h2>
            </div>
            <a href="{{ route('admin.sessions.live') }}" class="text-sky-400 text-xs hover:underline">View All →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-900/50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs text-slate-500">Username</th>
                        <th class="px-4 py-2 text-left text-xs text-slate-500">IP Address</th>
                        <th class="px-4 py-2 text-left text-xs text-slate-500">↓ Down</th>
                        <th class="px-4 py-2 text-left text-xs text-slate-500">↑ Up</th>
                        <th class="px-4 py-2 text-left text-xs text-slate-500">Duration</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse($recentSessions as $s)
                    <tr class="hover:bg-slate-700/50">
                        <td class="px-4 py-2.5 text-white font-mono text-xs">{{ $s->username }}</td>
                        <td class="px-4 py-2.5 text-slate-300 text-xs">{{ $s->framed_ip ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-green-400 text-xs">{{ $s->bytes_in_human }}</td>
                        <td class="px-4 py-2.5 text-blue-400 text-xs">{{ $s->bytes_out_human }}</td>
                        <td class="px-4 py-2.5 text-slate-300 text-xs font-mono">{{ $s->session_time_human }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500 text-sm">No active sessions</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Panels -->
    <div class="space-y-4">
        <!-- Offline Alerts -->
        <div class="bg-slate-800 border border-red-900/50 rounded-xl">
            <div class="px-4 py-3 border-b border-slate-700 flex items-center space-x-2">
                <i class="fas fa-exclamation-triangle text-red-400 text-sm"></i>
                <h3 class="text-white font-bold text-sm">Offline Alerts (12h+)</h3>
                <span class="ml-auto bg-red-600 text-white text-xs px-2 py-0.5 rounded-full">{{ $offlineAlerts->count() }}</span>
            </div>
            <div class="p-3 space-y-2">
                @forelse($offlineAlerts->take(4) as $alert)
                <div class="flex items-center justify-between bg-red-900/20 rounded-lg px-3 py-2">
                    <div>
                        <p class="text-white text-xs font-semibold">{{ $alert->client->full_name ?? 'Unknown' }}</p>
                        <p class="text-red-400 text-xs">{{ $alert->offline_hours }}h offline</p>
                    </div>
                    <a href="{{ route('admin.notifications.index') }}" class="text-xs text-sky-400 hover:underline">Notify</a>
                </div>
                @empty
                <p class="text-slate-500 text-xs text-center py-3">No offline alerts 🎉</p>
                @endforelse
            </div>
        </div>

        <!-- Notification Stats -->
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
            <h3 class="text-white font-bold text-sm mb-3">Notification Stats</h3>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-2"><i class="fas fa-sms text-green-400 text-sm"></i><span class="text-slate-300 text-xs">SMS Sent</span></div>
                    <span class="text-white font-bold text-sm">{{ number_format($notifStats['sms']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-2"><i class="fab fa-whatsapp text-green-500 text-sm"></i><span class="text-slate-300 text-xs">WhatsApp</span></div>
                    <span class="text-white font-bold text-sm">{{ number_format($notifStats['whatsapp']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-2"><i class="fas fa-envelope text-blue-400 text-sm"></i><span class="text-slate-300 text-xs">Email</span></div>
                    <span class="text-white font-bold text-sm">{{ number_format($notifStats['email']) }}</span>
                </div>
            </div>
            <a href="{{ route('admin.notifications.index') }}" class="mt-3 block text-center text-xs text-sky-400 hover:underline">Send Notification →</a>
        </div>

        <!-- Reseller Summary -->
        @if(session('admin_role') === 'superadmin')
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
            <h3 class="text-white font-bold text-sm mb-3"><i class="fas fa-building mr-2 text-purple-400"></i>Resellers</h3>
            <div class="flex justify-between">
                <div class="text-center"><p class="text-2xl font-black text-white">{{ $totalResellers }}</p><p class="text-slate-500 text-xs">Total</p></div>
                <div class="text-center"><p class="text-2xl font-black text-green-400">{{ $activeResellers }}</p><p class="text-slate-500 text-xs">Active</p></div>
                <div class="text-center"><p class="text-2xl font-black text-red-400">{{ $totalResellers - $activeResellers }}</p><p class="text-slate-500 text-xs">Inactive</p></div>
            </div>
            <a href="{{ route('admin.resellers.index') }}" class="mt-3 block text-center text-xs text-sky-400 hover:underline">Manage Resellers →</a>
        </div>
        @endif
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
    <h3 class="text-white font-bold text-sm mb-3">Quick Actions</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3">
        <a href="{{ route('admin.clients.create') }}" class="flex flex-col items-center p-3 bg-slate-700 hover:bg-sky-700 rounded-lg transition-all group">
            <i class="fas fa-user-plus text-sky-400 group-hover:text-white text-lg mb-1"></i>
            <span class="text-slate-300 group-hover:text-white text-xs text-center">Add Client</span>
        </a>
        <a href="{{ route('admin.nas.create') }}" class="flex flex-col items-center p-3 bg-slate-700 hover:bg-blue-700 rounded-lg transition-all group">
            <i class="fas fa-server text-blue-400 group-hover:text-white text-lg mb-1"></i>
            <span class="text-slate-300 group-hover:text-white text-xs text-center">Add NAS</span>
        </a>
        <a href="{{ route('admin.routers.create') }}" class="flex flex-col items-center p-3 bg-slate-700 hover:bg-purple-700 rounded-lg transition-all group">
            <i class="fas fa-network-wired text-purple-400 group-hover:text-white text-lg mb-1"></i>
            <span class="text-slate-300 group-hover:text-white text-xs text-center">Add Router</span>
        </a>
        <a href="{{ route('admin.plans.create') }}" class="flex flex-col items-center p-3 bg-slate-700 hover:bg-green-700 rounded-lg transition-all group">
            <i class="fas fa-plug text-green-400 group-hover:text-white text-lg mb-1"></i>
            <span class="text-slate-300 group-hover:text-white text-xs text-center">PPPoE Plan</span>
        </a>
        <a href="{{ route('admin.hotspot.create') }}" class="flex flex-col items-center p-3 bg-slate-700 hover:bg-orange-700 rounded-lg transition-all group">
            <i class="fas fa-wifi text-orange-400 group-hover:text-white text-lg mb-1"></i>
            <span class="text-slate-300 group-hover:text-white text-xs text-center">Hotspot Plan</span>
        </a>
        <a href="{{ route('admin.sessions.live') }}" class="flex flex-col items-center p-3 bg-slate-700 hover:bg-red-700 rounded-lg transition-all group">
            <i class="fas fa-satellite-dish text-red-400 group-hover:text-white text-lg mb-1"></i>
            <span class="text-slate-300 group-hover:text-white text-xs text-center">Live Sessions</span>
        </a>
        <a href="{{ route('admin.notifications.index') }}" class="flex flex-col items-center p-3 bg-slate-700 hover:bg-yellow-700 rounded-lg transition-all group">
            <i class="fas fa-bell text-yellow-400 group-hover:text-white text-lg mb-1"></i>
            <span class="text-slate-300 group-hover:text-white text-xs text-center">Notify Clients</span>
        </a>
        <a href="{{ route('admin.resellers.create') }}" class="flex flex-col items-center p-3 bg-slate-700 hover:bg-indigo-700 rounded-lg transition-all group">
            <i class="fas fa-building text-indigo-400 group-hover:text-white text-lg mb-1"></i>
            <span class="text-slate-300 group-hover:text-white text-xs text-center">Add Reseller</span>
        </a>
    </div>
</div>
@endsection
