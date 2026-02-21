@extends('layouts.admin')
@section('title','Services')
@section('page-title','Services Management')
@section('page-subtitle','Public-facing services listed on the website')
@section('content')
<div class="flex justify-between items-center mb-4">
    <p class="text-gray-400 text-sm">{{ $services->total() }} services</p>
    <a href="{{ route('admin.services.create') }}" class="text-white px-4 py-2 rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)">
        <i class="fas fa-plus mr-1"></i>Add Service
    </a>
</div>
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead style="background:#f8fafc">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Service</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Category</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($services as $service)
            <tr class="hover:bg-orange-50/30 transition-colors">
                <td class="px-4 py-3">
                    <div class="flex items-center space-x-3">
                        <span class="text-2xl">{{ $service->icon }}</span>
                        <div>
                            <p class="text-gray-800 font-semibold">{{ $service->name }}</p>
                            <p class="text-gray-400 text-xs">{{ Str::limit($service->short_description, 55) }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3"><span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-0.5 rounded capitalize">{{ $service->category }}</span></td>
                <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $service->active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $service->active ? 'Active' : 'Inactive' }}</span></td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('admin.services.edit', $service->id) }}" class="text-blue-500 hover:text-blue-700 text-xs font-medium"><i class="fas fa-edit mr-1"></i>Edit</a>
                    <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Delete?')" class="text-red-500 hover:text-red-700 text-xs ml-1"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-12 text-center text-gray-400">No services found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">{{ $services->links() }}</div>
</div>
@endsection
