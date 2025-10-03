@extends('layouts.app')
@section('title')
  Hasil Tryout {{ $result->exam->title }}
@endsection

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .main-card {
            background: linear-gradient(135deg, #3369a7 0%, #357abd 100%);
              /* background: linear-gradient(135deg, #20c997, #17a085); */
            border-radius: 15px;
            color: white;
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .score-display {
            font-size: 3.5rem;
            font-weight: bold;
            margin: 20px 0;
        }
        
        .stat-item {
            text-align: center;
            padding: 15px;
        }
        
        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 18px;
        }
        
        .stat-icon.success {
            background-color: rgba(255,255,255,0.3);
        }
        
        .stat-icon.danger {
            background-color: rgba(255,82,82,0.8);
        }
        
        .ranking-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .ranking-title {
            color: #20c997;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .choice-item {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid #dee2e6;
        }
        
        .choice-item.active {
            border-left-color: #20c997;
        }
        
        .rank-badge {
            background-color: #6c757d;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
        }
        
        .subject-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .score-bar {
            height: 8px;
            background-color: #dee2e6;
            border-radius: 4px;
            overflow: hidden;
            margin: 10px 0;
        }
        
        .score-fill {
            height: 100%;
            border-radius: 4px;
        }
        
        .score-fill.excellent {
            background-color: #28a745;
        }
        
        .score-fill.good {
            background-color: #ffc107;
        }
        .score-fill.average {
    background-color: #fd7e14; /* Atau warna lain yang kamu mau */
}
        
        .score-fill.poor {
            background-color: #dc3545;
        }
        
        .btn-custom {
            border-radius: 8px;
            font-weight: 500;
            padding: 12px 24px;
        }
        
        .btn-primary-custom {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
        }
        
        .btn-success-custom {
            background-color: #20c997;
            border-color: #20c997;
            color: white;
        }
        
        .btn-warning-custom {
            background-color: #fd7e14;
            border-color: #fd7e14;
            color: white;
        }
        
        .feedback-section {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .pdf-download-btn {
    color: #dc3545; /* warna merah (default bootstrap danger) */
    border-color: #dc3545;
    transition: all 0.3s ease;
}

.pdf-download-btn:hover {
    color: white !important;
    background-color: #dc3545;
}
        @media (max-width: 768px) {
            .score-display {
                font-size: 2.5rem;
            }
            
            .stat-item {
                padding: 10px;
            }
            
            .main-card {
                padding: 20px;
            }
        }
    </style>
    <style>
/* Additional CSS for enhanced ranking display */
.university-ranking-section, .major-ranking-section {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    background: #f8f9fa;
}

.ranking-subtitle {
    color: #495057;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
    padding-bottom: 0.5rem;
}

.choice-item.accepted {
    border-left: 4px solid #28a745;
    background: linear-gradient(135deg, #d4edda 0%, #ffffff 100%);
}

.choice-item.not-accepted {
    border-left: 4px solid #dc3545;
    background: linear-gradient(135deg, #f8d7da 0%, #ffffff 100%);
}

.rank-badge.bg-primary {
    background-color: #0d6efd !important;
}

.rank-badge.bg-success {
    background-color: #198754 !important;
}

.rank-badge.bg-secondary {
    background-color: #6c757d !important;
}

.progress {
    background-color: #e9ecef;
}

@media (max-width: 768px) {
    .university-ranking-section, .major-ranking-section {
        padding: 0.75rem;
    }
    
    .ranking-subtitle {
        font-size: 0.9rem;
    }
}
</style>
</head>
<body>
    <div class="container mt-5">
        <!-- Main Score Card -->
        <div class="main-card" style="margin-top: 100px">
            <div class="row align-items-center">
                <div class="col-12 text-center mb-3">
                    <small class="badge bg-light text-dark px-3 py-2 rounded-pill">
                        {{ $result->exam->title }}
                    </small>
                </div>
            </div>
            
            <div class="text-center mb-4">
                <h3 class="mb-2">{{ auth()->user()->name }}</h3>
                <div class="score-display">{{ $result->score }}</div>
                <p class="mb-0">Nilai rata-rata</p>
            </div>

            <div class="row">
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-icon success">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="fw-bold fs-4">{{ $correctCount }}</div>
                        <small>Benar</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-icon danger">
                            <i class="fas fa-times"></i>
                        </div>
                        <div class="fw-bold fs-4">{{ $inCorrectCount }}</div>
                        <small>Salah</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-icon success">
                            <i class="fas fa-question"></i>
                        </div>
                        <div class="fw-bold fs-4">{{ $nullCount }}</div>
                        <small>Kosong</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-icon success">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="fw-bold fs-4">{{ number_format($accuracy, 2) }} %</div>
                        <small>Akurasi</small>
                    </div>
                </div>
            </div>
        </div>

   <div class="ranking-section">
    <!-- Overall Tryout Ranking -->
    <h4 class="ranking-title">
        Rank {{ $ranking ?? '-' }} 
        <small class="text-muted">dari {{ $totalParticipants }} peserta</small>
    </h4>
    @if($result->score != 0)
        <a href="{{ route('tryout-leaderboard', ['exam' => $exam->id, 'id' => $result->id]) }}"
           class="btn btn-success-custom w-100 btn-custom mb-4">
            <i class="fas fa-trophy me-2"></i> Lihat Leaderboard Tryout dan Ranking Jurusan
             <br><small>Lihat Ranking Nilai Tryout Berdasarkan Nilai dan Jurusan Yang Kamu Pilih</small>
        </a> 
    @else
    <p>Nilai Belum Diberikan, Ranking Jurusan dibawah Belum Diseusaikan Dengan Nilai</p>
   

    <!-- University Rankings -->
    @if(!empty($universityRankings))
        <div class="university-ranking-section mb-4">
            <h6 class="ranking-subtitle mb-3">
                <i class="fas fa-university me-2"></i>Ranking per Universitas
            </h6>
            @foreach ($universityRankings as $index => $university)
                <div class="choice-item {{ $index === 0 ? 'active' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-primary fw-bold mb-1">
                                <i class="fas fa-university me-1"></i>Universitas
                            </p>
                            <h6 class="mb-1">{{ $university['university_name'] }}</h6>
                            <small class="text-muted">Ranking di universitas ini</small>
                        </div>
                        <div class="text-end">
                            <span class="rank-badge bg-primary">{{ $university['rank'] ?? '-' }}</span>
                            <br><small class="text-muted">dari {{ $university['total'] ?? '-' }}</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
     @endif

    <!-- Major Rankings -->
    @if(!empty($majorRankings))
        <div class="major-ranking-section">
            <h6 class="ranking-subtitle mb-3">
                <i class="fas fa-graduation-cap me-2"></i>Ranking per Jurusan
            </h6>
            @foreach ($majorRankings as $index => $major)
                <div class="choice-item {{ $index === 0 ? 'active' : '' }} {{ $major['is_accepted'] ? 'accepted' : 'not-accepted' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-1">
                                <p class="text-success fw-bold mb-0 me-2">
                                    <i class="fas fa-graduation-cap me-1"></i>Pilihan {{ $index + 1 }}
                                </p>
                                @if($major['is_accepted'])
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Diterima
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times me-1"></i>Tidak Diterima
                                    </span>
                                @endif
                            </div>
                            <h6 class="mb-1">{{ $major['major_name'] }}</h6>
                            <small class="text-muted">
                                {{ $major['university'] ?? '' }}
                                @if($major['quota'] > 0)
                                    • Kuota: {{ number_format($major['quota']) }}
                                @endif
                            </small>
                        </div>
                        <div class="text-end">
                            <span class="rank-badge {{ $major['is_accepted'] ? 'bg-success' : 'bg-secondary' }}">
                                {{ $major['rank'] ?? '-' }}
                            </span>
                            <br><small class="text-muted">dari {{ $major['total'] ?? '-' }}</small>
                        </div>
                    </div>
                    @if($major['quota'] > 0)
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar {{ $major['is_accepted'] ? 'bg-success' : 'bg-danger' }}" 
                                 style="width: {{ min(100, ($major['rank'] / $major['quota']) * 100) }}%">
                            </div>
                        </div>
                        <small class="text-muted">
                            @if($major['is_accepted'])
                                Anda dalam {{ $major['quota'] }} terbaik untuk jurusan ini
                            @else
                                Perlu masuk {{ $major['quota'] }} terbaik untuk diterima
                            @endif
                        </small>
                    @endif
                </div>
            @endforeach
            @endif
        </div>
    

    <div class="row g-2 mb-4">
        <div class="col-md-6">
            <a href="{{ route('tryout-recommendation', ['exam' => $exam->id, 'id' => $result->id]) }}" class="btn btn-warning-custom w-100 btn-custom">
              <i class="fas fa-chart-line me-2"></i>Rekomendasi Jurusan dan Universitas
               <br><small>Lihat Rekomendasi Jurusan Berdasarkan Nilai Tryout mu</small>
            </a>
             
         
        </div>
        <div class="col-md-6">
         <a href="{{ route('tryout-evaluation', ['exam' => $exam->id, 'id' => $result->id]) }} "class="btn btn-primary-custom w-100 btn-custom mb-4">
        <i class="fas fa-chart-bar me-2"></i>Lihat Evaluasi Tryout
       <br><small>Analisis setiap pertanyaan pada subtes tryout</small>
    </a>
        </div>
         <div class="col-md-12 d-flex justify-content-center">
    <a href="{{ route('tryout.download-pdf', ['exam' => $exam->id, 'result' => $result->id]) }}" 
       class="btn btn-outline-danger btn-custom pdf-download-btn text-center">
        <i class="fas fa-file-pdf me-2"></i>Download Hasil PDF
        <br><small>Unduh laporan lengkap hasil tryout</small>
    </a>
    @php
    use App\Models\RegistrationITI;
    $registered = RegistrationITI::where('result_id', $result->id)->exists();
@endphp

@if(!$registered)
        <a href="{{ route('register-iti.create', $result->id) }}"
           class="btn btn-outline-primary btn-custom text-center ms-3">
            <i class="fas fa-user-plus me-2"></i>Ajukan Pendaftaran di ITI
            <br><small>Belum daftar</small>
        </a>
    @else
        <button class="btn btn-success btn-custom text-center ms-3" disabled>
            <i class="fas fa-check-circle me-2"></i>Sudah Daftar
            <br><small>Terima kasih, Silahkan Menunggu Info Lebih Lanjut</small>
        </button>
    @endif
</div>

    </div>

    
</div> 


     <div class="row mt-4">
    <div class="col-12">
        <h5 class="fw-bold mb-3">Rekap Per Subtes</h5>

        @foreach ($perSubcategory as $sub)
            @php
                $color = $sub->average_score >= 750
                    ? 'text-success'
                    : ($sub->average_score >= 550
                        ? 'text-warning'
                        : 'text-danger');
                $fillClass = $sub->average_score >= 300
                    ? 'good'
                    : ($sub->average_score >= 150
                        ? 'average'
                        : 'poor');
            @endphp

            <div class="subject-card mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">{{ $sub->name }}</h6>
                    <span class="{{ $color }} fw-bold">
                        Skor: {{ number_format($sub->average_score, 2) }}
                    </span>
                </div>
               <small class="text-muted">
                    Benar: {{ $sub->correct }} / {{ $sub->total }} |
                    Salah: {{ $sub->wrong }} |
                    Kosong: {{ $sub->empty }}
                </small>
                <div class="score-bar my-2">
                 <div class="score-fill {{ $fillClass }}" style="width: {{ max(1, $sub->percentage) }}%"></div>
                </div>
                <a href="{{ route('tryout.review', [
                        'exam'        => $result->exam_id,
                        'subCategory' => $sub->id,
                        'question'    => $sub->firstQId
                    ]) }}"
                   class="btn btn-sm btn-outline-primary mt-2">
                    Lihat Pembahasan
                </a>
            </div>
        @endforeach
    </div>
</div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>