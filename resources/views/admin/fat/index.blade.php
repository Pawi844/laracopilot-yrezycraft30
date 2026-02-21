@extends('layouts.admin')
@section('title','FAT Nodes')
@section('page-title','FAT Node Management')
@section('page-subtitle','Fiber Access Terminals — each FAT maps to a specific number of ONUs')
@section('content')
<div class="flex justify-between items-center mb-4">
    <p class="text-gray-500 text-sm">{{ $nodes->total() }} FAT nodes configured</p>
    <div class="flex space-x-2">
        <a href="{{ route('admin.fat.index') }}" class="border border-gray-200 text-gray-600 px-3 py-2 rounded-lg text-xs hover:bg-gray-50"><i class="fas fa-sync mr-1"></i>Refresh</a>
        <a href="{{ route('admin.fat.create') }}" class="text-white px-4 py-2 rounded-xl text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-plus mr-1"></i>Add FAT Node</a>
    </div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($nodes as $node)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-all">
        <div class="px-4 py-3 flex justify-between items-center" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
            <div>
                <p class="text-white font-bold">{{ $node->name }}</p>
                <p class="text-blue-200 font-mono text-xs">{{ $node->code }}</p>
            </div>
            <span class="text-xs px-2 py-1 rounded-full font-semibold {{ $node->status === 'active' ? 'bg-green-400/20 text-green-300' : ($node->status === 'full' ? 'bg-red-400/20 text-red-300' : 'bg-gray-400/20 text-gray-300') }}">{{ ucfirst($node->status) }}</span>
        </div>
        <div class="p-4">
            <!-- Capacity Bar -->
            <div class="mb-3">
                <div class="flex justify-between text-xs mb-1">
                    <span class="text-gray-500">ONU Capacity</span>
                    <span class="font-bold {{ $node->usage_percent >= 90 ? 'text-red-600' : ($node->usage_percent >= 70 ? 'text-yellow-600' : 'text-green-600') }}">{{ $node->used_onu }}/{{ $node->max_onu }} ({{ $node->usage_percent }}%)</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5">
                    @php $barColor = $node->usage_percent>=90?'#ef4444':($node->usage_percent>=70?'#eab308':'#22c55e'); @endphp
                    <div class="h-2.5 rounded-full transition-all" style="width:{{ $node->usage_percent }}%;background:{{ $barColor }}"></div>
                </div>
                <p class="text-gray-400 text-xs mt-1">{{ $node->available_slots }} slots available</p>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div><p class="text-gray-400">Router</p><p class="font-semibold text-gray-700">{{ $node->router?->name ?? '-' }}</p></div>
                <div><p class="text-gray-400">Splitter</p><p class="font-semibold text-gray-700">{{ $node->splitter_type ?? '-' }}</p></div>
                <div><p class="text-gray-400">OLT Port</p><p class="font-semibold font-mono text-gray-700">{{ $node->olt_port ?? '-' }}</p></div>
                <div><p class="text-gray-400">Technician</p><p class="font-semibold text-gray-700">{{ $node->technician?->name ?? '-' }}</p></div>
            </div>
            @if($node->location)
            <p class="mt-2 text-gray-400 text-xs"><i class="fas fa-map-marker-alt mr-1 text-orange-400"></i>{{ Str::limit($node->location,45) }}</p>
            @endif
        </div>
        <div class="px-4 py-3 border-t border-gray-100 flex space-x-2">
            <a href="{{ route('admin.fat.show', $node->id) }}" class="flex-1 text-center text-xs font-semibold text-blue-600 hover:text-blue-800"><i class="fas fa-eye mr-1"></i>View</a>
            <a href="{{ route('admin.fat.edit', $node->id) }}" class="flex-1 text-center text-xs font-semibold text-orange-600 hover:text-orange-800"><i class="fas fa-edit mr-1"></i>Edit</a>
            <form action="{{ route('admin.fat.destroy', $node->id) }}" method="POST" class="flex-1">
                @csrf @method('DELETE')
                <button onclick="return confirm('Delete FAT node?')" class="w-full text-xs font-semibold text-red-500 hover:text-red-700"><i class="fas fa-trash mr-1"></i>Delete</button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-3 bg-white rounded-xl p-12 text-center border border-gray-100 shadow-sm">
        <i class="fas fa-project-diagram text-5xl text-gray-200 mb-3 block"></i>
        <h3 class="text-gray-600 font-bold">No FAT Nodes Configured</h3>
        <p class="text-gray-400 text-sm mt-1 mb-4">FAT nodes define where your fiber splitters are and how many ONUs each can serve.</p>
        <a href="{{ route('admin.fat.create') }}" class="text-white px-5 py-2.5 rounded-xl font-semibold text-sm" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-plus mr-1"></i>Add First FAT Node</a>
    </div>
    @endforelse
</div>
<div class="mt-4">{{ $nodes->links() }}</div>
@endsection
