@extends('layouts.admin')
@section('title','Notification Templates')
@section('page-title','Notification Templates')
@section('page-subtitle','SMS, Email & WhatsApp message templates for automated notifications')
@section('content')
<div class="flex justify-between items-center mb-4">
    <p class="text-gray-500 text-sm">{{ $templates->count() }} templates configured</p>
    <a href="{{ route('admin.settings.templates.create') }}" class="text-white px-4 py-2 rounded-xl text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)">
        <i class="fas fa-plus mr-1"></i>New Template
    </a>
</div>
@foreach($events as $eventKey => $eventLabel)
@php $eventTemplates = $templates->where('event',$eventKey); @endphp
@if($eventTemplates->count())
<div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-4 overflow-hidden">
    <div class="px-4 py-3" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
        <h3 class="text-white font-bold text-sm">{{ $eventLabel }}</h3>
        <p class="text-blue-200 text-xs">event: <code>{{ $eventKey }}</code></p>
    </div>
    <div class="divide-y divide-gray-50">
        @foreach($eventTemplates as $tpl)
        <div class="p-4 hover:bg-gray-50">
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-3">
                    @php
                    $chColors = ['sms'=>['bg-green-100','text-green-700','fa-sms'],'email'=>['bg-blue-100','text-blue-700','fa-envelope'],'whatsapp'=>['bg-green-100','text-green-800','fa-comment']];
                    $cc = $chColors[$tpl->channel] ?? ['bg-gray-100','text-gray-600','fa-bell'];
                    @endphp
                    <span class="inline-flex items-center space-x-1.5 {{ $cc[0] }} {{ $cc[1] }} text-xs px-2.5 py-1 rounded-full font-bold">
                        <i class="fas {{ $cc[2] }}"></i><span>{{ strtoupper($tpl->channel) }}</span>
                    </span>
                    @if($tpl->subject)
                    <p class="text-gray-700 font-semibold text-sm">{{ $tpl->subject }}</p>
                    @endif
                    @if(!$tpl->active)<span class="bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full">Disabled</span>@endif
                </div>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('admin.settings.templates.edit', $tpl->id) }}" class="text-blue-500 hover:text-blue-700 text-xs font-semibold"><i class="fas fa-edit mr-1"></i>Edit</a>
                    <form action="{{ route('admin.settings.templates.destroy', $tpl->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Delete template?')" class="text-red-400 hover:text-red-600 text-xs"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
            <div class="mt-2 bg-gray-50 rounded-lg px-3 py-2.5">
                <pre class="text-gray-600 text-xs whitespace-pre-wrap font-sans leading-relaxed">{{ Str::limit($tpl->body, 200) }}</pre>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endforeach

<!-- Available Variables Reference -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
    <h3 class="text-gray-800 font-bold text-sm mb-3"><i class="fas fa-code text-orange-500 mr-2"></i>Available Template Variables</h3>
    <div class="grid grid-cols-3 md:grid-cols-5 gap-2">
        @foreach(['{name}','{username}','{password}','{plan}','{expiry}','{days_left}','{amount}','{reference}','{company}','{support_phone}','{paybill_no}','{paybill_type}','{ip}','{email}','{phone}'] as $var)
        <span class="bg-blue-50 text-blue-700 font-mono text-xs px-2 py-1.5 rounded-lg text-center">{{ $var }}</span>
        @endforeach
    </div>
</div>
@endsection
