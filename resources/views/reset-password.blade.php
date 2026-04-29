@extends('layouts.app')
@section('title', 'Reset Password | Pentasara')

@section('content')
<main class="flex-1 flex items-center justify-center p-4 relative overflow-hidden min-h-[80vh]">
    <div class="bg-glow bg-glow-left"></div>
    <div class="bg-glow bg-glow-right"></div>

    <div class="relative z-10 bg-white rounded-3xl shadow-2xl p-8 md:p-10 w-full max-w-[460px] border border-gold/10 animate-fade-in">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gold/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <i data-lucide="key-round" class="w-8 h-8 text-gold"></i>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-ink mb-2">Reset Kata Sandi</h1>
            <p class="text-sm text-gray-500">Masukkan email kamu dan kami akan mengirimkan instruksi untuk mengatur ulang kata sandi.</p>
        </div>

        <form onsubmit="handleReset(event)" class="space-y-6">
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-ink uppercase tracking-wider">Alamat Email</label>
                <div class="relative">
                    <i data-lucide="mail" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                    <input type="email" id="reset-email" placeholder="you@example.com" required class="w-full pl-10 pr-4 py-3 border-2 border-gray-100 rounded-xl focus:border-gold outline-none transition-all text-sm">
                </div>
            </div>

            <div id="success-msg" class="hidden p-4 bg-green-50 border border-green-100 rounded-xl">
                <div class="flex gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-500 shrink-0"></i>
                    <p class="text-xs text-green-700 leading-relaxed">
                        Instruksi reset kata sandi telah dikirim ke email kamu. Silakan periksa kotak masuk atau folder spam.
                    </p>
                </div>
            </div>

            <button type="submit" id="submit-btn" class="w-full py-4 bg-rust text-white rounded-xl font-bold shadow-lg shadow-rust/20 hover:bg-rust-deep transform hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                Kirim Instruksi <i data-lucide="send" class="w-4 h-4"></i>
            </button>

            <div class="text-center pt-2">
                <a href="{{ url('/auth') }}" class="text-sm font-bold text-gold hover:text-rust transition-colors flex items-center justify-center gap-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Login
                </a>
            </div>
        </form>
    </div>
</main>
@endsection

@push('scripts')
<script>
    function handleReset(e) {
        e.preventDefault();
        const btn = document.getElementById('submit-btn');
        const msg = document.getElementById('success-msg');
        const email = document.getElementById('reset-email');

        btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Memproses...';
        btn.disabled = true;
        btn.classList.add('opacity-70', 'cursor-not-allowed');

        setTimeout(() => {
            msg.classList.remove('hidden');
            btn.innerHTML = 'Instruksi Terkirim!';
            email.disabled = true;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }, 1500);
    }
</script>
@endpush
