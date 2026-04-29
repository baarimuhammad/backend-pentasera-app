@extends('layouts.app')
@section('title', 'Tentang Kami | Pentasara')

@section('content')
<!-- Hero Section -->
<header class="hero-section">
    <p class="subtitle">PENTASERA</p>
    <h1 style="font-family: 'Playfair Display', serif; font-size: 56px; line-height: 1.1; font-weight: 400;">Menjaga <span style="font-style: italic; color: #A4510D;">Tradisi</span> <br> dalam Modernitas</h1>
    <p class="hero-desc">Kami adalah kurator pengalaman budaya yang berdedikasi untuk menghidupkan kembali warisan seni nusantara melalui panggung kontemporer yang relevan.</p>
</header>

<!-- Manifesto & Vision Section -->
<section class="manifesto-grid">
    <div class="manifesto-img">
        <div class="manifesto-content">
            <h3>Manifesto Budaya</h3>
            <p>Pentasera lahir dari keinginan untuk menjadikan seni tradisional bukan sekadar artefak museum, melainkan pengalaman yang bergetar di hati generasi hari ini.</p>
        </div>
    </div>
    <div class="vision-card">
        <div class="vision-icon">✦</div>
        <h3>Visi Kami</h3>
        <p>Menjadi jembatan utama antara kekayaan sejarah dan aspirasi modern, menciptakan ekosistem seni yang berkelanjutan dan membanggakan.</p>
        <div class="stats">
            <span class="number">100+</span>
            <p class="label">SENIMAN DIKURASI</p>
        </div>
    </div>
</section>

<!-- Kisah di Balik Layar Section -->
<section class="story-section">
    <div class="story-container">
        <div class="story-visual">
            <div class="story-bg-image">
                <img src="https://images.unsplash.com/photo-1578926375605-eaf7559b1458?q=80&w=1000" alt="Artisan Story">
            </div>
            <div class="quote-box">
                <span class="quote-mark">"</span>
                <p>Seni adalah bahasa jiwa yang melampaui waktu. Kami hanya penerjemahnya.</p>
            </div>
        </div>
        <div class="story-text">
            <h2>Kisah di Balik Layar</h2>
            <hr class="short-line">
            <p>Perjalanan kami dimulai dari sebuah keresahan sederhana di sudut galeri tua Jakarta. Melihat banyaknya pertunjukan tradisi yang kehilangan penonton muda, kami menyadari ada yang salah dengan cara kita menyajikan warisan kita.</p>
            <p>Pentasera didirikan untuk menjawab tantangan tersebut. Dengan sentuhan desain modern, kurasi narasi yang mendalam, dan teknologi aksesibilitas, kami membawa pertunjukan wayang, tari, hingga kriya ke dalam gaya hidup urban yang dinamis.</p>
            <a href="{{ url('/events') }}" class="link-arrow">Lihat Arsip Seni &rarr;</a>
        </div>
    </div>
</section>

<!-- Nilai Kami Section -->
<section class="values-section">
    <div class="values-header">
        <h2>Nilai yang Kami Pegang</h2>
        <p>Fondasi dalam setiap kurasi dan kolaborasi.</p>
    </div>
    <div class="values-grid">
        <div class="value-card">
            <div class="value-icon-box">📜</div>
            <h4>Otentisitas Berbasis Akar</h4>
            <p>Kami tidak pernah mengorbankan esensi tradisi demi tren. Setiap modifikasi dilakukan dengan menghormati akar filosofisnya.</p>
        </div>
        <div class="value-card">
            <div class="value-icon-box">👥</div>
            <h4>Inklusivitas Budaya</h4>
            <p>Pentasera adalah milik semua. Kami memastikan akses seni tradisional terbuka lebar bagi siapa pun, dari latar belakang apa pun.</p>
        </div>
        <div class="value-card">
            <div class="value-icon-box">📜</div>
            <h4>Ekosistem Berkelanjutan</h4>
            <p>Kami mendukung kesejahteraan ekonomi para seniman lokal melalui sistem kemitraan yang adil dan transparan.</p>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="cta-card">
        <h2>Jadilah Bagian dari <span>Sejarah Baru</span></h2>
        <p>Dukung pelestarian budaya kita dengan menghadiri acara-acara pilihan atau berkolaborasi sebagai kurator.</p>
        <div class="cta-buttons">
            <a href="{{ url('/') }}" class="btn-dark-round">Eksplorasi Acara</a>
            <a href="{{ url('/hubungi-kami') }}" class="btn-white-round">Hubungi Kami</a>
        </div>
        <!-- Elemen dekoratif bunga di pojok kanan -->
        <div class="flower-deco"></div>
    </div>
</section>
@endsection
