@extends('layouts.admin')
@section('title','Call Centre')
@section('page-title','Call Centre')
@section('page-subtitle','Log and manage customer calls — auto-creates support tickets on escalation')
@section('content')
<!-- VoIP Integration Banner -->
@if($settings['url'])
<div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4 flex items-center justify-between">
    <div class="flex items-center space-x-3">
        <i class="fas fa-phone-alt text-blue-600 text-xl"></i>
        <div>
            <p class="text-blue-800 font-bold text-sm">VoIP System Connected</p>
            <p class="text-blue-500 text-xs">{{ $settings['url'] }} · User: {{ $settings['username'] }}</p>
        </div>
    </div>
    <a href="{{ $settings['url'] }}" target="_blank" class="bg-blue-600 text-white text-xs px-3 py-1.5 rounded-lg font-semibold hover:bg-blue-700"><i class="fas fa-external-link-alt mr-1"></i>Open Panel</a>
</div>
@else
<div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 mb-4 text-sm text-yellow-800">
    <i class="fas fa-exclamation-triangle mr-1"></i>No VoIP system configured. <a href="{{ route('admin.settings.group','callcentre') }}" class="font-bold underline">Configure now</a>
</div>
@endif

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
    @foreach([
        ['Calls Today',$stats['total_today'],'fa-phone','blue'],
        ['Answered',$stats['answered_today'],'fa-phone-volume','green'],
        ['Missed',$stats['missed_today'],'fa-phone-slash','red'],
        ['Avg Duration',gmdate('i:s',$stats['avg_duration']),'fa-clock','purple'],
    ] as [$l,$v,$i,$c])
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center space-x-3">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-{{ $c }}-100"><i class="fas {{ $i }} text-{{ $c }}-600"></i></div>
        <div><p class="text-gray-400 text-xs">{{ $l }}</p><p class="text-2xl font-black text-gray-800">{{ $v }}</p></div>
    </div>
    @endforeach
</div>

<!-- Log Call button + filters -->
<div class="flex flex-wrap gap-2 mb-4">
    <form method="GET" class="flex flex-wrap gap-2 flex-1">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Phone, username..." class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
        <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            <option value="">All Status</option>
            @foreach(['answered'=>'Answered','missed'=>'Missed','voicemail'=>'Voicemail','dropped'=>'Dropped'] as $v=>$l)
            <option value="{{ $v }}" {{ request('status')===$v?'selected':'' }}>{{ $l }}</option>
            @endforeach
        </select>
        <select name="direction" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            <option value="">Both Directions</option>
            <option value="inbound" {{ request('direction')==='inbound'?'selected':'' }}>Inbound</option>
            <option value="outbound" {{ request('direction')==='outbound'?'selected':'' }}>Outbound</option>
        </select>
        <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-semibold"><i class="fas fa-search mr-1"></i>Filter</button>
    </form>
    <a href="{{ route('admin.callcentre.create') }}" class="text-white px-4 py-2 rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-phone-alt mr-1"></i>Log Call</a>
</div>

<!-- Calls Table -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead style="background:#f8fafc">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Call</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Client</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Agent</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Direction</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Duration</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Disposition</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Ticket</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Time</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
        @forelse($calls as $call)
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-3">
                <p class="font-bold text-gray-800 font-mono">{{ $call->caller_number }}</p>
            </td>
            <td class="px-4 py-3">
                @if($call->client)
                <p class="text-xs font-semibold">{{ $call->client->first_name }} {{ $call->client->last_name }}</p>
                <p class="text-gray-400 text-xs">{{ $call->client->username }}</p>
                @else<span class="text-gray-400 text-xs">Unknown</span>@endif
            </td>
            <td class="px-4 py-3"><span class="text-gray-700 text-xs">{{ $call->agent?->name ?? 'N/A' }}</span></td>
            <td class="px-4 py-3">
                <span class="text-xs px-2 py-0.5 rounded-full {{ $call->direction==='inbound'?'bg-blue-100 text-blue-700':'bg-purple-100 text-purple-700' }}">
                    <i class="fas fa-{{ $call->direction==='inbound'?'arrow-down':'arrow-up' }} mr-0.5"></i>{{ ucfirst($call->direction) }}
                </span>
            </td>
            <td class="px-4 py-3">
                @php $sc=['answered'=>['bg-green-100','text-green-700'],'missed'=>['bg-red-100','text-red-700'],'voicemail'=>['bg-purple-100','text-purple-700'],'dropped'=>['bg-gray-100','text-gray-700']][$call->status??'answered']; @endphp
                <span class="text-xs px-2 py-0.5 rounded-full {{ $sc[0] }} {{ $sc[1] }} font-semibold capitalize">{{ $call->status }}</span>
            </td>
            <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $call->duration_formatted }}</td>
            <td class="px-4 py-3 text-xs text-gray-600 capitalize">{{ str_replace('_',' ',$call->disposition ?? '-') }}</td>
            <td class="px-4 py-3">
                @if($call->ticket)
                <a href="{{ route('admin.support.show',$call->ticket->id) }}" class="text-orange-500 hover:underline text-xs font-bold">#{{ $call->ticket->id }}</a>
                @else<span class="text-gray-300 text-xs">—</span>@endif
            </td>
            <td class="px-4 py-3 text-gray-400 text-xs">{{ $call->created_at->diffForHumans() }}</td>
        </tr>
        @empty
        <tr><td colspan="9" class="px-4 py-12 text-center text-gray-400"><i class="fas fa-phone-slash text-4xl text-gray-200 mb-2 block"></i>No calls logged yet.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="px-4 py-3 border-t border-gray-100">{{ $calls->links() }}</div>
</div>
@endsection
