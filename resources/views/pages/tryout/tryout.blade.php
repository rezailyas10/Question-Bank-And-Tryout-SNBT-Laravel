@extends('layouts.app')

@section('title', 'Try Out')

@push('addon-style')
<link rel="stylesheet" href="{{ asset('/style/tryout.css') }}?v={{ time() }}">
@endpush

@section('content')

<div class="container py-3" style="margin-top: 100px">
  <!-- Search Bar dengan Form -->
  <form method="GET" action="{{ route('tryout') }}" class="mb-4">
    <div class="search-bar d-flex align-items-center">
      <i class="fas fa-search me-2"></i>
      <input type="text" name="search" value="{{ $search }}" placeholder="Cari Tryout..." />
      <button type="submit" class="btn btn-primary ms-2">
        <i class="fas fa-search"></i>
      </button>
      @if($search)
        <a href="{{ route('tryout') }}" class="btn btn-outline-secondary ms-2">
          <i class="fas fa-times"></i> Clear
        </a>
      @endif
    </div>
  </form>

  @if($search)
    <div class="alert alert-info">
      <i class="fas fa-info-circle me-2"></i>
      Menampilkan hasil pencarian untuk: <strong>"{{ $search }}"</strong>
    </div>
  @endif

  @auth
  <!-- Tab Navigation -->
  <div class="tab-navigation mb-4" data-aos="fade-up">
    <ul class="nav nav-tabs custom-tabs" id="myTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tryout-tab" data-bs-toggle="tab" data-bs-target="#tab-tryout" type="button" role="tab">
          <i class="fas fa-clipboard-list me-2"></i>Tryout
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="statistik-tab" data-bs-toggle="tab" data-bs-target="#tab-statistik" type="button" role="tab">
          <i class="fas fa-chart-bar me-2"></i>Statistik
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#tab-riwayat" type="button" role="tab">
          <i class="fas fa-history me-2"></i>Riwayat
        </button>
      </li>
    </ul>
  </div>

  <!-- Tab Content -->
  <div class="tab-content" id="myTabContent">
    <!-- Tab Tryout -->
    <div class="tab-pane fade show active" id="tab-tryout" role="tabpanel">
  @endauth

      <!-- —————— Tryout Saya Section —————— -->
      @if($myTryouts->isNotEmpty())
        <div class="tryout-section mb-5">
          <div class="section-header">
            <h3><i class="fas fa-user-check me-2"></i>Tryout Saya</h3>
            <span class="badge bg-success">{{ $myTryouts->count() }} Tryout Dikerjakan</span>
          </div>

          <div class="tryout-grid" data-aos="fade-up">
            @foreach($myTryouts as $index => $exam)
              @php
                $result = $userResults[$exam->id] ?? null;
                $isClosed = \Carbon\Carbon::parse($exam->tanggal_ditutup)->lessThanOrEqualTo(now());
            @endphp
              
              @if (!$isClosed)
    {{-- Exam masih dibuka → ke halaman detail --}}
    <a href="{{ route('tryout-detail', $exam->slug) }}" class="tryout-card tryout-card-urgent">
@else
    {{-- Exam sudah ditutup → ke halaman hasil --}}
    <a href="{{ route('tryout-result', ['exam' => $exam->id, 'id' => $result->id]) }}" class="tryout-card tryout-card-completed">
@endif
                <div class="tryout-card-header">
                  <div class="tryout-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                  <div class="tryout-info">
                    <span class="tryout-badge tryout-badge-completed">SNBT</span>
                    <div class="tryout-status status-completed">
                      <i class="fas fa-check-circle me-1"></i> Selesai
                    </div>
                  </div>
                </div>
                <h4 class="tryout-title">{{ $exam->title }}</h4>
                <div class="tryout-meta">
                   @if (!$isClosed)
                    {{-- Exam masih dibuka → ke halaman detail --}}
                        <div class="tryout-meta-item">
                           <i class="fas fa-hourglass-end"></i>
                    Berakhir: {{ Carbon\Carbon::parse($exam->tanggal_ditutup)->format('d M Y ') }}
                        </div>
                    @else
                        {{-- Exam sudah ditutup → ke halaman hasil --}}
                       <div class="tryout-meta-item">
                    <i class="fas fa-chart-line text-success"></i>
                    Lihat Hasil
                  </div>
                    @endif
                  
                  <div class="tryout-meta-item">
                    <i class="fas fa-calendar"></i>
                    {{ Carbon\Carbon::parse($result->created_at)->format('d M Y') }}
                  </div>
                   <div class="tryout-meta-item">
                    <i class="fas fa-users"></i>
                    {{ number_format($examParticipants[$exam->id] ?? 0) }}  Peserta
                  </div>
                </div>
                <div class="tryout-arrow">
                  <i class="fas fa-chevron-right"></i>
                </div>
              </a>
            @endforeach
          </div>
        </div>
      @endif

      <!-- —————— Ongoing Try Out Section —————— -->
      @if($ongoing->isNotEmpty())
        <div class="tryout-section mb-5">
          <div class="section-header">
            <h3><i class="fas fa-play-circle me-2 text-warning"></i>Sedang Berlangsung</h3>
            <span class="badge bg-warning text-dark">{{ $ongoing->count() }} Tryout Aktif</span>
          </div>

          <div class="tryout-grid" data-aos="fade-up">
            @foreach($ongoing as $index => $exam)
              <a href="{{ route('tryout-detail', $exam->slug) }}" class="tryout-card tryout-card-urgent">
                <div class="tryout-card-header">
                  <div class="tryout-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                  <div class="tryout-info">
                    <span class="tryout-badge tryout-badge-ongoing">SNBT</span>
                    <div class="tryout-status status-ongoing">
                      <i class="fas fa-clock me-1"></i>Berlangsung
                    </div>
                  </div>
                </div>
                <h4 class="tryout-title">{{ $exam->title }}</h4>
                <div class="tryout-meta">
                  <div class="tryout-meta-item">
                    <i class="fas fa-star text-warning"></i>
                    Segera Kerjakan
                  </div>
                  <div class="tryout-meta-item">
                    <i class="fas fa-users"></i>
                    {{ number_format($examParticipants[$exam->id] ?? 0) }}  Peserta
                  </div>
                </div>
                <div class="tryout-countdown">
                  <small class="text-muted">
                    <i class="fas fa-hourglass-end"></i>
                    Berakhir: {{ Carbon\Carbon::parse($exam->tanggal_ditutup)->format('d M Y H:i') }}
                  </small>
                </div>
                <div class="tryout-arrow">
                  <i class="fas fa-chevron-right"></i>
                </div>
              </a>
            @endforeach
          </div>
        </div>
      @endif

      <!-- —————— Upcoming Try Out Section —————— -->
      @if($upcoming->isNotEmpty())
        <div class="tryout-section mb-5">
          <div class="section-header">
            <h3><i class="fas fa-calendar-plus me-2 text-info"></i>Akan Datang</h3>
            <span class="badge bg-info">{{ $upcoming->count() }} Tryout</span>
          </div>

          <div class="tryout-grid" data-aos="fade-up" data-aos-delay="100">
            @foreach($upcoming as $index => $exam)
              <a href="{{ route('tryout-detail', $exam->slug) }}" class="tryout-card">
                <div class="tryout-card-header">
                  <div class="tryout-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                  <div class="tryout-info">
                    <span class="tryout-badge">SNBT</span>
                    <div class="tryout-status status-upcoming">
                      <i class="fas fa-calendar-alt me-1"></i>Segera Dimulai
                    </div>
                  </div>
                </div>
                <h4 class="tryout-title">{{ $exam->title }}</h4>
                <div class="tryout-meta">
                  <div class="tryout-meta-item">
                    <i class="fas fa-star"></i>
                    Bisa Dibeli
                  </div>
                  <div class="tryout-meta-item">
                    <i class="fas fa-users"></i>
                    {{ number_format($examParticipants[$exam->id] ?? 0) }}  Peserta
                  </div>
                </div>
                <div class="tryout-countdown">
                  <small class="text-muted">
                    <i class="fas fa-play-circle"></i>
                    Mulai: {{ Carbon\Carbon::parse($exam->tanggal_dibuka)->format('d M Y H:i') }}
                  </small>
                </div>
                <div class="tryout-arrow">
                  <i class="fas fa-chevron-right"></i>
                </div>
              </a>
            @endforeach
          </div>
        </div>
      @endif

      <!-- —————— Past Try Out Section —————— -->
      @if($past->isNotEmpty())
        <div class="tryout-section mb-5">
          <div class="section-header">
            <h3><i class="fas fa-history me-2 text-secondary"></i>Tryout Sebelumnya</h3>
            <span class="badge bg-secondary">{{ $past->count() }} Tryout</span>
          </div>

          <div class="tryout-grid" data-aos="fade-up" data-aos-delay="200">
            @foreach($past as $index => $exam)
              <a href="{{ route('tryout-detail', $exam->slug) }}" class="tryout-card">
                <div class="tryout-card-header">
                  <div class="tryout-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                  <div class="tryout-info">
                    <span class="tryout-badge tryout-badge-past">SNBT</span>
                    <div class="tryout-status status-available">
                      <i class="fas fa-book-open me-1"></i>Tersedia
                    </div>
                  </div>
                </div>
                <h4 class="tryout-title">{{ $exam->title }}</h4>
                <div class="tryout-meta">
                  <div class="tryout-meta-item">
                    <i class="fas fa-dumbbell"></i>
                    Tryout
                  </div>
                  <div class="tryout-meta-item">
                    <i class="fas fa-users"></i>
                    {{ number_format($examParticipants[$exam->id] ?? 0) }}  Peserta
                  </div>
                </div>
                <div class="tryout-arrow">
                  <i class="fas fa-chevron-right"></i>
                </div>
              </a>
            @endforeach
          </div>
        </div>
      @endif

      <!-- Empty State -->
      @if($myTryouts->isEmpty() && $upcoming->isEmpty() && $ongoing->isEmpty() && $past->isEmpty())
        <div class="empty-state text-center py-5">
          <div class="empty-icon mb-3">
            <i class="fas fa-search fa-4x text-muted"></i>
          </div>
          @if($search)
            <h4>Tidak ada tryout yang ditemukan</h4>
            <p class="text-muted">Coba gunakan kata kunci yang berbeda atau hapus filter pencarian.</p>
            <a href="{{ route('tryout.index') }}" class="btn btn-primary">
              <i class="fas fa-arrow-left me-2"></i>Kembali ke Semua Tryout
            </a>
          @else
            <h4>Belum ada tryout tersedia</h4>
            <p class="text-muted">Tryout akan segera tersedia. Silakan cek kembali nanti.</p>
          @endif
        </div>
      @endif

  @auth
    </div>

    <!-- Tab Content Statistik -->
    <div class="tab-pane fade" id="tab-statistik" role="tabpanel">
         @if($subCategoryStats->count() > 0)
           <div class="row" data-aos="fade-up" data-aos-duration="500" data-aos-delay="200">
               @foreach($subCategoryStats as $stat)
               <div class="col-md-6 col-lg-4 mb-4">
                   <div class="stats-card">
                       <h6 class="stats-title">{{ $stat->name }}</h6>
                       
                       <div class="stats-item">
                           <span class="stats-label">Total Soal Dikerjakan</span>
                           <span class="stats-value">{{ $stat->total_questions }}</span>
                       </div>
                       
                       <div class="stats-item">
                           <span class="stats-label">Persentase Benar</span>
                           <span class="score-badge">{{ number_format($stat->accuracy_percentage, 1) }}%</span>
                       </div>
                       
                       <div class="stats-item">
                           <span class="stats-label">Jawaban Benar</span>
                           <span class="stats-value correct-count">{{ $stat->correct_answers }}</span>
                       </div>

                       <div class="stats-item">
                           <span class="stats-label">Jawaban Salah</span>
                           <span class="stats-value wrong-count">{{ $stat->wrong_answers }}</span>
                       </div>

                       <div class="stats-item">
                           <span class="stats-label">Tidak Dijawab</span>
                           <span class="stats-value empty-count">{{ $stat->empty_answers }}</span>
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
    <div class="tab-pane fade" id="tab-riwayat" role="tabpanel">
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
  </div>
  @endauth

</div>

@endsection

@push('addon-style')
<style>
  
</style>
@endpush

@push('addon-script')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="/vendor/jquery/jquery.min.js"></script>
<script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({ duration: 800, once: true });
  
  // Initialize Bootstrap tabs
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize all tab triggers
    var tabTriggers = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabTriggers.forEach(function(trigger) {
      new bootstrap.Tab(trigger);
    });
    
    // Tab click handlers
    document.getElementById('tryout-tab').addEventListener('click', function(e) {
      e.preventDefault();
      showTab('tab-tryout');
      setActiveTab('tryout-tab');
    });
    
    document.getElementById('statistik-tab').addEventListener('click', function(e) {
      e.preventDefault();
      showTab('tab-statistik');
      setActiveTab('statistik-tab');
    });
    
    document.getElementById('riwayat-tab').addEventListener('click', function(e) {
      e.preventDefault();
      showTab('tab-riwayat');
      setActiveTab('riwayat-tab');
    });
  });
  
  function showTab(tabId) {
    // Hide all tab panes
    document.querySelectorAll('.tab-pane').forEach(function(pane) {
      pane.classList.remove('show', 'active');
    });
    
    // Show selected tab pane
    document.getElementById(tabId).classList.add('show', 'active');
  }
  
  function setActiveTab(tabId) {
    // Remove active class from all nav links
    document.querySelectorAll('.nav-link').forEach(function(link) {
      link.classList.remove('active');
    });
    
    // Add active class to clicked tab
    document.getElementById(tabId).classList.add('active');
  }
  
  // Auto submit search on Enter
  if (document.querySelector('.search-bar input')) {
    document.querySelector('.search-bar input').addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        this.closest('form').submit();
      }
    });
  }
  
  // Live search dengan debounce (optional)
  let searchTimeout;
  if (document.querySelector('.search-bar input')) {
    document.querySelector('.search-bar input').addEventListener('input', function() {
      clearTimeout(searchTimeout);
      const form = this.closest('form');
      
      searchTimeout = setTimeout(() => {
        // Uncomment untuk auto search
        // form.submit();
      }, 500);
    });
  }
</script>
<script src="/script/navbar-scroll.js"></script>
@endpush