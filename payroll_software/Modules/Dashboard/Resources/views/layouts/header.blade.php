<header class="fixed top-0 w-full z-50 bg-[#2D4762] shadow-xl">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-center justify-between h-20">

            <!-- Logo + Title -->
            <a href="/" class="flex items-center gap-4">
                @if(isset($getBanner->first()->company_logo))
                    <img
                        {{-- src="{{ asset($getBanner->first()->company_logo) ?? 'images/DJI_0204.webp' }}" --}}
                        src="images/logo_new.png"

                        alt="Asloob Bedaa"
                        class="h-12 w-auto"
                    >
                @endif

                <h2 class="text-white text-lg md:text-xl font-semibold leading-tight">
                    <span class="text-[#E67E22]">ABC</span>
                    Simplified Payroll Management
                </h2>
            </a>

            <!-- Desktop Menu -->
            <nav class="hidden md:flex items-center gap-8 text-white">
                <a href="{{ route('dashboard.home') }}" class="hover:text-[#E67E22] transition">
                    Home
                </a>
                {{-- <a href="{{ route('dashboard.live') }}" class="hover:text-[#E67E22] transition">
                    Live Dashboard
                </a> --}}

                {{-- <a href="#contact" class="hover:text-[#E67E22] transition">Contact</a> --}}

                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-[#E67E22]">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-[#E67E22]">
                            Login
                        </a>
                    @endauth
                @endif
            </nav>

            <!-- Mobile Menu Button -->
            <button
                class="md:hidden text-white text-2xl"
                onclick="document.getElementById('mobileMenu').classList.toggle('hidden')"
            >
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden md:hidden bg-[#2D4762] border-t border-white/20">
        <div class="flex flex-col px-6 py-4 space-y-4 text-white">
            <a href="#home" class="hover:text-[#E67E22]">Home</a>
            <a href="#team" class="hover:text-[#E67E22]">Team</a>
            <a href="#contact" class="hover:text-[#E67E22]">Contact</a>

            @if (Route::has('login'))
                @auth
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                @else
                    <a href="{{ route('login') }}">Login</a>
                @endauth
            @endif
        </div>
    </div>
</header>
