@extends('layouts.admin')
@section('title','Routers')
@section('page-title','MikroTik Routers')
@section('page-subtitle','Manage routers — API access, OpenVPN tunnels, sync')
@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-4">
    <form method="GET" class="flex gap-2">
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or IP..." class="border border-gray-200 rounded-lg pl-8 pr-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none w-48 sm:w-64">
        </div>
        <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm">Search</button>
    </form>
    <div class="flex gap-2">
        <p class="text-gray-400 text-sm self-center">{{ $routers->total() }} router(s)</p>
        <a href="{{ route('admin.routers.create') }}" class="text-white px-4 py-2 rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)">
            <i class="fas fa-plus mr-1"></i>Add Router
        </a>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead style="background:#f8fafc">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Router</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 hidden sm:table-cell">IP Address</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 hidden md:table-cell">API Port</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">OVPN</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 hidden lg:table-cell">Reseller</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
        @forelse($routers as $router)
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-4 py-3">
                <p class="font-bold text-gray-800 text-sm">{{ $router->name }}</p>
                <p class="text-gray-400 text-xs font-mono sm:hidden">{{ $router->ip_address }}</p>
            </td>
            <td class="px-4 py-3 hidden sm:table-cell">
                <span class="font-mono text-xs text-gray-700">{{ $router->ip_address }}</span>
            </td>
            <td class="px-4 py-3 hidden md:table-cell">
                <span class="font-mono text-xs text-gray-500">{{ $router->api_port ?? 8728 }}</span>
            </td>
            <td class="px-4 py-3">
                @if($router->use_ovpn)
                    <span class="bg-orange-100 text-orange-700 text-xs px-2 py-0.5 rounded-full font-semibold">
                        <i class="fas fa-shield-alt mr-0.5"></i>{{ $router->ovpn_gateway ?: 'OVPN' }}
                    </span>
                @else
                    <span class="text-gray-300 text-xs">Direct</span>
                @endif
            </td>
            <td class="px-4 py-3 hidden lg:table-cell">
                <span class="text-gray-500 text-xs">{{ $router->reseller?->company_name ?? '—' }}</span>
            </td>
            <td class="px-4 py-3">
                <div class="flex justify-end items-center gap-1 flex-wrap">
                    <a href="{{ route('admin.mikrotik.dashboard', $router->id) }}" class="w-7 h-7 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors" title="MikroTik Dashboard">
                        <i class="fas fa-tachometer-alt text-xs"></i>
                    </a>
                    <form action="{{ route('admin.routers.sync', $router->id) }}" method="POST" class="inline">
                        @csrf
                        <button title="Test Connection" class="w-7 h-7 flex items-center justify-center rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition-colors">
                            <i class="fas fa-plug text-xs"></i>
                        </button>
                    </form>
                    @if($router->use_ovpn)
                    <a href="{{ route('admin.routers.ovpn_config', $router->id) }}" class="w-7 h-7 flex items-center justify-center rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-100 transition-colors" title="Download .ovpn config">
                        <i class="fas fa-download text-xs"></i>
                    </a>
                    @endif
                    <a href="{{ route('admin.routers.edit', $router->id) }}" class="w-7 h-7 flex items-center justify-center rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-100 transition-colors" title="Edit">
                        <i class="fas fa-edit text-xs"></i>
                    </a>
                    <form action="{{ route('admin.routers.destroy', $router->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Delete {{ $router->name }}?')" title="Delete" class="w-7 h-7 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition-colors">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="px-4 py-16 text-center">
                <i class="fas fa-router text-5xl text-gray-200 mb-3 block"></i>
                <p class="text-gray-500 font-semibold">No routers configured yet</p>
                <a href="{{ route('admin.routers.create') }}" class="mt-2 inline-block text-orange-600 text-sm font-semibold hover:underline">Add your first router →</a>
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>
    </div>
    @if($routers->hasPages())
    <div class="px-4 py-3 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <p class="text-gray-400 text-xs">Showing {{ $routers->firstItem() }}–{{ $routers->lastItem() }} of {{ $routers->total() }}</p>
        {{ $routers->links() }}
    </div>
    @endif
</div>
@endsection
