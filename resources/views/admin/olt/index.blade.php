@extends('layouts.admin')
@section('title','OLT Devices')
@section('page-title','OLT Management')
@section('page-subtitle','Optical Line Terminals — monitor ports, ONU counts, online status')
@section('content')
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
    @foreach([
        ['Total OLTs',$stats['total'],'fa-server','blue'],
        ['Online',$stats['online'],'fa-signal','green'],
        ['Offline',$stats['offline'],'fa-times-circle','red'],
        ['Ports Full',$stats['ports_full'],'fa-exclamation-triangle','orange'],
    ] as [$l,$v,$i,$c])
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center space-x-3">
        <div class="w-10 h-10 rounded-xl bg-{{ $c }}-100 flex items-center justify-center flex-shrink-0"><i class="fas {{ $i }} text-{{ $c }}-600"></i></div>
        <div><p class="text-gray-400 text-xs">{{ $l }}</p><p class="text-2xl font-black text-gray-800">{{ $v }}</p></div>
    </div>
    @endforeach
</div>

<div class="flex justify-between items-center mb-4">
    <p class="text-gray-500 text-sm">{{ $olts->count() }} OLT device(s) registered</p>
    <a href="{{ route('admin.olt.create') }}" class="text-white px-4 py-2 rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-plus mr-1"></i>Add OLT</a>
</div>

<div class="space-y-4">
@forelse($olts as $olt)
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <!-- OLT Header -->
    <div class="px-5 py-4 flex flex-wrap items-center justify-between gap-3" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center"><i class="fas fa-server text-orange-400"></i></div>
            <div>
                <p class="text-white font-black">{{ $olt->name }}</p>
                <p class="text-blue-200 text-xs">{{ $olt->brand }} {{ $olt->model }} · {{ $olt->ip_address }} · {{ $olt->total_ports }} ports</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            @php $sc=['online'=>['bg-green-500','Online'],'offline'=>['bg-red-500','Offline'],'unknown'=>['bg-gray-500','Unknown']][$olt->status]; @endphp
            <span class="{{ $sc[0] }} text-white text-xs px-3 py-1 rounded-full font-semibold"><i class="fas fa-circle text-xs mr-1"></i>{{ $sc[1] }}</span>
            <form action="{{ route('admin.olt.poll',$olt->id) }}" method="POST" class="inline">
                @csrf
                <button class="bg-white/20 hover:bg-white/30 text-white text-xs px-3 py-1 rounded-lg"><i class="fas fa-sync mr-1"></i>Poll</button>
            </form>
            <a href="{{ route('admin.olt.show',$olt->id) }}" class="bg-orange-500 hover:bg-orange-600 text-white text-xs px-3 py-1 rounded-lg font-semibold">Manage Ports</a>
        </div>
    </div>
    <!-- Port Grid -->
    <div class="p-4">
        <p class="text-gray-500 text-xs mb-3 font-semibold">PORT OCCUPANCY — {{ $olt->ports->where('onu_status','online')->count() }} online, {{ $olt->ports->where('onu_status','offline')->count() }} offline</p>
        <div class="grid grid-cols-8 sm:grid-cols-16 gap-1.5">
            @foreach($olt->ports->sortBy('port_number') as $port)
            @php
                $pct  = $port->max_onu ? round(($port->onu_count / $port->max_onu)*100) : 0;
                $bg   = $port->is_full ? 'bg-red-500' : ($pct>70 ? 'bg-orange-400' : ($port->onu_status==='online'?'bg-green-500':'bg-gray-300'));
            @endphp
            <div class="relative group cursor-pointer">
                <div class="{{ $bg }} rounded text-white text-xs font-bold flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 transition-transform hover:scale-110" title="Port {{ $port->port_number }}">
                    {{ $port->port_number }}
                </div>
                <!-- Tooltip -->
                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 bg-gray-900 text-white text-xs rounded px-2 py-1 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">
                    Port {{ $port->port_number }} · {{ $port->onu_count }}/{{ $port->max_onu }} ONUs<br>
                    {{ $port->is_full ? '🔴 FULL' : ($pct>70 ? '🟠 High' : '🟢 Available') }}
                    @if($port->fatNode) · FAT: {{ $port->fatNode->code }}@endif
                </div>
            </div>
            @endforeach
        </div>
        <!-- Legend -->
        <div class="flex flex-wrap gap-3 mt-3 text-xs text-gray-500">
            <span><span class="inline-block w-3 h-3 rounded bg-green-500 mr-1"></span>Online / Available</span>
            <span><span class="inline-block w-3 h-3 rounded bg-orange-400 mr-1"></span>High (>70%)</span>
            <span><span class="inline-block w-3 h-3 rounded bg-red-500 mr-1"></span>Full / Offline</span>
            <span><span class="inline-block w-3 h-3 rounded bg-gray-300 mr-1"></span>Offline / Unknown</span>
        </div>
    </div>
</div>
@empty
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center">
    <i class="fas fa-server text-5xl text-gray-200 mb-3 block"></i>
    <p class="text-gray-500 font-semibold">No OLT devices added yet.</p>
    <a href="{{ route('admin.olt.create') }}" class="mt-3 inline-block text-orange-600 text-sm font-semibold hover:underline">Add your first OLT →</a>
</div>
@endforelse
</div>
@endsection
