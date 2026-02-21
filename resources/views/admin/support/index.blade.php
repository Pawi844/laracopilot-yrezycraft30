@extends('layouts.admin')
@section('title','Support Tickets')
@section('page-title','Complaints & Support')
@section('page-subtitle','All client tickets — assign to technicians, track resolution')
@section('content')
<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
    @foreach([
        ['Open','open',$stats['open'],'fa-envelope-open','blue'],
        ['In Progress','in_progress',$stats['in_progress'],'fa-tools','yellow'],
        ['Resolved','resolved',$stats['resolved'],'fa-check-circle','green'],
        ['Urgent','urgent',$stats['urgent'],'fa-fire','red'],
    ] as [$label,$key,$count,$icon,$color])
    <a href="{{ route('admin.support.index',['status'=>$key]) }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center space-x-3 hover:shadow-md transition-all">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-{{ $color }}-100">
            <i class="fas {{ $icon }} text-{{ $color }}-600"></i>
        </div>
        <div><p class="text-gray-400 text-xs">{{ $label }}</p><p class="text-2xl font-black text-gray-800">{{ $count }}</p></div>
    </a>
    @endforeach
</div>
<!-- Filters -->
<form method="GET" class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 mb-4 flex flex-wrap gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ticket, client..." class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
    <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
        <option value="">All Status</option>
        @foreach(['open'=>'Open','in_progress'=>'In Progress','resolved'=>'Resolved','closed'=>'Closed'] as $v=>$l)
        <option value="{{ $v }}" {{ request('status')===$v?'selected':'' }}>{{ $l }}</option>
        @endforeach
    </select>
    <select name="priority" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
        <option value="">All Priority</option>
        @foreach(['urgent'=>'Urgent','high'=>'High','medium'=>'Medium','low'=>'Low'] as $v=>$l)
        <option value="{{ $v }}" {{ request('priority')===$v?'selected':'' }}>{{ $l }}</option>
        @endforeach
    </select>
    <select name="tech" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
        <option value="">All Technicians</option>
        @foreach($technicians as $t)<option value="{{ $t->id }}" {{ request('tech')==$t->id?'selected':'' }}>{{ $t->name }}</option>@endforeach
    </select>
    <select name="source" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
        <option value="">All Sources</option>
        @foreach(['call_centre'=>'Call Centre','portal'=>'Client Portal','admin'=>'Admin','email'=>'Email','walk_in'=>'Walk-in'] as $v=>$l)
        <option value="{{ $v }}" {{ request('source')===$v?'selected':'' }}>{{ $l }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-semibold"><i class="fas fa-search mr-1"></i>Filter</button>
    <a href="{{ route('admin.support.index') }}" class="border border-gray-200 text-gray-500 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">Clear</a>
    <a href="{{ route('admin.support.create') }}" class="ml-auto text-white px-4 py-2 rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-plus mr-1"></i>New Ticket</a>
</form>
<!-- Table -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead style="background:#f8fafc">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">#</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Subject</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Client</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Technician</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Priority</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Source</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Replies</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Created</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
        @forelse($tickets as $t)
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-3 font-mono text-blue-700 font-bold">#{{ $t->id }}</td>
            <td class="px-4 py-3">
                <a href="{{ route('admin.support.show',$t->id) }}" class="text-gray-800 font-semibold hover:text-orange-600">{{ Str::limit($t->subject,50) }}</a>
                @if($t->category)<p class="text-gray-400 text-xs">{{ \App\Models\SupportTicket::categories()[$t->category] ?? $t->category }}</p>@endif
            </td>
            <td class="px-4 py-3">
                @if($t->client)
                <div class="flex items-center space-x-1.5">
                    <div class="w-6 h-6 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs">{{ strtoupper(substr($t->client->first_name,0,1)) }}</div>
                    <div><p class="text-xs font-semibold">{{ $t->client->first_name }}</p><p class="text-gray-400 text-xs">{{ $t->client->username }}</p></div>
                </div>
                @else<span class="text-gray-400 text-xs">Unlinked</span>@endif
            </td>
            <td class="px-4 py-3">
                @if($t->technician)
                <div class="flex items-center space-x-1">
                    <div class="w-6 h-6 rounded-full bg-orange-500 flex items-center justify-center text-white text-xs">{{ strtoupper(substr($t->technician->name,0,1)) }}</div>
                    <span class="text-xs font-semibold">{{ $t->technician->name }}</span>
                </div>
                @else<span class="text-yellow-600 text-xs font-semibold">Unassigned</span>@endif
            </td>
            <td class="px-4 py-3">
                @php $pc=['urgent'=>'red','high'=>'orange','medium'=>'yellow','low'=>'green'][$t->priority??'medium']; @endphp
                <span class="px-2 py-0.5 bg-{{ $pc }}-100 text-{{ $pc }}-700 rounded-full text-xs font-semibold capitalize">{{ $t->priority }}</span>
            </td>
            <td class="px-4 py-3">
                @php $sc=['open'=>'blue','in_progress'=>'yellow','resolved'=>'green','closed'=>'gray'][$t->status??'open']; @endphp
                <span class="px-2 py-0.5 bg-{{ $sc }}-100 text-{{ $sc }}-700 rounded-full text-xs font-semibold capitalize">{{ str_replace('_',' ',$t->status) }}</span>
            </td>
            <td class="px-4 py-3"><span class="text-gray-500 text-xs">{{ ucfirst(str_replace('_',' ',$t->source ?? 'admin')) }}</span></td>
            <td class="px-4 py-3 text-center"><span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">{{ $t->replies->count() }}</span></td>
            <td class="px-4 py-3 text-gray-400 text-xs">{{ $t->created_at->diffForHumans() }}</td>
            <td class="px-4 py-3 text-right">
                <a href="{{ route('admin.support.show',$t->id) }}" class="text-blue-500 hover:text-blue-700 text-xs font-semibold mr-2"><i class="fas fa-eye"></i></a>
                <form action="{{ route('admin.support.destroy',$t->id) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('Delete ticket #{{ $t->id }}?')" class="text-red-400 hover:text-red-600 text-xs"><i class="fas fa-trash"></i></button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="10" class="px-4 py-12 text-center text-gray-400"><i class="fas fa-ticket-alt text-4xl mb-2 block text-gray-200"></i>No tickets found.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="px-4 py-3 border-t border-gray-100">{{ $tickets->links() }}</div>
</div>
@endsection
