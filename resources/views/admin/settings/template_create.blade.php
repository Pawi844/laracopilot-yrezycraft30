@extends('layouts.admin')
@section('title','New Template')
@section('page-title','Create Notification Template')
@section('content')
<div class="max-w-3xl">
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
    <form action="{{ route('admin.settings.templates.store') }}" method="POST" class="space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Event *</label>
                <select name="event" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                    <option value="">-- Select Event --</option>
                    @foreach($events as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Channel *</label>
                <select name="channel" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                    <option value="sms">SMS</option>
                    <option value="email">Email</option>
                    <option value="whatsapp">WhatsApp</option>
                </select>
            </div>
        </div>
        <div><label class="block text-xs font-semibold text-gray-600 mb-1">Subject (Email only)</label>
            <input type="text" name="subject" value="{{ old('subject') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
        </div>
        <div><label class="block text-xs font-semibold text-gray-600 mb-1">Message Body *</label>
            <p class="text-gray-400 text-xs mb-1">Variables: {{ implode(' ', $vars) }}</p>
            <textarea name="body" rows="7" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none" required>{{ old('body') }}</textarea>
        </div>
        <div><label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="active" value="1" checked class="rounded accent-orange-500"><span class="text-gray-600 text-sm">Active</span></label></div>
        <div class="flex justify-between pt-2">
            <a href="{{ route('admin.settings.templates') }}" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50">← Back</a>
            <button type="submit" class="px-5 py-2.5 text-white rounded-xl text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-save mr-1"></i>Create Template</button>
        </div>
    </form>
</div>
</div>
@endsection
