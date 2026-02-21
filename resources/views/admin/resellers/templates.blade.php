@extends('layouts.admin')
@section('title', 'Reseller Templates: ' . $reseller->company_name)
@section('page-title', $reseller->company_name . ' — Notification Templates')
@section('page-subtitle', 'Custom templates override system defaults for this reseller\'s clients')
@section('content')
<div class="max-w-3xl space-y-4">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 flex space-x-2">
        <a href="{{ route('admin.resellers.settings', $reseller->id) }}" class="mk-tab"><i class="fas fa-cog"></i> Settings</a>
        <a href="{{ route('admin.resellers.templates', $reseller->id) }}" class="mk-tab active"><i class="fas fa-file-alt"></i> Templates</a>
        <a href="{{ route('admin.resellers.show', $reseller->id) }}" class="mk-tab"><i class="fas fa-arrow-left"></i> Back</a>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-xl p-3">
        <p class="text-blue-800 text-xs"><i class="fas fa-info-circle mr-1"></i>These templates <strong>override system defaults</strong> for clients belonging to <strong>{{ $reseller->company_name }}</strong>. Leave empty to inherit system templates.</p>
    </div>

    @if($templates->count())
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-4 py-3 border-b border-gray-100"><h3 class="text-gray-800 font-bold text-sm">{{ $templates->count() }} Custom Templates</h3></div>
        <div class="divide-y divide-gray-50">
            @foreach($templates as $tpl)
            <div class="p-4 flex justify-between items-start">
                <div>
                    <div class="flex items-center space-x-2 mb-1">
                        <span class="bg-orange-100 text-orange-700 text-xs px-2 py-0.5 rounded font-bold">{{ strtoupper($tpl->channel) }}</span>
                        <span class="text-gray-700 font-semibold text-xs">{{ $events[$tpl->event] ?? $tpl->event }}</span>
                    </div>
                    <p class="text-gray-500 text-xs">{{ Str::limit($tpl->body, 100) }}</p>
                </div>
                <form action="{{ route('admin.resellers.templates.destroy', [$reseller->id, $tpl->id]) }}" method="POST">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('Delete?')" class="text-red-400 hover:text-red-600 text-xs"><i class="fas fa-trash"></i></button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h3 class="text-gray-800 font-bold text-sm mb-4">Add Custom Template</h3>
        <form action="{{ route('admin.resellers.templates.store', $reseller->id) }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1">Event</label>
                    <select name="event" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                        @foreach($events as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                    </select>
                </div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1">Channel</label>
                    <select name="channel" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                        <option value="sms">SMS</option><option value="email">Email</option><option value="whatsapp">WhatsApp</option>
                    </select>
                </div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1">Subject (Email)</label>
                    <input type="text" name="subject" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                </div>
                <div class="col-span-2"><label class="block text-xs font-semibold text-gray-600 mb-1">Body *</label>
                    <p class="text-gray-400 text-xs mb-1">Variables: {name} {username} {plan} {expiry} {amount} {company} {paybill_no}</p>
                    <textarea name="body" rows="5" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none" required></textarea>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2.5 text-white rounded-xl text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-save mr-1"></i>Add Template</button>
            </div>
        </form>
    </div>
</div>
@endsection
