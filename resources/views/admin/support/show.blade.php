@extends('layouts.admin')
@section('title','Ticket #' . $ticket->id)
@section('page-title','Ticket #' . $ticket->id)
@section('page-subtitle', $ticket->subject)
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <!-- Main -->
    <div class="lg:col-span-2 space-y-4">
        <!-- Description -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-gray-800 font-bold mb-3">{{ $ticket->subject }}</h3>
            <p class="text-gray-600 text-sm whitespace-pre-wrap">{{ $ticket->description }}</p>
            @if($ticket->resolution)
            <div class="mt-4 bg-green-50 border border-green-200 rounded-xl p-3">
                <p class="text-green-700 font-semibold text-xs mb-1"><i class="fas fa-check-circle mr-1"></i>Resolution</p>
                <p class="text-green-800 text-sm">{{ $ticket->resolution }}</p>
                @if($ticket->resolved_at)<p class="text-green-500 text-xs mt-1">Resolved {{ $ticket->resolved_at->diffForHumans() }}</p>@endif
            </div>
            @endif
        </div>

        <!-- Replies -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-5 py-3 border-b border-gray-100"><h3 class="text-gray-700 font-bold text-sm"><i class="fas fa-comments text-orange-500 mr-2"></i>Replies ({{ $ticket->replies->count() }})</h3></div>
            <div class="p-5 space-y-4">
                @forelse($ticket->replies as $reply)
                <div class="flex space-x-3 {{ $reply->user_id ? '' : 'flex-row-reverse space-x-reverse' }}">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-black flex-shrink-0 {{ $reply->user_id ? 'bg-blue-600' : 'bg-orange-500' }}">
                        {{ strtoupper(substr($reply->author_name,0,1)) }}
                    </div>
                    <div class="flex-1 {{ $reply->user_id ? '' : 'text-right' }}">
                        <div class="inline-block rounded-xl px-4 py-2.5 text-sm max-w-md {{ $reply->user_id ? 'bg-gray-100 text-gray-800' : 'bg-orange-500 text-white' }}">
                            {{ $reply->message }}
                        </div>
                        <p class="text-gray-400 text-xs mt-1">{{ $reply->author_name }} · {{ $reply->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <p class="text-gray-400 text-sm text-center py-4">No replies yet.</p>
                @endforelse
            </div>
            <!-- Reply form -->
            <div class="px-5 pb-5">
                <form action="{{ route('admin.support.reply',$ticket->id) }}" method="POST" class="flex space-x-2">
                    @csrf
                    <input type="text" name="message" placeholder="Type a reply..." class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                    <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-orange-600"><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-4">
        <!-- Ticket Info -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-gray-700 font-bold text-sm mb-3">Ticket Details</h3>
            @php
            $pc=['urgent'=>['bg-red-100','text-red-700'],'high'=>['bg-orange-100','text-orange-700'],'medium'=>['bg-yellow-100','text-yellow-700'],'low'=>['bg-green-100','text-green-700']][$ticket->priority??'medium'];
            $sc=['open'=>['bg-blue-100','text-blue-700'],'in_progress'=>['bg-yellow-100','text-yellow-700'],'resolved'=>['bg-green-100','text-green-700'],'closed'=>['bg-gray-100','text-gray-700']][$ticket->status??'open'];
            @endphp
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-400">Status</span><span class="{{ $sc[0] }} {{ $sc[1] }} text-xs font-bold px-2 py-0.5 rounded-full capitalize">{{ str_replace('_',' ',$ticket->status) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Priority</span><span class="{{ $pc[0] }} {{ $pc[1] }} text-xs font-bold px-2 py-0.5 rounded-full capitalize">{{ $ticket->priority }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Source</span><span class="text-gray-700 text-xs">{{ ucfirst(str_replace('_',' ',$ticket->source ?? 'admin')) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Category</span><span class="text-gray-700 text-xs">{{ \App\Models\SupportTicket::categories()[$ticket->category] ?? ($ticket->category ?? 'N/A') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">FAT Node</span><span class="text-gray-700 text-xs">{{ $ticket->fatNode?->name ?? 'N/A' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Opened</span><span class="text-gray-700 text-xs">{{ $ticket->created_at->format('d M Y H:i') }}</span></div>
                @if($ticket->call_id)<div class="flex justify-between"><span class="text-gray-400">Call ID</span><span class="text-blue-700 text-xs font-mono">#{{ $ticket->call_id }}</span></div>@endif
            </div>
        </div>

        <!-- Client -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-gray-700 font-bold text-sm mb-3"><i class="fas fa-user text-blue-500 mr-1"></i>Client</h3>
            @if($ticket->client)
            <div class="text-sm space-y-1">
                <p class="font-bold text-gray-800">{{ $ticket->client->first_name }} {{ $ticket->client->last_name }}</p>
                <p class="text-gray-500 font-mono text-xs">{{ $ticket->client->username }}</p>
                <p class="text-gray-500 text-xs">{{ $ticket->client->phone }}</p>
                <a href="{{ route('admin.clients.show',$ticket->client->id) }}" class="text-orange-500 hover:text-orange-700 text-xs font-semibold"><i class="fas fa-arrow-right mr-1"></i>View Client</a>
            </div>
            @else<p class="text-gray-400 text-sm">No client linked.</p>@endif
        </div>

        <!-- Quick Update -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-gray-700 font-bold text-sm mb-3"><i class="fas fa-edit text-orange-500 mr-1"></i>Update Ticket</h3>
            <form action="{{ route('admin.support.update',$ticket->id) }}" method="POST" class="space-y-3">
                @csrf @method('PUT')
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Status</label>
                    <select name="status" class="w-full border border-gray-200 rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                        @foreach(['open'=>'Open','in_progress'=>'In Progress','resolved'=>'Resolved','closed'=>'Closed'] as $v=>$l)
                        <option value="{{ $v }}" {{ $ticket->status===$v?'selected':'' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Technician</label>
                    <select name="technician_id" class="w-full border border-gray-200 rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                        <option value="">Unassigned</option>
                        @foreach($technicians as $t)<option value="{{ $t->id }}" {{ $ticket->technician_id==$t->id?'selected':'' }}>{{ $t->name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Priority</label>
                    <select name="priority" class="w-full border border-gray-200 rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                        @foreach(['low'=>'Low','medium'=>'Medium','high'=>'High','urgent'=>'Urgent'] as $v=>$l)
                        <option value="{{ $v }}" {{ $ticket->priority===$v?'selected':'' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Resolution</label>
                    <textarea name="resolution" rows="2" class="w-full border border-gray-200 rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="How was this resolved?">{{ $ticket->resolution }}</textarea>
                </div>
                <button type="submit" class="w-full py-2 text-white rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)">Update Ticket</button>
            </form>
        </div>
    </div>
</div>
@endsection
