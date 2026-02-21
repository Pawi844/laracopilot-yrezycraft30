@extends('layouts.portal')
@section('title','My Dashboard')
@section('content')
<div class="space-y-5">
    <!-- Welcome Banner -->
    <div class="rounded-2xl p-5 text-white" style="background:linear-gradient(135deg,#1e3a5f,#0f2744)">
        <div class="flex justify-between items-start flex-wrap gap-4">
            <div>
                <p class="text-blue-200 text-sm">Welcome back,</p>
                <h2 class="text-2xl font-black">{{ $client->first_name }} {{ $client->last_name }}</h2>
                <p class="text-blue-300 text-sm font-mono">{{ $client->username }}</p>
            </div>
            <div class="text-right">
                @php
                $daysLeft = $client->expiry_date ? now()->diffInDays($client->expiry_date, false) : 0;
                $expiryColor = $daysLeft > 7 ? 'text-green-300' : ($daysLeft > 0 ? 'text-yellow-300' : 'text-red-300');
                @endphp
                <p class="{{ $expiryColor }} font-black text-2xl">{{ max(0,$daysLeft) }} days</p>
                <p class="text-blue-200 text-xs">until expiry</p>
                <p class="text-blue-300 text-xs mt-1">{{ $client->expiry_date?->format('d M Y') ?? 'No expiry set' }}</p>
            </div>
        </div>
        <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach([
                ['Plan', $client->plan?->name ?? 'N/A', 'fa-plug'],
                ['Status', ucfirst($client->status), 'fa-circle'],
                ['Type', strtoupper($client->connection_type), 'fa-network-wired'],
                ['FAT', $client->fat?->code ?? 'N/A', 'fa-project-diagram'],
            ] as [$l,$v,$i])
            <div class="bg-white/10 rounded-xl p-3">
                <p class="text-blue-200 text-xs"><i class="fas {{ $i }} mr-1"></i>{{ $l }}</p>
                <p class="text-white font-bold text-sm">{{ $v }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @foreach([
            [route('portal.topup'),'fa-money-bill-wave','#16a34a','Top Up / Pay','Pay via M-Pesa'],
            [route('portal.bills'),'fa-file-invoice','#2563eb','My Bills','View invoices'],
            [route('portal.change_plan'),'fa-exchange-alt','#7c3aed','Change Plan','Upgrade/Downgrade'],
            [route('portal.devices'),'fa-router','#ea580c','My Devices','WiFi & ONU'],
        ] as [$url,$icon,$color,$title,$sub])
        <a href="{{ $url }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center hover:shadow-md transition-all group">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mx-auto mb-2" style="background:{{ $color }}18">
                <i class="fas {{ $icon }}" style="color:{{ $color }}"></i>
            </div>
            <p class="text-gray-800 font-bold text-sm group-hover:text-orange-600">{{ $title }}</p>
            <p class="text-gray-400 text-xs">{{ $sub }}</p>
        </a>
        @endforeach
    </div>

    <!-- Live Utilization Graph -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="px-5 py-3.5 border-b border-gray-100 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                <h3 class="text-gray-800 font-bold text-sm">Live Bandwidth</h3>
            </div>
            <span id="live-badge" class="text-green-600 text-xs font-semibold">● Updating...</span>
        </div>
        <div class="p-4"><canvas id="bwChart" height="100"></canvas></div>
        <div class="px-5 pb-4 grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-green-50 rounded-xl p-3 text-center"><p class="text-green-700 font-black text-lg" id="dl-rate">--</p><p class="text-gray-400 text-xs">↓ Download</p></div>
            <div class="bg-blue-50 rounded-xl p-3 text-center"><p class="text-blue-700 font-black text-lg" id="ul-rate">--</p><p class="text-gray-400 text-xs">↑ Upload</p></div>
            <div class="bg-orange-50 rounded-xl p-3 text-center"><p class="text-orange-700 font-bold text-sm" id="sess-ip">--</p><p class="text-gray-400 text-xs">IP Address</p></div>
            <div class="bg-purple-50 rounded-xl p-3 text-center"><p class="text-purple-700 font-bold text-sm" id="sess-time">--</p><p class="text-gray-400 text-xs">Session Time</p></div>
        </div>
    </div>

    <!-- M-Pesa Payment Info -->
    @if($mpesa['shortcode'])
    <div class="bg-green-50 border border-green-200 rounded-2xl p-5">
        <h3 class="text-green-800 font-bold mb-3"><i class="fas fa-mobile-alt mr-2"></i>Pay via M-Pesa</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-white rounded-xl p-3"><p class="text-gray-400 text-xs">{{ ucfirst($mpesa['type']) }} No.</p><p class="text-green-700 font-black text-xl">{{ $mpesa['shortcode'] }}</p></div>
            <div class="bg-white rounded-xl p-3"><p class="text-gray-400 text-xs">Account Ref</p><p class="text-gray-800 font-bold">{{ $client->username }}</p></div>
            <div class="bg-white rounded-xl p-3"><p class="text-gray-400 text-xs">Amount</p><p class="text-gray-800 font-bold">KES {{ $client->plan?->price ?? '—' }}</p></div>
            <div class="bg-white rounded-xl p-3"><p class="text-gray-400 text-xs">Plan</p><p class="text-gray-800 font-bold">{{ $client->plan?->name ?? '—' }}</p></div>
        </div>
        <p class="text-green-600 text-xs mt-3"><i class="fas fa-info-circle mr-1"></i>After payment, your account will be renewed automatically within 1-2 minutes.</p>
    </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function(){
    function fmtBytes(b){if(b>=1073741824)return(b/1073741824).toFixed(2)+' GB';if(b>=1048576)return(b/1048576).toFixed(2)+' MB';if(b>=1024)return(b/1024).toFixed(2)+' KB';return b+' B';}
    const init = @json($graphData);
    const ctx = document.getElementById('bwChart').getContext('2d');
    const chart = new Chart(ctx,{type:'line',data:{labels:init.map(d=>d.time),datasets:[
        {label:'↓ Download',data:init.map(d=>d.bytes_in),borderColor:'#22c55e',backgroundColor:'rgba(34,197,94,0.1)',tension:0.4,fill:true,borderWidth:2,pointRadius:2},
        {label:'↑ Upload',data:init.map(d=>d.bytes_out),borderColor:'#3b82f6',backgroundColor:'rgba(59,130,246,0.08)',tension:0.4,fill:true,borderWidth:2,pointRadius:2}
    ]},options:{responsive:true,plugins:{legend:{position:'top',labels:{font:{size:11}}},tooltip:{callbacks:{label:c=>c.dataset.label+': '+fmtBytes(c.raw)}}},scales:{y:{beginAtZero:true,ticks:{callback:v=>fmtBytes(v),font:{size:10}},grid:{color:'rgba(0,0,0,0.04)'}},x:{ticks:{font:{size:10},maxTicksLimit:8},grid:{display:false}}}}});
    setInterval(()=>{
        fetch('{{ route("portal.live_traffic") }}')
        .then(r=>r.json()).then(data=>{
            const g = data.graph;
            chart.data.labels = g.map(d=>d.time);
            chart.data.datasets[0].data = g.map(d=>d.bytes_in);
            chart.data.datasets[1].data = g.map(d=>d.bytes_out);
            chart.update('none');
            if(g.length){
                document.getElementById('dl-rate').textContent=fmtBytes(g[g.length-1].bytes_in)+'/s';
                document.getElementById('ul-rate').textContent=fmtBytes(g[g.length-1].bytes_out)+'/s';
            }
            if(data.session){
                document.getElementById('sess-ip').textContent=data.session.framed_ip||'--';
                document.getElementById('sess-time').textContent=data.session.session_time||'--';
                document.getElementById('live-badge').textContent='● Live';
            } else {
                document.getElementById('live-badge').textContent='○ Offline';
            }
        });
    },5000);
})();
</script>
@endsection
