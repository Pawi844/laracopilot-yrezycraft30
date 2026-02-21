@extends('layouts.admin')
@section('title','Permissions')
@section('page-title','Staff Permissions')
@section('page-subtitle','Assign granular permissions to operators and technicians')
@section('content')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
        <h2 class="text-white font-black">Permission Matrix</h2>
        <p class="text-blue-200 text-xs">Only admins can modify permissions</p>
    </div>
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead style="background:#f8fafc">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Staff Member</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Role</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Permissions Granted</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">Manage</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($users as $user)
            @php $perms = \App\Models\AdminPermission::userPermissions($user->id); @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-black" style="background:linear-gradient(135deg,#1e3a5f,#0f2744)">{{ strtoupper(substr($user->name,0,1)) }}</div>
                        <div><p class="text-gray-800 font-semibold">{{ $user->name }}</p><p class="text-gray-400 text-xs">{{ $user->email }}</p></div>
                    </div>
                </td>
                <td class="px-4 py-3"><span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full font-semibold">{{ ucfirst($user->role ?? 'operator') }}</span></td>
                <td class="px-4 py-3">
                    <div class="flex flex-wrap gap-1">
                        @if(count($perms))
                            @foreach(array_slice($perms,0,5) as $p)
                            <span class="bg-green-100 text-green-700 text-xs px-1.5 py-0.5 rounded">{{ $allPerms[$p] ?? $p }}</span>
                            @endforeach
                            @if(count($perms)>5)<span class="text-gray-400 text-xs">+{{ count($perms)-5 }} more</span>@endif
                        @else
                            <span class="text-gray-400 text-xs">No permissions assigned</span>
                        @endif
                    </div>
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('admin.permissions.show', $user->id) }}" class="text-orange-500 hover:text-orange-700 text-xs font-semibold"><i class="fas fa-key mr-1"></i>Edit Permissions</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
@endsection
