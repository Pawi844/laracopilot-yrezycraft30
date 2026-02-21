<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MtaaKonnect ISP - Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="min-h-screen bg-slate-900 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-sky-500 rounded-2xl mb-4">
                <i class="fas fa-wifi text-white text-2xl"></i>
            </div>
            <h1 class="text-2xl font-black text-white">MtaaKonnect ISP</h1>
            <p class="text-slate-400 text-sm">Management System v2.0</p>
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 mb-5">
            <p class="font-bold text-sky-400 text-sm mb-2"><i class="fas fa-key mr-2"></i>Demo Credentials</p>
            <div class="space-y-1 text-xs text-slate-300">
                <div class="flex justify-between"><span>Super Admin:</span><code class="text-yellow-400">admin@mtaakonnect.co.ke / admin123</code></div>
                <div class="flex justify-between"><span>Manager:</span><code class="text-yellow-400">manager@mtaakonnect.co.ke / manager123</code></div>
                <div class="flex justify-between"><span>Support:</span><code class="text-yellow-400">support@mtaakonnect.co.ke / support123</code></div>
            </div>
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-xl p-8">
            @if($errors->any())
                <div class="mb-4 bg-red-900/50 border border-red-600 text-red-300 px-4 py-3 rounded-lg text-sm">{{ $errors->first() }}</div>
            @endif
            <form action="/admin/login" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-1.5">Email Address</label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-slate-500"><i class="fas fa-envelope text-sm"></i></span>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="admin@mtaakonnect.co.ke" class="w-full pl-10 pr-4 py-2.5 bg-slate-900 border border-slate-600 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-slate-500"><i class="fas fa-lock text-sm"></i></span>
                        <input type="password" name="password" required placeholder="••••••••" class="w-full pl-10 pr-4 py-2.5 bg-slate-900 border border-slate-600 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 text-sm">
                    </div>
                </div>
                <button type="submit" class="w-full bg-sky-600 text-white py-2.5 rounded-lg font-semibold hover:bg-sky-700 transition-all text-sm">
                    <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                </button>
            </form>
        </div>

        <p class="text-center text-slate-500 text-xs mt-6">
            <a href="{{ route('home') }}" class="hover:text-sky-400"><i class="fas fa-arrow-left mr-1"></i>Back to Website</a>
        </p>
    </div>
</body>
</html>
