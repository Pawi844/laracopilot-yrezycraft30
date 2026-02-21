@extends('layouts.admin')
@section('title','Routers')
@section('page-title','MikroTik Routers')
@section('page-subtitle','Manage your MikroTik devices via API')
@section('content')
<div class="flex justify-between items-center mb-4">
    <p class="text-gray-500 text-sm">{{ $routers->total() }} routers configured</p>
    <a href="{{ route('admin.routers.create') }}" class="text-white px-4 py-2 rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)">
        <i class="fas fa-plus mr-1"></i>Add Router
    </a>
</div>
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead style="background:#f8fafc">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Name</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">IP / Port</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Model</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">NAS</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Last Sync</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($routers as $r)
            <tr class="hover:bg-orange-50/30 transition-colors">
                <td class="px-4 py-3">
                    <p class="text-gray-800 font-semibold">{{ $r->name }}</p>
                    <p class="text-gray-400 text-xs">{{ $r->firmware ?? 'RouterOS' }}</p>
                </td>
                <td class="px-4 py-3">
                    <p class="text-blue-600 font-mono text-xs font-semibold">{{ $r->ip_address }}</p>
                    <p class="text-gray-400 text-xs">Port: {{ $r->api_port }}</p>
                </td>
                <td class="px-4 py-3 text-gray-600 text-xs">{{ $r->model ?? 'MikroTik' }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $r->nas->name ?? '-' }}</td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $r->status === 'online' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $r->status === 'offline' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $r->status === 'unknown' ? 'bg-gray-100 text-gray-600' : '' }}">
                        <span class="w-1.5 h-1.5 rounded-full mr-1 {{ $r->status === 'online' ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                        {{ ucfirst($r->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $r->last_sync ? $r->last_sync->diffForHumans() : 'Never' }}</td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end space-x-2">
                        <form action="{{ route('admin.routers.sync', $r->id) }}" method="POST" class="inline">
                            @csrf
                            <button class="text-green-600 hover:text-green-800 text-xs font-medium" title="Sync"><i class="fas fa-sync-alt mr-1"></i>Sync</button>
                        </form>
                        <a href="{{ route('admin.routers.edit', $r->id) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium"><i class="fas fa-edit mr-1"></i>Edit</a>
                        <form action="{{ route('admin.routers.destroy', $r->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Delete this router?')" class="text-red-500 hover:text-red-700 text-xs"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">
                <i class="fas fa-network-wired text-4xl mb-2 block text-gray-200"></i>
                No routers configured. <a href="{{ route('admin.routers.create') }}" class="text-orange-500 font-semibold">Add one →</a>
            </td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">{{ $routers->links() }}</div>
</div>
@endsection
