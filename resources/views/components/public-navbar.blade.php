<nav class="fixed top-0 left-0 right-0 bg-white/95 backdrop-blur-sm border-b border-gray-100 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-12">
            <!-- Logo -->
            <div class="flex items-center gap-2">
                <img src="{{ asset('Image/logo-wa-api-black.png') }}" alt="Orbit API Logo" class="h-12 w-auto">
                <span
                    class="text-lg font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">Orbit
                    WhatsApp API</span>
            </div>

            <!-- Nav Menu -->
            <div class="hidden md:flex items-center gap-6">
                <a href="{{ route('home') }}#features"
                    class="text-xs text-gray-600 hover:text-emerald-600 transition">Features</a>
                <a href="{{ route('home') }}#pricing"
                    class="text-xs text-gray-600 hover:text-emerald-600 transition">Pricing</a>
                <a href="{{ route('docs.index') }}"
                    class="text-xs text-gray-600 hover:text-emerald-600 transition">API Docs</a>
                <a href="{{ route('home') }}#contact"
                    class="text-xs text-gray-600 hover:text-emerald-600 transition">Contact</a>
            </div>

            <!-- Auth Buttons -->
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">Log
                        In</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-xs font-medium text-white px-4 py-1.5 rounded-lg">Get
                            Started Free</a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</nav>
