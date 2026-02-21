@extends('layouts.admin')
@section('title', 'Support Tickets - Mobilink Admin')
@section('page-title', 'Support Tickets')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Subject</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">From</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Priority</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($tickets as $ticket)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm text-gray-500">#{{ $ticket->id }}</td>
                <td class="px-6 py-4">
                    <p class="font-semibold text-gray-800 text-sm">{{ Str::limit($ticket->subject, 45) }}</p>
                </td>
                <td class="px-6 py-4">
                    <p class="text-sm font-medium text-gray-800">{{ $ticket->name }}</p>
                    <p class="text-xs text-gray-500">{{ $ticket->email }}</p>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $ticket->priority === 'urgent' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $ticket->priority === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                        {{ $ticket->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $ticket->priority === 'low' ? 'bg-gray-100 text-gray-600' : '' }}">
                        {{ ucfirst($ticket->priority) }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $ticket->status === 'open' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $ticket->status === 'in_progress' ? 'bg-purple-100 text-purple-800' : '' }}
                        {{ $ticket->status === 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $ticket->status === 'closed' ? 'bg-gray-100 text-gray-600' : '' }}">
                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-xs text-gray-500">{{ $ticket->created_at->diffForHumans() }}</td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="{{ route('admin.support.show', $ticket->id) }}" class="text-sky-600 hover:text-sky-800 text-sm"><i class="fas fa-eye"></i></a>
                    <form action="{{ route('admin.support.destroy', $ticket->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete ticket?')" class="text-red-500 hover:text-red-700 text-sm ml-2"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500">No support tickets found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4 border-t">{{ $tickets->links() }}</div>
</div>
@endsection
