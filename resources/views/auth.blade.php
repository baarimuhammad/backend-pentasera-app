@extends('layouts.app', ['hideChrome' => true])

@section('title', 'Login / Sign Up | Pentasara')

@section('content')
    <!-- Main Content -->
    <div class="bg-glow bg-glow-left"></div>
    <div class="bg-glow bg-glow-right"></div>

    <div class="flex-1 flex items-center justify-center p-4 relative overflow-hidden" style="min-height: 100vh;">
        <div class="relative z-10 bg-white rounded-3xl shadow-2xl p-8 md:p-10 w-full max-w-[460px] border border-gold/10 animate-fade-in">
            <div class="text-center mb-8">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 mb-4 group">
                    <img src="{{ asset('assets/logo pentasera.png') }}" alt="Pentasara Logo" class="w-8 h-8 object-contain">
                    <span class="logo-text text-sm">PENTASARA</span>
                </a>
                <h1 class="text-2xl md:text-3xl font-bold text-ink mb-2" id="auth-title">Welcome Back!</h1>
                <p class="text-sm text-gray-500" id="auth-subtitle">Your Gateway to Simple Fun starts here.</p>
            </div>

            <div class="flex bg-gray-100 p-1 rounded-xl mb-8 gap-1">
                <button onclick="toggleAuth('login')" id="btn-login-tab" class="flex-1 py-2.5 rounded-lg text-sm font-bold transition-all bg-white text-ink shadow-sm">Login</button>
                <button onclick="toggleAuth('signup')" id="btn-signup-tab" class="flex-1 py-2.5 rounded-lg text-sm font-bold transition-all text-gray-400 hover:text-gray-600">Sign Up</button>
            </div>

            <form onsubmit="handleAuth(event)" class="space-y-5">
                <div id="signup-fields" class="hidden space-y-5">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-ink uppercase tracking-wider">Full Name</label>
                        <input type="text" id="reg-name" placeholder="Nama lengkap kamu" class="w-full px-4 py-3 border-2 border-gray-100 rounded-xl focus:border-gold outline-none transition-all text-sm">
                    </div>
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-ink uppercase tracking-wider">Email Address</label>
                    <div class="relative">
                        <i data-lucide="mail" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <input type="email" id="auth-email" placeholder="you@example.com" required class="w-full pl-10 pr-4 py-3 border-2 border-gray-100 rounded-xl focus:border-gold outline-none transition-all text-sm">
                    </div>
                </div>

                <div id="signup-fields-2" class="hidden space-y-5">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-ink uppercase tracking-wider">Phone Number</label>
                        <div class="relative">
                            <i data-lucide="phone" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                            <input type="tel" placeholder="+62 8xx xxxx xxxx" class="w-full pl-10 pr-4 py-3 border-2 border-gray-100 rounded-xl focus:border-gold outline-none transition-all text-sm">
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-ink uppercase tracking-wider">Password</label>
                    <div class="relative">
                        <i data-lucide="lock" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <input type="password" id="auth-password" placeholder="••••••••" required class="w-full pl-10 pr-12 py-3 border-2 border-gray-100 rounded-xl focus:border-gold outline-none transition-all text-sm">
                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gold"><i data-lucide="eye" class="w-4 h-4"></i></button>
                    </div>
                </div>

                <div id="login-options" class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-gray-500">
                        <input type="checkbox" class="w-4 h-4 accent-gold"> Ingat saya
                    </label>
                    <a href="{{ url('/reset-password') }}" class="font-bold text-gold hover:text-rust transition-colors">Lupa kata sandi?</a>
                </div>

                <button type="submit" class="w-full py-4 bg-rust text-white rounded-xl font-bold shadow-lg shadow-rust/20 hover:bg-rust-deep transform hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2" id="auth-submit-btn">
                    <span id="auth-btn-text">Masuk Sekarang</span> <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>

                <div class="relative py-2">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-100"></div></div>
                    <div class="relative flex justify-center text-xs uppercase"><span class="bg-white px-2 text-gray-400">Atau masuk dengan</span></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <button type="button" onclick="handleSocialLogin()" class="flex items-center justify-center gap-2 py-2.5 border-2 border-gray-100 rounded-xl hover:bg-gray-50 transition-colors font-bold text-sm">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-4 h-4" alt="Google"> Google
                    </button>
                    <button type="button" onclick="handleSocialLogin()" class="flex items-center justify-center gap-2 py-2.5 border-2 border-gray-100 rounded-xl hover:bg-gray-50 transition-colors font-bold text-sm">
                        <i data-lucide="facebook" class="w-4 h-4 text-blue-600 fill-current"></i> Facebook
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection


@push('scripts')
<script>
    function handleAuth(e) {
        e.preventDefault();
        // Direct localStorage auth (UI-only, no backend call)
        localStorage.setItem('isLoggedIn', 'true');
        if (localStorage.getItem('isCreator') === null) {
            localStorage.setItem('isCreator', 'false');
        }
        window.location.href = '/';
    }

    function handleSocialLogin() {
        localStorage.setItem('isLoggedIn', 'true');
        if (localStorage.getItem('isCreator') === null) {
            localStorage.setItem('isCreator', 'false');
        }
        window.location.href = '/';
    }

    function toggleAuth(tab) {
        const isSignup = tab === 'signup';
        document.getElementById('auth-title').innerText = isSignup ? 'Buat Akun Baru' : 'Welcome Back!';
        document.getElementById('auth-subtitle').innerText = isSignup ? 'Daftar dan mulai nikmati pertunjukan budaya!' : 'Your Gateway to Simple Fun starts here.';
        document.getElementById('auth-submit-btn').innerHTML = isSignup ? 'Buat Akun' : 'Masuk Sekarang <i data-lucide="arrow-right" class="w-4 h-4"></i>';
        
        document.getElementById('signup-fields').classList.toggle('hidden', !isSignup);
        document.getElementById('signup-fields-2').classList.toggle('hidden', !isSignup);
        document.getElementById('login-options').classList.toggle('hidden', isSignup);
        
        document.getElementById('btn-login-tab').className = `flex-1 py-2.5 rounded-lg text-sm font-bold transition-all ${!isSignup ? 'bg-white text-ink shadow-sm' : 'text-gray-400 hover:text-gray-600'}`;
        document.getElementById('btn-signup-tab').className = `flex-1 py-2.5 rounded-lg text-sm font-bold transition-all ${isSignup ? 'bg-white text-ink shadow-sm' : 'text-gray-400 hover:text-gray-600'}`;
        
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    // Check URL params on load
    window.onload = () => {
        const params = new URLSearchParams(window.location.search);
        if (params.get('tab') === 'signup') toggleAuth('signup');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    };
</script>
@endpush
