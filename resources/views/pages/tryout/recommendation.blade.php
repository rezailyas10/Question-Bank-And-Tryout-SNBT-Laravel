@extends('layouts.app')

@section('title') 
Rekomendasi Jurusan - {{ $exam->title }}
@endsection

@section('content')
<div class="container" style="margin-top: 100px">
    {{-- Header --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="text-success mb-1">🎓 Rekomendasi Jurusan</h4>
                    <p class="text-muted mb-0">Berdasarkan nilai tryout kamu</p>
                </div>
                <div class="col-md-4 text-end">
                    <!-- Nilai utama -->
                    <span class="badge bg-success fs-5">{{ $result->score }}/1000</span>
                    <br><small class="text-muted">{{ $result->user->name }}</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Rekomendasi AI dengan Status --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="mb-3">
                <i class="fas fa-magic text-success me-2"></i>
                Rekomendasi AI
            </h5>
            
            @if($aiStatus === 'loading')
                <!-- Loading state -->
                <div class="text-center p-4">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">AI sedang menganalisis pilihan jurusan...</p>
                </div>

            @elseif($aiStatus === 'success')
                <!-- Success state -->
                <div class="bg-light p-4 rounded">
                    <div class="d-flex align-items-start">
                        <div class="me-3">
                            <!-- AI avatar -->
                            <div class="bg-success rounded-circle p-2 text-white">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                        </div>
                        
                        <div class="ai-content">
                            {!! $aiRecommendation !!}
                        </div>
                    </div>
                </div>
                
                <!-- Success indicator -->
                <div class="mt-3 mb-3">
                    <small class="text-success">
                        <i class="fas fa-check-circle me-1"></i>
                        Rekomendasi selesai - {{ now()->format('H:i, d/m/Y') }}
                    </small>
                </div>

                {{-- Tombol Generate Ulang Rekomendasi AI --}}
                <form 
                     action="{{ route('tryout-generate-recommendation', ['exam' => $exam->id, 'id' => $result->id]) }}" 
                    method="POST"
                    class="text-center mb-4"
                >
                    @csrf
                    <button type="submit" class="btn btn-outline-primary">
                        🔄 Generate Ulang Rekomendasi AI
                    </button>
                </form>
                {{-- End tombol --}}
                
            @else
                <!-- Error state -->
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ $aiRecommendation }}
                </div>
                
                <!-- Retry button -->
                <div class="text-center mb-4">
                    <button class="btn btn-outline-success btn-sm" onclick="location.reload()">
                        <i class="fas fa-redo me-1"></i>
                        Coba Lagi
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Status Jurusan Pilihan --}}
    @if(count($majorRankings) > 0)
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="mb-3">📋 Analisis Jurusan Pilihan</h5>
            
            <div class="row">
                @foreach($majorRankings as $major)
                @php
                    // Hitung gap nilai
                    $gap = $major['passing_score'] - $result->score;
                    $isSecure = $gap <= 0; // Nilai sudah mencukupi
                    $statusColor = $isSecure ? 'success' : ($gap <= 50 ? 'warning' : 'danger');
                    $progress = $major['passing_score'] > 0 
                        ? min(100, ($result->score / $major['passing_score']) * 100) 
                        : 100;
                @endphp
                
                <div class="col-md-6 mb-3">
                    <div class="card border-{{ $statusColor }}">
                        <div class="card-body">
                            <!-- Header jurusan -->
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-0">{{ $major['major_name'] }}</h6>
                                <span class="badge bg-{{ $statusColor }}">
                                    @if($isSecure)
                                        ✅ Aman
                                    @elseif($gap <= 50)
                                        ⚠️ {{ $gap }} poin lagi
                                    @else
                                        ❌ {{ $gap }} poin lagi
                                    @endif
                                </span>
                            </div>
                            
                            <!-- Nama universitas -->
                            <p class="text-muted small mb-3">🏛️ {{ $major['university'] }}</p>
                            
                            <!-- Info nilai -->
                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <strong class="text-primary">{{ $result->score }}</strong>
                                    <br><small class="text-muted">Nilai Kamu</small>
                                </div>
                                <div class="col-4">
                                    <strong class="text-info">{{ $major['passing_score'] }}</strong>
                                    <br><small class="text-muted">Passing Score</small>
                                </div>
                                <div class="col-4">
                                    <strong class="text-warning">{{ $major['quota'] }}</strong>
                                    <br><small class="text-muted">Kuota</small>
                                </div>
                            </div>

                            <!-- Progress bar gap -->
                            <div class="mb-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <small>Progress Nilai</small>
                                    <small>{{ round($progress) }}%</small>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-{{ $statusColor }}" 
                                         style="width: {{ $progress }}%"></div>
                                </div>
                            </div>
                            
                            <!-- Status text -->
                            @if($isSecure)
                                <small class="text-success">
                                    <i class="fas fa-check me-1"></i>
                                    Nilai sudah memenuhi syarat
                                </small>
                            @else
                                <small class="text-{{ $statusColor }}">
                                    <i class="fas fa-arrow-up me-1"></i>
                                    Perlu tambahan {{ $gap }} poin
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Navigasi --}}
    <div class="text-center mb-4">
        <!-- Tombol kembali ke evaluasi -->
        <a href="{{ route('tryout-evaluation', [$exam->id, $result->id]) }}" 
           class="btn btn-primary me-2">
            <i class="fas fa-chart-line me-1"></i>
            Lihat Evaluasi AI
        </a>
        
        <!-- Tombol kembali ke hasil -->
        <a href="{{ route('tryout-result', [$exam->id, $result->id]) }}" 
           class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Kembali ke Hasil
        </a>
    </div>
</div>

<style>
/* Simple transitions */
.card { transition: transform 0.2s ease; }
.card:hover { transform: translateY(-2px); }
.progress, .progress-bar { border-radius: 6px; }
.badge { font-size: 0.75em; }
.ai-content,
.ai-evaluation-content,
.ai-recommendation-content {
    white-space: normal;
    line-height: 1.6;
}
.ai-content p,
.ai-evaluation-content p,
.ai-recommendation-content p {
    margin: 6px 0;
}
.ai-content ul,
.ai-evaluation-content ul,
.ai-recommendation-content ul,
.ai-content ol,
.ai-evaluation-content ol,
-ai-recommendation-content ol {
    margin: 4px 0;
    padding-left: 20px;
}
.ai-content li,
.ai-evaluation-content li,
.ai-recommendation-content li {
    margin-bottom: 5px;
}
@media (max-width: 768px) {
    .ai-content { font-size: 0.9em; }
}
</style>
@endsection
