@extends('layouts.app')

@section('title')
  Statistik - Bank Soal
@endsection

<style>
    .navbar {
        background-color: #007bff;
        padding: 15px 0;
    }

    .brand-text {
        color: white;
        font-weight: bold;
        font-size: 1.5rem;
        text-decoration: none;
    }

    .tabs-container {
        margin-bottom: 20px;
    }

    .tab-active {
        border-bottom: 3px solid #007bff;
        color: #007bff;
        font-weight: bold;
    }

    .tab-button {
        padding: 10px 0;
        text-decoration: none;
        color: #333;
        margin-right: 30px;
        display: inline-block;
        transition: all 0.3s;
    }

    .tab-button:hover {
        color: #007bff;
        text-decoration: none;
    }

    .stats-card {
        background-color: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        border-left: 4px solid #007bff;
    }

    .stats-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
    }

    .stats-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .stats-label {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .stats-value {
        font-weight: 600;
        color: #333;
    }

    .score-badge {
        background-color: #e3f2fd;
        color: #1976d2;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
        color: #dee2e6;
    }
</style>

@section('content')
   <!-- Navbar -->
   <nav class="navbar">
       <div class="container">
           <a href="{{ route('bank-soal') }}" class="brand-text">Bank Soal</a>
       </div>
   </nav>

   <!-- Main Content -->
   <div class="container mt-4">
       <!-- Tabs -->
       <div class="tabs-container" data-aos="fade-up" data-aos-duration="500">
           <a href="{{ route('bank-soal') }}" class="tab-button">Bank Soal</a>
           <a href="{{ route('bank-soal.statistics') }}" class="tab-button tab-active">Statistik</a>
           <a href="{{ route('bank-soal.history') }}" class="tab-button">Riwayat</a>
       </div>

       <!-- Statistics Content -->
       @if($subCategoryStats->count() > 0)
           <div class="row" data-aos="fade-up" data-aos-duration="500" data-aos-delay="200">
               @foreach($subCategoryStats as $stat)
               <div class="col-md-6 col-lg-4 mb-4">
                   <div class="stats-card">
                       <h6 class="stats-title">{{ $stat->name }}</h6>
                       
                       <div class="stats-item">
                           <span class="stats-label">Total Pengerjaan</span>
                           <span class="stats-value">{{ $stat->total_attempts }}x</span>
                       </div>
                       
                       <div class="stats-item">
                           <span class="stats-label">Rata-rata Skor</span>
                           <span class="score-badge">{{ number_format($stat->avg_score, 1) }}</span>
                       </div>
                       
                       <div class="stats-item">
                           <span class="stats-label">Skor Terbaik</span>
                           <span class="stats-value">{{ $stat->best_score }}</span>
                       </div>
                   </div>
               </div>
               @endforeach
           </div>
       @else
           <div class="empty-state" data-aos="fade-up" data-aos-duration="500" data-aos-delay="200">
               <i class="fas fa-chart-bar"></i>
               <h5>Belum Ada Statistik</h5>
               <p>Mulai kerjakan soal untuk melihat statistik pembelajaran Anda</p>
               <a href="{{ route('bank-soal') }}" class="btn btn-primary">
                   <i class="fas fa-play me-2"></i>Mulai Latihan
               </a>
           </div>
       @endif
   </div>
@endsection

@push('addon-script')
<script>
    AOS.init();
</script>
@endpush