@extends('layouts.admin')
@section('title','Captive Portal')
@section('page-title','Hotspot Captive Portal')
@section('page-subtitle','Customise the WiFi login page shown to users')
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    <!-- Settings Form -->
    <div>
        <form action="{{ route('admin.hotspot.captive.save') }}" method="POST" class="space-y-4">
            @csrf
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-4">
                <h3 class="text-gray-800 font-bold text-sm"><i class="fas fa-paint-brush text-orange-500 mr-2"></i>Portal Appearance</h3>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1">Portal Title</label>
                    <input type="text" name="captive_title" value="{{ $settings['captive_title'] }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="e.g. Free WiFi — Connect">
                </div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1">Welcome Message</label>
                    <textarea name="captive_message" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">{{ $settings['captive_message'] }}</textarea>
                </div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1">Background / Brand Colour</label>
                    <div class="flex space-x-2">
                        <input type="color" name="captive_bg" value="{{ $settings['captive_bg'] ?? '#1e3a5f' }}" class="h-10 w-16 border border-gray-200 rounded-lg cursor-pointer">
                        <input type="text" value="{{ $settings['captive_bg'] ?? '#1e3a5f' }}" class="flex-1 border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none" onchange="this.previousElementSibling.value=this.value" placeholder="#1e3a5f">
                    </div>
                </div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1">Logo URL <span class="font-normal text-gray-400">(optional)</span></label>
                    <input type="url" name="captive_logo" value="{{ $settings['captive_logo'] }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="https://cdn.yourdomain.com/logo.png">
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-4">
                <h3 class="text-gray-800 font-bold text-sm"><i class="fas fa-clock text-blue-500 mr-2"></i>Session Limits</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-semibold text-gray-600 mb-1">Session Timeout (seconds)</label>
                        <input type="number" name="session_timeout" value="{{ $settings['session_timeout'] ?? 86400 }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                        <p class="text-gray-400 text-xs mt-1">86400 = 24 hours</p>
                    </div>
                    <div><label class="block text-xs font-semibold text-gray-600 mb-1">Idle Timeout (seconds)</label>
                        <input type="number" name="idle_timeout" value="{{ $settings['idle_timeout'] ?? 3600 }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                        <p class="text-gray-400 text-xs mt-1">3600 = 1 hour</p>
                    </div>
                </div>
            </div>
            <!-- MikroTik Deployment -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <p class="text-blue-800 font-bold text-sm mb-2"><i class="fas fa-info-circle mr-1"></i>Deploy to MikroTik</p>
                <p class="text-blue-700 text-xs mb-2">To use this captive page on MikroTik Hotspot:</p>
                <ol class="text-blue-700 text-xs space-y-1 list-decimal ml-4">
                    <li>Download or copy the HTML from the preview</li>
                    <li>On MikroTik: <code class="bg-blue-100 rounded px-1">/ip hotspot</code> → Hotspot Server Profile → HTML Directory</li>
                    <li>Upload the file as <code class="bg-blue-100 rounded px-1">login.html</code> to the hotspot HTML directory via FTP/Winbox Files</li>
                    <li>The <code class="bg-blue-100 rounded px-1">$(link-login-only)</code> variables are replaced automatically by MikroTik</li>
                </ol>
                <a href="{{ route('admin.hotspot.captive.preview') }}" target="_blank" class="mt-3 inline-block bg-blue-600 hover:bg-blue-700 text-white text-xs px-4 py-2 rounded-lg font-semibold">
                    <i class="fas fa-external-link-alt mr-1"></i>Preview Captive Page
                </a>
            </div>
            <button type="submit" class="w-full py-3 text-white rounded-xl font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)">
                <i class="fas fa-save mr-1"></i>Save Captive Portal Settings
            </button>
        </form>
    </div>

    <!-- Live Preview -->
    <div class="hidden lg:block">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-gray-600 font-bold text-sm mb-3"><i class="fas fa-eye text-orange-500 mr-2"></i>Live Preview</p>
            <div id="preview-frame" class="rounded-xl overflow-hidden" style="height:520px;">
                <iframe src="{{ route('admin.hotspot.captive.preview') }}" class="w-full h-full border-0" id="preview-iframe"></iframe>
            </div>
            <p class="text-gray-400 text-xs mt-2">Save settings to update preview</p>
        </div>
    </div>
</div>
@endsection
