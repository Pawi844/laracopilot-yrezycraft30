<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $settings['title'] }} — Connect to WiFi</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { font-family:'Inter',system-ui,sans-serif; min-height:100vh; display:flex; align-items:center; justify-content:center; }
.card { animation: slideUp .4s ease; }
@keyframes slideUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
</style>
</head>
<body style="background:{{ $settings['bg'] }};">
<div class="w-full min-h-screen flex flex-col items-center justify-center px-4 py-8" style="background:linear-gradient(135deg,{{ $settings['bg'] }}dd,{{ $settings['bg'] }}aa);">

    <!-- Floating dots decoration -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-10 left-10 w-32 h-32 bg-white/5 rounded-full blur-xl"></div>
        <div class="absolute bottom-20 right-10 w-48 h-48 bg-white/5 rounded-full blur-xl"></div>
        <div class="absolute top-1/2 left-1/4 w-24 h-24 bg-white/5 rounded-full blur-xl"></div>
    </div>

    <div class="card bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden relative z-10">
        <!-- Header -->
        <div class="py-8 px-6 text-center" style="background:{{ $settings['bg'] }};">
            @if($settings['logo'])
            <img src="{{ $settings['logo'] }}" alt="Logo" class="h-12 mx-auto mb-3 object-contain">
            @else
            <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-wifi text-white text-3xl"></i>
            </div>
            @endif
            <h1 class="text-white font-black text-xl">{{ $settings['title'] }}</h1>
            <p class="text-white/70 text-sm mt-1">{{ $settings['message'] }}</p>
        </div>

        <!-- Login Form -->
        <div class="p-6">
            @if(isset($error))
            <div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-4 text-center">
                <p class="text-red-600 text-sm font-semibold">{{ $error }}</p>
            </div>
            @endif
            <!-- MikroTik Hotspot login form — action must point to /login on the hotspot -->
            <form name="sendin" action="$(link-login-only)" method="post" class="space-y-3">
                <input type="hidden" name="dst" value="$(link-orig)">
                <input type="hidden" name="popup" value="true">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Username / Phone Number</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="username" class="w-full border border-gray-200 rounded-xl pl-9 pr-3 py-3 text-sm focus:ring-2 focus:outline-none" style="--tw-ring-color:{{ $settings['bg'] }}" placeholder="Enter username" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Password / Voucher Code</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="password" name="password" class="w-full border border-gray-200 rounded-xl pl-9 pr-3 py-3 text-sm focus:ring-2 focus:outline-none" placeholder="Enter password" required>
                    </div>
                </div>
                <button type="submit" class="w-full py-3 text-white rounded-xl font-bold text-sm transition-all hover:opacity-90 active:scale-95" style="background:{{ $settings['bg'] }}">
                    <i class="fas fa-sign-in-alt mr-2"></i>Connect to Internet
                </button>
            </form>

            <!-- Session info / status if logged in -->
            <div class="mt-4 pt-4 border-t border-gray-100 text-center">
                <p class="text-gray-400 text-xs">By connecting you agree to our <a href="#" class="underline">Terms of Use</a></p>
            </div>

            <!-- Disconnect button (shown when already connected) -->
            <div class="mt-3 text-center hidden" id="disconnect-btn">
                <a href="$(link-logout)" class="text-red-500 hover:text-red-700 text-xs font-semibold">
                    <i class="fas fa-sign-out-alt mr-1"></i>Disconnect / Logout
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-3 border-t border-gray-100 text-center">
            <p class="text-gray-400 text-xs">{{ $settings['company'] }} &nbsp;·&nbsp; <i class="fas fa-shield-alt"></i> Secure WiFi</p>
        </div>
    </div>

    <!-- Status messages from MikroTik variables -->
    @if(isset($mt_error))
    <div class="mt-4 bg-red-500/20 border border-red-400/30 rounded-xl px-4 py-2 text-white text-sm">
        {{ $mt_error }}
    </div>
    @endif
</div>
</body>
</html>
