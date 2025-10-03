@extends('layouts.dashboard')

@section('title')
  User Dashboard
@endsection

@section('content')
<div class="section-content section-dashboard-home" data-aos="fade-up">
  <div class="container-fluid">
    <div class="dashboard-heading mb-4">
      <h2 class="dashboard-title">Dashboard Saya</h2>
      <p class="dashboard-subtitle text-muted">
        Pantau progress belajar dan hasil ujian Anda
      </p>
    </div>
    
    <!-- Statistics Cards -->
    <div class="dashboard-content">
      <!-- Row 1: Total Statistik -->
      <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <div class="bg-primary bg-opacity-10 p-2 rounded">
                    <i class="fas fa-clipboard-check text-primary"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="card-title mb-0 text-muted">Total Pengerjaan</h6>
                  <h4 class="mb-0 fw-bold">{{ $total_attempts ?? 0 }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <div class="bg-info bg-opacity-10 p-2 rounded">
                    <i class="fas fa-calendar-week text-info"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="card-title mb-0 text-muted">Pengerjaan Minggu Ini</h6>
                  <h4 class="mb-0 fw-bold text-info">{{ $attempts_this_week ?? 0 }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <div class="bg-success bg-opacity-10 p-2 rounded">
                    <i class="fas fa-percentage text-success"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="card-title mb-0 text-muted">Tingkat Akurasi</h6>
                  <h4 class="mb-0 fw-bold text-success">{{ $accuracy_rate ?? 0 }}%</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <div class="bg-warning bg-opacity-10 p-2 rounded">
                    <i class="fas fa-chart-line text-warning"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="card-title mb-0 text-muted">Jawaban Benar</h6>
                  <h4 class="mb-0 fw-bold text-warning">{{ $total_correct ?? 0 }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Row 2: Latihan Soal vs Tryout -->
      <div class="row g-3 mb-4">
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0">
              <h5 class="mb-0 text-primary">
                <i class="fas fa-dumbbell me-2"></i>Latihan Soal
              </h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-6">
                  <div class="text-center">
                    <h3 class="mb-0 text-primary fw-bold">{{ $latihan_soal_attempts ?? 0 }}</h3>
                    <small class="text-muted">Total Pengerjaan</small>
                  </div>
                </div>
                <div class="col-6">
                  <div class="text-center">
                    <h3 class="mb-0 text-info fw-bold">{{ $latihan_soal_this_week ?? 0 }}</h3>
                    <small class="text-muted">Minggu Ini</small>
                  </div>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-6">
                  <div class="text-center">
                    <h4 class="mb-0 text-success fw-bold">{{ number_format($avg_latihan_soal_score ?? 0, 1) }}</h4>
                    <small class="text-muted">Nilai Rata-rata</small>
                  </div>
                </div>
                <div class="col-6">
                  <div class="text-center">
                    <h4 class="mb-0 text-warning fw-bold">{{ number_format($highest_latihan_soal_score ?? 0, 1) }}</h4>
                    <small class="text-muted">Nilai Tertinggi</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0">
              <h5 class="mb-0 text-warning">
                <i class="fas fa-trophy me-2"></i>Tryout
              </h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-6">
                  <div class="text-center">
                    <h3 class="mb-0 text-warning fw-bold">{{ $tryout_attempts ?? 0 }}</h3>
                    <small class="text-muted">Total Pengerjaan</small>
                  </div>
                </div>
                <div class="col-6">
                  <div class="text-center">
                    <h3 class="mb-0 text-info fw-bold">{{ $tryout_this_week ?? 0 }}</h3>
                    <small class="text-muted">Minggu Ini</small>
                  </div>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-6">
                  <div class="text-center">
                    <h4 class="mb-0 text-success fw-bold">{{ number_format($avg_tryout_score ?? 0, 1) }}</h4>
                    <small class="text-muted">Nilai Rata-rata</small>
                  </div>
                </div>
                <div class="col-6">
                  <div class="text-center">
                    <h4 class="mb-0 text-warning fw-bold">{{ number_format($highest_tryout_score ?? 0, 1) }}</h4>
                    <small class="text-muted">Nilai Tertinggi</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Row 3: Statistik Detail -->
      <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <div class="bg-success bg-opacity-10 p-2 rounded">
                    <i class="fas fa-check-circle text-success"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="card-title mb-0 text-muted">Jawaban Benar</h6>
                  <h4 class="mb-0 fw-bold text-success">{{ $total_correct ?? 0 }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <div class="bg-danger bg-opacity-10 p-2 rounded">
                    <i class="fas fa-times-circle text-danger"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="card-title mb-0 text-muted">Jawaban Salah</h6>
                  <h4 class="mb-0 fw-bold text-danger">{{ $total_wrong ?? 0 }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <div class="bg-secondary bg-opacity-10 p-2 rounded">
                    <i class="fas fa-circle text-secondary"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="card-title mb-0 text-muted">Tidak Dijawab</h6>
                  <h4 class="mb-0 fw-bold text-secondary">{{ $total_empty ?? 0 }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <div class="bg-purple bg-opacity-10 p-2 rounded">
                    <i class="fas fa-chart-bar text-purple"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="card-title mb-0 text-muted">Total Soal Dikerjakan</h6>
                  <h4 class="mb-0 fw-bold text-purple">{{ ($total_correct + $total_wrong + $total_empty) ?? 0 }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- History Section -->
      <div class="row">
        <div class="col-12">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pb-0">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                  <i class="fas fa-history me-2"></i>Riwayat Pengerjaan Terbaru
                </h5>
               <!-- <a href="#" class="btn btn-outline-primary btn-sm">
                  Lihat Semua
                </a> -->
              </div>
            </div>
            <div class="card-body p-0">
              @forelse ($recent_evaluations as $evaluation)
                <div class="border-bottom last:border-bottom-0">
                  <div class="p-3">
                    <div class="d-flex align-items-center">
                      <div class="flex-shrink-0 me-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                             style="width: 50px; height: 50px;">
                          @if($evaluation->result->exam->exam_type == 'tryout')
                            <i class="fas fa-trophy text-warning"></i>
                          @else
                            <i class="fas fa-dumbbell text-primary"></i>
                          @endif
                        </div>
                      </div>
                      
                      <div class="flex-grow-1 min-width-0">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                          <h6 class="mb-0 text-truncate pe-2">
                            {{ $evaluation->result->exam->title ?? 'Ujian' }}
                          </h6>
                          <span class="badge 
                            @if($evaluation->score >= 80) bg-success
                            @elseif($evaluation->score >= 60) bg-warning
                            @else bg-danger
                            @endif flex-shrink-0">
                            {{ number_format($evaluation->score ?? 0, 1) }}
                          </span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center text-muted small mb-2">
                          <div class="d-flex gap-3">
                            <span>
                              <i class="fas fa-tag me-1"></i>
                              {{ ucfirst($evaluation->result->exam->exam_type ?? 'ujian') }}
                            </span>
                            <span class="text-success">
                              <i class="fas fa-check me-1"></i>
                              {{ $evaluation->correct ?? 0 }} benar
                            </span>
                            <span class="text-danger">
                              <i class="fas fa-times me-1"></i>
                              {{ $evaluation->wrong ?? 0 }} salah
                            </span>
                            @if($evaluation->empty > 0)
                              <span class="text-secondary">
                                <i class="fas fa-circle me-1"></i>
                                {{ $evaluation->empty }} kosong
                              </span>
                            @endif
                          </div>
                        </div>
                        
                        @if($evaluation->recommendation)
                          <div class="text-muted small">
                            <i class="fas fa-lightbulb me-1"></i>
                            {{ Str::limit($evaluation->recommendation, 80) }}
                          </div>
                        @endif
                        
                        <div class="text-muted small mt-1">
                          <i class="fas fa-clock me-1"></i>
                          {{ $evaluation->created_at ? $evaluation->created_at->diffForHumans() : '' }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              @empty
                <div class="text-center py-5">
                  <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                  <h6 class="text-muted">Belum ada riwayat pengerjaan</h6>
                  <p class="text-muted mb-3">Mulai kerjakan latihan soal atau tryout untuk melihat progress Anda</p>
                  <div class="d-flex gap-2 justify-content-center">
                    <a href="#" class="btn btn-primary">
                      <i class="fas fa-dumbbell me-2"></i>Latihan Soal
                    </a>
                    <a href="#" class="btn btn-warning">
                      <i class="fas fa-trophy me-2"></i>Tryout
                    </a>
                  </div>
                </div>
              @endforelse
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<style>
.hover-bg-light:hover {
  background-color: #f8f9fa !important;
}

.min-width-0 {
  min-width: 0;
}

.last\:border-bottom-0:last-child {
  border-bottom: 0 !important;
}

.card {
  transition: all 0.2s ease-in-out;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}

.bg-opacity-10 {
  background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
}

.bg-opacity-10.bg-success {
  background-color: rgba(var(--bs-success-rgb), 0.1) !important;
}

.bg-opacity-10.bg-warning {
  background-color: rgba(var(--bs-warning-rgb), 0.1) !important;
}

.bg-opacity-10.bg-danger {
  background-color: rgba(var(--bs-danger-rgb), 0.1) !important;
}

.bg-opacity-10.bg-info {
  background-color: rgba(var(--bs-info-rgb), 0.1) !important;
}

.bg-opacity-10.bg-secondary {
  background-color: rgba(var(--bs-secondary-rgb), 0.1) !important;
}

.text-purple {
  color: #6f42c1 !important;
}

.bg-purple {
  background-color: #6f42c1 !important;
}

.bg-opacity-10.bg-purple {
  background-color: rgba(111, 66, 193, 0.1) !important;
}

/* Progress Bar Styles */
.progress-sm {
  height: 6px;
}

/* Badge Styles */
.badge {
  font-size: 0.75em;
  font-weight: 600;
}

/* Card hover effects */
.card-body:hover .card-title {
  color: var(--bs-primary) !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .dashboard-subtitle {
    font-size: 0.9rem;
  }
  
  .card-body h4 {
    font-size: 1.5rem;
  }
  
  .card-body h6 {
    font-size: 0.8rem;
  }
}
</style>

@endsection