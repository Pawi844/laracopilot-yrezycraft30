@extends('layouts.admin')
@section('title', 'Services - Mobilink Admin')
@section('page-title', 'Services Management')

@section('content')
<div class="flex justify-between items-center mb-6">
    <p class="text-gray-500">Manage your service offerings</p>
    <a href="{{ route('admin.services.create') }}" class="bg-sky-600 text-white px-5 py-2 rounded-lg hover:bg-sky-700 transition-all font-semibold">
        <i class="fas fa-plus mr-2"></i>Add Service
    </a>
</div>
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Service</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Category</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($services as $service)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="flex items-center space-x-3">
                        <span class="text-2xl">{{ $service->icon }}</span>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $service->name }}</p>
                            <p class="text-xs text-gray-500">{{ Str::limit($service->short_description, 60) }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4"><span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded capitalize">{{ $service->category }}</span></td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $service->active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                        {{ $service->active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="{{ route('admin.services.edit', $service->id) }}" class="text-sky-600 hover:text-sky-800 font-medium text-sm"><i class="fas fa-edit"></i> Edit</a>
                    <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this service?')" class="text-red-500 hover:text-red-700 font-medium text-sm ml-2"><i class="fas fa-trash"></i> Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500"><i class="fas fa-network-wired text-4xl mb-3 block"></i>No services found. Add your first service!</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4 border-t">{{ $services->links() }}</div>
</div>
@endsection
