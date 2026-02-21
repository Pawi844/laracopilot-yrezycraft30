@extends('layouts.admin')
@section('title', 'Ticket Details - Mobilink Admin')
@section('page-title', 'Support Ticket Details')

@section('content')
<div class="max-w-3xl">
    <div class="grid grid-cols-1 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $ticket->subject }}</h2>
                    <p class="text-sm text-gray-500 mt-1">From: <strong>{{ $ticket->name }}</strong> ({{ $ticket->email }}) · {{ $ticket->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div class="flex space-x-2">
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $ticket->priority === 'urgent' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">{{ ucfirst($ticket->priority) }}</span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $ticket->status === 'open' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-gray-700 text-sm leading-relaxed">
                {{ $ticket->message }}
            </div>
            @if($ticket->admin_notes)
            <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-xs font-semibold text-yellow-700 mb-1">Admin Notes:</p>
                <p class="text-sm text-yellow-800">{{ $ticket->admin_notes }}</p>
            </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-bold text-gray-800 mb-4">Update Ticket</h3>
            <form action="{{ route('admin.support.update', $ticket->id) }}" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                            @foreach(['open', 'in_progress', 'resolved', 'closed'] as $s)
                                <option value="{{ $s }}" {{ $ticket->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Priority</label>
                        <select name="priority" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                            @foreach(['low', 'medium', 'high', 'urgent'] as $p)
                                <option value="{{ $p }}" {{ $ticket->priority === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Admin Notes</label>
                        <textarea name="admin_notes" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-sky-500 focus:outline-none" placeholder="Add internal notes...">{{ $ticket->admin_notes }}</textarea>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.support.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">← Back</a>
                    <button type="submit" class="px-5 py-2.5 bg-sky-600 text-white rounded-lg hover:bg-sky-700 font-semibold">Update Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
