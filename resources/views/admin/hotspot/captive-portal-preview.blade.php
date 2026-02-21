<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hotspot — {{ \App\Models\SystemSetting::get('general','company_name','WiFi Hotspot') }}</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
body { background: linear-gradient(135deg, #1e3a5f 0%, #0f2744 50%, #1a1a2e 100%); min-height: 100vh; }
.wifi-anim { animation: pulse 2s infinite; }
@keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.7;transform:scale(1.05)} }
</style>
</head>
<body class="flex items-center justify-center p-4">
<div class="w-full max-w-sm">
    <!-- Logo / Branding -->
    <div class="text-center mb-6">
        @if(\App\Models\SystemSetting::get('general','logo_url'))
        <img src="{{ \App\Models\SystemSetting::get('general','logo_url') }}" alt="Logo" class="h-16 mx-auto mb-3">
        @else
        <div class="w-20 h-20 rounded-2xl mx-auto mb-3 flex items-center justify-center wifi-anim" style="background:linear-gradient(135deg,#f97316,#ea580c)">
            <i class="fas fa-wifi text-white text-3xl"></i>
        </div>
        @endif
        <h1 class="text-white text-2xl font-black">{{ \App\Models\SystemSetting::get('general','company_name','WiFi Hotspot') }}</h1>
        <p class="text-blue-300 text-sm">{{ \App\Models\SystemSetting::get('hotspot','portal_tagline','Fast. Reliable. Connected.') }}</p>
    </div>

    <!-- Login Card -->
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4" style="background:linear-gradient(90deg,#f97316,#ea580c)">
            <p class="text-white font-bold text-center">Sign in to get online</p>
        </div>
        <div class="p-6">
            @if(isset($error))
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-2.5 mb-4">
                <i class="fas fa-exclamation-circle mr-1"></i>{{ $error }}
            </div>
            @endif
            <form action="{{ $loginUrl ?? '/hotspot/login' }}" method="POST">
                @csrf
                <input type="hidden" name="dst" value="{{ request('dst','http://www.google.com') }}">
                <input type="hidden" name="popup" value="true">
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Username</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="username" placeholder="Enter your username" class="w-full border border-gray-200 rounded-xl pl-9 pr-4 py-3 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required autocomplete="username">
                    </div>
                </div>
                <div class="mb-5">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="password" name="password" placeholder="Enter your password" class="w-full border border-gray-200 rounded-xl pl-9 pr-4 py-3 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required autocomplete="current-password">
                    </div>
                </div>
                <button type="submit" class="w-full py-3 text-white font-bold rounded-xl text-sm" style="background:linear-gradient(90deg,#f97316,#ea580c)">
                    <i class="fas fa-sign-in-alt mr-2"></i>Connect to Internet
                </button>
            </form>

            <!-- Plans / Buy Voucher -->
            @if(\App\Models\SystemSetting::get('hotspot','show_plans_on_portal','1') === '1')
            <div class="mt-5 pt-4 border-t border-gray-100">
                <p class="text-center text-xs text-gray-500 mb-3">No account? Buy a voucher:</p>
                <div class="grid grid-cols-2 gap-2">
                    @foreach(\App\Models\Plan::where('active',true)->where('type','hotspot')->take(4)->get() as $plan)
                    <div class="border border-orange-200 rounded-xl p-2.5 text-center cursor-pointer hover:bg-orange-50 transition-colors">
                        <p class="font-black text-orange-600 text-sm">KES {{ number_format($plan->price) }}</p>
                        <p class="text-gray-600 text-xs">{{ $plan->name }}</p>
                        <p class="text-gray-400 text-xs">{{ $plan->validity_days }}d · {{ $plan->speed_download }}Mbps</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-3 text-center">
            <p class="text-gray-400 text-xs">Support: {{ \App\Models\SystemSetting::get('general','company_phone','') }}</p>
        </div>
    </div>
    <p class="text-center text-blue-300 text-xs mt-4">© {{ date('Y') }} {{ \App\Models\SystemSetting::get('general','company_name','') }}</p>
</div>
</body>
</html>
