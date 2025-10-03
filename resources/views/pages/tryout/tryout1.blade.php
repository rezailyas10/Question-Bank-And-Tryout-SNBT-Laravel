@extends('layouts.app')

@section('title')
  Bank Soal
@endsection
<style>
    .subject-card {
    background: #fff;
    padding: 15px;
    border-radius: 12px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    transition: transform 0.3s;
    text-align: center;
}

.subject-card:hover {
    transform: translateY(-5px);
}

.exam-card {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

  .brand-text {
      color: white;
      font-weight: bold;
      font-size: 1.5rem;
      text-decoration: none;
  }

  .stats-card {
      background-color: white;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
  }

  .tabs-container {
      margin-bottom: 15px;
  }

  .tab-active {
      border-bottom: 3px solid var(--primary-blue);
      color: var(--primary-blue);
      font-weight: bold;
  }

  .tab-button {
      padding: 10px 0;
      text-decoration: none;
      color: #333;
      margin-right: 20px;
      display: inline-block;
  }

  .search-box {
      background-color: var(--light-gray);
      border-radius: 8px;
      padding: 10px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
  }

  .search-input {
      border: none;
      background: transparent;
      width: 100%;
      outline: none;
      color: #777;
  }

  .subject-card {
      background-color: white;
      border-radius: 12px;
      text-align: center;
      padding: 20px 10px;
      margin-bottom: 20px;
      transition: transform 0.3s ease;
      cursor: pointer;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
  }

  .subject-card:hover {
      transform: translateY(-5px);
  }

  .subject-icon {
      width: 80px;
      height: 80px;
      margin: 0 auto 15px;
      display: block;
  }

  .btn-leaderboard {
      background-color: var(--primary-blue);
      color: white;
      border-radius: 20px;
      padding: 6px 15px;
      font-size: 0.85rem;
  }

  .dropdown-toggle {
      background-color: #fff;
      color: #333;
      border: 1px solid #ccc;
      border-radius: 6px;
      padding: 8px 15px;
      font-size: 0.9rem;
      cursor: pointer;
  }

  .progress-title {
      color: #6c757d;
      font-size: 1.1rem;
      margin-bottom: 5px;
  }

  .stats-number {
      font-size: 1.1rem;
      font-weight: bold;
      color: #333;
  }

  .rank-text {
      color: #6c757d;
  }
</style>


@section('content')

   <!-- Navbar -->
   <nav class="navbar">
       <div class="container">
           <a href="" class="brand-text">Bank Soal</a>
       </div>
   </nav>

   <!-- Main Content -->
<div class="container mt-4">
    <!-- Stats Card -->
    <div class="stats-card" data-aos="fade-up" data-aos-duration="500">
        <div class="row align-items-center">
            <div class="col-8">
                <h5 class="progress-title">Progres Minggu Ini</h5>
                <p class="mb-1">
                    Jumlah soal yang dikerjakan:
                    <span class="stats-number">10</span>
                </p>
                <p class="mb-0">
                    Peringkatmu: <span class="stats-number">1566</span
                    ><span class="rank-text">/1727</span>
                </p>
            </div>
            <div class="col-4 text-end">
                <img
                    src="/api/placeholder/80/60"
                    alt="Statistics Icon"
                    class="mb-2"
                />
                <button class="btn btn-leaderboard">
                    Lihat Leaderboard
                </button>
            </div>
        </div>
    </div>

   <!-- Main Content -->
   <div class="container mt-4">
       <!-- Stats Card (seperti contoh yang sudah dibuat) -->
       <!-- ...kode stats card... -->

       <!-- Tabs -->
       <div class="tabs-container" data-aos="fade-up" data-aos-duration="500" data-aos-delay="100">
           <a href="{{ route('dashboard') }}" class="tab-button tab-active">Bank Soal</a>
           <a href="#" class="tab-button">Riwayat</a>
       </div>

     

       <!-- Daftar Sub Kategori -->
       <div class="row" data-aos="fade-up" data-aos-duration="500" data-aos-delay="400">
           @foreach($subCategories as $subCategory)
           <div class="col-6 col-md-3">
               <div class="subject-card">
                   <a href="{{ route('bank-soal-detail', $subCategory->slug) }}">
                       <div style="background-color: #e0e0e0; width: 80px; height: 80px; border-radius: 12px; margin: 0 auto 15px;
                           display: flex; align-items: center; justify-content: center;">
                           @if($subCategory->photo)
                               <img src="{{ asset('storage/' . $subCategory->photo) }}" alt="{{ $subCategory->name }}" class="img-fluid">
                           @else
                               <!-- Bila tidak ada photo, bisa ganti dengan icon default -->
                               <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#FFF" class="bi bi-folder" viewBox="0 0 16 16">
                                   <path d="M9.828 4a.5.5 0 0 0-.354-.146H2a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H9.828z"/>
                               </svg>
                           @endif
                       </div>
                       <h6 class="text-center">{{ $subCategory->name }}</h6>
                   </a>
               </div>
           </div>
           @endforeach
       </div>
   </div>
@endsection

@push('addon-script')
<script>
    AOS.init();

    // Untuk navbar scroll behavior
    document.addEventListener("DOMContentLoaded", function () {
        const navbar = document.querySelector(".navbar");
        window.addEventListener("scroll", function () {
            if (window.scrollY > 50) {
                navbar.style.boxShadow = "0 2px 10px rgba(0,0,0,0.1)";
            } else {
                navbar.style.boxShadow = "none";
            }
        });
    });
</script>
@endpush
