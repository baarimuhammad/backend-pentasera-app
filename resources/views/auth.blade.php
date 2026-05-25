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

            <!-- Verification / Info Message Area -->
            <div id="verify-area" class="hidden mb-6 p-5 bg-amber-50 border border-amber-200 rounded-2xl text-center">
                <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-3 text-amber-600">
                    <i data-lucide="mail" class="w-6 h-6"></i>
                </div>
                <h3 class="font-bold text-ink mb-2" id="verify-heading">Verifikasi Diperlukan</h3>
                <p class="text-xs text-gray-600 mb-4" id="verify-message-text">Cek email kamu untuk verifikasi akun sebelum login.</p>
                
                <button type="button" onclick="handleResendVerification()" id="btn-resend-verification" class="w-full py-3 bg-rust text-white rounded-xl font-bold shadow-md hover:bg-rust-deep transition-all text-xs flex items-center justify-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i> Kirim Ulang Email Verifikasi
                </button>
                
                <button type="button" onclick="resetAuthView()" class="mt-3 text-xs text-gray-400 hover:text-gray-600 font-medium">
                    Kembali ke Form Login
                </button>
            </div>

            <div id="auth-form-container">
                <div class="flex bg-gray-100 p-1 rounded-xl mb-8 gap-1">
                    <button onclick="toggleAuth('login')" id="btn-login-tab" class="flex-1 py-2.5 rounded-lg text-sm font-bold transition-all bg-white text-ink shadow-sm">Login</button>
                    <button onclick="toggleAuth('signup')" id="btn-signup-tab" class="flex-1 py-2.5 rounded-lg text-sm font-bold transition-all text-gray-400 hover:text-gray-600">Sign Up</button>
                </div>

                <!-- Error Display -->
                <div id="auth-error-box" class="hidden p-4 mb-4 bg-red-50 border border-red-200 text-red-600 rounded-xl text-xs font-medium"></div>
                <div id="auth-success-box" class="hidden p-4 mb-4 bg-green-50 border border-green-200 text-green-600 rounded-xl text-xs font-medium"></div>

                <form onsubmit="handleSubmit(event)" class="space-y-5">
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
                                <input type="tel" id="reg-phone" placeholder="+62 8xx xxxx xxxx" class="w-full pl-10 pr-4 py-3 border-2 border-gray-100 rounded-xl focus:border-gold outline-none transition-all text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-ink uppercase tracking-wider">Password</label>
                        <div class="relative">
                            <i data-lucide="lock" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                            <input type="password" id="auth-password" placeholder="••••••••" required class="w-full pl-10 pr-12 py-3 border-2 border-gray-100 rounded-xl focus:border-gold outline-none transition-all text-sm">
                            <button type="button" onclick="togglePasswordVisibility('auth-password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gold"><i data-lucide="eye" class="w-4 h-4"></i></button>
                        </div>
                    </div>

                    <!-- Confirm Password (signup only) -->
                    <div id="signup-confirm-password" class="hidden space-y-1.5">
                        <label class="text-xs font-bold text-ink uppercase tracking-wider">Confirm Password</label>
                        <div class="relative">
                            <i data-lucide="lock" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                            <input type="password" id="auth-password-confirm" placeholder="••••••••" class="w-full pl-10 pr-12 py-3 border-2 border-gray-100 rounded-xl focus:border-gold outline-none transition-all text-sm">
                            <button type="button" onclick="togglePasswordVisibility('auth-password-confirm')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gold"><i data-lucide="eye" class="w-4 h-4"></i></button>
                        </div>
                    </div>

                    <div id="login-options" class="flex items-center justify-between text-xs">
                        <label class="flex items-center gap-2 cursor-pointer text-gray-500">
                            <input type="checkbox" class="w-4 h-4 accent-gold"> Ingat saya
                        </label>
                        <a href="{{ url('/reset-password') }}" class="font-bold text-gold hover:text-rust transition-colors">Lupa kata sandi?</a>
                    </div>

                    <button type="submit" id="btn-submit" class="w-full py-4 bg-rust text-white rounded-xl font-bold shadow-lg shadow-rust/20 hover:bg-rust-deep transform hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
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
    </div>
@endsection


@push('scripts')
<script>
    let activeTab = 'login';

    function toggleAuth(tab) {
        activeTab = tab;
        const isSignup = tab === 'signup';
        document.getElementById('auth-title').innerText = isSignup ? 'Buat Akun Baru' : 'Welcome Back!';
        document.getElementById('auth-subtitle').innerText = isSignup ? 'Daftar dan mulai nikmati pertunjukan budaya!' : 'Your Gateway to Simple Fun starts here.';
        document.getElementById('auth-btn-text').innerText = isSignup ? 'Buat Akun' : 'Masuk Sekarang';
        
        document.getElementById('signup-fields').classList.toggle('hidden', !isSignup);
        document.getElementById('signup-fields-2').classList.toggle('hidden', !isSignup);
        document.getElementById('signup-confirm-password').classList.toggle('hidden', !isSignup);
        document.getElementById('login-options').classList.toggle('hidden', isSignup);
        
        document.getElementById('btn-login-tab').className = `flex-1 py-2.5 rounded-lg text-sm font-bold transition-all ${!isSignup ? 'bg-white text-ink shadow-sm' : 'text-gray-400 hover:text-gray-600'}`;
        document.getElementById('btn-signup-tab').className = `flex-1 py-2.5 rounded-lg text-sm font-bold transition-all ${isSignup ? 'bg-white text-ink shadow-sm' : 'text-gray-400 hover:text-gray-600'}`;
        
        // Clear boxes
        document.getElementById('auth-error-box').classList.add('hidden');
        document.getElementById('auth-success-box').classList.add('hidden');

        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function togglePasswordVisibility(fieldId) {
        const field = document.getElementById(fieldId);
        if (field) {
            field.type = field.type === 'password' ? 'text' : 'password';
        }
    }

    function resetAuthView() {
        document.getElementById('verify-area').classList.add('hidden');
        document.getElementById('auth-form-container').classList.remove('hidden');
        toggleAuth('login');
    }

    function showVerificationUI(titleMsg, bodyMsg) {
        document.getElementById('verify-heading').textContent = titleMsg;
        document.getElementById('verify-message-text').textContent = bodyMsg;
        document.getElementById('verify-area').classList.remove('hidden');
        document.getElementById('auth-form-container').classList.add('hidden');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    async function handleRegister(nama, email, password, passwordConfirm) {
        const errBox = document.getElementById('auth-error-box');
        const successBox = document.getElementById('auth-success-box');
        errBox.classList.add('hidden');
        successBox.classList.add('hidden');

        const res = await fetch('/api/register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ 
                nama, email, password, 
                password_confirmation: passwordConfirm, 
                role: 'buyer'  // default buyer untuk register publik
            })
        });
        const data = await res.json();

        if (data.requires_verification) {
            sessionStorage.setItem('pending_verify_email', email);
            if (data.email_delivery_failed) {
                showVerificationUI(
                    'Email Gagal Terkirim',
                    'Akun Anda terdaftar, namun email verifikasi gagal terkirim. Klik tombol di bawah untuk kirim ulang.'
                );
            } else {
                showVerificationUI(
                    'Registrasi Berhasil',
                    'Cek email kamu untuk verifikasi akun sebelum login.'
                );
            }
            return;
        }

        if (res.ok) {
            if (data.token) {
                localStorage.setItem('auth_token', data.token);
                localStorage.setItem('user', JSON.stringify(data.user));
                window.location.href = data.user.role === 'creator' ? '/my-events' : '/';
            } else if (data.data && data.data.token) {
                localStorage.setItem('auth_token', data.data.token);
                localStorage.setItem('user', JSON.stringify(data.data.user));
                window.location.href = data.data.user.role === 'creator' ? '/my-events' : '/';
            } else {
                successBox.textContent = data.message || 'Registrasi berhasil! Silakan login.';
                successBox.classList.remove('hidden');
                toggleAuth('login');
            }
        } else {
            errBox.textContent = data.message || 'Pendaftaran gagal. Silakan periksa kembali data Anda.';
            errBox.classList.remove('hidden');
        }
    }

    async function handleLogin(email, password) {
        const errBox = document.getElementById('auth-error-box');
        errBox.classList.add('hidden');

        const res = await fetch('/api/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ email, password })
        });
        const data = await res.json();

        if (res.status === 403 && data.requires_verification) {
            sessionStorage.setItem('pending_verify_email', email);
            showVerificationUI(
                'Email Belum Diverifikasi',
                'Email belum diverifikasi. Cek inbox atau klik tombol di bawah untuk mengirim ulang.'
            );
            return;
        }

        if (res.ok) {
            // Check nesting for data token (some api formats wrap inside data)
            const token = data.token || (data.data && data.data.token);
            const user = data.user || (data.data && data.data.user);

            localStorage.setItem('auth_token', token);
            localStorage.setItem('user', JSON.stringify(user));
            const role = user.role;
            window.location.href = role === 'creator' ? '/my-events' : '/';
        } else {
            errBox.textContent = data.message || 'Email atau password salah.';
            errBox.classList.remove('hidden');
        }
    }

    async function handleResendVerification() {
        const email = sessionStorage.getItem('pending_verify_email');
        if (!email) {
            alert('Email tidak ditemukan di session storage.');
            return;
        }

        const btn = document.getElementById('btn-resend-verification');
        const oldContent = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i data-lucide="loader" class="animate-spin w-4 h-4"></i> Mengirim...';
        if (typeof lucide !== 'undefined') lucide.createIcons();

        try {
            const res = await fetch('/api/email/resend-verification', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email })
            });
            const data = await res.json();
            alert(data.message || 'Email verifikasi berhasil dikirim ulang!');
        } catch (e) {
            alert('Gagal mengirim ulang verifikasi.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = oldContent;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

    async function handleSubmit(e) {
        e.preventDefault();
        const btnSubmit = document.getElementById('btn-submit');
        const oldHTML = btnSubmit.innerHTML;
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = 'Memproses...';

        const email = document.getElementById('auth-email').value;
        const password = document.getElementById('auth-password').value;

        try {
            if (activeTab === 'signup') {
                const nama = document.getElementById('reg-name').value;
                const passwordConfirm = document.getElementById('auth-password-confirm').value;
                await handleRegister(nama, email, password, passwordConfirm);
            } else {
                await handleLogin(email, password);
            }
        } catch (err) {
            console.error(err);
            const errBox = document.getElementById('auth-error-box');
            errBox.textContent = 'Terjadi kesalahan sistem. Silakan coba beberapa saat lagi.';
            errBox.classList.remove('hidden');
        } finally {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = oldHTML;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

    function handleSocialLogin() {
        alert('Fitur sosial login belum tersedia.');
    }

    // Check URL params on load
    window.onload = () => {
        const params = new URLSearchParams(window.location.search);
        if (params.get('tab') === 'signup') toggleAuth('signup');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    };
</script>
@endpush
