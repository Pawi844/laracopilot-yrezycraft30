@extends('layouts.admin')
@section('title','Permissions: ' . $user->name)
@section('page-title','Edit Permissions — ' . $user->name)
@section('page-subtitle','Check/uncheck permissions for this staff member')
@section('content')
<div class="max-w-3xl">
<form action="{{ route('admin.permissions.update', $user->id) }}" method="POST">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-orange-500 flex items-center justify-center text-white font-black">{{ strtoupper(substr($user->name,0,1)) }}</div>
                <div><p class="text-white font-bold">{{ $user->name }}</p><p class="text-blue-200 text-xs">{{ $user->email }} · {{ ucfirst($user->role ?? 'operator') }}</p></div>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($allPerms as $perm => $label)
                <label class="flex items-start space-x-3 p-3 rounded-xl border cursor-pointer hover:bg-orange-50 hover:border-orange-200 transition-all {{ in_array($perm,$granted) ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }}">
                    <input type="checkbox" name="permissions[]" value="{{ $perm }}" {{ in_array($perm,$granted)?'checked':'' }} class="mt-0.5 accent-orange-500 w-4 h-4">
                    <div>
                        <p class="text-gray-800 text-sm font-semibold">{{ $label }}</p>
                        <p class="text-gray-400 text-xs font-mono">{{ $perm }}</p>
                    </div>
                </label>
                @endforeach
            </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-between">
            <a href="{{ route('admin.permissions.index') }}" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-100">← Back</a>
            <button type="submit" class="px-6 py-2.5 text-white rounded-xl text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-save mr-1"></i>Save Permissions</button>
        </div>
    </div>
</form>
</div>
@endsection
