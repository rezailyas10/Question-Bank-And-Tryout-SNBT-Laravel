@extends('layouts.validator')

@section('title')
  validator Dashboard
@endsection

@section('content')
<div class="section-content section-dashboard-home" data-aos="fade-up">
  <div class="container-fluid">
    <div class="dashboard-heading mb-4">
      <h2 class="dashboard-title">Dashboard validator</h2>
      <p class="dashboard-subtitle text-muted">
        Kelola pertanyaan dan ujian Anda
      </p>
    </div>
    
    <!-- Statistics Cards -->
    <div class="dashboard-content">
      <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <div class="bg-primary bg-opacity-10 p-2 rounded">
                    <i class="fas fa-question-circle text-primary"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="card-title mb-0 text-muted">Total Pertanyaan</h6>
                  <h4 class="mb-0 fw-bold">{{ $total_questions ?? 0 }}</h4>
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
                  <h6 class="card-title mb-0 text-muted">Latihan Soal</h6>
                  <h4 class="mb-0 fw-bold text-info">{{ $latihan_soal_count ?? 0 }}</h4>
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
                  <h6 class="card-title mb-0 text-muted">Tryout</h6>
                  <h4 class="mb-0 fw-bold text-warning">{{ $tryout_count ?? 0 }}</h4>
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
                  <h6 class="card-title mb-0 text-muted">Pertanyaan Minggu Ini</h6>
                  <h4 class="mb-0 fw-bold text-warning">{{ $questions_this_week ?? 0 }}</h4>
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
                  <div class="bg-success bg-opacity-10 p-2 rounded">
                    <i class="fas fa-check-circle text-success"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="card-title mb-0 text-muted">Diterima</h6>
                  <h4 class="mb-0 fw-bold text-success">{{ $accepted_questions ?? 0 }}</h4>
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
                    <i class="fas fa-clock text-warning"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="card-title mb-0 text-muted">Ditinjau</h6>
                  <h4 class="mb-0 fw-bold text-warning">{{ $reviewed_questions ?? 0 }}</h4>
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
                  <h6 class="card-title mb-0 text-muted">Ditolak</h6>
                  <h4 class="mb-0 fw-bold text-danger">{{ $rejected_questions ?? 0 }}</h4>
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
                  <h6 class="card-title mb-0 text-muted">Tingkat penerimaan</h6>
                  <h4 class="mb-0 fw-bold text-danger">{{ $acceptance_rate ?? 0 }} %</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Questions Section -->
      <div class="row">
        <div class="col-12">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pb-0">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Pertanyaan Terbaru</h5>
                <div class="d-flex align-items-center gap-3">
                  <div class="btn-group" role="group" aria-label="Filter pertanyaan">
                    <button type="button" class="btn btn-outline-primary btn-sm active" id="all-questions">Semua</button>
                    <button type="button" class="btn btn-outline-success btn-sm" id="accepted-questions">Diterima</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body p-0">
              <div id="all-questions-content">
                @forelse ($recent_questions as $question)
                  <div class="border-bottom last:border-bottom-0">
                    <a href="{{ route('question.show', $question->id) }}" 
                       class="d-block p-3 text-decoration-none text-dark hover-bg-light">
                      <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                          @if($question->photo)
                            <img src="{{ Storage::url($question->photo) }}" 
                                 class="rounded" 
                                 style="width: 40px; height: 40px; object-fit: cover;"
                                 alt="Question">
                          @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                 style="width: 40px; height: 40px;">
                              <i class="fas fa-question text-muted"></i>
                            </div>
                          @endif
                        </div>
                        
                        <div class="flex-grow-1 min-width-0">
                          <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="mb-0 text-truncate pe-2">
                              {{ Str::limit(strip_tags($question->question_text), 50) }}
                            </h6>
                            <span class="badge 
                              @if($question->status == 'Diterima') bg-success
                              @elseif($question->status == 'Ditolak') bg-danger
                              @else bg-warning
                              @endif flex-shrink-0">
                              {{ $question->status }}
                            </span>
                          </div>
                          
                          <div class="d-flex justify-content-between align-items-center text-muted small">
                            <div class="d-flex gap-3">
                              <span>
                                <i class="fas fa-tag me-1"></i>
                                {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}
                              </span>
                              @if($question->difficulty)
                                <span>
                                  <i class="fas fa-signal me-1"></i>
                                  {{ $question->difficulty }}
                                </span>
                              @endif
                            </div>
                            <span>
                              {{ $question->updated_at ? $question->updated_at->diffForHumans() : '' }}
                            </span>
                          </div>
                        </div>
                        
                        <div class="flex-shrink-0 ms-2">
                          <i class="fas fa-chevron-right text-muted"></i>
                        </div>
                      </div>
                    </a>
                  </div>
                @empty
                  <div class="text-center py-5">
                    <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Belum ada pertanyaan</h6>
                    <p class="text-muted mb-3">Mulai buat pertanyaan pertama Anda</p>
                    <a href="{{ route('question.create') }}" class="btn btn-primary">
                      <i class="fas fa-plus me-2"></i>Buat Pertanyaan
                    </a>
                  </div>
                @endforelse
              </div>
              
              <div id="accepted-questions-content" style="display: none;">
                @forelse ($recent_accepted_questions as $question)
                  <div class="border-bottom last:border-bottom-0">
                    <a href="{{ route('question.show', $question->id) }}" 
                       class="d-block p-3 text-decoration-none text-dark hover-bg-light">
                      <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                          @if($question->photo)
                            <img src="{{ Storage::url($question->photo) }}" 
                                 class="rounded" 
                                 style="width: 40px; height: 40px; object-fit: cover;"
                                 alt="Question">
                          @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                 style="width: 40px; height: 40px;">
                              <i class="fas fa-question text-muted"></i>
                            </div>
                          @endif
                        </div>
                        
                        <div class="flex-grow-1 min-width-0">
                          <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="mb-0 text-truncate pe-2">
                              {{ Str::limit(strip_tags($question->question_text), 50) }}
                            </h6>
                            <span class="badge bg-success flex-shrink-0">
                              {{ $question->status }}
                            </span>
                          </div>
                          
                          <div class="d-flex justify-content-between align-items-center text-muted small">
                            <div class="d-flex gap-3">
                              <span>
                                <i class="fas fa-tag me-1"></i>
                                {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}
                              </span>
                              @if($question->difficulty)
                                <span>
                                  <i class="fas fa-signal me-1"></i>
                                  {{ $question->difficulty }}
                                </span>
                              @endif
                              @if($question->exam_type)
                                <span>
                                  <i class="fas fa-clipboard me-1"></i>
                                  {{ ucfirst($question->exam_type) }}
                                </span>
                              @endif
                            </div>
                            <span>
                              {{ $question->updated_at ? $question->updated_at->diffForHumans() : '' }}
                            </span>
                          </div>
                        </div>
                        
                        <div class="flex-shrink-0 ms-2">
                          <i class="fas fa-chevron-right text-muted"></i>
                        </div>
                      </div>
                    </a>
                  </div>
                @empty
                  <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h6 class="text-muted">Belum ada pertanyaan yang diterima</h6>
                    <p class="text-muted mb-3">Tunggu review dari admin untuk pertanyaan Anda</p>
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
  const allQuestionsBtn = document.getElementById('all-questions');
  const acceptedQuestionsBtn = document.getElementById('accepted-questions');
  const allQuestionsContent = document.getElementById('all-questions-content');
  const acceptedQuestionsContent = document.getElementById('accepted-questions-content');
  
  allQuestionsBtn.addEventListener('click', function() {
    // Remove active class from all buttons
    allQuestionsBtn.classList.remove('active');
    acceptedQuestionsBtn.classList.remove('active');
    
    // Add active class to clicked button
    this.classList.add('active');
    
    // Show/hide content
    allQuestionsContent.style.display = 'block';
    acceptedQuestionsContent.style.display = 'none';
  });
  
  acceptedQuestionsBtn.addEventListener('click', function() {
    // Remove active class from all buttons
    allQuestionsBtn.classList.remove('active');
    acceptedQuestionsBtn.classList.remove('active');
    
    // Add active class to clicked button
    this.classList.add('active');
    
    // Show/hide content
    allQuestionsContent.style.display = 'none';
    acceptedQuestionsContent.style.display = 'block';
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