@extends('layouts.admin')
@section('title','System Settings')
@section('page-title','System Settings')
@section('page-subtitle','Configure gateways, notifications, billing and system preferences')
@section('content')
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @foreach([
        ['general',    'Company & System',     'fa-building',      '#2563eb', 'Company name, timezone, grace period, currency'],
        ['mpesa',      'M-Pesa Gateway',        'fa-mobile-alt',    '#16a34a', 'Paybill/Till, Daraja API keys, callback URL'],
        ['sms',        'SMS Gateway',           'fa-sms',           '#0284c7', 'Africa\'s Talking, Twilio, Vonage, custom API'],
        ['whatsapp',   'WhatsApp Gateway',      'fa-whatsapp',      '#16a34a', '360dialog, Twilio, UltraMsg configuration'],
        ['mail',       'Email / SMTP',          'fa-envelope',      '#7c3aed', 'SMTP, Mailgun, SES configuration + test'],
        ['billing',    'Billing & Auto-Actions','fa-file-invoice',  '#ea580c', 'Auto-suspend, auto-reconnect, tax settings'],
    ] as [$group, $title, $icon, $color, $desc])
    <a href="{{ route('admin.settings.group', $group) }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-orange-200 transition-all group">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-3" style="background:{{ $color }}15">
            @if($icon === 'fa-whatsapp')
                <i class="fab fa-whatsapp text-2xl" style="color:{{ $color }}"></i>
            @else
                <i class="fas {{ $icon }} text-xl" style="color:{{ $color }}"></i>
            @endif
        </div>
        <h3 class="text-gray-800 font-bold text-sm group-hover:text-orange-600 transition-colors">{{ $title }}</h3>
        <p class="text-gray-400 text-xs mt-1 leading-relaxed">{{ $desc }}</p>
        <div class="mt-3 flex items-center text-orange-500 text-xs font-semibold">
            <span>Configure</span><i class="fas fa-arrow-right ml-1 text-xs group-hover:translate-x-1 transition-transform"></i>
        </div>
    </a>
    @endforeach

    <!-- Notification Templates Card -->
    <a href="{{ route('admin.settings.templates') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-orange-200 transition-all group">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-3" style="background:#f9731615">
            <i class="fas fa-file-alt text-xl text-orange-500"></i>
        </div>
        <h3 class="text-gray-800 font-bold text-sm group-hover:text-orange-600 transition-colors">Notification Templates</h3>
        <p class="text-gray-400 text-xs mt-1 leading-relaxed">SMS, Email & WhatsApp message templates for all events</p>
        <div class="mt-3 flex items-center text-orange-500 text-xs font-semibold">
            <span>Manage Templates</span><i class="fas fa-arrow-right ml-1 text-xs"></i>
        </div>
    </a>
</div>
@endsection
