@extends('layouts.app')

@section('title') 
Evaluasi AI - {{ $exam->title }}
@endsection

@section('content')
<div class="container" style="margin-top: 100px">
    {{-- Header Hasil --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="text-primary mb-1">🤖 Evaluasi AI</h4>
                    <p class="text-muted mb-0">{{ $exam->title }}</p>
                </div>
                <div class="col-md-4 text-end">
                    <!-- Nilai utama -->
                    <span class="badge bg-primary fs-5">{{ $result->score }}/1000</span>
                    @if($ranking)
                        <br><small class="text-muted">Ranking {{ $ranking }}/{{ $totalParticipants }}</small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik Singkat --}}
    <div class="row mb-4">
        <div class="col-6 col-md-3">
            <!-- Soal benar -->
            <div class="card text-center border-success">
                <div class="card-body py-3">
                    <h4 class="text-success mb-1">{{ $correctCount }}</h4>
                    <small class="text-muted">Benar</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <!-- Total soal -->
            <div class="card text-center border-info">
                <div class="card-body py-3">
                    <h4 class="text-info mb-1">{{ $totalQuestions }}</h4>
                    <small class="text-muted">Total</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <!-- Persentase -->
            <div class="card text-center border-warning">
                <div class="card-body py-3">
                    <h4 class="text-warning mb-1">{{ round(($correctCount/$totalQuestions)*100) }}%</h4>
                    <small class="text-muted">Akurasi</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <!-- Ranking -->
            <div class="card text-center border-secondary">
                <div class="card-body py-3">
                    <h4 class="text-secondary mb-1">{{ $ranking ?? 'N/A' }}</h4>
                    <small class="text-muted">Ranking</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Evaluasi AI dengan Status --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="mb-3">
                <i class="fas fa-robot text-primary me-2"></i>
                Evaluasi Personal AI
            </h5>
            
            @if($aiStatus === 'loading')
                <!-- Loading state -->
                <div class="text-center p-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">AI sedang menganalisis...</p>
                </div>
            @elseif($aiStatus === 'success')
                <!-- Success state - AI response available -->
                <div class="bg-light p-4 rounded">
                    <div class="d-flex align-items-start">
                        <div class="me-3">
                            <!-- AI avatar -->
                            <div class="bg-primary rounded-circle p-2 text-white">
                                <i class="fas fa-brain"></i>
                            </div>
                        </div>
                        <div class="ai-content">
                            {!! $aiEvaluation !!}
                        </div>
                    </div>
                </div>
                
                <!-- Success indicator -->  
                <div class="mt-3">
                    <small class="text-success">
                        <i class="fas fa-check-circle me-1"></i>
                        Analisis selesai - {{ now()->format('H:i, d/m/Y') }}
                    </small>
                </div>
                {{-- Tombol Generate Ulang Rekomendasi AI --}}
                <form 
                     action="{{ route('tryout-generate-evaluation', ['exam' => $exam->id, 'id' => $result->id]) }}" 
                    method="POST"
                    class="text-center mb-4"
                >
                    @csrf
                    <button type="submit" class="btn btn-outline-primary">
                        🔄 Generate Ulang Evaluasi AI
                    </button>
                </form>
                {{-- End tombol --}}
            @else
                <!-- Error state -->
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ $aiEvaluation }}
                </div>
                
                <!-- Retry button -->
                <div class="text-center">
                    <button class="btn btn-outline-primary btn-sm" onclick="location.reload()">
                        <i class="fas fa-redo me-1"></i>
                        Coba Lagi
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Performa Per Kategori --}}
    @if($perSubcategory->count() > 0)
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="mb-3">📊 Performa Per Mata Pelajaran</h5>
            
            @foreach($perSubcategory as $sub)
            <div class="mb-3">
                <!-- Nama kategori dan persentase -->
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span><strong>{{ $sub->name }}</strong></span>
                    <span class="badge bg-{{ $sub->percentage >= 70 ? 'success' : ($sub->percentage >= 50 ? 'warning' : 'danger') }}">
                        {{ round($sub->percentage) }}%
                    </span>
                </div>
                
                <!-- Progress bar -->
                <div class="progress mb-1" style="height: 8px;">
                    <div class="progress-bar bg-{{ $sub->percentage >= 70 ? 'success' : ($sub->percentage >= 50 ? 'warning' : 'danger') }}" 
                         style="width: {{ $sub->percentage }}%"></div>
                </div>
                
                <!-- Detail benar/salah -->
                <small class="text-muted">{{ $sub->correct }} dari {{ $sub->total }} soal benar</small>
            </div>
            @endforeach
        </div>
    </div>
    @endif
 <h5 class="mb-3">📘 Performa Per Sub Bab Pembahasan</h5>
    {{-- Performa Per Sub Bab Pembahasan --}}
{{-- Performa Per SubCategory dan Lesson --}}
{{-- @if($perLesson->count() > 0)
    @foreach($perLesson as $subcategory)
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="mb-3">📘 {{ $subcategory->name }}</h5>

            @foreach($subcategory->lessons as $lesson)
            <div class="mb-3">
                <!-- Nama lesson dan persentase -->
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span><strong>{{ $lesson->name }}</strong></span>
                    <span class="badge bg-{{ $lesson->percentage >= 70 ? 'success' : ($lesson->percentage >= 50 ? 'warning' : 'danger') }}">
                        {{ round($lesson->percentage) }}%
                    </span>
                </div>

                <!-- Progress bar -->
                <div class="progress mb-1" style="height: 8px;">
                    <div class="progress-bar bg-{{ $lesson->percentage >= 70 ? 'success' : ($lesson->percentage >= 50 ? 'warning' : 'danger') }}"
                        style="width: {{ $lesson->percentage }}%"></div>
                </div>

                <!-- Detail benar/salah -->
                <small class="text-muted">{{ $lesson->correct }} dari {{ $lesson->total }} soal benar</small>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
@endif --}}


    {{-- Navigasi --}}
    <div class="text-center mb-4">
        <!-- Tombol rekomendasi jurusan -->
        <a href="{{ route('tryout-recommendation', [$exam->id, $result->id]) }}" 
           class="btn btn-success me-2">
            <i class="fas fa-graduation-cap me-1"></i>
            Lihat Rekomendasi Jurusan
        </a>
        
        <!-- Tombol kembali -->
        <a href="{{ route('tryout-result', [$exam->id, $result->id]) }}" 
           class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Kembali
        </a>
    </div>
</div>

<style>
.card { transition: transform 0.2s ease; }
.card:hover { transform: translateY(-2px); }
.progress, .progress-bar { border-radius: 8px; }
.ai-content {
    line-height: 1.6; /* default biasanya 1.6–1.8, ini lebih rapat */
    font-size: 15px;
}

.ai-content p {
    margin: 4px 0;
}

.ai-content ul,
.ai-content ol {
    margin: 5px 0;
    padding-left: 20px;
}

.ai-content li {
    margin-bottom: 5px;
}
@media (max-width: 768px) {
    .ai-content { font-size: 0.9em; }
}
</style>
@endsection
