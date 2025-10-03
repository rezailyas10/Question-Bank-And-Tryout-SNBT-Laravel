@extends('layouts.app')

@section('title')
  Bank Soal
@endsection

<link rel="stylesheet" href="{{ asset('/style/bank-soal.css') }}?v={{ time() }}">
{{-- @extends('includes.bank-soal') --}}

@section('content')
   <!-- Main Content -->
   <div class="container" style="margin-top: 100px">
      @if(Auth::check() && $weeklyStats)
       <!-- Stats Card untuk User Login -->
       <div class="stats-card" data-aos="fade-up" data-aos-duration="500">
           <div class="row align-items-center">
               <div class="col-8">
                   <h5 class="progress-title">Progres Minggu Ini</h5>
                   <p class="mb-1">
                       Jumlah soal yang dikerjakan:
                       <span class="stats-number">{{ $weeklyStats['questions_answered'] }}</span>
                   </p>
                   <p class="mb-0">
                       Rata-rata nilai:
                       <span class="stats-number">{{ $weeklyStats['avg_score'] }}</span>
                       <span class="rank-text">dari {{ $weeklyStats['total_attempts'] }} pengerjaan</span>
                   </p>
               </div>
               
           </div>
       </div>
       @else
       <!-- Pesan untuk Guest -->
       <div class="guest-message" data-aos="fade-up" data-aos-duration="500">
           <p class="mb-0">
               <i class="fas fa-info-circle me-2"></i>
               Masuk untuk melihat progress dan statistik pembelajaran Anda
           </p>
       </div>
       @endif

       <!-- Tabs -->
       <!-- Tabs -->
    <div class="tabs-container" data-aos="fade-up" data-aos-duration="500" data-aos-delay="100">
        <a href="javascript:void(0)" class="tab-button tab-bank-soal tab-active" onclick="showTab('bank-soal')">Bank Soal</a>
        @auth
        <a href="javascript:void(0)" class="tab-button tab-statistik" onclick="showTab('statistik')">Statistik</a>
        <a href="javascript:void(0)" class="tab-button tab-riwayat" onclick="showTab('riwayat')">Riwayat</a>
        @endauth
    </div>

       
    <!-- Tab Content Bank Soal -->
    <div id="tab-bank-soal" class="tab-content">
        <!-- Filter Category -->
        <div class="row mb-3" data-aos="fade-up" data-aos-duration="500" data-aos-delay="200">
            <div class="col-md-4">
                <form method="GET" action="{{ route('bank-soal') }}">
                    <select name="category_id" class="form-select filter-select" onchange="this.form.submit()">
                        <option value="">Semua Mata Pelajaran</option>
                        <option value="snbt" {{ $selectedCategoryId == 'snbt' ? 'selected' : '' }}>SNBT</option>
                        <option value="mandiri" {{ $selectedCategoryId == 'mandiri' ? 'selected' : '' }}>Ujian Mandiri</option>
                        {{-- @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $selectedCategoryId == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach --}}
                    </select>
                </form>
            </div>
        </div>

        <!-- Daftar Sub Kategori -->
        <div class="row" data-aos="fade-up" data-aos-duration="500" data-aos-delay="400">
            @forelse($subCategories as $subCategory)
            <div class="col-6 col-md-3 mb-4">
                <a href="{{ route('bank-soal-detail', $subCategory->slug) }}" style="text-decoration: none;">
                    <div class="subject-card">
                        <div class="subject-image-container">
                            @if($subCategory->photo)
                                <img src="{{ asset('storage/' . $subCategory->photo) }}" alt="{{ $subCategory->name }}">
                            @else
                                <i class="fas fa-book" style="color: #6c757d; font-size: 32px;"></i>
                            @endif
                        </div>
                        <h6 class="subject-title">{{ $subCategory->name }}</h6>
                    </div>
                </a>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-folder-open" style="font-size: 48px; color: #dee2e6; margin-bottom: 15px;"></i>
                    <p class="text-muted">Belum ada sub kategori tersedia</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    @auth
    <!-- Tab Content Statistik -->
    <div id="tab-statistik" class="tab-content" style="display: none;">
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

    <!-- Tab Content Riwayat -->
    <div id="tab-riwayat" class="tab-content" style="display: none;">
     @if($results->count() > 0)
    <div data-aos="fade-up" data-aos-duration="500" data-aos-delay="200">
        @foreach($results as $result)
        <div class="history-card">
            <div class="history-header">
                <div>
                    {{-- Menampilkan judul exam --}}
                    <h6 class="history-title">
                        {{ $result->result->exam->title ?? 'Exam #' . $result->exam_id }}
                    </h6>
                    <p class="history-date">
                        <i class="fas fa-clock me-1"></i>
                        {{-- Tampilkan waktu dari results_evaluation.created_at --}}
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
                    @php
                        $emptyCount = $result->empty ?? 0;
                        $totalQuestions = $result->correct + $result->wrong + $emptyCount;
                    @endphp
                    <span>Total Soal: <span class="detail-value">{{ $totalQuestions }}</span></span>
                </div>
                <div class="detail-item">
                    <i class="fas fa-check-circle" style="color: #2e7d32;"></i>
                    <span>Benar: <span class="detail-value correct-count">{{ $result->correct }}</span></span>
                </div>
                <div class="detail-item">
                    <i class="fas fa-times-circle" style="color: #d32f2f;"></i>
                    <span>Salah: <span class="detail-value wrong-count">{{ $result->wrong }}</span></span>
                </div>
                <div class="detail-item">
                    <i class="fas fa-minus-circle" style="color: #ffc107;"></i>
                    <span>Kosong: 
                        <span class="detail-value empty-count">
                            {{ $result->empty ?? 0 }}
                        </span>
                    </span>
                </div>
            </div>
        </div>
        @endforeach

        <div class="text-center mt-3">
            <p class="text-muted">Menampilkan {{ $results->count() }} riwayat terbaru</p>
        </div>
    </div>
@else
    <div class="empty-state" data-aos="fade-up" data-aos-duration="500" data-aos-delay="200">
        <i class="fas fa-history"></i>
        <h5>Belum Ada Riwayat</h5>
        <p>Mulai kerjakan soal untuk melihat riwayat pengerjaan Anda</p>
    </div>
@endif
    </div>
    @endauth
</div>
@endsection

@push('addon-script')
<script>
    AOS.init();

    // Navbar scroll behavior
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

   function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(function(content) {
        content.style.display = 'none';
    });

    // Remove active class from all tabs
    document.querySelectorAll('.tab-button').forEach(function(tab) {
        tab.classList.remove('tab-active');
    });

    // Show selected tab content jika ada
    const target = document.getElementById('tab-' + tabName);
    if (target) {
        target.style.display = 'block';
    } else {
        console.warn('Tab content not found: tab-' + tabName);
    }

    // Add active class to selected tab button jika ada
    const tabBtn = document.querySelector('.tab-' + tabName);
    if (tabBtn) {
        tabBtn.classList.add('tab-active');
    }
}

</script>
@endpush