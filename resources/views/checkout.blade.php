@extends('layouts.app')
@section('title', 'Checkout | Pentasara')

@push('styles')
<meta name="base-url" content="{{ rtrim(asset(''), '/') }}">
<style>body{background:#F9F9F9 !important;padding-bottom:8rem}</style>
@endpush

{{-- Replace footer with bottom bar --}}
@section('custom-footer')
<div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 shadow-[0_-10px_40px_rgba(0,0,0,0.03)] z-50">
    <div class="max-w-7xl mx-auto px-6 h-28 flex items-center justify-between">
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">TOTAL PEMBAYARAN</p>
            <p id="bottom-total" class="text-2xl font-black text-[#B84C2B]">Rp 0</p>
        </div>
        <button onclick="processPayment()" class="bg-[#B84C2B] text-white px-12 py-4 rounded-xl font-bold hover:bg-rust-deep transition-all shadow-lg shadow-rust/20 flex items-center gap-3">
            Bayar Tiket
            <i data-lucide="arrow-right" class="w-5 h-5"></i>
        </button>
    </div>
</div>
@endsection

@section('content')
{{-- Checkout Progress Header --}}
<div class="bg-white border-b border-gray-100 py-4">
    <div class="max-w-7xl mx-auto px-6 flex items-center justify-center">
        <div class="flex items-center gap-8">
            <div class="flex items-center gap-3">
                <span class="w-7 h-7 rounded-full bg-rust text-white flex items-center justify-center text-[10px] font-bold">1</span>
                <span class="text-[11px] font-bold text-rust">Data Pemesan</span>
            </div>
            <div class="w-12 h-px bg-gray-200"></div>
            <div class="flex items-center gap-3 opacity-40">
                <span class="w-7 h-7 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-[10px] font-bold">2</span>
                <span class="text-[11px] font-bold text-gray-400">Pembayaran</span>
            </div>
            <div class="w-12 h-px bg-gray-200"></div>
            <div class="flex items-center gap-3 opacity-40">
                <span class="w-7 h-7 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-[10px] font-bold">3</span>
                <span class="text-[11px] font-bold text-gray-400">Konfirmasi</span>
            </div>
        </div>
    </div>
</div>

{{-- Timer Banner --}}
<div class="bg-[#B84C2B] text-white py-3 text-center text-[13px] font-medium">
    Selesaikan pembayaran dalam <span id="timer" class="font-bold">05:31</span> untuk mengamankan tiketmu
    <span class="ml-2">●</span>
</div>

<main class="max-w-7xl mx-auto px-6 py-10 flex flex-col lg:flex-row gap-10">
    {{-- Left Content --}}
    <div class="flex-1 space-y-10">
        {{-- Buyer Info --}}
        <section class="bg-white rounded-2xl shadow-[0_2px_15px_rgba(0,0,0,0.03)] border border-gray-100 p-10">
            <div class="flex items-center gap-3 mb-8">
                <i data-lucide="user" class="w-5 h-5 text-rust"></i>
                <h2 class="font-bold text-lg text-[#2C1A0E]">Informasi Pemesan</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-[#2C1A0E]">Nama Lengkap *</label>
                    <input type="text" id="buyer-name" placeholder="Sesuai KTP/Paspor" class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-rust outline-none transition-all text-sm placeholder:text-gray-300">
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-[#2C1A0E]">Email *</label>
                    <input type="email" id="buyer-email" placeholder="nama@email.com" class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-rust outline-none transition-all text-sm placeholder:text-gray-300">
                    <p class="text-[10px] text-[#C8922A] font-medium italic flex items-center gap-1">
                        <span class="text-lg leading-none">●</span> E-tiket akan dikirim ke alamat ini
                    </p>
                </div>
                <div class="md:col-span-2 space-y-2">
                    <label class="text-[11px] font-bold text-[#2C1A0E]">Nomor KTP/Paspor *</label>
                    <input type="text" id="buyer-id" maxlength="16" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16)" placeholder="Masukkan 16 digit nomor identitas" class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-rust outline-none transition-all text-sm placeholder:text-gray-300">
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-[#2C1A0E]">Nomor WhatsApp *</label>
                    <div class="flex">
                        <span class="px-4 py-3.5 bg-gray-50 border border-r-0 border-gray-200 rounded-l-xl text-sm text-gray-400">+62</span>
                        <input type="text" id="buyer-phone" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="8123456789" class="w-full px-4 py-3.5 rounded-r-xl border border-gray-200 focus:border-rust outline-none transition-all text-sm placeholder:text-gray-300">
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-[#2C1A0E]">Jenis Kelamin *</label>
                    <div class="flex gap-4">
                        <label class="relative flex-1 cursor-pointer group">
                            <input type="radio" name="gender" value="Laki-laki" class="peer absolute opacity-0">
                            <div class="border-2 border-gray-100 rounded-xl py-4 flex items-center justify-center transition-all peer-checked:border-rust peer-checked:bg-rust/5 peer-checked:text-rust peer-checked:ring-1 peer-checked:ring-rust text-gray-400 font-bold text-sm">Laki-laki</div>
                        </label>
                        <label class="relative flex-1 cursor-pointer group">
                            <input type="radio" name="gender" value="Perempuan" class="peer absolute opacity-0">
                            <div class="border-2 border-gray-100 rounded-xl py-4 flex items-center justify-center transition-all peer-checked:border-rust peer-checked:bg-rust/5 peer-checked:text-rust peer-checked:ring-1 peer-checked:ring-rust text-gray-400 font-bold text-sm">Perempuan</div>
                        </label>
                    </div>
                </div>
            </div>
        </section>

        {{-- Visitor Data Container (filled by JS) --}}
        <div id="visitor-container" class="space-y-10"></div>

        {{-- Payment Method --}}
        <section class="bg-white rounded-2xl shadow-[0_2px_15px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50">
                <div class="flex items-center gap-3">
                    <i data-lucide="credit-card" class="w-5 h-5 text-rust"></i>
                    <h2 class="font-bold text-lg text-[#2C1A0E]">Pilih Metode Pembayaran</h2>
                </div>
            </div>

            {{-- E-Wallet --}}
            <div class="border-b border-gray-50">
                <button class="w-full px-8 py-5 flex items-center justify-between hover:bg-gray-50 transition-all" onclick="togglePaymentGroup('ewallet')">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">E-WALLET</span>
                    <i data-lucide="chevron-up" class="w-4 h-4 text-gray-400 transition-transform" id="ewallet-icon"></i>
                </button>
                <div id="ewallet-group" class="px-8 pb-8 grid grid-cols-1 md:grid-cols-2 gap-5">
                    @php
                        $ewalletOptions = [
                            ['value' => 'shopeepay', 'name' => 'ShopeePay', 'img' => 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 40"><rect width="120" height="40" rx="6" fill="%23EE4D2D"/><text x="60" y="26" font-family="sans-serif" font-size="16" font-weight="bold" fill="white" text-anchor="middle">ShopeePay</text></svg>'],
                            ['value' => 'gopay', 'name' => 'GoPay', 'img' => 'https://cdn.jsdelivr.net/gh/Zyknn/paymentlogo@main/Payment%20Channel/E-Wallet/Gopay.svg'],
                            ['value' => 'ovo', 'name' => 'OVO', 'img' => 'https://cdn.jsdelivr.net/gh/Zyknn/paymentlogo@main/Payment%20Channel/E-Wallet/OVO.svg'],
                            ['value' => 'dana', 'name' => 'DANA', 'img' => 'https://cdn.jsdelivr.net/gh/Zyknn/paymentlogo@main/Payment%20Channel/E-Wallet/DANA.svg'],
                            ['value' => 'qris', 'name' => 'QRIS', 'img' => 'https://cdn.jsdelivr.net/gh/Zyknn/paymentlogo@main/Payment%20Channel/Miscellaneous/QRIS.svg'],
                        ];
                    @endphp
                    @foreach($ewalletOptions as $ewallet)
                    <label class="relative group cursor-pointer block">
                        <input type="radio" name="payment" value="{{ $ewallet['value'] }}" class="peer absolute opacity-0">
                        <div class="border-2 border-gray-100 rounded-2xl p-6 flex items-center justify-between transition-all peer-checked:border-rust peer-checked:bg-rust/5 peer-checked:shadow-sm peer-checked:[&_.circle-indicator]:bg-rust peer-checked:[&_.circle-indicator]:border-rust peer-checked:[&_.check-icon]:opacity-100 peer-checked:[&_.check-icon]:scale-100">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-10 flex items-center justify-center">
                                    <img src="{{ $ewallet['img'] }}" alt="{{ $ewallet['name'] }}" class="max-h-full max-w-full object-contain">
                                </div>
                                <span class="text-[14px] font-bold text-[#2C1A0E]">{{ $ewallet['name'] }}</span>
                            </div>
                            <div class="circle-indicator w-6 h-6 rounded-full border-2 border-gray-200 flex items-center justify-center transition-all shadow-sm">
                                <svg class="check-icon w-3.5 h-3.5 text-white opacity-0 transform scale-50 transition-all" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Virtual Account --}}
            <div>
                <button class="w-full px-8 py-5 flex items-center justify-between hover:bg-gray-50 transition-all" onclick="togglePaymentGroup('va')">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">VIRTUAL ACCOUNT</span>
                    <i data-lucide="chevron-up" class="w-4 h-4 text-gray-400 transition-transform" id="va-icon"></i>
                </button>
                <div id="va-group" class="px-8 pb-8 space-y-4">
                    @php
                        $vaOptions = [
                            ['value' => 'bni', 'name' => 'BNI Virtual Account', 'img' => 'https://cdn.jsdelivr.net/gh/Zyknn/paymentlogo@main/Bank/Bank%20Logo/BNI.svg'],
                            ['value' => 'bca', 'name' => 'BCA Virtual Account', 'img' => 'https://cdn.jsdelivr.net/gh/Zyknn/paymentlogo@main/Bank/Bank%20Logo/BCA.svg'],
                            ['value' => 'mandiri', 'name' => 'Mandiri Virtual Account', 'img' => 'https://cdn.jsdelivr.net/gh/Zyknn/paymentlogo@main/Bank/Bank%20Logo/Mandiri.svg'],
                            ['value' => 'bri', 'name' => 'BRI Virtual Account', 'img' => 'https://cdn.jsdelivr.net/gh/Zyknn/paymentlogo@main/Bank/Bank%20Logo/BRI.svg'],
                        ];
                    @endphp
                    @foreach($vaOptions as $va)
                    <label class="relative group cursor-pointer block">
                        <input type="radio" name="payment" value="{{ $va['value'] }}" class="peer absolute opacity-0">
                        <div class="border-2 border-gray-100 rounded-2xl p-6 flex items-center justify-between transition-all peer-checked:border-rust peer-checked:bg-rust/5 peer-checked:shadow-sm peer-checked:[&_.circle-indicator]:bg-rust peer-checked:[&_.circle-indicator]:border-rust peer-checked:[&_.check-icon]:opacity-100 peer-checked:[&_.check-icon]:scale-100">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-10 bg-gray-50 rounded-lg flex items-center justify-center p-2">
                                    <img src="{{ $va['img'] }}" alt="{{ $va['value'] }}" class="max-h-full max-w-full object-contain">
                                </div>
                                <div class="text-left">
                                    <p class="text-[14px] font-bold text-[#2C1A0E]">{{ $va['name'] }}</p>
                                    <p class="text-[10px] text-gray-400">Dicek otomatis</p>
                                </div>
                            </div>
                            <div class="circle-indicator w-6 h-6 rounded-full border-2 border-gray-200 flex items-center justify-center transition-all shadow-sm">
                                <svg class="check-icon w-3.5 h-3.5 text-white opacity-0 transform scale-50 transition-all" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
        </section>
    </div>

    {{-- Right Sidebar --}}
    <aside class="w-full lg:w-[400px] space-y-8">
        <div class="bg-white rounded-3xl shadow-[0_2px_15px_rgba(0,0,0,0.03)] border border-gray-100 p-8 sticky top-28">
            <h3 class="font-bold text-xl text-[#2C1A0E] mb-8">Ringkasan Pesanan</h3>
            <div class="flex gap-5 mb-8">
                <img id="event-thumb" src="" alt="Event" class="w-24 h-24 rounded-2xl object-cover shadow-sm">
                <div class="py-1">
                    <h4 id="event-name" class="font-bold text-[15px] text-[#2C1A0E] mb-2 leading-tight">-</h4>
                    <div class="flex items-center gap-2 text-[11px] text-gray-400 mb-1.5">
                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                        <span id="event-date">-</span>
                    </div>
                    <div class="flex items-center gap-2 text-[11px] text-gray-400">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                        <span id="event-venue">-</span>
                    </div>
                </div>
            </div>

            {{-- Voucher --}}
            <div id="voucher-trigger" onclick="openVoucherModal()" class="bg-[#FFF9F2] border border-[#FFE8CC] rounded-2xl p-4 mb-8 flex items-center justify-between cursor-pointer hover:bg-[#FFF5E6] transition-all">
                <div class="flex items-center gap-3">
                    <i data-lucide="ticket" class="w-5 h-5 text-[#C8922A]"></i>
                    <span id="voucher-text" class="text-[11px] font-bold text-[#8B2E12]">Gunakan Voucher Diskon</span>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4 text-[#C8922A]"></i>
            </div>

            <div class="space-y-4 mb-8 border-t border-gray-50 pt-8">
                <div id="ticket-list" class="space-y-3"></div>
                <div class="flex justify-between text-[13px]">
                    <span class="text-gray-400">Biaya Layanan</span>
                    <span id="service-fee" class="font-bold text-[#2C1A0E]">Rp 0</span>
                </div>
            </div>

            <div class="flex justify-between items-center pt-6 border-t border-gray-50">
                <div class="space-y-1">
                    <span class="text-[13px] font-bold text-[#2C1A0E]">Total</span>
                    <p class="text-[13px] font-bold text-[#2C1A0E]">Pembayaran</p>
                </div>
                <span id="total-payment" class="text-2xl font-black text-[#B84C2B]">Rp 0</span>
            </div>

            <div class="mt-10 flex items-center justify-between">
                <button onclick="openDetailModal()" class="text-[11px] font-bold text-rust hover:underline">Lihat Detail</button>
                <div class="flex items-center gap-2 text-[10px] text-green-500 font-bold">
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                    SECURE CHECKOUT
                </div>
            </div>
        </div>
    </aside>
</main>

{{-- Detail Modal --}}
<div id="detail-modal" class="fixed inset-0 bg-black/60 z-[100] hidden flex items-center justify-center p-6 backdrop-blur-sm">
    <div class="bg-white rounded-3xl w-full max-w-lg overflow-hidden animate-fade-in-up">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-cream/10">
            <h3 class="font-bold text-xl text-[#2C1A0E]">Detail Pesanan</h3>
            <button onclick="closeDetailModal()" class="w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center transition-all">
                <i data-lucide="x" class="w-6 h-6 text-gray-400"></i>
            </button>
        </div>
        <div class="p-8 space-y-6 max-h-[60vh] overflow-y-auto no-scrollbar">
            <div class="flex gap-4">
                <img id="modal-event-thumb" src="" class="w-20 h-20 rounded-xl object-cover">
                <div>
                    <h4 id="modal-event-name" class="font-bold text-lg text-[#2C1A0E] leading-tight">-</h4>
                    <p id="modal-event-date" class="text-xs text-gray-400 mt-1">-</p>
                </div>
            </div>
            <div class="space-y-4 border-t border-gray-50 pt-6">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">TIKET YANG DIBELI</p>
                <div id="modal-ticket-list" class="space-y-3"></div>
            </div>
            <div class="space-y-3 border-t border-gray-50 pt-6">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Subtotal</span>
                    <span id="modal-subtotal" class="font-bold text-[#2C1A0E]">-</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Biaya Layanan (10%)</span>
                    <span id="modal-service-fee" class="font-bold text-[#2C1A0E]">-</span>
                </div>
            </div>
        </div>
        <div class="p-8 bg-gray-50 flex justify-between items-center">
            <span class="text-sm font-bold text-gray-500">Total Bayar</span>
            <span id="modal-total" class="text-2xl font-black text-[#B84C2B]">-</span>
        </div>
    </div>
</div>

{{-- Voucher Modal --}}
<div id="voucher-modal" class="fixed inset-0 bg-black/60 z-[110] hidden flex items-center justify-center p-6 backdrop-blur-sm">
    <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden animate-fade-in-up">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-xl text-[#2C1A0E]">Gunakan Voucher</h3>
            <button onclick="closeVoucherModal()" class="w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center transition-all">
                <i data-lucide="x" class="w-6 h-6 text-gray-400"></i>
            </button>
        </div>
        <div class="p-8 space-y-6">
            <div class="space-y-2">
                <label class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">KODE VOUCHER</label>
                <div class="flex gap-3">
                    <input type="text" id="voucher-input" placeholder="Masukkan kode voucher" class="flex-1 px-4 py-3.5 rounded-xl border border-gray-200 focus:border-rust outline-none transition-all text-sm uppercase font-bold tracking-widest">
                    <button onclick="applyVoucher()" class="bg-rust text-white px-6 py-3.5 rounded-xl font-bold text-sm hover:bg-rust-deep transition-all">Terapkan</button>
                </div>
            </div>
            <div class="space-y-4">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">VOUCHER TERSEDIA</p>
                <div class="border border-gray-100 rounded-2xl p-4 flex items-center justify-between bg-gray-50 opacity-60">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-rust/10 flex items-center justify-center">
                            <i data-lucide="percent" class="w-5 h-5 text-rust"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-[#2C1A0E]">DISKON10</p>
                            <p class="text-[10px] text-gray-400">Potongan 10% s/d Rp 50.000</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-gray-300">BELUM TERSEDIA</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/checkout.js') }}"></script>
@endpush
