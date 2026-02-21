@extends('layouts.admin')
@section('title','Session History')
@section('page-title','Session History')
@section('content')
<div class="flex justify-between items-center mb-4">
    <div class="flex space-x-4">
        <div class="bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-center">
            <p class="text-green-400 font-bold text-lg">{{ $activeSessions }}</p>
            <p class="text-slate-500 text-xs">Active Now</p>
        </div>
        <div class="bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-center">
            <p class="text-white font-bold text-lg">{{ $totalToday }}</p>
            <p class="text-slate-500 text-xs">Today Total</p>
        </div>
    </div>
    <a href="{{ route('admin.sessions.live') }}" class="bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-600">
        <span class="w-2 h-2 bg-green-300 rounded-full inline-block mr-1 animate-pulse"></span>Live View
    </a>
</div>
<div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-900">
            <tr>
                <th class="px-4 py-2.5 text-left text-xs text-slate-500 uppercase">Username</th>
                <th class="px-4 py-2.5 text-left text-xs text-slate-500 uppercase">IP</th>
                <th class="px-4 py-2.5 text-left text-xs text-slate-500 uppercase">↓/↑ Traffic</th>
                <th class="px-4 py-2.5 text-left text-xs text-slate-500 uppercase">Duration</th>
                <th class="px-4 py-2.5 text-left text-xs text-slate-500 uppercase">Status</th>
                <th class="px-4 py-2.5 text-left text-xs text-slate-500 uppercase">Start</th>
                <th class="px-4 py-2.5 text-left text-xs text-slate-500 uppercase">End</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-700">
            @forelse($sessions as $s)
            <tr class="hover:bg-slate-700/40">
                <td class="px-4 py-2.5 text-white font-mono text-xs">{{ $s->username }}</td>
                <td class="px-4 py-2.5 text-slate-300 text-xs font-mono">{{ $s->framed_ip ?? '-' }}</td>
                <td class="px-4 py-2.5 text-xs"><span class="text-green-400">{{ $s->bytes_in_human }}</span> / <span class="text-blue-400">{{ $s->bytes_out_human }}</span></td>
                <td class="px-4 py-2.5 text-white font-mono text-xs">{{ $s->session_time_human }}</td>
                <td class="px-4 py-2.5">
                    <span class="px-2 py-0.5 rounded-full text-xs
                        {{ $s->status === 'active' ? 'bg-green-900 text-green-300' : '' }}
                        {{ $s->status === 'closed' ? 'bg-slate-700 text-slate-400' : '' }}
                        {{ $s->status === 'disconnect' ? 'bg-red-900 text-red-300' : '' }}
                        {{ $s->status === 'timeout' ? 'bg-yellow-900 text-yellow-300' : '' }}">
                        {{ ucfirst($s->status) }}
                    </span>
                </td>
                <td class="px-4 py-2.5 text-slate-400 text-xs">{{ $s->start_time ? $s->start_time->format('d M H:i') : '-' }}</td>
                <td class="px-4 py-2.5 text-slate-400 text-xs">{{ $s->stop_time ? $s->stop_time->format('d M H:i') : '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-10 text-center text-slate-500">No session history found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-slate-700">{{ $sessions->links() }}</div>
</div>
@endsection
