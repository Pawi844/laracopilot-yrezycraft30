@extends('layouts.admin')
@section('title','NAS Servers')
@section('page-title','NAS Server Management')
@section('content')
<div class="flex justify-between items-center mb-4">
    <p class="text-slate-400 text-sm">{{ $nas->total() }} NAS servers configured</p>
    <a href="{{ route('admin.nas.create') }}" class="bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-sky-700"><i class="fas fa-plus mr-1"></i>Add NAS</a>
</div>
<div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-900">
            <tr>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">IP Addresses</th>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">Type</th>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">Secret</th>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">Status</th>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">Last Seen</th>
                <th class="px-4 py-3 text-right text-xs text-slate-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-700">
            @forelse($nas as $n)
            <tr class="hover:bg-slate-700/40">
                <td class="px-4 py-3">
                    <p class="text-white font-semibold">{{ $n->name }}</p>
                    <p class="text-slate-500 text-xs">{{ $n->shortname }}</p>
                </td>
                <td class="px-4 py-3">
                    @foreach($n->ip_addresses ?? [] as $ip)
                    <span class="bg-slate-700 text-slate-300 text-xs px-2 py-0.5 rounded font-mono mr-1">{{ $ip }}</span>
                    @endforeach
                </td>
                <td class="px-4 py-3 text-slate-300 text-xs capitalize">{{ $n->type }}</td>
                <td class="px-4 py-3 text-slate-400 text-xs font-mono">{{ Str::limit($n->secret, 15) }}</td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $n->status === 'active' ? 'bg-green-900 text-green-300' : '' }}
                        {{ $n->status === 'inactive' ? 'bg-slate-700 text-slate-400' : '' }}
                        {{ $n->status === 'unreachable' ? 'bg-red-900 text-red-300' : '' }}">
                        {{ ucfirst($n->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-slate-400 text-xs">{{ $n->last_seen ? $n->last_seen->diffForHumans() : 'Never' }}</td>
                <td class="px-4 py-3 text-right space-x-1">
                    <form action="{{ route('admin.nas.test', $n->id) }}" method="POST" class="inline">
                        @csrf <button type="submit" class="text-green-400 hover:text-green-300 text-xs" title="Test"><i class="fas fa-plug"></i></button>
                    </form>
                    <a href="{{ route('admin.nas.show', $n->id) }}" class="text-slate-400 hover:text-white text-xs ml-2"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('admin.nas.edit', $n->id) }}" class="text-sky-400 hover:text-sky-300 text-xs ml-2"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.nas.destroy', $n->id) }}" method="POST" class="inline ml-2">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete NAS?')" class="text-red-400 hover:text-red-300 text-xs"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-10 text-center text-slate-500">No NAS servers configured yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-slate-700">{{ $nas->links() }}</div>
</div>
@endsection
