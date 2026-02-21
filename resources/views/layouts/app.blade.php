<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MtaaKonnect - Your Digital Connectivity Partner')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .gradient-bg { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 50%, #075985 100%); }
        .nav-link { transition: all 0.3s ease; }
        .hero-gradient { background: linear-gradient(135deg, #0c4a6e 0%, #0369a1 40%, #0ea5e9 100%); }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-sky-500 to-blue-700 rounded-lg flex items-center justify-center">
                            <i class="fas fa-wifi text-white text-lg"></i>
                        </div>
                        <div>
                            <span class="text-2xl font-black text-sky-600">MtaaKonnect</span>
                            <span class="block text-xs text-gray-500 -mt-1">Kenya</span>
                        </div>
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="nav-link text-gray-700 hover:text-sky-600 font-medium {{ request()->routeIs('home') ? 'text-sky-600 border-b-2 border-sky-600' : '' }}">Home</a>
                    <a href="{{ route('services') }}" class="nav-link text-gray-700 hover:text-sky-600 font-medium {{ request()->routeIs('services') ? 'text-sky-600 border-b-2 border-sky-600' : '' }}">Services</a>
                    <a href="{{ route('plans') }}" class="nav-link text-gray-700 hover:text-sky-600 font-medium {{ request()->routeIs('plans') ? 'text-sky-600 border-b-2 border-sky-600' : '' }}">Plans & Pricing</a>
                    <a href="{{ route('coverage') }}" class="nav-link text-gray-700 hover:text-sky-600 font-medium">Coverage</a>
                    <a href="{{ route('about') }}" class="nav-link text-gray-700 hover:text-sky-600 font-medium">About</a>
                    <a href="{{ route('contact') }}" class="nav-link text-gray-700 hover:text-sky-600 font-medium">Contact</a>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="tel:+254700000000" class="hidden md:flex items-center space-x-2 text-sky-600 font-semibold">
                        <i class="fas fa-phone"></i>
                        <span>+254 700 000 000</span>
                    </a>
                    <a href="{{ route('plans') }}" class="bg-sky-600 text-white px-5 py-2 rounded-full hover:bg-sky-700 transition-all duration-300 font-semibold shadow-md hover:shadow-lg">Get Connected</a>
                </div>
                <button id="mobile-menu-btn" class="md:hidden text-gray-700">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-200 py-4 px-4">
            <div class="flex flex-col space-y-3">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-sky-600 font-medium py-2">Home</a>
                <a href="{{ route('services') }}" class="text-gray-700 hover:text-sky-600 font-medium py-2">Services</a>
                <a href="{{ route('plans') }}" class="text-gray-700 hover:text-sky-600 font-medium py-2">Plans & Pricing</a>
                <a href="{{ route('coverage') }}" class="text-gray-700 hover:text-sky-600 font-medium py-2">Coverage</a>
                <a href="{{ route('about') }}" class="text-gray-700 hover:text-sky-600 font-medium py-2">About</a>
                <a href="{{ route('contact') }}" class="text-gray-700 hover:text-sky-600 font-medium py-2">Contact</a>
            </div>
        </div>
    </nav>

    @yield('content')

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 py-16 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-gradient-to-br from-sky-500 to-blue-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-wifi text-white"></i>
                    </div>
                    <span class="text-2xl font-black text-white">MtaaKonnect</span>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed">Kenya's most reliable internet and mobile connectivity provider. Connecting communities across the nation since 2015.</p>
                <div class="flex space-x-4 mt-4">
                    <a href="#" class="text-gray-400 hover:text-sky-400 transition-colors"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-gray-400 hover:text-sky-400 transition-colors"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-gray-400 hover:text-sky-400 transition-colors"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-gray-400 hover:text-sky-400 transition-colors"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div>
                <h3 class="text-white font-bold text-lg mb-4">Our Services</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('services') }}" class="text-gray-400 hover:text-sky-400 transition-colors">Home Fiber Internet</a></li>
                    <li><a href="{{ route('services') }}" class="text-gray-400 hover:text-sky-400 transition-colors">Business Internet</a></li>
                    <li><a href="{{ route('services') }}" class="text-gray-400 hover:text-sky-400 transition-colors">4G/5G Mobile Data</a></li>
                    <li><a href="{{ route('services') }}" class="text-gray-400 hover:text-sky-400 transition-colors">TV Streaming</a></li>
                    <li><a href="{{ route('services') }}" class="text-gray-400 hover:text-sky-400 transition-colors">VoIP Solutions</a></li>
                    <li><a href="{{ route('services') }}" class="text-gray-400 hover:text-sky-400 transition-colors">Cloud & Hosting</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-white font-bold text-lg mb-4">Quick Links</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('plans') }}" class="text-gray-400 hover:text-sky-400 transition-colors">Plans & Pricing</a></li>
                    <li><a href="{{ route('coverage') }}" class="text-gray-400 hover:text-sky-400 transition-colors">Coverage Map</a></li>
                    <li><a href="{{ route('about') }}" class="text-gray-400 hover:text-sky-400 transition-colors">About Us</a></li>
                    <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-sky-400 transition-colors">Contact Us</a></li>
                    <li><a href="{{ route('admin.login') }}" class="text-gray-400 hover:text-sky-400 transition-colors">Admin Portal</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-white font-bold text-lg mb-4">Contact Info</h3>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li class="flex items-start space-x-3"><i class="fas fa-map-marker-alt text-sky-400 mt-1"></i><span>Westlands Business Park,<br>Nairobi, Kenya</span></li>
                    <li class="flex items-center space-x-3"><i class="fas fa-phone text-sky-400"></i><span>+254 700 000 000</span></li>
                    <li class="flex items-center space-x-3"><i class="fas fa-envelope text-sky-400"></i><span>info@mtaakonnect.co.ke</span></li>
                    <li class="flex items-center space-x-3"><i class="fas fa-clock text-sky-400"></i><span>24/7 Customer Support</span></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 py-6 text-center text-sm text-gray-500">
            <p>© {{ date('Y') }} MtaaKonnect Kenya. All rights reserved. | Licensed by Communications Authority of Kenya</p>
            <p class="mt-2">Made with ❤️ by <a href="https://laracopilot.com/" target="_blank" class="text-sky-400 hover:underline">LaraCopilot</a></p>
        </div>
    </footer>

    <script>
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
</body>
</html>
