@extends('layouts.admin')

@section('title')
  Admin Dashboard
@endsection

@section('content')
<div class="section-content section-dashboard-home" data-aos="fade-up">
  <div class="container-fluid">
    <div class="dashboard-heading mb-4">
      <h2 class="dashboard-title">Dashboard Admin</h2>
      <p class="dashboard-subtitle text-muted">
        Kelola sistem ujian dan pantau aktivitas pengguna
      </p>
    </div>
    
    <!-- Statistics Cards - First Row -->
    <div class="dashboard-content">
      <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <div class="bg-primary bg-opacity-10 p-2 rounded">
                    <i class="fas fa-users text-primary"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="card-title mb-0 text-muted">Total User</h6>
                  <h4 class="mb-0 fw-bold">{{ $total_users ?? 0 }}</h4>
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
                    <i class="fas fa-user-tie text-success"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="card-title mb-0 text-muted">Total Kontributor</h6>
                  <h4 class="mb-0 fw-bold text-success">{{ $total_kontributor ?? 0 }}</h4>
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
                    <i class="fas fa-dumbbell text-info"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="card-title mb-0 text-muted">Total Latihan Soal</h6>
                  <h4 class="mb-0 fw-bold text-info">{{ $total_latihan_soal ?? 0 }}</h4>
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
                    <i class="fas fa-trophy text-warning"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="card-title mb-0 text-muted">Total Tryout</h6>
                  <h4 class="mb-0 fw-bold text-warning">{{ $total_tryout ?? 0 }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Second Row Statistics -->
      <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <div class="bg-purple bg-opacity-10 p-2 rounded">
                    <i class="fas fa-question-circle text-purple"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="card-title mb-0 text-muted">Total Pertanyaan</h6>
                  <h4 class="mb-0 fw-bold text-purple">{{ $total_questions ?? 0 }}</h4>
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
                    <i class="fas fa-chart-line text-info"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="card-title mb-0 text-muted">Total Pengerjaan</h6>
                  <h4 class="mb-0 fw-bold text-info">{{ $total_results ?? 0 }}</h4>
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
                    <i class="fas fa-clock text-secondary"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="card-title mb-0 text-muted">Perlu Review Pertanyaan</h6>
                  <h4 class="mb-0 fw-bold text-secondary">{{ $pending_questions ?? 0 }}</h4>
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
                    <i class="fas fa-calendar-day text-success"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="card-title mb-0 text-muted">Ujian Hari Ini</h6>
                  <h4 class="mb-0 fw-bold text-success">{{ $today_exams ?? 0 }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Activity History Section -->
      <div class="row">
        <div class="col-12">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pb-0">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Aktivitas Terbaru</h5>
                <div class="d-flex align-items-center gap-3">
                  <div class="btn-group" role="group" aria-label="Filter aktivitas">
                    <button type="button" class="btn btn-outline-primary btn-sm active" id="exam-activities">Ujian</button>
                    <button type="button" class="btn btn-outline-success btn-sm" id="result-activities">Hasil</button>
                    <button type="button" class="btn btn-outline-info btn-sm" id="user-activities">User Baru</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body p-0">
              <!-- Exam Activities -->
              <div id="exam-activities-content">
                @forelse ($recent_exams as $exam)
                  <div class="border-bottom last:border-bottom-0">
                    <div class="d-block p-3">
                      <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                          <div class="bg-primary bg-opacity-10 p-2 rounded">
                            @if($exam->exam_type == 'tryout')
                              <i class="fas fa-trophy text-warning"></i>
                            @else
                              <i class="fas fa-dumbbell text-info"></i>
                            @endif
                          </div>
                        </div>
                        
                        <div class="flex-grow-1 min-width-0">
                          <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="mb-0 text-truncate pe-2">
                              {{ Str::limit($exam->title, 50) }}
                            </h6>
                            <span class="badge 
                              @if($exam->is_published) bg-success
                              @else bg-secondary
                              @endif flex-shrink-0">
                              @if($exam->is_published) Dipublikasi @else Draft @endif
                            </span>
                          </div>
                          
                          <div class="d-flex justify-content-between align-items-center text-muted small">
                            <div class="d-flex gap-3">
                              <span>
                                <i class="fas fa-tag me-1"></i>
                                {{ ucfirst($exam->exam_type) }}
                              </span>
                              <span>
                                <i class="fas fa-user me-1"></i>
                                {{ $exam->created_by }}
                              </span>
                              @if($exam->questions_count)
                                <span>
                                  <i class="fas fa-question-circle me-1"></i>
                                  {{ $exam->questions_count }} soal
                                </span>
                              @endif
                            </div>
                            <span>
                              {{ $exam->created_at ? $exam->created_at->diffForHumans() : '' }}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @empty
                  <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Belum ada ujian terbaru</h6>
                    <p class="text-muted">Aktivitas pembuatan ujian akan muncul di sini</p>
                  </div>
                @endforelse
              </div>
              
              <!-- Result Activities -->
              <div id="result-activities-content" style="display: none;">
                @forelse ($recent_results as $result)
                  <div class="border-bottom last:border-bottom-0">
                    <div class="d-block p-3">
                      <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                          <div class="bg-success bg-opacity-10 p-2 rounded">
                            <i class="fas fa-chart-line text-success"></i>
                          </div>
                        </div>
                        
                        <div class="flex-grow-1 min-width-0">
                          <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="mb-0 text-truncate pe-2">
                              {{ $result->user->name ?? 'Unknown User' }} - {{ Str::limit($result->exam->title ?? 'Unknown Exam', 30) }}
                            </h6>
                            <span class="badge 
                              @if($result->score >= 80) bg-success
                              @elseif($result->score >= 60) bg-warning
                              @else bg-danger
                              @endif flex-shrink-0">
                              {{ number_format($result->score, 1) }}
                            </span>
                          </div>
                          
                          <div class="d-flex justify-content-between align-items-center text-muted small">
                            <div class="d-flex gap-3">
                              <span>
                                <i class="fas fa-user me-1"></i>
                                {{ $result->user->username ?? 'N/A' }}
                              </span>
                              @if($result->exam)
                                <span>
                                  <i class="fas fa-tag me-1"></i>
                                  {{ ucfirst($result->exam->exam_type) }}
                                </span>
                              @endif
                            </div>
                            <span>
                              {{ $result->created_at ? $result->created_at->diffForHumans() : '' }}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @empty
                  <div class="text-center py-5">
                    <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Belum ada hasil ujian terbaru</h6>
                    <p class="text-muted">Hasil pengerjaan ujian akan muncul di sini</p>
                  </div>
                @endforelse
              </div>

              <!-- User Activities -->
              <div id="user-activities-content" style="display: none;">
                @forelse ($recent_users as $user)
                  <div class="border-bottom last:border-bottom-0">
                    <div class="d-block p-3">
                      <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                          @if($user->photos)
                            <img src="{{ Storage::url($user->photos) }}" 
                                 class="rounded-circle" 
                                 style="width: 40px; height: 40px; object-fit: cover;"
                                 alt="User">
                          @else
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 40px; height: 40px;">
                              <i class="fas fa-user text-muted"></i>
                            </div>
                          @endif
                        </div>
                        
                        <div class="flex-grow-1 min-width-0">
                          <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="mb-0 text-truncate pe-2">
                              {{ $user->name }}
                            </h6>
                            <span class="badge 
                              @if($user->roles == 'KONTRIBUTOR') bg-primary
                              @elseif($user->roles == 'ADMIN') bg-danger
                              @else bg-secondary
                              @endif flex-shrink-0">
                              {{ $user->roles }}
                            </span>
                          </div>
                          
                          <div class="d-flex justify-content-between align-items-center text-muted small">
                            <div class="d-flex gap-3">
                              <span>
                                <i class="fas fa-at me-1"></i>
                                {{ $user->username }}
                              </span>
                              <span>
                                <i class="fas fa-envelope me-1"></i>
                                {{ Str::limit($user->email, 25) }}
                              </span>
                            </div>
                            <span>
                              {{ $user->created_at ? $user->created_at->diffForHumans() : '' }}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @empty
                  <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Belum ada user baru</h6>
                    <p class="text-muted">Pendaftar baru akan muncul di sini</p>
                  </div>
                @endforelse
              </div>
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const examBtn = document.getElementById('exam-activities');
  const resultBtn = document.getElementById('result-activities');
  const userBtn = document.getElementById('user-activities');
  
  const examContent = document.getElementById('exam-activities-content');
  const resultContent = document.getElementById('result-activities-content');
  const userContent = document.getElementById('user-activities-content');
  
  function showContent(activeBtn, activeContent) {
    // Remove active class from all buttons
    [examBtn, resultBtn, userBtn].forEach(btn => btn.classList.remove('active'));
    
    // Hide all content
    [examContent, resultContent, userContent].forEach(content => {
      content.style.display = 'none';
    });
    
    // Add active class to clicked button and show corresponding content
    activeBtn.classList.add('active');
    activeContent.style.display = 'block';
  }
  
  examBtn.addEventListener('click', function() {
    showContent(this, examContent);
  });
  
  resultBtn.addEventListener('click', function() {
    showContent(this, resultContent);
  });
  
  userBtn.addEventListener('click', function() {
    showContent(this, userContent);
  });
});
</script>

<style>
.btn-group .btn.active {
  background-color: var(--bs-primary);
  border-color: var(--bs-primary);
  color: white;
}
</style>

@endsection