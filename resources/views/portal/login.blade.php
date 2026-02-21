<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Client Portal — Login</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body style="background:linear-gradient(135deg,#1e3a5f 0%,#0f2744 50%,#1a1a2e 100%);" class="min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-sm">
    <div class="text-center mb-8">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:linear-gradient(135deg,#f97316,#ea580c)">
            <i class="fas fa-wifi text-white text-2xl"></i>
        </div>
        <h1 class="text-white font-black text-2xl">Client Portal</h1>
        <p class="text-blue-300 text-sm mt-1">Sign in to manage your account</p>
    </div>
    <div class="bg-white rounded-2xl shadow-2xl p-6">
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-3 py-2.5 rounded-lg mb-4">
            {{ $errors->first() }}
        </div>
        @endif
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-3 py-2.5 rounded-lg mb-4">
            {{ session('success') }}
        </div>
        @endif
        <form action="{{ route('portal.login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Username or Email</label>
                <div class="relative">
                    <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="username" value="{{ old('username') }}" placeholder="Your username" class="w-full border border-gray-200 rounded-xl pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required autofocus>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Password</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="password" name="password" placeholder="Your password" class="w-full border border-gray-200 rounded-xl pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                </div>
                <p class="text-gray-400 text-xs mt-1">Default password is your username if not set</p>
            </div>
            <button type="submit" class="w-full text-white py-2.5 rounded-xl font-bold text-sm" style="background:linear-gradient(90deg,#f97316,#ea580c)">
                <i class="fas fa-sign-in-alt mr-1"></i>Sign In
            </button>
        </form>
        <div class="mt-4 pt-4 border-t border-gray-100 text-center">
            <p class="text-gray-400 text-xs">Having trouble? Call us:</p>
            <p class="text-blue-700 font-bold text-sm">{{ \App\Models\SystemSetting::get('general','company_phone','+254700000000') }}</p>
        </div>
    </div>
</div>
</body>
</html>
