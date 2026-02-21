@extends('layouts.admin')
@section('title','Resellers')
@section('page-title','Reseller Management (Multi-tenancy)')
@section('content')
<div class="flex justify-between items-center mb-4">
    <p class="text-slate-400 text-sm">{{ $resellers->total() }} ISP resellers registered</p>
    <a href="{{ route('admin.resellers.create') }}" class="bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-sky-700"><i class="fas fa-plus mr-1"></i>Add Reseller</a>
</div>
<div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-900">
            <tr>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">Company</th>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">Contact</th>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">Clients</th>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">Operators</th>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">Commission</th>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">Status</th>
                <th class="px-4 py-3 text-right text-xs text-slate-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-700">
            @forelse($resellers as $r)
            <tr class="hover:bg-slate-700/40">
                <td class="px-4 py-3">
                    <p class="text-white font-semibold">{{ $r->company_name }}</p>
                    <p class="text-slate-500 text-xs">{{ $r->domain ?? 'No domain' }}</p>
                </td>
                <td class="px-4 py-3">
                    <p class="text-slate-300 text-xs">{{ $r->contact_name }}</p>
                    <p class="text-slate-500 text-xs">{{ $r->email }}</p>
                </td>
                <td class="px-4 py-3 text-white font-bold">{{ $r->clients_count }}</td>
                <td class="px-4 py-3 text-white font-bold">{{ $r->operators_count }}</td>
                <td class="px-4 py-3 text-yellow-400 font-bold">{{ $r->commission_rate }}%</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs
                        {{ $r->status === 'active' ? 'bg-green-900 text-green-300' : '' }}
                        {{ $r->status === 'suspended' ? 'bg-red-900 text-red-300' : '' }}
                        {{ $r->status === 'pending' ? 'bg-yellow-900 text-yellow-300' : '' }}">
                        {{ ucfirst($r->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('admin.resellers.show', $r->id) }}" class="text-slate-400 hover:text-white text-xs"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('admin.resellers.edit', $r->id) }}" class="text-sky-400 hover:text-sky-300 text-xs"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.resellers.destroy', $r->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete reseller?')" class="text-red-400 hover:text-red-300 text-xs"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-10 text-center text-slate-500">No resellers registered yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-slate-700">{{ $resellers->links() }}</div>
</div>
@endsection
