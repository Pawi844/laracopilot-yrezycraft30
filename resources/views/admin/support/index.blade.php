@extends('layouts.admin')
@section('title','Support Tickets')
@section('page-title','Support Tickets')
@section('page-subtitle','Customer support requests')
@section('content')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead style="background:#f8fafc">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Subject</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">From</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Priority</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($tickets as $ticket)
            <tr class="hover:bg-orange-50/30 transition-colors">
                <td class="px-4 py-3 text-gray-400 text-xs">#{{ $ticket->id }}</td>
                <td class="px-4 py-3"><p class="text-gray-800 font-semibold text-xs">{{ Str::limit($ticket->subject, 45) }}</p></td>
                <td class="px-4 py-3">
                    <p class="text-gray-700 text-xs font-medium">{{ $ticket->name }}</p>
                    <p class="text-gray-400 text-xs">{{ $ticket->email }}</p>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $ticket->priority === 'urgent' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $ticket->priority === 'high' ? 'bg-orange-100 text-orange-700' : '' }}
                        {{ $ticket->priority === 'medium' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $ticket->priority === 'low' ? 'bg-gray-100 text-gray-500' : '' }}">
                        {{ ucfirst($ticket->priority) }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $ticket->status === 'open' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $ticket->status === 'in_progress' ? 'bg-purple-100 text-purple-700' : '' }}
                        {{ $ticket->status === 'resolved' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $ticket->status === 'closed' ? 'bg-gray-100 text-gray-500' : '' }}">
                        {{ ucfirst(str_replace('_',' ',$ticket->status)) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $ticket->created_at->diffForHumans() }}</td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end space-x-2">
                        <a href="{{ route('admin.support.show', $ticket->id) }}" class="text-blue-500 hover:text-blue-700 text-xs"><i class="fas fa-eye"></i></a>
                        <form action="{{ route('admin.support.destroy', $ticket->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Delete ticket?')" class="text-red-500 hover:text-red-700 text-xs"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400"><i class="fas fa-headset text-4xl mb-2 block text-gray-200"></i>No support tickets.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">{{ $tickets->links() }}</div>
</div>
@endsection
