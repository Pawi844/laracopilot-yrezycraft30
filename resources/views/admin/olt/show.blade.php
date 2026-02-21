@extends('layouts.admin')
@section('title', $olt->name)
@section('page-title', $olt->name)
@section('page-subtitle', $olt->brand . ' ' . $olt->model . ' · ' . $olt->ip_address)
@section('content')
<div class="flex flex-wrap gap-2 mb-4">
    <form action="{{ route('admin.olt.poll',$olt->id) }}" method="POST" class="inline">
        @csrf
        <button class="border border-blue-200 text-blue-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-50"><i class="fas fa-sync mr-1"></i>Poll OLT</button>
    </form>
    <a href="{{ route('admin.olt.edit',$olt->id) }}" class="border border-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50"><i class="fas fa-edit mr-1"></i>Edit OLT</a>
    <a href="{{ route('admin.olt.index') }}" class="border border-gray-200 text-gray-500 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">← All OLTs</a>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="text-gray-800 font-bold"><i class="fas fa-sitemap text-orange-500 mr-2"></i>Port Management — {{ $olt->ports->count() }} ports</h3>
        <p class="text-gray-400 text-xs mt-0.5">Click a port row to update its ONU count, status, or link to a FAT node</p>
    </div>
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead style="background:#f8fafc">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Port</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">ONU Count</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Capacity</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Fill</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">FAT Node</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Signal</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">Update</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
        @foreach($olt->ports->sortBy('port_number') as $port)
        @php
            $pct = $port->max_onu ? round(($port->onu_count / $port->max_onu)*100) : 0;
            $barColor = $port->is_full ? 'bg-red-500' : ($pct>70 ? 'bg-orange-400' : 'bg-green-500');
        @endphp
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-3 font-bold text-gray-700 font-mono">{{ $port->port_name ?? 'Port '.$port->port_number }}</td>
            <td class="px-4 py-3 font-bold text-gray-800">{{ $port->onu_count }}</td>
            <td class="px-4 py-3 text-gray-500 text-xs">/ {{ $port->max_onu }}</td>
            <td class="px-4 py-3">
                <div class="w-24">
                    <div class="flex justify-between text-xs mb-0.5">
                        <span class="{{ $port->is_full?'text-red-600 font-bold':($pct>70?'text-orange-600':'text-green-600') }}">{{ $pct }}%</span>
                        @if($port->is_full)<span class="text-red-600 font-bold text-xs">FULL</span>@endif
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full {{ $barColor }} rounded-full" style="width:{{ $pct }}%"></div>
                    </div>
                </div>
            </td>
            <td class="px-4 py-3">
                @php $ss=['online'=>['bg-green-100','text-green-700'],'offline'=>['bg-red-100','text-red-700'],'alarm'=>['bg-orange-100','text-orange-700'],'unknown'=>['bg-gray-100','text-gray-500']][$port->onu_status??'unknown']; @endphp
                <span class="{{ $ss[0] }} {{ $ss[1] }} text-xs px-2 py-0.5 rounded-full capitalize font-semibold">{{ $port->onu_status ?? 'unknown' }}</span>
            </td>
            <td class="px-4 py-3">
                @if($port->fatNode)<span class="font-mono text-xs text-blue-700">{{ $port->fatNode->code }}</span>
                @else<span class="text-gray-300 text-xs">—</span>@endif
            </td>
            <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $port->signal_level ?? '—' }}</td>
            <td class="px-4 py-3">
                <button onclick="openPortModal({{ $port->id }}, {{ $port->onu_count }}, {{ $port->max_onu }}, '{{ $port->onu_status }}', '{{ $port->fat_node_id }}', '{{ $port->signal_level }}')" class="text-orange-500 hover:text-orange-700 text-xs font-semibold"><i class="fas fa-edit mr-0.5"></i>Edit</button>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>

<!-- Port Edit Modal -->
<div id="port-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
        <div class="px-5 py-4 border-b border-gray-100 flex justify-between">
            <h3 class="font-bold text-gray-800">Update Port</h3>
            <button onclick="closePortModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        <form id="port-form" method="POST" class="p-5 space-y-3">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-3">
                <div><label class="text-xs font-semibold text-gray-600 mb-1 block">ONU Count</label>
                    <input type="number" id="pm-onu" name="onu_count" min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                </div>
                <div><label class="text-xs font-semibold text-gray-600 mb-1 block">Max ONU</label>
                    <input type="number" id="pm-max" name="max_onu" min="1" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                </div>
            </div>
            <div><label class="text-xs font-semibold text-gray-600 mb-1 block">Status</label>
                <select id="pm-status" name="onu_status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    @foreach(['online','offline','alarm','unknown'] as $s)
                    <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="text-xs font-semibold text-gray-600 mb-1 block">FAT Node</label>
                <select id="pm-fat" name="fat_node_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- None --</option>
                    @foreach($fatNodes as $f)<option value="{{ $f->id }}">{{ $f->code }} — {{ $f->name }}</option>@endforeach
                </select>
            </div>
            <div><label class="text-xs font-semibold text-gray-600 mb-1 block">Signal Level (dBm)</label>
                <input type="text" id="pm-signal" name="signal_level" placeholder="e.g. -23.5" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="closePortModal()" class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600">Cancel</button>
                <button type="submit" class="px-5 py-2 text-white rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)">Save Port</button>
            </div>
        </form>
    </div>
</div>
<script>
function openPortModal(id, onu, max, status, fat, signal) {
    document.getElementById('port-form').action = '{{ route("admin.olt.update_port", [$olt->id, "PORT"]) }}'.replace('PORT',id);
    document.getElementById('pm-onu').value    = onu;
    document.getElementById('pm-max').value    = max;
    document.getElementById('pm-status').value = status;
    document.getElementById('pm-fat').value    = fat || '';
    document.getElementById('pm-signal').value = signal || '';
    document.getElementById('port-modal').classList.remove('hidden');
}
function closePortModal() { document.getElementById('port-modal').classList.add('hidden'); }
</script>
@endsection
