@extends('layouts.app')
@section('title', 'Pembayaran | Pentasara')

@push('styles')
<style>
    body { background: #F9F9F9 !important; padding-bottom: 5rem; }
    .animate-fade-in-up {
        animation: fadeInUp 0.3s ease-out;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

{{-- Hide default footer --}}
@section('custom-footer')
<span class="hidden"></span>
@endsection

@section('content')
{{-- Checkout Progress Header --}}
<div class="bg-white border-b border-gray-100 py-4">
    <div class="max-w-7xl mx-auto px-6 flex items-center justify-center">
        {{-- Progress Steps --}}
        <div class="flex items-center gap-8">
            <div class="flex items-center gap-3 opacity-40">
                <span class="w-7 h-7 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-[10px] font-bold">1</span>
                <span class="text-[11px] font-bold text-gray-400">Data Pemesan</span>
            </div>
            <div class="w-12 h-px bg-gray-200"></div>
            <div class="flex items-center gap-3">
                <span class="w-7 h-7 rounded-full bg-rust text-white flex items-center justify-center text-[10px] font-bold">2</span>
                <span class="text-[11px] font-bold text-rust">Pembayaran</span>
            </div>
            <div class="w-12 h-px bg-gray-200"></div>
            <div class="flex items-center gap-3 opacity-40">
                <span class="w-7 h-7 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-[10px] font-bold">3</span>
                <span class="text-[11px] font-bold text-gray-400">Konfirmasi</span>
            </div>
        </div>
    </div>
</div>

{{-- Progress Bar Section (Only for VA) --}}
<div id="va-progress" class="max-w-3xl mx-auto px-6 mt-10 space-y-4">
    <div class="flex justify-between items-center text-[11px] font-bold">
        <span class="text-[#2C1A0E]">Step 2 of 3: Payment</span>
        <span class="text-[#B84C2B]">66% Complete</span>
    </div>
    <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
        <div class="w-[66%] h-full bg-[#B84C2B]"></div>
    </div>
    <div class="flex items-center gap-2 text-[11px] font-bold text-[#B84C2B]">
        <i data-lucide="clock" class="w-4 h-4"></i>
        Waiting for your payment
    </div>
</div>

{{-- Timer Banner (Only for QRIS) --}}
<div id="qris-banner" class="hidden bg-[#B84C2B] text-white py-3 text-center text-[13px] font-medium mt-6 max-w-5xl mx-auto rounded-xl">
    <i data-lucide="clock" class="w-4 h-4 inline-block mr-2"></i>
    Selesaikan pembayaran dalam <span id="qris-timer-banner" class="font-bold">23:59:54</span> untuk mengamankan tiketmu
</div>

<main class="max-w-5xl mx-auto px-6 py-10">
    {{-- VA Layout --}}
    <div id="va-layout" class="max-w-2xl mx-auto space-y-8">
        {{-- Timer Card --}}
        <div class="bg-white rounded-3xl shadow-[0_2px_15px_rgba(0,0,0,0.03)] border border-gray-100 p-10 text-center">
            <h2 class="font-bold text-lg text-[#2C1A0E] mb-8">Complete Payment Within</h2>
            <div class="flex justify-center items-center gap-4">
                <div class="space-y-2">
                    <div class="w-16 h-16 bg-[#FFF5F2] rounded-2xl flex items-center justify-center text-2xl font-black text-[#B84C2B]" id="h-box">23</div>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">HOURS</p>
                </div>
                <span class="text-2xl font-black text-[#B84C2B] mb-6">:</span>
                <div class="space-y-2">
                    <div class="w-16 h-16 bg-[#FFF5F2] rounded-2xl flex items-center justify-center text-2xl font-black text-[#B84C2B]" id="m-box">59</div>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">MINUTES</p>
                </div>
                <span class="text-2xl font-black text-[#B84C2B] mb-6">:</span>
                <div class="space-y-2">
                    <div class="w-16 h-16 bg-[#FFF5F2] rounded-2xl flex items-center justify-center text-2xl font-black text-[#B84C2B]" id="s-box">54</div>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">SECONDS</p>
                </div>
            </div>
        </div>

        {{-- Payment Detail Card --}}
        <div class="bg-white rounded-3xl shadow-[0_2px_15px_rgba(0,0,0,0.03)] border border-gray-100 p-10">
            <div class="flex justify-between items-start mb-8">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">TOTAL AMOUNT</p>
                    <p id="va-total" class="text-3xl font-black text-[#B84C2B]">Rp 117.500</p>
                </div>
                <div class="w-16 h-12 bg-gray-50 rounded-xl flex items-center justify-center p-2">
                    <img id="va-logo" src="" alt="Bank" class="max-h-full max-w-full object-contain">
                </div>
            </div>

            <div class="bg-[#FFF9F2] border border-[#FFE8CC] rounded-2xl p-8">
                <p id="va-name-label" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">BNI VIRTUAL ACCOUNT</p>
                <div class="flex items-center justify-between">
                    <p id="va-number" class="text-2xl font-black text-[#2C1A0E] tracking-[0.2em]">8277 0812 3456 7890</p>
                    <button onclick="copyVA()" class="flex items-center gap-2 text-[13px] font-bold text-[#B84C2B] hover:underline">
                        <i data-lucide="copy" class="w-4 h-4"></i>
                        Copy
                    </button>
                </div>
            </div>

            <div class="mt-10">
                <h3 class="font-bold text-sm text-[#2C1A0E] mb-6">Payment Instructions</h3>
                <div class="flex border-b border-gray-100 mb-8" id="tab-container">
                    <button onclick="switchTab('mobile')" id="tab-mobile" class="px-6 py-3 text-[13px] font-bold text-[#B84C2B] border-b-2 border-[#B84C2B] transition-all">Mobile Banking</button>
                    <button onclick="switchTab('atm')" id="tab-atm" class="px-6 py-3 text-[13px] font-bold text-gray-400 border-b-2 border-transparent transition-all">ATM</button>
                    <button onclick="switchTab('internet')" id="tab-internet" class="px-6 py-3 text-[13px] font-bold text-gray-400 border-b-2 border-transparent transition-all">Internet</button>
                </div>
                <div id="instructions" class="space-y-5">
                    {{-- Instructions rendered by JS --}}
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <button id="confirm-payment-btn" class="w-full bg-[#B84C2B] text-white py-5 rounded-2xl font-bold hover:bg-rust-deep transition-all shadow-lg shadow-rust/20 flex items-center justify-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                I Have Paid
            </button>
            <button class="w-full bg-white border border-gray-200 text-[#2C1A0E] py-5 rounded-2xl font-bold hover:bg-gray-50 transition-all">
                Check Payment Status
            </button>
        </div>

        <div class="text-center space-y-2 pt-6">
            <p class="text-[11px] text-gray-400 font-medium">Need help with your payment? <a href="{{ url('/hubungi-kami') }}" class="text-[#B84C2B] font-bold">Contact Customer Support</a></p>
        </div>
    </div>

    {{-- QRIS Layout --}}
    <div id="qris-layout" class="hidden flex flex-col lg:flex-row gap-10">
        <div class="flex-1 bg-white rounded-3xl shadow-[0_2px_15px_rgba(0,0,0,0.03)] border border-gray-100 p-12 text-center space-y-10">
            <h2 class="font-bold text-2xl text-[#2C1A0E]">QRIS Payment</h2>

            <div class="flex justify-center items-center gap-4">
                <div class="space-y-1">
                    <div class="w-20 h-16 bg-[#FFF5F2] rounded-2xl flex items-center justify-center text-2xl font-black text-[#B84C2B]" id="q-h">23</div>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">JAM</p>
                </div>
                <div class="space-y-1">
                    <div class="w-20 h-16 bg-[#FFF5F2] rounded-2xl flex items-center justify-center text-2xl font-black text-[#B84C2B]" id="q-m">59</div>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">MENIT</p>
                </div>
                <div class="space-y-1">
                    <div class="w-20 h-16 bg-[#FFF5F2] rounded-2xl flex items-center justify-center text-2xl font-black text-[#B84C2B]" id="q-s">54</div>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">DETIK</p>
                </div>
            </div>

            <div class="max-w-[300px] mx-auto p-4 border border-gray-100 rounded-3xl">
                <img src="https://upload.wikimedia.org/wikipedia/commons/d/d0/QR_code_for_mobile_English_Wikipedia.svg" alt="QRIS" class="w-full h-auto">
            </div>

            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">TOTAL PEMBAYARAN</p>
                <p id="qris-total" class="text-4xl font-black text-[#2C1A0E]">Rp 117.500</p>
            </div>

            <div class="space-y-4">
                <button class="w-full bg-[#B84C2B] text-white py-5 rounded-2xl font-bold hover:bg-rust-deep transition-all shadow-lg shadow-rust/20 flex items-center justify-center gap-3">
                    <i data-lucide="download" class="w-5 h-5"></i>
                    Download QR Code
                </button>
                <button id="qris-confirm-btn" class="w-full bg-white border border-[#B84C2B] text-[#B84C2B] py-5 rounded-2xl font-bold hover:bg-gray-50 transition-all flex items-center justify-center gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    Saya Sudah Bayar
                </button>
            </div>
        </div>

        <aside class="w-full lg:w-[400px] space-y-8">
            <div class="bg-white rounded-3xl shadow-[0_2px_15px_rgba(0,0,0,0.03)] border border-gray-100 p-10">
                <div class="flex items-center gap-3 mb-8">
                    <i data-lucide="list-ordered" class="w-5 h-5 text-[#B84C2B]"></i>
                    <h3 class="font-bold text-lg text-[#2C1A0E]">Cara Pembayaran</h3>
                </div>
                <div class="space-y-8">
                    <div class="flex gap-5">
                        <span class="w-8 h-8 rounded-full bg-[#FFF5F2] text-[#B84C2B] flex items-center justify-center text-sm font-bold shrink-0">1</span>
                        <div class="space-y-1">
                            <p class="text-[13px] font-bold text-[#2C1A0E]">Buka Aplikasi</p>
                            <p class="text-[11px] text-gray-400 leading-relaxed">Buka aplikasi e-wallet (Gopay, OVO, Dana) atau mobile banking pilihan Anda.</p>
                        </div>
                    </div>
                    <div class="flex gap-5">
                        <span class="w-8 h-8 rounded-full bg-[#FFF5F2] text-[#B84C2B] flex items-center justify-center text-sm font-bold shrink-0">2</span>
                        <div class="space-y-1">
                            <p class="text-[13px] font-bold text-[#2C1A0E]">Scan QR</p>
                            <p class="text-[11px] text-gray-400 leading-relaxed">Cari tombol 'Scan' atau 'Bayar' dan arahkan kamera ke kode QR di samping.</p>
                        </div>
                    </div>
                    <div class="flex gap-5">
                        <span class="w-8 h-8 rounded-full bg-[#FFF5F2] text-[#B84C2B] flex items-center justify-center text-sm font-bold shrink-0">3</span>
                        <div class="space-y-1">
                            <p class="text-[13px] font-bold text-[#2C1A0E]">Cek Detail</p>
                            <p class="text-[11px] text-gray-400 leading-relaxed">Pastikan nama merchant adalah <span class="font-bold text-[#2C1A0E]">Pentasara</span> dan total <span id="qris-total-small" class="font-bold text-[#2C1A0E]">Rp117.500</span>.</p>
                        </div>
                    </div>
                    <div class="flex gap-5">
                        <span class="w-8 h-8 rounded-full bg-[#FFF5F2] text-[#B84C2B] flex items-center justify-center text-sm font-bold shrink-0">4</span>
                        <div class="space-y-1">
                            <p class="text-[13px] font-bold text-[#2C1A0E]">Konfirmasi</p>
                            <p class="text-[11px] text-gray-400 leading-relaxed">Masukkan PIN untuk menyelesaikan transaksi pembayaran Anda.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-12 bg-[#F9F9F9] rounded-2xl p-6 flex items-center gap-4">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center border border-gray-100">
                        <i data-lucide="help-circle" class="w-5 h-5 text-gray-400"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-gray-400">Butuh bantuan?</p>
                        <a href="{{ url('/hubungi-kami') }}" class="text-[11px] font-bold text-[#B84C2B]">Hubungi Support</a>
                    </div>
                </div>
            </div>
            <button onclick="location.href='{{ url('/checkout') }}'" class="w-full bg-white border border-gray-200 text-[#2C1A0E] py-5 rounded-2xl font-bold hover:bg-gray-50 transition-all">
                Ganti Metode Pembayaran
            </button>
        </aside>
    </div>
</main>

{{-- Success Modal --}}
<div id="success-modal" class="fixed inset-0 bg-black/60 z-[100] hidden flex items-center justify-center p-6 backdrop-blur-sm">
    <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden p-10 text-center space-y-6 animate-fade-in-up">
        <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mx-auto">
            <i data-lucide="check-circle" class="w-10 h-10 text-green-500"></i>
        </div>
        <div class="space-y-2">
            <h3 class="font-bold text-2xl text-[#2C1A0E]">Pembayaran Berhasil!</h3>
            <p class="text-sm text-gray-400">Tiket Anda telah berhasil dipesan. Silakan cek email Anda untuk e-tiket.</p>
        </div>
        <button onclick="location.href='{{ url('/my-tickets') }}'" class="w-full bg-[#B84C2B] text-white py-4 rounded-xl font-bold hover:bg-rust-deep transition-all">
            Lihat Tiket Saya
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/payment.js') }}"></script>
@endpush
