@extends('layouts.admin')
@section('title','Clients')
@section('page-title','Client Management')
@section('page-subtitle','All ISP subscribers — search, filter, export, import')
@section('content')
<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
    @foreach([
        ['Total',$stats['total'],'fa-users','blue'],
        ['Active',$stats['active'],'fa-check-circle','green'],
        ['Expired',$stats['expired'],'fa-clock','red'],
        ['Suspended',$stats['suspended'],'fa-ban','yellow'],
    ] as [$l,$v,$i,$c])
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 sm:p-4 flex items-center space-x-3">
        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center bg-{{ $c }}-100 flex-shrink-0">
            <i class="fas {{ $i }} text-{{ $c }}-600 text-sm"></i>
        </div>
        <div><p class="text-gray-400 text-xs">{{ $l }}</p><p class="text-xl sm:text-2xl font-black text-gray-800">{{ $v }}</p></div>
    </div>
    @endforeach
</div>

<!-- Search + Filters -->
<form method="GET" class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 mb-4">
    <!-- Global search bar -->
    <div class="flex space-x-2 mb-3">
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search username, name, phone, email, IP, MAC, FAT..." class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
        </div>
        <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap transition-colors">
            <i class="fas fa-search mr-1 sm:inline hidden"></i>Search
        </button>
        @if(request()->hasAny(['q','status','plan_id','fat_id','type']))
        <a href="{{ route('admin.clients.index') }}" class="border border-gray-200 text-gray-500 px-3 py-2 rounded-lg text-sm hover:bg-gray-50 whitespace-nowrap">Clear</a>
        @endif
    </div>
    <!-- Filters row -->
    <div class="flex flex-wrap gap-2">
        <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            <option value="">All Status</option>
            @foreach(['active'=>'Active','inactive'=>'Inactive','suspended'=>'Suspended','expired'=>'Expired','pending'=>'Pending'] as $v=>$l)
            <option value="{{ $v }}" {{ request('status')===$v?'selected':'' }}>{{ $l }}</option>
            @endforeach
        </select>
        <select name="plan_id" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            <option value="">All Plans</option>
            @foreach($plans as $p)<option value="{{ $p->id }}" {{ request('plan_id')==$p->id?'selected':'' }}>{{ $p->name }}</option>@endforeach
        </select>
        <select name="fat_id" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            <option value="">All FAT Nodes</option>
            @foreach($fatNodes as $f)<option value="{{ $f->id }}" {{ request('fat_id')==$f->id?'selected':'' }}>{{ $f->name }} ({{ $f->code }})</option>@endforeach
        </select>
        <select name="type" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            <option value="">All Types</option>
            @foreach(['pppoe'=>'PPPoE','hotspot'=>'Hotspot','static'=>'Static','dhcp'=>'DHCP'] as $v=>$l)
            <option value="{{ $v }}" {{ request('type')===$v?'selected':'' }}>{{ $l }}</option>
            @endforeach
        </select>
        <!-- Action buttons -->
        <div class="ml-auto flex flex-wrap gap-2">
            <a href="{{ route('admin.clients.export').(request()->getQueryString() ? '?'.request()->getQueryString() : '') }}" class="flex items-center space-x-1.5 border border-green-200 text-green-700 px-3 py-2 rounded-lg text-sm font-semibold hover:bg-green-50 transition-colors">
                <i class="fas fa-file-export"></i><span class="hidden sm:inline">Export CSV</span><span class="sm:hidden">Export</span>
            </a>
            <a href="{{ route('admin.clients.import') }}" class="flex items-center space-x-1.5 border border-blue-200 text-blue-700 px-3 py-2 rounded-lg text-sm font-semibold hover:bg-blue-50 transition-colors">
                <i class="fas fa-file-import"></i><span class="hidden sm:inline">Import CSV</span><span class="sm:hidden">Import</span>
            </a>
            <a href="{{ route('admin.clients.create') }}" class="flex items-center space-x-1.5 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors" style="background:linear-gradient(90deg,#f97316,#ea580c)">
                <i class="fas fa-plus"></i><span>Add Client</span>
            </a>
        </div>
    </div>
</form>

<!-- Clients Table -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead style="background:#f8fafc">
            <tr>
                <th class="px-3 sm:px-4 py-3 text-left text-xs font-semibold text-gray-500 whitespace-nowrap">Client</th>
                <th class="px-3 sm:px-4 py-3 text-left text-xs font-semibold text-gray-500 hidden md:table-cell">Plan</th>
                <th class="px-3 sm:px-4 py-3 text-left text-xs font-semibold text-gray-500 hidden lg:table-cell">FAT Node</th>
                <th class="px-3 sm:px-4 py-3 text-left text-xs font-semibold text-gray-500 hidden sm:table-cell">Type</th>
                <th class="px-3 sm:px-4 py-3 text-left text-xs font-semibold text-gray-500">Status</th>
                <th class="px-3 sm:px-4 py-3 text-left text-xs font-semibold text-gray-500 hidden lg:table-cell">Expiry</th>
                <th class="px-3 sm:px-4 py-3 text-right text-xs font-semibold text-gray-500">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
        @forelse($clients as $client)
        <tr class="hover:bg-orange-50/30 transition-colors">
            <td class="px-3 sm:px-4 py-3">
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-black flex-shrink-0" style="background:linear-gradient(135deg,#1e3a5f,#0f2744)">
                        {{ strtoupper(substr($client->first_name??$client->username,0,1)) }}
                    </div>
                    <div class="min-w-0">
                        <!-- Clickable username -->
                        <a href="{{ route('admin.clients.show',$client->id) }}" class="text-orange-600 hover:text-orange-800 font-bold font-mono text-xs block truncate hover:underline">{{ $client->username }}</a>
                        <p class="text-gray-600 text-xs truncate">{{ $client->first_name }} {{ $client->last_name }}</p>
                        <p class="text-gray-400 text-xs truncate md:hidden">{{ $client->plan?->name ?? '—' }}</p>
                    </div>
                </div>
            </td>
            <td class="px-3 sm:px-4 py-3 hidden md:table-cell">
                <span class="text-gray-700 text-xs font-semibold">{{ $client->plan?->name ?? '—' }}</span>
                @if($client->plan)<p class="text-gray-400 text-xs">KES {{ number_format($client->plan->price) }}</p>@endif
            </td>
            <td class="px-3 sm:px-4 py-3 hidden lg:table-cell">
                @if($client->fat)
                <div class="flex items-center space-x-1">
                    @php $fp = $client->fat->usage_percent; $fc = $fp>=90?'red':($fp>=70?'yellow':'green'); @endphp
                    <span class="w-2 h-2 rounded-full bg-{{ $fc }}-500 flex-shrink-0"></span>
                    <div>
                        <p class="text-xs font-semibold font-mono text-gray-700">{{ $client->fat->code }}</p>
                        <p class="text-gray-400 text-xs">{{ $client->fat->name }}</p>
                    </div>
                </div>
                @else
                <span class="text-gray-300 text-xs">Not assigned</span>
                @endif
            </td>
            <td class="px-3 sm:px-4 py-3 hidden sm:table-cell">
                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded font-semibold uppercase">{{ $client->connection_type }}</span>
            </td>
            <td class="px-3 sm:px-4 py-3">
                @php $sc=['active'=>['bg-green-100','text-green-700'],'inactive'=>['bg-gray-100','text-gray-600'],'suspended'=>['bg-red-100','text-red-700'],'expired'=>['bg-orange-100','text-orange-700'],'pending'=>['bg-yellow-100','text-yellow-700']][$client->status??'inactive']; @endphp
                <span class="{{ $sc[0] }} {{ $sc[1] }} text-xs px-2 py-0.5 rounded-full font-semibold capitalize">{{ $client->status }}</span>
            </td>
            <td class="px-3 sm:px-4 py-3 hidden lg:table-cell">
                @if($client->expiry_date)
                @php $days = now()->diffInDays($client->expiry_date, false); @endphp
                <p class="text-xs {{ $days < 0 ? 'text-red-600 font-bold' : ($days <= 3 ? 'text-orange-600 font-semibold' : 'text-gray-600') }}">
                    {{ $client->expiry_date->format('d M Y') }}
                </p>
                <p class="text-gray-400 text-xs">{{ $days < 0 ? abs($days).'d ago' : $days.'d left' }}</p>
                @else<span class="text-gray-300 text-xs">No expiry</span>@endif
            </td>
            <td class="px-3 sm:px-4 py-3">
                <div class="flex justify-end space-x-1">
                    <a href="{{ route('admin.clients.show',$client->id) }}" class="w-7 h-7 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors" title="View">
                        <i class="fas fa-eye text-xs"></i>
                    </a>
                    <a href="{{ route('admin.clients.edit',$client->id) }}" class="w-7 h-7 flex items-center justify-center rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-100 transition-colors" title="Edit">
                        <i class="fas fa-edit text-xs"></i>
                    </a>
                    <form action="{{ route('admin.clients.destroy',$client->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Delete {{ $client->username }}?')" class="w-7 h-7 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition-colors" title="Delete">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="px-4 py-16 text-center">
                <i class="fas fa-users text-5xl text-gray-200 mb-3 block"></i>
                <p class="text-gray-500 font-semibold">No clients found</p>
                @if(request('q'))<p class="text-gray-400 text-sm mt-1">No results for "{{ request('q') }}"</p>@endif
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="px-4 py-3 border-t border-gray-100 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
        <p class="text-gray-400 text-xs">Showing {{ $clients->firstItem() }}–{{ $clients->lastItem() }} of {{ $clients->total() }} clients</p>
        {{ $clients->links() }}
    </div>
</div>
@endsection
