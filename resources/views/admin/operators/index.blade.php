@extends('layouts.admin')
@section('title','Operators')
@section('page-title','Operator & Access Management')
@section('content')
<div class="flex justify-between items-center mb-4">
    <p class="text-slate-400 text-sm">{{ $operators->total() }} operators with system access</p>
    <a href="{{ route('admin.operators.create') }}" class="bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-sky-700"><i class="fas fa-user-plus mr-1"></i>Add Operator</a>
</div>
<div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-900">
            <tr>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">Operator</th>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">Role</th>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">Reseller</th>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">Permissions</th>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">Last Login</th>
                <th class="px-4 py-3 text-left text-xs text-slate-500 uppercase">Status</th>
                <th class="px-4 py-3 text-right text-xs text-slate-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-700">
            @forelse($operators as $op)
            <tr class="hover:bg-slate-700/40">
                <td class="px-4 py-3">
                    <p class="text-white font-semibold">{{ $op->name }}</p>
                    <p class="text-slate-500 text-xs">{{ $op->email }}</p>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded text-xs uppercase font-bold
                        {{ $op->role === 'superadmin' ? 'bg-red-900 text-red-300' : '' }}
                        {{ $op->role === 'admin' ? 'bg-orange-900 text-orange-300' : '' }}
                        {{ $op->role === 'operator' ? 'bg-blue-900 text-blue-300' : '' }}
                        {{ $op->role === 'support' ? 'bg-green-900 text-green-300' : '' }}">
                        {{ $op->role }}
                    </span>
                </td>
                <td class="px-4 py-3 text-slate-400 text-xs">{{ $op->reseller->company_name ?? 'Main ISP' }}</td>
                <td class="px-4 py-3">
                    @if(in_array($op->role, ['superadmin','admin']))
                    <span class="text-yellow-400 text-xs">Full Access</span>
                    @else
                    <span class="text-slate-400 text-xs">{{ count($op->permissions ?? []) }} permissions</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-slate-400 text-xs">{{ $op->last_login ? $op->last_login->diffForHumans() : 'Never' }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs {{ $op->active ? 'bg-green-900 text-green-300' : 'bg-slate-700 text-slate-400' }}">{{ $op->active ? 'Active' : 'Inactive' }}</span>
                </td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('admin.operators.edit', $op->id) }}" class="text-sky-400 hover:text-sky-300 text-xs"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.operators.destroy', $op->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete operator?')" class="text-red-400 hover:text-red-300 text-xs ml-2"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-10 text-center text-slate-500">No operators found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-slate-700">{{ $operators->links() }}</div>
</div>
@endsection
