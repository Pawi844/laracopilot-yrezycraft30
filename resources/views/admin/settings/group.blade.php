@extends('layouts.admin')
@section('title', ucfirst($group) . ' Settings')
@section('page-title', ucfirst($group) . ' Configuration')
@section('page-subtitle', 'Configure ' . $group . ' settings')
@section('content')
@php
$icons = ['general'=>'fa-building','mpesa'=>'fa-mobile-alt','sms'=>'fa-sms','whatsapp'=>'fa-comment','mail'=>'fa-envelope','billing'=>'fa-file-invoice'];
$colors = ['general'=>'#2563eb','mpesa'=>'#16a34a','sms'=>'#0284c7','whatsapp'=>'#16a34a','mail'=>'#7c3aed','billing'=>'#ea580c'];
$selects = [
    'type'        => ['paybill'=>'Paybill','till'=>'Till Number'],
    'environment' => ['sandbox'=>'Sandbox (Testing)','production'=>'Production (Live)'],
    'gateway'     => [
        'sms'       => ['africastalking'=>"Africa's Talking",'twilio'=>'Twilio','vonage'=>'Vonage','infobip'=>'Infobip','custom'=>'Custom HTTP API'],
        'whatsapp'  => ['360dialog'=>'360dialog','twilio'=>'Twilio','vonage'=>'Vonage','ultramsg'=>'UltraMsg','custom'=>'Custom HTTP API'],
        'mail'      => ['smtp'=>'SMTP','mailgun'=>'Mailgun','ses'=>'Amazon SES','postmark'=>'Postmark'],
    ],
    'mailer'      => ['smtp'=>'SMTP','mailgun'=>'Mailgun','ses'=>'Amazon SES','postmark'=>'Postmark'],
    'encryption'  => ['tls'=>'TLS','ssl'=>'SSL','none'=>'None'],
];
@endphp
<div class="max-w-3xl">
    <!-- Breadcrumb -->
    <div class="flex items-center space-x-2 text-xs text-gray-400 mb-4">
        <a href="{{ route('admin.settings.index') }}" class="hover:text-orange-500">Settings</a>
        <i class="fas fa-chevron-right text-gray-300"></i>
        <span class="text-gray-700 font-semibold capitalize">{{ $group }}</span>
    </div>

    <form action="{{ route('admin.settings.update', $group) }}" method="POST">
        @csrf @method('PUT')
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 flex items-center space-x-3" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
                <i class="fas {{ $icons[$group] ?? 'fa-cog' }} text-orange-400 text-lg"></i>
                <div>
                    <h2 class="text-white font-black capitalize">{{ $group }} Settings</h2>
                    <p class="text-blue-200 text-xs">All changes are saved immediately</p>
                </div>
            </div>
            <div class="p-6 space-y-5">
                @foreach($settings as $s)
                <div class="{{ $s->type === 'toggle' ? 'flex items-center justify-between' : '' }}">
                    @if($s->type === 'toggle')
                        <div>
                            <p class="text-gray-700 font-semibold text-sm">{{ $s->label }}</p>
                            @if($s->description)<p class="text-gray-400 text-xs">{{ $s->description }}</p>@endif
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="{{ $s->key }}" value="1" {{ $s->value === '1' ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-orange-400 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                        </label>
                    @elseif($s->type === 'textarea')
                        <label class="block text-xs font-semibold text-gray-600 mb-1">{{ $s->label }}</label>
                        @if($s->description)<p class="text-gray-400 text-xs mb-1">{{ $s->description }}</p>@endif
                        <textarea name="{{ $s->key }}" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">{{ old($s->key, $s->value) }}</textarea>
                    @elseif($s->type === 'select')
                        <label class="block text-xs font-semibold text-gray-600 mb-1">{{ $s->label }}</label>
                        @if($s->description)<p class="text-gray-400 text-xs mb-1">{{ $s->description }}</p>@endif
                        @php $opts = $selects[$s->key] ?? ($selects['gateway'][$group] ?? []); @endphp
                        <select name="{{ $s->key }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                            @foreach($opts as $val => $label)
                            <option value="{{ $val }}" {{ old($s->key,$s->value) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    @elseif($s->type === 'password')
                        <label class="block text-xs font-semibold text-gray-600 mb-1">{{ $s->label }} @if($s->is_secret)<i class="fas fa-lock text-gray-400 ml-1"></i>@endif</label>
                        @if($s->description)<p class="text-gray-400 text-xs mb-1">{{ $s->description }}</p>@endif
                        <input type="password" name="{{ $s->key }}" placeholder="{{ $s->value ? '●●●●●●●● (saved)' : 'Enter ' . $s->label }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none">
                        <p class="text-gray-400 text-xs mt-1"><i class="fas fa-info-circle"></i> Leave blank to keep existing value</p>
                    @else
                        <label class="block text-xs font-semibold text-gray-600 mb-1">{{ $s->label }}</label>
                        @if($s->description)<p class="text-gray-400 text-xs mb-1">{{ $s->description }}</p>@endif
                        <input type="text" name="{{ $s->key }}" value="{{ old($s->key, $s->value) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    @endif
                </div>
                @if(!$loop->last && $settings[$loop->index]->sort_order !== $settings[$loop->index+1]->sort_order ?? false)
                <hr class="border-gray-100">
                @endif
                @endforeach
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                <a href="{{ route('admin.settings.index') }}" class="text-gray-500 hover:text-gray-700 text-sm"><i class="fas fa-arrow-left mr-1"></i>Back</a>
                <button type="submit" class="text-white px-6 py-2.5 rounded-xl font-semibold text-sm" style="background:linear-gradient(90deg,#f97316,#ea580c)">
                    <i class="fas fa-save mr-1"></i>Save {{ ucfirst($group) }} Settings
                </button>
            </div>
        </div>
    </form>

    @if($group === 'mail')
    <div class="mt-5 bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h3 class="text-gray-800 font-bold text-sm mb-3"><i class="fas fa-paper-plane text-orange-500 mr-2"></i>Send Test Email</h3>
        <form action="{{ route('admin.settings.test_mail') }}" method="POST" class="flex space-x-3">
            @csrf
            <input type="email" name="to" placeholder="test@example.com" class="flex-1 border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
            <button type="submit" class="bg-purple-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-purple-700"><i class="fas fa-send mr-1"></i>Send Test</button>
        </form>
    </div>
    @endif
</div>
@endsection
