@extends('layouts.app')

@section('title')
Hasil Ujian & Evaluasi AI
@endsection

@section('content')
<div class="container" style="margin-top: 100px">
    {{-- Header Hasil --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h4 class="text-primary mb-3">📊 Hasil Ujian</h4>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nama:</strong> {{ auth()->user()->name }}</p>
                    <p><strong>Ujian:</strong> {{ $result->exam->title }}</p>
                    <p><strong>Nilai:</strong>
                        <span class="badge bg-{{ $result->score >= 700 ? 'success' : ($result->score >= 500 ? 'warning' : 'danger') }} fs-6">
                            {{ $result->score }}/100
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Benar:</strong> {{ $correctCount }} dari {{ $totalQuestions }} soal</p>
                    <p><strong>Akurasi:</strong> {{ round(($correctCount / $totalQuestions) * 100) }}%</p>
                    <p><strong>Tanggal:</strong> {{ $result->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Analisis Per Materi --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="mb-4">📚 Analisis Performa per Materi</h5>
            @foreach($resultDetails->groupBy('question.lesson') as $lesson => $details)
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0"><strong>{{ $lesson ?? 'Materi Umum' }}</strong></h6>
                        @php
                            $correct    = $details->where('correct', true)->count();
                            $total      = $details->count();
                            $percentage = round(($correct / $total) * 100);
                        @endphp
                        <span class="badge bg-{{ $percentage >= 80 ? 'success' : ($percentage >= 60 ? 'warning' : 'danger') }}">
                            {{ $percentage }}% ({{ $correct }}/{{ $total }})
                        </span>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="progress mb-2" style="height: 8px;">
                        <div
                            class="progress-bar bg-{{ $percentage >= 80 ? 'success' : ($percentage >= 60 ? 'warning' : 'danger') }}"
                            role="progressbar"
                            style="width: {{ $percentage }}%"
                            aria-valuenow="{{ $percentage }}"
                            aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                    </div>

                    {{-- Tombol Nomor Soal --}}
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        @foreach($details as $detail)
                            @php
                                $globalIndex = $resultDetails->search(fn($d) => $d->id === $detail->id);
                                $btnClass = is_null($detail->correct)
                                            ? 'btn-outline-secondary'
                                            : ($detail->correct ? 'btn-success' : 'btn-danger');
                            @endphp
                            <a style="margin-right: 8px; margin-bottom: 8px;" href="{{ route('exam.review', ['exam' => $result->exam_id, 'question' => $detail->question_id]) }}"
                               class="btn {{ $btnClass }} btn-sm"
                               title="{{ $detail->correct ? 'Benar' : 'Salah' }}">
                                {{ $globalIndex + 1 }}
                            </a>
                        @endforeach
                    </div>

                    <small class="text-muted">
                        <i class="fas fa-chart-pie me-1"></i>
                        Tingkat Penguasaan:
                        @if($percentage >= 80)
                            <span class="text-success">Sangat Baik</span>
                        @elseif($percentage >= 60)
                            <span class="text-warning">Cukup Baik</span>
                        @else
                            <span class="text-danger">Perlu Diperbaiki</span>
                        @endif
                    </small>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Evaluasi AI --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="text-info mb-3">🤖 Evaluasi AI</h5>

            {{-- Tombol Generate Ulang (hanya muncul jika sudah ada setidaknya satu record) --}}
            @if($allEvaluations->count() >= 1)
                <form action="{{ route('bank-soal-generate-ai', ['exam' => $result->exam_id, 'id' => $result->id]) }}" method="POST" class="mb-3">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary">
                        🔄 Generate Ulang Evaluasi AI
                    </button>
                </form>
            @endif

            {{-- Tampilkan Evaluasi Terbaru --}}
            @if($existingEvaluation)
                <div class="ai-evaluation-content bg-light p-3 rounded mb-4">
                    {!! $existingEvaluation->evaluation !!}
                </div>
            @endif

            {{-- Riwayat Evaluasi AI --}}
           {{-- Tombol untuk show/hide Riwayat Evaluasi AI --}}
 {{-- Tombol Show/Hide History --}}
            @if($allEvaluations->count() > 1)
                <button
                    class="btn btn-link mb-3"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#historySection"
                    aria-expanded="false"
                    aria-controls="historySection">
                    📚 Tampilkan/Sembunyikan Riwayat Evaluasi AI
                </button>
            @endif

            {{-- Section Collapse Riwayat --}}
            <div class="collapse" id="historySection">
                @if($allEvaluations->count() > 1)
                    <hr>
                    <h6 class="text-secondary mb-2">📚 Riwayat Evaluasi AI</h6>
                    <ul class="list-group">
                        @foreach($allEvaluations->skip(1) as $item)
                            <li class="list-group-item">
                                <small class="text-muted">
                                    Dibuat: {{ $item->created_at->format('d/m/Y H:i') }}
                                </small>
                                <button
                                    class="btn btn-sm btn-link"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#eval-{{ $item->id }}"
                                    aria-expanded="false"
                                    aria-controls="eval-{{ $item->id }}">
                                    Lihat Detail
                                </button>
                                <div class="collapse mt-2" id="eval-{{ $item->id }}">
                                    <div class="ai-content">
                                        {!! $item->evaluation !!}
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
        </div>
    </div>

    {{-- Rekomendasi Jurusan untuk TKA
@if($needsMajorRecommendation && $majorRecommendation)
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <h5 class="text-success mb-3">🎓 Rekomendasi Jurusan & Alasan</h5>
      <div class="bg-light p-4 rounded">
        {!! $majorRecommendation !!}
      </div>
    </div>
  </div>
@elseif(!$needsMajorRecommendation)
  <div class="alert alert-info mb-4">
    Rekomendasi jurusan hanya tersedia untuk Tes Kemampuan Akademik.
  </div>
@endif --}}

  

    {{-- Tombol Aksi --}}
    <div class="text-center mb-4">
        <a href="{{ route('exam', $result->exam->slug) }}" 
           class="btn btn-primary me-2">
            <i class="fas fa-redo me-1"></i> Ulangi Ujian
        </a>
    </div>
</div>

<style>
    .card {
        transition: transform 0.2s ease;
    }
    .card:hover {
        transform: translateY(-2px);
    }
    .progress, .progress-bar {
        border-radius: 6px;
    }
    .badge {
        font-size: 0.75em;
    }

    /* Konten evaluasi AI: rapat spacing */
    .ai-content,
    .ai-evaluation-content {
        white-space: normal;
        line-height: 1.3;
    }
    .ai-content p,
    .ai-evaluation-content p {
        margin: 4px 0;
    }
    .ai-content ul,
    .ai-evaluation-content ul,
    .ai-content ol,
    .ai-evaluation-content ol {
        margin: 4px 0;
        padding-left: 20px;
    }
    .ai-content li,
    .ai-evaluation-content li {
        margin-bottom: 3px;
    }


/* Print Styles */
@media print {
    .btn, .card:hover {
        display: none !important;
        transform: none !important;
    }
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }
}
</style>

@push('addon-script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
@endpush
@endsection