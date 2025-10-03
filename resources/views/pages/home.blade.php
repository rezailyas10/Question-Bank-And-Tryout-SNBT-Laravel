@extends('layouts.app')

@section('title')
  Home
@endsection

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> 
 <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('/style/home.css') }}?v={{ time() }}">
@section('content')
   <!-- Page Content -->
 <div class="page-content page-home">
 <section class="store-carousel">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="pahamify-banner">
          <div class="row align-items-center">
            <div class="col-lg-6">
              <div class="content">
                <h2 class="banner-title">
                  Latih dan Ukur Pemahamanmu dengan Bank Soal SoalIN!
                </h2>
                <p class="banner-description">
                  Dapetin <span class="highlight">ratusan</span> bank soal lengkap dan tryout untuk latih pemahamanmu 
                  hadapi <span class="highlight">SNBT</span>. Kamu bisa tau pemahaman materi 
                  mana yang perlu kamu tingkatin dan siap bersaing dengan siswa 
                  di seluruh penjuru Indonesia untuk masuk perguruan tinggi.
                </p>
                <a href="{{ route('bank-soal') }}" class="btn soalin-btn" style="background-color: #dee2e6;">
                  Kerjain Bank Soal Sekarang
                </a>
              </div>
            </div>
           <div class="col-lg-6 d-flex justify-content-center">
  <img src="/images/img-bank-soal-hero.svg" alt="">
</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

  <section class="store-new-products pt-0" style="padding-bottom: 80px">
    <div class="container">
            <div class="row">
                <div class="col-12 text-center pt-4 pb-2">
                    <h2 class="section-title">Apa aja sih fitur unggulan aplikasi kami?</h2>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            📚
                        </div>
                        <h4 class="feature-title">Bank Soal Lengkap</h4>
                        <p class="feature-desc">Ribuan soal berkualitas dari berbagai mata pelajaran untuk masuk perguruan tinggi</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            ⏱️
                        </div>
                        <h4 class="feature-title">Tryout</h4>
                        <p class="feature-desc">Latihan tryout dengan sistem penilaian yang akurat sesuai standar SNBT</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            🤖
                        </div>
                        <h4 class="feature-title">Evaluasi AI</h4>
                        <p class="feature-desc">Analisis kompetensi mendalam dengan teknologi AI untuk identifikasi kekuatan dan kelemahan</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            🎓
                        </div>
                        <h4 class="feature-title">Rekomendasi Jurusan AI</h4>
                        <p class="feature-desc">Dapatkan rekomendasi jurusan kuliah terbaik dengan teknologi AI berdasarkan hasil tryout dan minat kamu</p>
                    </div>
                </div>
            </div>
        </div>
  </section>
<section class="store-trend-categories pt-0 pb-5">
    <div class="container">
        <div class="row mb-3">
            <div class="col-12 text-center">
                <h2 class="section-title mt-4">Universitas Terfavorit</h2>
            </div>
        </div>
        
        <div class="universities-grid pb-4">
            @foreach($universities as $university)
            <div class="university-item">
                <a href="#" class="university-card">
                    <div class="university-image-container">
                        <img src="{{ asset('storage/' . $university->photo) }}" 
                             alt="{{ $university->name }}" 
                             class="university-img">
                             
                    </div>
                    <p class="university-name">{{ $university->name }}</p>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ITI Alternative Section -->
<!-- <section class="iti-alternative-section py-5" style="background-color: #FBFBFB">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 text-center text-lg-start mb-4 mb-lg-0">
                <div class="iti-content">
                   <h2 class="iti-title mb-3">
                        Gagal Masuk PTN Favorit? 
                        <span class="text-primary">Institut Teknologi Indonesia</span> Terima Nilai SNBT Tanpa Tes!
                    </h2>
                    <p class="iti-description text-muted mb-4">
                        Jangan khawatir! Kini kamu bisa daftar ke ITI hanya dengan menggunakan nilai SNBT tanpa perlu ikut tes lagi. 
                        Raih masa depan cerah bersama ITI melalui program studi unggulan, fasilitas lengkap, dan peluang karier menjanjikan.
                    </p>
                    <div class="iti-features mb-4">
                        <div class="feature-item mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Fasilitas Laboratorium Lengkap & Modern
                        </div>
                        <div class="feature-item mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Program Beasiswa & Bantuan Biaya Pendidikan
                        </div>
                        <div class="feature-item mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Alumni Tersebar di Perusahaan Ternama
                        </div>
                    </div>
                    <a href="#" class="btn btn-primary btn-lg">
                        Daftar ITI Sekarang
                    </a>
                </div>
            </div>
            
            <div class="col-lg-6 text-center">
                <img src="/images/logo-iti.png" alt="Institut Teknologi Indonesia" class="iti-logo img-fluid" style="max-width: 250px;">
            </div>
        </div>
    </div>
</section> -->

<style>
.iti-title {
    font-size: 2rem;
    font-weight: 600;
}

@media (max-width: 768px) {
    .iti-title {
        font-size: 1.5rem;
    }
}
</style>



   <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</div>

 {{-- footer --}}
 @include('includes.footer')

@endsection

