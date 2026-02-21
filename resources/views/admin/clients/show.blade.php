@extends('layouts.admin')
@section('title','Client Details')
@section('page-title','Client Details')
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <!-- Profile Card -->
    <div class="space-y-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="text-center mb-4">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3 text-2xl font-black text-white" style="background:linear-gradient(135deg,#2563eb,#1d4ed8)">{{ strtoupper(substr($client->first_name,0,1)) }}</div>
                <h2 class="text-gray-800 font-black">{{ $client->full_name }}</h2>
                <p class="text-blue-600 font-mono text-sm">{{ $client->username }}</p>
                <span class="inline-flex items-center mt-2 px-3 py-1 rounded-full text-xs font-semibold
                    {{ $client->status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                    {{ $client->status === 'suspended' ? 'bg-red-100 text-red-700' : '' }}
                    {{ $client->status === 'expired' ? 'bg-orange-100 text-orange-700' : '' }}
                    {{ $client->status === 'inactive' ? 'bg-gray-100 text-gray-600' : '' }}">
                    {{ ucfirst($client->status) }}
                </span>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-400 text-xs">Phone</span><span class="text-gray-700 text-xs">{{ $client->phone ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400 text-xs">Email</span><span class="text-gray-700 text-xs">{{ $client->email ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400 text-xs">Type</span><span class="text-xs font-bold uppercase px-2 py-0.5 rounded {{ $client->connection_type === 'pppoe' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700' }}">{{ $client->connection_type }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400 text-xs">Plan</span><span class="text-gray-700 text-xs">{{ $client->plan->name ?? 'None' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400 text-xs">NAS</span><span class="text-gray-700 text-xs">{{ $client->nas->name ?? 'None' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400 text-xs">Expiry</span><span class="text-xs {{ $client->expiry_date && $client->expiry_date->isPast() ? 'text-red-600 font-semibold' : 'text-gray-700' }}">{{ $client->expiry_date ? $client->expiry_date->format('d M Y') : '-' }}</span></div>
            </div>
            <div class="mt-4 flex space-x-2">
                <a href="{{ route('admin.clients.edit', $client->id) }}" class="flex-1 text-center text-white py-2 rounded-lg text-xs font-semibold" style="background:#f97316">Edit</a>
                <form action="{{ route('admin.clients.disconnect', $client->id) }}" method="POST" class="flex-1">
                    @csrf <button class="w-full bg-red-600 text-white py-2 rounded-lg text-xs font-semibold hover:bg-red-700">Disconnect</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Sessions & Logs -->
    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-gray-800 font-bold text-sm">Recent Sessions</h3></div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead style="background:#f8fafc"><tr>
                        <th class="px-4 py-2 text-left text-gray-500 font-semibold">IP</th>
                        <th class="px-4 py-2 text-left text-gray-500 font-semibold">↓ Down</th>
                        <th class="px-4 py-2 text-left text-gray-500 font-semibold">↑ Up</th>
                        <th class="px-4 py-2 text-left text-gray-500 font-semibold">Duration</th>
                        <th class="px-4 py-2 text-left text-gray-500 font-semibold">Status</th>
                        <th class="px-4 py-2 text-left text-gray-500 font-semibold">Start</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($client->sessions as $s)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 font-mono text-blue-600">{{ $s->framed_ip ?? '-' }}</td>
                            <td class="px-4 py-2 text-green-600 font-semibold">{{ $s->bytes_in_human }}</td>
                            <td class="px-4 py-2 text-blue-500 font-semibold">{{ $s->bytes_out_human }}</td>
                            <td class="px-4 py-2 font-mono">{{ $s->session_time_human }}</td>
                            <td class="px-4 py-2"><span class="px-1.5 py-0.5 rounded text-xs {{ $s->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ ucfirst($s->status) }}</span></td>
                            <td class="px-4 py-2 text-gray-400">{{ $s->start_time ? $s->start_time->format('d M H:i') : '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">No sessions recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-gray-800 font-bold text-sm">Notification History</h3></div>
            <div class="p-4 space-y-2">
                @forelse($client->notificationLogs as $log)
                <div class="flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs px-2 py-0.5 rounded {{ $log->channel === 'sms' ? 'bg-green-100 text-green-700' : ($log->channel === 'email' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-800') }}">{{ strtoupper($log->channel) }}</span>
                        <p class="text-gray-600 text-xs">{{ Str::limit($log->message, 50) }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $log->status === 'sent' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ ucfirst($log->status) }}</span>
                        <span class="text-gray-400 text-xs">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @empty
                <p class="text-gray-400 text-sm text-center py-4">No notifications sent to this client.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
