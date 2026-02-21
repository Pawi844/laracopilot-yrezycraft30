@extends('layouts.admin')
@section('title','Live Sessions')
@section('page-title','Live Client Sessions')
@section('content')
<!-- Stats -->
<div class="grid grid-cols-3 gap-3 mb-5">
    <div class="bg-slate-800 border border-green-900/50 rounded-xl p-4 flex items-center space-x-3">
        <div class="w-10 h-10 bg-green-900/50 rounded-lg flex items-center justify-center">
            <span class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></span>
        </div>
        <div><p class="text-slate-400 text-xs">Online Now</p><p class="text-2xl font-black text-green-400">{{ $totalOnline }}</p></div>
    </div>
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 flex items-center space-x-3">
        <div class="w-10 h-10 bg-blue-900/50 rounded-lg flex items-center justify-center"><i class="fas fa-arrow-down text-blue-400"></i></div>
        <div><p class="text-slate-400 text-xs">Total Download</p><p class="text-xl font-black text-blue-400">{{ number_format($totalDownload / 1073741824, 2) }} GB</p></div>
    </div>
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 flex items-center space-x-3">
        <div class="w-10 h-10 bg-orange-900/50 rounded-lg flex items-center justify-center"><i class="fas fa-arrow-up text-orange-400"></i></div>
        <div><p class="text-slate-400 text-xs">Total Upload</p><p class="text-xl font-black text-orange-400">{{ number_format($totalUpload / 1073741824, 2) }} GB</p></div>
    </div>
</div>

<div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
    <div class="px-5 py-3 border-b border-slate-700 flex items-center space-x-2">
        <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
        <h2 class="text-white font-bold text-sm">Live Sessions — Auto-refreshing every 30s</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-900">
                <tr>
                    <th class="px-4 py-2.5 text-left text-xs text-slate-500 uppercase">Username</th>
                    <th class="px-4 py-2.5 text-left text-xs text-slate-500 uppercase">IP Address</th>
                    <th class="px-4 py-2.5 text-left text-xs text-slate-500 uppercase">NAS IP</th>
                    <th class="px-4 py-2.5 text-left text-xs text-slate-500 uppercase">MAC Address</th>
                    <th class="px-4 py-2.5 text-left text-xs text-slate-500 uppercase">↓ Download</th>
                    <th class="px-4 py-2.5 text-left text-xs text-slate-500 uppercase">↑ Upload</th>
                    <th class="px-4 py-2.5 text-left text-xs text-slate-500 uppercase">Duration</th>
                    <th class="px-4 py-2.5 text-left text-xs text-slate-500 uppercase">Started</th>
                    <th class="px-4 py-2.5 text-right text-xs text-slate-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @forelse($sessions as $s)
                <tr class="hover:bg-slate-700/40">
                    <td class="px-4 py-2.5">
                        <p class="text-white font-mono text-xs font-semibold">{{ $s->username }}</p>
                        @if($s->client)<p class="text-slate-500 text-xs">{{ $s->client->full_name }}</p>@endif
                    </td>
                    <td class="px-4 py-2.5 text-sky-300 font-mono text-xs">{{ $s->framed_ip ?? '-' }}</td>
                    <td class="px-4 py-2.5 text-slate-400 font-mono text-xs">{{ $s->nas_ip }}</td>
                    <td class="px-4 py-2.5 text-slate-400 font-mono text-xs">{{ $s->calling_station_id ?? '-' }}</td>
                    <td class="px-4 py-2.5 text-green-400 text-xs font-semibold">{{ $s->bytes_in_human }}</td>
                    <td class="px-4 py-2.5 text-blue-400 text-xs font-semibold">{{ $s->bytes_out_human }}</td>
                    <td class="px-4 py-2.5 text-white font-mono text-xs">{{ $s->session_time_human }}</td>
                    <td class="px-4 py-2.5 text-slate-400 text-xs">{{ $s->start_time ? $s->start_time->format('H:i:s') : '-' }}</td>
                    <td class="px-4 py-2.5 text-right">
                        <form action="{{ route('admin.sessions.destroy', $s->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Disconnect session?')" class="text-red-400 hover:text-red-300 text-xs" title="Disconnect">
                                <i class="fas fa-times-circle"></i> Disconnect
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-4 py-12 text-center text-slate-500"><i class="fas fa-satellite-dish text-3xl mb-2 block"></i>No active sessions</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    // Auto-refresh every 30 seconds
    setTimeout(function() { window.location.reload(); }, 30000);
</script>
@endsection
