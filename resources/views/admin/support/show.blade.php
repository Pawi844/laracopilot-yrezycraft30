@extends('layouts.admin')
@section('title','Ticket Details')
@section('page-title','Support Ticket')
@section('content')
<div class="max-w-3xl space-y-5">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h2 class="text-gray-800 font-bold text-lg">{{ $ticket->subject }}</h2>
                <p class="text-gray-400 text-sm">From <strong class="text-gray-600">{{ $ticket->name }}</strong> ({{ $ticket->email }}) · {{ $ticket->created_at->format('d M Y, H:i') }}</p>
            </div>
            <div class="flex space-x-2">
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $ticket->priority === 'urgent' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">{{ ucfirst($ticket->priority) }}</span>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $ticket->status === 'open' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">{{ ucfirst(str_replace('_',' ',$ticket->status)) }}</span>
            </div>
        </div>
        <div class="bg-gray-50 rounded-xl p-4 text-gray-700 text-sm leading-relaxed">{{ $ticket->message }}</div>
        @if($ticket->admin_notes)
        <div class="mt-4 bg-orange-50 border border-orange-200 rounded-xl p-4">
            <p class="text-xs font-bold text-orange-600 mb-1">Admin Notes:</p>
            <p class="text-gray-700 text-sm">{{ $ticket->admin_notes }}</p>
        </div>
        @endif
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-gray-800 font-bold text-sm mb-4">Update Ticket</h3>
        <form action="{{ route('admin.support.update', $ticket->id) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                        @foreach(['open','in_progress','resolved','closed'] as $s)
                        <option value="{{ $s }}" {{ $ticket->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1">Priority</label>
                    <select name="priority" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                        @foreach(['low','medium','high','urgent'] as $p)
                        <option value="{{ $p }}" {{ $ticket->priority === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2"><label class="block text-xs font-semibold text-gray-600 mb-1">Admin Notes</label>
                    <textarea name="admin_notes" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="Internal notes...">{{ $ticket->admin_notes }}</textarea>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.support.index') }}" class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">← Back</a>
                <button type="submit" class="px-5 py-2.5 text-white rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)">Update Ticket</button>
            </div>
        </form>
    </div>
</div>
@endsection
