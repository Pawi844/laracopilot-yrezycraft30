@extends('layouts.admin')
@section('title', 'Client: ' . $client->username)
@section('page-title', $client->full_name)
@section('page-subtitle', $client->username . ' · ' . strtoupper($client->connection_type) . ' Client')
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    <!-- LEFT: Profile -->
    <div class="space-y-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="text-center mb-4">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3 text-2xl font-black text-white" style="background:linear-gradient(135deg,#1e3a5f,#0f2744)">
                    {{ strtoupper(substr($client->first_name,0,1)) }}
                </div>
                <h2 class="text-gray-800 font-black text-lg">{{ $client->full_name }}</h2>
                <p class="text-blue-600 font-mono text-sm">{{ $client->username }}</p>
                <span class="inline-flex items-center mt-2 px-3 py-1 rounded-full text-xs font-semibold {{ $client->status === 'active' ? 'bg-green-100 text-green-700' : ($client->status === 'suspended' ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700') }}">
                    <span class="w-1.5 h-1.5 rounded-full mr-1 {{ $client->status === 'active' ? 'bg-green-500 animate-pulse' : 'bg-red-400' }}"></span>
                    {{ ucfirst($client->status) }}
                </span>
            </div>
            <div class="space-y-2 text-sm divide-y divide-gray-50">
                @foreach([
                    ['Phone', $client->phone ?? '-'],
                    ['Email', $client->email ?? '-'],
                    ['Plan', $client->plan->name ?? 'None'],
                    ['NAS', $client->nas->name ?? 'None'],
                    ['Type', strtoupper($client->connection_type)],
                    ['Static IP', $client->static_ip ?? 'Dynamic'],
                    ['MAC', $client->mac_address ?? 'N/A'],
                    ['Expiry', $client->expiry_date ? $client->expiry_date->format('d M Y') : '-'],
                ] as [$lbl,$val])
                <div class="flex justify-between py-1.5">
                    <span class="text-gray-400 text-xs">{{ $lbl }}</span>
                    <span class="text-gray-700 text-xs font-semibold">{{ $val }}</span>
                </div>
                @endforeach
            </div>
            <div class="mt-4 grid grid-cols-2 gap-2">
                <a href="{{ route('admin.clients.edit', $client->id) }}" class="text-center text-white py-2 rounded-lg text-xs font-bold" style="background:#f97316"><i class="fas fa-edit mr-1"></i>Edit</a>
                <form action="{{ route('admin.clients.disconnect', $client->id) }}" method="POST">
                    @csrf <button class="w-full bg-red-600 text-white py-2 rounded-lg text-xs font-bold hover:bg-red-700"><i class="fas fa-unlink mr-1"></i>Disconnect</button>
                </form>
            </div>
        </div>

        <!-- Totals -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <h3 class="text-gray-700 font-bold text-xs uppercase tracking-wider mb-3">Session Totals</h3>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-green-50 rounded-xl p-3 text-center">
                    <p class="text-green-700 font-black text-lg">{{ \App\Services\MikrotikService::formatBytes($totalIn) }}</p>
                    <p class="text-gray-400 text-xs">↓ Downloaded</p>
                </div>
                <div class="bg-blue-50 rounded-xl p-3 text-center">
                    <p class="text-blue-700 font-black text-lg">{{ \App\Services\MikrotikService::formatBytes($totalOut) }}</p>
                    <p class="text-gray-400 text-xs">↑ Uploaded</p>
                </div>
            </div>
        </div>

        <!-- Current Session -->
        @if($currentSession)
        <div class="bg-white rounded-xl border border-green-200 shadow-sm p-4">
            <div class="flex items-center space-x-2 mb-3">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                <h3 class="text-green-700 font-bold text-sm">Live Session</h3>
            </div>
            <div class="space-y-1.5">
                <div class="flex justify-between"><span class="text-gray-400 text-xs">IP Address</span><span class="text-blue-700 font-mono text-xs font-bold">{{ $currentSession->framed_ip ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400 text-xs">Duration</span><span class="text-gray-700 text-xs">{{ $currentSession->session_time_human ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400 text-xs">Downloaded</span><span class="text-green-600 font-semibold text-xs">{{ \App\Services\MikrotikService::formatBytes($currentSession->bytes_in ?? 0) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400 text-xs">Uploaded</span><span class="text-blue-500 font-semibold text-xs">{{ \App\Services\MikrotikService::formatBytes($currentSession->bytes_out ?? 0) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400 text-xs">NAS</span><span class="text-gray-600 text-xs">{{ $currentSession->nas_ip ?? '-' }}</span></div>
            </div>
        </div>
        @endif
    </div>

    <!-- RIGHT: Live Graph + Tabs -->
    <div class="lg:col-span-2 space-y-5">

        <!-- Live Traffic Graph -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm" id="graph-container">
            <div class="px-5 py-3.5 border-b border-gray-100 flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse" id="graph-status-dot"></span>
                    <h3 class="text-gray-800 font-bold text-sm">Live Bandwidth Consumption</h3>
                </div>
                <div class="flex items-center space-x-3">
                    <span id="graph-status-text" class="text-green-600 text-xs font-semibold">● Live</span>
                    <select id="graph-period" class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none">
                        <option value="20">Last 20 polls</option>
                        <option value="40">Last 40 polls</option>
                        <option value="60">Last 60 polls</option>
                    </select>
                </div>
            </div>
            <div class="p-4">
                <canvas id="trafficChart" height="120"></canvas>
            </div>
            <div class="px-5 pb-4 grid grid-cols-4 gap-3" id="live-stats">
                <div class="bg-green-50 rounded-xl p-3 text-center">
                    <p class="text-green-700 font-black text-lg" id="stat-rx-rate">0 B/s</p>
                    <p class="text-gray-400 text-xs">↓ DL Rate</p>
                </div>
                <div class="bg-blue-50 rounded-xl p-3 text-center">
                    <p class="text-blue-700 font-black text-lg" id="stat-tx-rate">0 B/s</p>
                    <p class="text-gray-400 text-xs">↑ UL Rate</p>
                </div>
                <div class="bg-orange-50 rounded-xl p-3 text-center">
                    <p class="text-orange-700 font-black text-lg" id="stat-session-time">--</p>
                    <p class="text-gray-400 text-xs">Session Time</p>
                </div>
                <div class="bg-purple-50 rounded-xl p-3 text-center">
                    <p class="text-purple-700 font-black text-sm font-mono" id="stat-ip">--</p>
                    <p class="text-gray-400 text-xs">IP Address</p>
                </div>
            </div>
        </div>

        <!-- Session History -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-gray-800 font-bold text-sm">Session History</h3></div>
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
                            <td class="px-4 py-2 font-mono text-blue-600 font-semibold">{{ $s->framed_ip ?? '-' }}</td>
                            <td class="px-4 py-2 text-green-600 font-semibold">{{ \App\Services\MikrotikService::formatBytes($s->bytes_in ?? 0) }}</td>
                            <td class="px-4 py-2 text-blue-500 font-semibold">{{ \App\Services\MikrotikService::formatBytes($s->bytes_out ?? 0) }}</td>
                            <td class="px-4 py-2 font-mono">{{ $s->session_time_human ?? '-' }}</td>
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

        <!-- Notifications -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-gray-800 font-bold text-sm">Notification History</h3></div>
            <div class="p-4 space-y-2">
                @forelse($client->notificationLogs as $log)
                <div class="flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs px-2 py-0.5 rounded {{ $log->channel === 'sms' ? 'bg-green-100 text-green-700' : ($log->channel === 'email' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-800') }}">{{ strtoupper($log->channel) }}</span>
                        <p class="text-gray-600 text-xs">{{ Str::limit($log->message, 55) }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $log->status === 'sent' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ ucfirst($log->status) }}</span>
                        <span class="text-gray-400 text-xs">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @empty
                <p class="text-gray-400 text-sm text-center py-4">No notifications sent.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Chart.js + Live Updater -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    const clientId = {{ $client->id }};
    const initialData = @json($graphData);

    function fmtBytes(b) {
        if (b >= 1073741824) return (b/1073741824).toFixed(2)+' GB';
        if (b >= 1048576)    return (b/1048576).toFixed(2)+' MB';
        if (b >= 1024)       return (b/1024).toFixed(2)+' KB';
        return b+' B';
    }

    const labels = initialData.map(d => d.time);
    const rxData = initialData.map(d => d.bytes_in);
    const txData = initialData.map(d => d.bytes_out);

    const ctx = document.getElementById('trafficChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: '↓ Download',
                    data: rxData,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34,197,94,0.12)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 2,
                },
                {
                    label: '↑ Upload',
                    data: txData,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.10)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 2,
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { position: 'top', labels: { font: { size: 11 }, padding: 16 } },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.dataset.label + ': ' + fmtBytes(ctx.raw)
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: v => fmtBytes(v),
                        font: { size: 10 }
                    },
                    grid: { color: 'rgba(0,0,0,0.04)' }
                },
                x: { ticks: { font: { size: 10 }, maxTicksLimit: 10 }, grid: { display: false } }
            }
        }
    });

    // Live polling every 5 seconds
    let isLive = true;
    setInterval(function() {
        if (!isLive) return;
        fetch(`/admin/clients/${clientId}/traffic`)
            .then(r => r.json())
            .then(data => {
                const maxPts = parseInt(document.getElementById('graph-period').value);
                const g = data.graph.slice(-maxPts);

                chart.data.labels = g.map(d => d.time);
                chart.data.datasets[0].data = g.map(d => d.bytes_in);
                chart.data.datasets[1].data = g.map(d => d.bytes_out);
                chart.update('none');

                // Update stat boxes
                if (g.length >= 2) {
                    document.getElementById('stat-rx-rate').textContent = fmtBytes(g[g.length-1].bytes_in) + '/s';
                    document.getElementById('stat-tx-rate').textContent = fmtBytes(g[g.length-1].bytes_out) + '/s';
                }
                if (data.session) {
                    document.getElementById('stat-session-time').textContent = data.session.session_time || '--';
                    document.getElementById('stat-ip').textContent = data.session.framed_ip || '--';
                    document.getElementById('graph-status-dot').classList.add('animate-pulse');
                    document.getElementById('graph-status-text').textContent = '● Live';
                } else {
                    document.getElementById('graph-status-dot').classList.remove('animate-pulse');
                    document.getElementById('graph-status-text').textContent = '○ Offline';
                }
            })
            .catch(() => {
                document.getElementById('graph-status-text').textContent = '○ No data';
            });
    }, 5000);
})();
</script>
@endsection
