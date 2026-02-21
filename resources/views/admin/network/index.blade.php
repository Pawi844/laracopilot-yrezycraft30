@extends('layouts.admin')
@section('title', 'Network Coverage - Mobilink Admin')
@section('page-title', 'Network Coverage Zones')

@section('content')
<div class="flex justify-between items-center mb-6">
    <p class="text-gray-500">{{ $zones->total() }} coverage zones managed</p>
    <a href="{{ route('admin.network.create') }}" class="bg-sky-600 text-white px-5 py-2 rounded-lg hover:bg-sky-700 font-semibold">
        <i class="fas fa-plus mr-2"></i>Add Zone
    </a>
</div>
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Location</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Coverage Type</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Signal</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($zones as $zone)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <p class="font-semibold text-gray-800">{{ $zone->area }}</p>
                    <p class="text-xs text-gray-500">{{ $zone->county }} County</p>
                </td>
                <td class="px-6 py-4">
                    <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded uppercase">{{ $zone->coverage_type }}</span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center space-x-1">
                        @for($i = 1; $i <= 5; $i++)
                            <div class="w-2 h-{{ $i * 1 + 2 }} rounded-sm {{ $i <= $zone->signal_strength ? 'bg-sky-500' : 'bg-gray-200' }}"></div>
                        @endfor
                        <span class="text-xs text-gray-500 ml-1">{{ $zone->signal_strength }}/5</span>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $zone->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $zone->status === 'planned' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $zone->status === 'maintenance' ? 'bg-orange-100 text-orange-800' : '' }}
                        {{ $zone->status === 'limited' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                        {{ ucfirst($zone->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="{{ route('admin.network.edit', $zone->id) }}" class="text-sky-600 hover:text-sky-800 text-sm"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.network.destroy', $zone->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Remove zone?')" class="text-red-500 hover:text-red-700 text-sm ml-2"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">No network zones added.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4 border-t">{{ $zones->links() }}</div>
</div>
@endsection
