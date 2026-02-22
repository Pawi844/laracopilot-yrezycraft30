@extends('layouts.admin')
@section('title','Live Sessions')
@section('page-title','Live Sessions')
@section('page-subtitle','Real-time active connections from MikroTik — auto-refreshes every 15s')
@section('content')

<!-- Router selector -->
<div class="flex flex-wrap items-center gap-3 mb-4">
    <form method="GET" class="flex gap-2 flex-wrap">
        <select name="router_id" onchange="this.form.submit()" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            @foreach($routers as $r)
            <option value="{{ $r->id }}" {{ $routerId==$r->id?'selected':'' }}>{{ $r->name }} ({{ $r->ip_address }}){{ $r->use_ovpn?' [OVPN]':'' }}</option>
            @endforeach
        </select>
    </form>
    <div class="flex items-center space-x-2">
        <span id="refresh-badge" class="bg-green-100 text-green-700 text-xs px-3 py-1.5 rounded-full font-semibold">
            <i class="fas fa-circle animate-pulse mr-1 text-green-500"></i>Live — refreshes in <span id="countdown">15</span>s
        </span>
        <button onclick="forceRefresh()" class="border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg text-xs hover:bg-gray-50">
            <i class="fas fa-sync mr-1"></i>Refresh Now
        </button>
    </div>
    @if($selectedRouter)
    <span class="ml-auto text-gray-400 text-xs">
        Router: <strong class="text-gray-700">{{ $selectedRouter->name }}</strong>
        @if($selectedRouter->use_ovpn)<span class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs ml-1">via OVPN tunnel {{ $selectedRouter->ovpn_gateway }}</span>@endif
    </span>
    @endif
</div>

@if($error)
<div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
    <p class="text-red-700 font-semibold text-sm"><i class="fas fa-exclamation-triangle mr-2"></i>Cannot reach MikroTik</p>
    <p class="text-red-600 text-xs mt-1">{{ $error }}</p>
    <p class="text-red-500 text-xs mt-2">Showing RADIUS database sessions below as fallback. Check router IP, API port (8728), OpenVPN tunnel, and that API service is enabled on MikroTik (<code class="bg-red-100 rounded px-1">/ip service enable api</code>).</p>
</div>
@endif

<!-- Stats -->
<div class="grid grid-cols-3 gap-3 mb-4">
    @foreach([
        ['PPPoE Active',$stats['pppoe'],'fa-plug','blue'],
        ['Hotspot Active',$stats['hotspot'],'fa-wifi','green'],
        ['RADIUS DB',$stats['radius'],'fa-database','purple'],
    ] as [$l,$v,$i,$c])
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 sm:p-4 flex items-center space-x-3">
        <div class="w-9 h-9 rounded-xl bg-{{ $c }}-100 flex items-center justify-center flex-shrink-0">
            <i class="fas {{ $i }} text-{{ $c }}-600 text-sm"></i>
        </div>
        <div><p class="text-gray-400 text-xs">{{ $l }}</p><p class="text-xl font-black text-gray-800" id="stat-{{ $c }}">{{ $v }}</p></div>
    </div>
    @endforeach
</div>

<!-- PPPoE Sessions -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-4 overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-bold text-gray-800 text-sm"><i class="fas fa-plug text-blue-500 mr-2"></i>PPPoE Active Sessions (<span id="pppoe-count">{{ $sessions->count() }}</span>)</h3>
    </div>
    <div class="overflow-x-auto">
    <table class="w-full text-sm" id="pppoe-table">
        <thead style="background:#f8fafc">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Username</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 hidden sm:table-cell">IP Address</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 hidden md:table-cell">Uptime</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 hidden lg:table-cell">Caller ID</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">Disconnect</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50" id="pppoe-body">
        @forelse($sessions as $s)
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-3 font-mono font-bold text-orange-600 text-xs">{{ $s->username }}</td>
            <td class="px-4 py-3 font-mono text-xs text-gray-600 hidden sm:table-cell">{{ $s->ip_address }}</td>
            <td class="px-4 py-3 text-xs text-green-600 font-semibold hidden md:table-cell">{{ $s->uptime }}</td>
            <td class="px-4 py-3 font-mono text-xs text-gray-400 hidden lg:table-cell">{{ $s->caller_id }}</td>
            <td class="px-4 py-3 text-right">
                @if($s->id)
                <form action="{{ route('admin.sessions.destroy',$s->id) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <input type="hidden" name="router_id" value="{{ $routerId }}">
                    <input type="hidden" name="type" value="pppoe">
                    <button onclick="return confirm('Disconnect {{ $s->username }}?')" class="text-red-500 hover:text-red-700 text-xs font-semibold">
                        <i class="fas fa-times mr-0.5"></i>Kick
                    </button>
                </form>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">No active PPPoE sessions</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
</div>

<!-- Hotspot Sessions -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-4 overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-100">
        <h3 class="font-bold text-gray-800 text-sm"><i class="fas fa-wifi text-green-500 mr-2"></i>Hotspot Active Sessions (<span id="hs-count">{{ $hotspot->count() }}</span>)</h3>
    </div>
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead style="background:#f8fafc">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Username</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 hidden sm:table-cell">IP Address</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 hidden md:table-cell">MAC</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 hidden md:table-cell">Uptime</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">Disconnect</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50" id="hs-body">
        @forelse($hotspot as $h)
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-3 font-mono font-bold text-green-600 text-xs">{{ $h->username }}</td>
            <td class="px-4 py-3 font-mono text-xs text-gray-600 hidden sm:table-cell">{{ $h->ip_address }}</td>
            <td class="px-4 py-3 font-mono text-xs text-gray-400 hidden md:table-cell">{{ $h->mac }}</td>
            <td class="px-4 py-3 text-xs text-green-600 font-semibold hidden md:table-cell">{{ $h->uptime }}</td>
            <td class="px-4 py-3 text-right">
                @if($h->id)
                <form action="{{ route('admin.sessions.destroy',$h->id) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <input type="hidden" name="router_id" value="{{ $routerId }}">
                    <input type="hidden" name="type" value="hotspot">
                    <button onclick="return confirm('Disconnect {{ $h->username }}?')" class="text-red-500 hover:text-red-700 text-xs font-semibold">
                        <i class="fas fa-times mr-0.5"></i>Kick
                    </button>
                </form>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">No active hotspot sessions</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
</div>

<!-- RADIUS DB Fallback -->
@if($radiusSessions->count())
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-100">
        <h3 class="font-bold text-gray-800 text-sm"><i class="fas fa-database text-purple-500 mr-2"></i>RADIUS Database Sessions ({{ $radiusSessions->count() }})
            <span class="text-gray-400 font-normal text-xs ml-2">— from accounting records</span>
        </h3>
    </div>
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead style="background:#f8fafc">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Username</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 hidden sm:table-cell">IP</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 hidden md:table-cell">NAS</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 hidden md:table-cell">Start</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Duration</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
        @foreach($radiusSessions as $rs)
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-3 font-mono font-bold text-purple-600 text-xs">{{ $rs->username }}</td>
            <td class="px-4 py-3 font-mono text-xs text-gray-600 hidden sm:table-cell">{{ $rs->framedipaddress ?? '—' }}</td>
            <td class="px-4 py-3 text-xs text-gray-500 hidden md:table-cell">{{ $rs->nasipaddress ?? '—' }}</td>
            <td class="px-4 py-3 text-xs text-gray-400 hidden md:table-cell">{{ $rs->acctstarttime ? \Carbon\Carbon::parse($rs->acctstarttime)->diffForHumans() : '—' }}</td>
            <td class="px-4 py-3 text-xs text-green-600">{{ $rs->acctsessiontime ? gmdate('H:i:s',$rs->acctsessiontime) : '—' }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>
@endif

<script>
let countdown = 15;
let timer;
const routerId = {{ $routerId ?? 'null' }};

function forceRefresh() { countdown = 0; }

function updateSessions() {
    if (!routerId) return;
    fetch('{{ route("admin.sessions.live") }}?router_id='+routerId)
    .then(r => r.json())
    .then(data => {
        if (data.error) {
            document.getElementById('refresh-badge').innerHTML = '<i class="fas fa-exclamation-triangle text-red-500 mr-1"></i>Router unreachable';
            return;
        }
        // Update PPPoE count
        document.getElementById('pppoe-count').textContent = data.sessions.length;
        document.getElementById('hs-count').textContent    = data.hotspot.length;
        document.getElementById('stat-blue').textContent   = data.sessions.length;
        document.getElementById('stat-green').textContent  = data.hotspot.length;
        // Rebuild PPPoE rows
        const pb = document.getElementById('pppoe-body');
        if (data.sessions.length) {
            pb.innerHTML = data.sessions.map(s =>
                `<tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono font-bold text-orange-600 text-xs">${s['name']||'—'}</td>
                    <td class="px-4 py-3 font-mono text-xs text-gray-600 hidden sm:table-cell">${s['address']||'—'}</td>
                    <td class="px-4 py-3 text-xs text-green-600 font-semibold hidden md:table-cell">${s['uptime']||'—'}</td>
                    <td class="px-4 py-3 font-mono text-xs text-gray-400 hidden lg:table-cell">${s['caller-id']||'—'}</td>
                    <td class="px-4 py-3 text-right"></td>
                </tr>`).join('');
        } else {
            pb.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">No active PPPoE sessions</td></tr>';
        }
        // Rebuild Hotspot rows
        const hb = document.getElementById('hs-body');
        if (data.hotspot.length) {
            hb.innerHTML = data.hotspot.map(h =>
                `<tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono font-bold text-green-600 text-xs">${h['user']||'—'}</td>
                    <td class="px-4 py-3 font-mono text-xs text-gray-600 hidden sm:table-cell">${h['address']||'—'}</td>
                    <td class="px-4 py-3 font-mono text-xs text-gray-400 hidden md:table-cell">${h['mac-address']||'—'}</td>
                    <td class="px-4 py-3 text-xs text-green-600 font-semibold hidden md:table-cell">${h['uptime']||'—'}</td>
                    <td class="px-4 py-3 text-right"></td>
                </tr>`).join('');
        } else {
            hb.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">No active hotspot sessions</td></tr>';
        }
        document.getElementById('refresh-badge').innerHTML = '<i class="fas fa-circle animate-pulse mr-1 text-green-500"></i>Live — refreshes in <span id="countdown">'+countdown+'</span>s';
    }).catch(() => {});
}

timer = setInterval(() => {
    countdown--;
    const cd = document.getElementById('countdown');
    if (cd) cd.textContent = countdown;
    if (countdown <= 0) {
        countdown = 15;
        updateSessions();
    }
}, 1000);
</script>
@endsection
