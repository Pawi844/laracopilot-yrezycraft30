@extends('layouts.admin')
@section('title','TR-069 Devices')
@section('page-title','TR-069 / ONU Devices')
@section('page-subtitle','Manage ONUs — ACS provisioning, internet credentials, remote management')
@section('content')

<!-- TR-069 Credential Explainer -->
<div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
    <div class="flex items-start space-x-3">
        <i class="fas fa-info-circle text-blue-600 mt-0.5 flex-shrink-0"></i>
        <div class="text-xs text-blue-800">
            <p class="font-bold mb-1">TR-069 Credential Guide</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div><p class="font-semibold text-blue-700">ACS URL</p><p>The URL of your ACS server (GenieACS, FreeACS, etc.) that the ONU calls home to. e.g. <code class="bg-blue-100 rounded px-1">http://acs.yourisp.com:7547</code></p></div>
                <div><p class="font-semibold text-blue-700">ACS Username / Password</p><p>The credentials your ONU uses to authenticate TO the ACS server. Set these on the ONU's WAN/TR-069 config page.</p></div>
                <div><p class="font-semibold text-blue-700">Internet Username / Password</p><p>PPPoE or WAN credentials pushed TO the ONU for internet connectivity. These are provisioned via TR-069 automatically.</p></div>
            </div>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
    @foreach([
        ['Total',$stats['total'],'fa-broadcast-tower','blue'],
        ['Online',$stats['online'],'fa-signal','green'],
        ['Offline',$stats['offline'],'fa-times-circle','red'],
        ['Unassigned',$stats['unassigned'],'fa-question-circle','yellow'],
    ] as [$l,$v,$i,$c])
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center space-x-3">
        <div class="w-10 h-10 rounded-xl bg-{{ $c }}-100 flex items-center justify-center flex-shrink-0">
            <i class="fas {{ $i }} text-{{ $c }}-600"></i>
        </div>
        <div><p class="text-gray-400 text-xs">{{ $l }}</p><p class="text-2xl font-black text-gray-800">{{ $v }}</p></div>
    </div>
    @endforeach
</div>

<!-- Filters -->
<div class="flex flex-wrap gap-2 mb-4">
    <form method="GET" class="flex flex-wrap gap-2 flex-1">
        <div class="relative flex-1 min-w-48">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Serial, MAC, client username..." class="w-full border border-gray-200 rounded-lg pl-8 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
        </div>
        <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            <option value="">All Status</option>
            @foreach(['online'=>'Online','offline'=>'Offline','unknown'=>'Unknown'] as $v=>$l)
            <option value="{{ $v }}" {{ request('status')===$v?'selected':'' }}>{{ $l }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-semibold">Filter</button>
    </form>
    <a href="{{ route('admin.tr069.create') }}" class="text-white px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap" style="background:linear-gradient(90deg,#f97316,#ea580c)">
        <i class="fas fa-plus mr-1"></i>Add ONU
    </a>
    @if($globalAcs)
    <a href="{{ $globalAcs }}" target="_blank" class="border border-blue-200 text-blue-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-50 whitespace-nowrap">
        <i class="fas fa-external-link-alt mr-1"></i>Open ACS Panel
    </a>
    @endif
</div>

<!-- Table -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead style="background:#f8fafc">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Serial / MAC</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 hidden md:table-cell">Model</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Client</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 hidden lg:table-cell">FAT Node</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 hidden md:table-cell">Internet User</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
        @forelse($devices as $d)
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-3">
                <p class="font-mono font-bold text-gray-800 text-xs">{{ $d->serial_number }}</p>
                <p class="text-gray-400 text-xs font-mono">{{ $d->mac_address }}</p>
            </td>
            <td class="px-4 py-3 hidden md:table-cell"><span class="text-gray-600 text-xs">{{ $d->model ?? '—' }}</span></td>
            <td class="px-4 py-3">
                @if($d->client)
                <a href="{{ route('admin.clients.show',$d->client->id) }}" class="text-orange-600 hover:underline font-bold text-xs font-mono">{{ $d->client->username }}</a>
                <p class="text-gray-400 text-xs">{{ $d->client->first_name }}</p>
                @else<span class="text-yellow-600 text-xs font-semibold">Unassigned</span>@endif
            </td>
            <td class="px-4 py-3 hidden lg:table-cell">
                @if($d->fatNode)<span class="font-mono text-xs text-gray-700">{{ $d->fatNode->code }}</span><p class="text-gray-400 text-xs">{{ $d->fatNode->name }}</p>
                @else<span class="text-gray-300 text-xs">—</span>@endif
            </td>
            <td class="px-4 py-3">
                @php $sc=['online'=>['bg-green-100','text-green-700'],'offline'=>['bg-red-100','text-red-700'],'unknown'=>['bg-gray-100','text-gray-500']][$d->onu_status??'unknown']; @endphp
                <span class="{{ $sc[0] }} {{ $sc[1] }} text-xs px-2 py-0.5 rounded-full font-semibold capitalize">{{ $d->onu_status ?? 'unknown' }}</span>
            </td>
            <td class="px-4 py-3 hidden md:table-cell">
                @if($d->internet_username)
                <span class="font-mono text-xs text-blue-700">{{ $d->internet_username }}</span>
                @else<span class="text-gray-300 text-xs">Not set</span>@endif
            </td>
            <td class="px-4 py-3">
                <div class="flex justify-end space-x-1">
                    <a href="{{ route('admin.tr069.show',$d->id) }}" class="w-7 h-7 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100" title="View"><i class="fas fa-eye text-xs"></i></a>
                    <a href="{{ route('admin.tr069.edit',$d->id) }}" class="w-7 h-7 flex items-center justify-center rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-100" title="Edit"><i class="fas fa-edit text-xs"></i></a>
                    <form action="{{ route('admin.tr069.reboot',$d->id) }}" method="POST" class="inline">
                        @csrf
                        <button title="Reboot" class="w-7 h-7 flex items-center justify-center rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-100"><i class="fas fa-redo text-xs"></i></button>
                    </form>
                    <form action="{{ route('admin.tr069.destroy',$d->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Remove device?')" class="w-7 h-7 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-100" title="Delete"><i class="fas fa-trash text-xs"></i></button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400"><i class="fas fa-broadcast-tower text-5xl text-gray-200 mb-3 block"></i>No ONU devices registered.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="px-4 py-3 border-t border-gray-100">{{ $devices->links() }}</div>
</div>
@endsection
