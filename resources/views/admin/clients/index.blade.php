@extends('layouts.admin')
@section('title','Clients')
@section('page-title','Client Management')
@section('page-subtitle','All ISP subscribers')
@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-4">
    <form method="GET" class="flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search username, name, phone..." class="border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-orange-400 focus:outline-none w-56">
        <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-orange-400 focus:outline-none">
            <option value="">All Status</option>
            @foreach(['active','inactive','suspended','expired'] as $s)
            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <select name="type" class="border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-orange-400 focus:outline-none">
            <option value="">All Types</option>
            @foreach(['pppoe','hotspot','static'] as $t)
            <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ strtoupper($t) }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-xs hover:bg-gray-200 font-semibold"><i class="fas fa-search mr-1"></i>Filter</button>
    </form>
    <a href="{{ route('admin.clients.create') }}" class="text-white px-4 py-2 rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)">
        <i class="fas fa-user-plus mr-1"></i>Add Client
    </a>
</div>
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead style="background:#f8fafc">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Username</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name / Contact</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Plan</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Expiry</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($clients as $client)
            <tr class="hover:bg-orange-50/30 transition-colors">
                <td class="px-4 py-3">
                    <p class="text-blue-700 font-mono text-xs font-bold">{{ $client->username }}</p>
                    @if($client->static_ip)<p class="text-gray-400 text-xs font-mono">{{ $client->static_ip }}</p>@endif
                </td>
                <td class="px-4 py-3">
                    <p class="text-gray-800 font-medium text-xs">{{ $client->full_name }}</p>
                    <p class="text-gray-400 text-xs">{{ $client->phone }}</p>
                </td>
                <td class="px-4 py-3">
                    <span class="text-xs font-bold px-2 py-0.5 rounded uppercase
                        {{ $client->connection_type === 'pppoe' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $client->connection_type === 'hotspot' ? 'bg-orange-100 text-orange-700' : '' }}
                        {{ $client->connection_type === 'static' ? 'bg-purple-100 text-purple-700' : '' }}">
                        {{ $client->connection_type }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-600 text-xs">{{ $client->plan->name ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $client->expiry_date ? $client->expiry_date->format('d M Y') : '-' }}</td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $client->status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $client->status === 'inactive' ? 'bg-gray-100 text-gray-500' : '' }}
                        {{ $client->status === 'suspended' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $client->status === 'expired' ? 'bg-orange-100 text-orange-700' : '' }}">
                        {{ ucfirst($client->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end space-x-2">
                        <a href="{{ route('admin.clients.show', $client->id) }}" class="text-gray-400 hover:text-gray-600 text-xs" title="View"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.clients.edit', $client->id) }}" class="text-blue-500 hover:text-blue-700 text-xs" title="Edit"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.clients.disconnect', $client->id) }}" method="POST" class="inline">
                            @csrf <button class="text-orange-500 hover:text-orange-700 text-xs" title="Disconnect"><i class="fas fa-unlink"></i></button>
                        </form>
                        <form action="{{ route('admin.clients.destroy', $client->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Delete client?')" class="text-red-500 hover:text-red-700 text-xs"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">
                <i class="fas fa-users text-4xl mb-2 block text-gray-200"></i>
                No clients found.
            </td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">{{ $clients->links() }}</div>
</div>
@endsection
