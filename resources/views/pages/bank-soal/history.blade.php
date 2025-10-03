@extends('layouts.app')

@section('title')
  Riwayat - Bank Soal
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

    .history-card {
        background-color: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        border-left: 4px solid #007bff;
    }

    .history-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .history-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }

    .history-date {
        color: #6c757d;
        font-size: 0.85rem;
    }

    .score-badge {
        background-color: #e8f5e8;
        color: #2e7d32;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .score-badge-low {
        background-color: #fff3e0;
        color: #f57c00;
    }

    .score-badge-medium {
        background-color: #e3f2fd;
        color: #1976d2;
    }

    .history-details {
        display: flex;
        gap: 20px;
        font-size: 0.9rem;
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 5px;
        color: #6c757d;
    }

    .detail-value {
        font-weight: 600;
        color: #333;
    }

    .correct-count {
        color: #2e7d32;
    }

    .wrong-count {
        color: #d32f2f;
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

    .pagination {
        justify-content: center;
        margin-top: 30px;
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
           <a href="{{ route('bank-soal.statistics') }}" class="tab-button">Statistik</a>
           <a href="{{ route('bank-soal.history') }}" class="tab-button tab-active">Riwayat</a>
       </div>

       <!-- History Content -->
       @if($results->count() > 0)
           <div data-aos="fade-up" data-aos-duration="500" data-aos-delay="200">
               @foreach($results as $result)
               <div class="history-card">
                   <div class="history-header">
                       <div>
                           <h6 class="history-title">{{ $result->exam->subCategory->name ?? 'Ujian' }}</h6>
                           <p class="history-date">
                               <i class="fas fa-clock me-1"></i>
                               {{ $result->created_at->format('d M Y, H:i') }}
                           </p>
                       </div>
                       <div>
                           @php
                               $scoreClass = 'score-badge';
                               if ($result->score < 60) $scoreClass .= ' score-badge-low';
                               elseif ($result->score < 80) $scoreClass .= ' score-badge-medium';
                           @endphp
                           <span class="{{ $scoreClass }}">{{ $result->score }}</span>
                       </div>
                   </div>
                   
                   <div class="history-details">
                       <div class="detail-item">
                           <i class="fas fa-question-circle"></i>
                           <span>Total Soal: <span class="detail-value">{{ $result->total_questions }}</span></span>
                       </div>
                       <div class="detail-item">
                           <i class="fas fa-check-circle" style="color: #2e7d32;"></i>
                           <span>Benar: <span class="detail-value correct-count">{{ $result->correct_answers }}</span></span>
                       </div>
                       <div class="detail-item">
                           <i class="fas fa-times-circle" style="color: #d32f2f;"></i>
                           <span>Salah: <span class="detail-value wrong-count">{{ $result->wrong_answers }}</span></span>
                       </div>
                   </div>
               </div>
               @endforeach

               <!-- Pagination -->
               <div class="d-flex justify-content-center">
                   {{ $results->links() }}
               </div>
           </div>
       @else
           <div class="empty-state" data-aos="fade-up" data-aos-duration="500" data-aos-delay="200">
               <i class="fas fa-history"></i>
               <h5>Belum Ada Riwayat</h5>
               <p>Mulai kerjakan soal untuk melihat riwayat pengerjaan Anda</p>
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