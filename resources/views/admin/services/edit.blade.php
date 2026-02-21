@extends('layouts.admin')
@section('title','Edit Service')
@section('page-title','Edit Service')
@section('content')
<div class="max-w-2xl">
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
    <form action="{{ route('admin.services.update', $service->id) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2"><label class="block text-xs font-semibold text-gray-600 mb-1">Service Name</label><input type="text" name="name" value="{{ old('name',$service->name) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Slug</label><input type="text" name="slug" value="{{ old('slug',$service->slug) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none" required></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Category</label>
                <select name="category" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    @foreach(['internet','mobile','tv','business','voip'] as $c)<option value="{{ $c }}" {{ old('category',$service->category) === $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Icon</label><input type="text" name="icon" value="{{ old('icon',$service->icon) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required></div>
            <div class="col-span-2"><label class="block text-xs font-semibold text-gray-600 mb-1">Short Description</label><input type="text" name="short_description" value="{{ old('short_description',$service->short_description) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required></div>
            <div class="col-span-2"><label class="block text-xs font-semibold text-gray-600 mb-1">Description</label><textarea name="description" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>{{ old('description',$service->description) }}</textarea></div>
            <div><label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="active" value="1" {{ old('active',$service->active) ? 'checked' : '' }} class="rounded accent-orange-500"><span class="text-gray-600 text-sm">Active</span></label></div>
        </div>
        <div class="flex justify-end space-x-3 pt-2">
            <a href="{{ route('admin.services.index') }}" class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-5 py-2.5 text-white rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-save mr-1"></i>Update Service</button>
        </div>
    </form>
</div>
</div>
@endsection
