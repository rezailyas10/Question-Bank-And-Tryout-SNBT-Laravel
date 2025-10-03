@php
    switch (auth()->user()->roles) {
        case 'ADMIN':
            $layout = 'layouts.admin';
            break;
        case 'KONTRIBUTOR':
            $layout = 'layouts.kontributor';
            break;
        case 'VALIDATOR':
            $layout = 'layouts.validator';
            break;
        default:
            $layout = 'layouts.app'; // fallback
    }
@endphp

@extends($layout)


@section('title', 'Detail Soal')


@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-question-circle"></i> Detail Soal #{{ $question->id }}</h1>
        <div>
        </div>
    </div>

    <!-- Debug Info -->
   <!-- <div class="alert alert-info">
        <strong>Debug Info:</strong><br>
        Question Type: {{ $question->question_type }}<br>
        Has Multiple Choice: {{ $question->multipleChoice ? 'Yes' : 'No' }}<br>
        Has Multiple Option: {{ $question->multipleOption ? 'Yes' : 'No' }}<br>
        Has Essay: {{ $question->essay ? 'Yes' : 'No' }}<br>
        @if($question->multipleChoice)
            MC ID: {{ $question->multipleChoice->id }}<br>
        @endif
        @if($question->multipleOption)
            MO ID: {{ $question->multipleOption->id }}<br>
        @endif
        @if($question->essay)
            Essay ID: {{ $question->essay->id }}<br>
        @endif
    </div> -->

    <div class="row">
        <!-- Detail Soal -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Soal</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong><i class="fas fa-tag"></i> Tipe Soal:</strong>
                            <span class="badge badge-info ms-2">{{ ucfirst($question->question_type) }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-book"></i> Materi:</strong>
                            <span class="text-muted">{{ $question->lesson ?? '-' }}</span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong><i class="fas fa-folder"></i> Kategori:</strong>
                            <span class="text-muted">{{ $question->subCategory?->name ?? '-' }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-clipboard-list"></i> Ujian:</strong>
                            <a href="{{ route('exam.show', $question->exam->slug) }}" class="text-decoration-none">
                                {{ $question->exam->title }}
                            </a>
                        </div>
                    </div>
                    @php
                        $hasTable = str_contains($question->question_text, '<table');
                    @endphp

                    <div class="mb-3">
                    <strong><i class="fas fa-question"></i> Soal:</strong>
                    <div class="border rounded p-3 mt-2 bg-light content-container">
                        @if ($hasTable)
                            {{-- Tambahkan wrapper styling untuk tabel --}}
                            <div class="table-responsive">
                                {{-- Tambahkan style table agar rapi --}}
                                <style>
                                    table {
                                        width: 100%;
                                        border-collapse: collapse;
                                        margin-top: 10px;
                                    }
                                    th, td {
                                        border: 1px solid #ccc;
                                        padding: 8px;
                                        text-align: center;
                                    }
                                    
                                </style>
                                {!! $question->question_text !!}
                            </div>
                        @else
                            {!! $question->question_text !!}
                        @endif
                    </div>
                </div>

                    @if($question->explanation)
                    @php
                        $hasExplanationTable = str_contains($question->explanation, '<table');
                    @endphp

                    <div class="mb-3">
                        <strong><i class="fas fa-lightbulb"></i> Penjelasan:</strong>
                        <div class="border rounded p-3 mt-2 bg-light content-container">
                            @if ($hasExplanationTable)
                                <div class="table-responsive">
                                    <style>
                                        table {
                                            width: 100%;
                                            border-collapse: collapse;
                                            margin-top: 10px;
                                        }
                                        th, td {
                                            border: 1px solid #ccc;
                                            padding: 8px;
                                            text-align: center;
                                        }
                                    </style>
                                    {!! $question->explanation !!}
                                </div>
                            @else
                                {!! $question->explanation !!}
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>



        <!-- Jawaban -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle"></i> Jawaban</h5>
                </div>
                <div class="card-body">
                    @if($question->question_type === 'pilihan_ganda')
                        <h6><i class="fas fa-list"></i> Pilihan Ganda</h6>
                        @if($question->multipleChoice)
                            <div class="mb-3">
                                @if($question->multipleChoice->option1)
                                    <div class="form-check mb-2 {{ $question->multipleChoice->correct_answer === $question->multipleChoice->option1 ? 'bg-success bg-opacity-10 border border-success rounded p-2' : '' }}">
                                        <input class="form-check-input" type="radio" disabled {{ $question->multipleChoice->correct_answer === $question->multipleChoice->option1 ? 'checked' : '' }}>
                                        <label class="form-check-label w-100">
                                            <strong>A.</strong> 
                                            <div class="content-container">{!! $question->multipleChoice->option1 !!}</div>
                                            @if($question->multipleChoice->correct_answer === $question->multipleChoice->option1)
                                                <i class="fas fa-check text-success ms-2"></i>
                                            @endif
                                        </label>
                                    </div>
                                @endif
                                
                                @if($question->multipleChoice->option2)
                                    <div class="form-check mb-2 {{ $question->multipleChoice->correct_answer === $question->multipleChoice->option2 ? 'bg-success bg-opacity-10 border border-success rounded p-2' : '' }}">
                                        <input class="form-check-input" type="radio" disabled {{ $question->multipleChoice->correct_answer === $question->multipleChoice->option2 ? 'checked' : '' }}>
                                        <label class="form-check-label w-100">
                                            <strong>B.</strong> 
                                            <div class="content-container">{!! $question->multipleChoice->option2 !!}</div>
                                            @if($question->multipleChoice->correct_answer === $question->multipleChoice->option2)
                                                <i class="fas fa-check text-success ms-2"></i>
                                            @endif
                                        </label>
                                    </div>
                                @endif
                                
                                @if($question->multipleChoice->option3)
                                    <div class="form-check mb-2 {{ $question->multipleChoice->correct_answer === $question->multipleChoice->option3 ? 'bg-success bg-opacity-10 border border-success rounded p-2' : '' }}">
                                        <input class="form-check-input" type="radio" disabled {{ $question->multipleChoice->correct_answer === $question->multipleChoice->option3 ? 'checked' : '' }}>
                                        <label class="form-check-label w-100">
                                            <strong>C.</strong> 
                                            <div class="content-container">{!! $question->multipleChoice->option3 !!}</div>
                                            @if($question->multipleChoice->correct_answer === $question->multipleChoice->option3)
                                                <i class="fas fa-check text-success ms-2"></i>
                                            @endif
                                        </label>
                                    </div>
                                @endif
                                
                                @if($question->multipleChoice->option4)
                                    <div class="form-check mb-2 {{ $question->multipleChoice->correct_answer === $question->multipleChoice->option4 ? 'bg-success bg-opacity-10 border border-success rounded p-2' : '' }}">
                                        <input class="form-check-input" type="radio" disabled {{ $question->multipleChoice->correct_answer === $question->multipleChoice->option4 ? 'checked' : '' }}>
                                        <label class="form-check-label w-100">
                                            <strong>D.</strong> 
                                            <div class="content-container">{!! $question->multipleChoice->option4 !!}</div>
                                            @if($question->multipleChoice->correct_answer === $question->multipleChoice->option4)
                                                <i class="fas fa-check text-success ms-2"></i>
                                            @endif
                                        </label>
                                    </div>
                                @endif
                                
                                @if($question->multipleChoice->option5)
                                    <div class="form-check mb-2 {{ $question->multipleChoice->correct_answer === $question->multipleChoice->option5 ? 'bg-success bg-opacity-10 border border-success rounded p-2' : '' }}">
                                        <input class="form-check-input" type="radio" disabled {{ $question->multipleChoice->correct_answer === $question->multipleChoice->option5 ? 'checked' : '' }}>
                                        <label class="form-check-label w-100">
                                            <strong>E.</strong> 
                                            <div class="content-container">{!! $question->multipleChoice->option5 !!}</div>
                                            @if($question->multipleChoice->correct_answer === $question->multipleChoice->option5)
                                                <i class="fas fa-check text-success ms-2"></i>
                                            @endif
                                        </label>
                                    </div>
                                @endif
                            </div>
                            <div class="alert alert-success">
                                <strong><i class="fas fa-star"></i> Jawaban Benar:</strong> {!! $question->multipleChoice->correct_answer !!}
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Belum ada jawaban untuk soal ini.
                            </div>
                        @endif

                    @elseif($question->question_type === 'pilihan_majemuk')
                        <h6><i class="fas fa-check-square"></i> Pilihan Majemuk</h6>
                        @if($question->multipleOption)
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Pernyataan</th>
                                            <th class="text-center" style="width: 100px;">Jawaban</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($question->multipleOption->multiple1)
                                            <tr>
                                                <td>
                                                    <div class="content-container">{!! $question->multipleOption->multiple1 !!}</div>
                                                </td>
                                                <td class="text-center">
                                                    @if($question->multipleOption->{'yes/no1'} === 'yes')
                                                        <span class="badge bg-success">Yes</span>
                                                    @else
                                                        <span class="badge bg-danger">No</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        
                                        @if($question->multipleOption->multiple2)
                                            <tr>
                                                <td>
                                                    <div class="content-container">{!! $question->multipleOption->multiple2 !!}</div>
                                                </td>
                                                <td class="text-center">
                                                    @if($question->multipleOption->{'yes/no2'} === 'yes')
                                                        <span class="badge bg-success">Yes</span>
                                                    @else
                                                        <span class="badge bg-danger">No</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        
                                        @if($question->multipleOption->multiple3)
                                            <tr>
                                                <td>
                                                    <div class="content-container">{!! $question->multipleOption->multiple3 !!}</div>
                                                </td>
                                                <td class="text-center">
                                                    @if($question->multipleOption->{'yes/no3'} === 'yes')
                                                        <span class="badge bg-success">Yes</span>
                                                    @else
                                                        <span class="badge bg-danger">No</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        
                                        @if($question->multipleOption->multiple4)
                                            <tr>
                                                <td>
                                                    <div class="content-container">{!! $question->multipleOption->multiple4 !!}</div>
                                                </td>
                                                <td class="text-center">
                                                    @if($question->multipleOption->{'yes/no4'} === 'yes')
                                                        <span class="badge bg-success">Yes</span>
                                                    @else
                                                        <span class="badge bg-danger">No</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        
                                        @if($question->multipleOption->multiple5)
                                            <tr>
                                                <td>
                                                    <div class="content-container">{!! $question->multipleOption->multiple5 !!}</div>
                                                </td>
                                                <td class="text-center">
                                                    @if($question->multipleOption->{'yes/no5'} === 'yes')
                                                        <span class="badge bg-success">Yes</span>
                                                    @else
                                                        <span class="badge bg-danger">No</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Belum ada jawaban untuk soal ini.
                            </div>
                        @endif

                    @elseif($question->question_type === 'isian')
                        <h6><i class="fas fa-file-alt"></i> Essay</h6>
                        @if($question->essay)
                            <div class="border rounded p-3 bg-light">
                                <strong>Jawaban:</strong><br>
                                <div class="content-container">{{ $question->essay->text }}</div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Belum ada jawaban untuk soal ini.
                            </div>
                        @endif

                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Tipe soal tidak dikenali atau belum ada jawaban.
                        </div>
                    @endif
                </div>
            </div>
         <div class="mb-3">
                    <strong><i class="fas fa-question"></i> Komentar</strong>
                    <div class="border rounded p-3 mt-2 bg-light content-container">
                            {!! $question->note !!}
    
                    </div>
                </div>
    </div>
        </div>

    <!-- Action Buttons -->
     @if ($question->exam->exam_type == 'latihan soal')
            <div class="d-flex justify-content-between align-items-center">
        <a href="{{ route('exam.show', $question->exam->slug) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Ujian
        </a>
        @else
             <div class="d-flex justify-content-between align-items-center">
        <a href="{{ route('tryout.show', $question->exam->slug) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Ujian
        </a>
        @endif
        
@if($question->user_id == auth()->id())
    <div class="btn-group">
        <a href="{{ route('question.edit', $question->id) }}" class="btn btn-outline-warning">
            <i class="fas fa-edit"></i> Edit Soal
        </a>
        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
            <i class="fas fa-trash"></i> Hapus Soal
        </button>
    </div>
@elseif(auth()->user()->roles == 'VALIDATOR' || auth()->user()->is_validator == 1)
    <div class="btn-group">
        <a href="{{ route('question.edit', $question->id) }}" class="btn btn-outline-warning">
            <i class="fas fa-edit"></i> Review Soal
        </a>
    </div>
@endif

    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus soal ini?</p>
                <p class="text-muted">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('question.destroy', $question->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .card {
        transition: transform 0.2s;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .form-check-input:disabled {
        opacity: 0.5;
    }
    
    .bg-success.bg-opacity-10 {
        background-color: rgba(25, 135, 84, 0.1) !important;
    }

    /* Strict image size control */
    .content-container {
        max-width: 100%;
        overflow: hidden;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .content-container img {
        max-width: 100% !important;
        max-height: 500px !important;
        width: auto !important;
        height: auto !important;
        object-fit: contain !important;
        display: block !important;
        margin: 5px auto !important;
    }

    .content-container p {
        margin-bottom: 0.5rem;
    }

    .content-container table {
        max-width: 100% !important;
        table-layout: fixed !important;
        word-wrap: break-word;
    }

    .content-container table td,
    .content-container table th {
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
        max-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Force image resize using important */
    img {
        max-width: 100% !important;
        max-height: 200px !important;
        width: auto !important;
        height: auto !important;
        object-fit: contain !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .content-container img,
        img {
            max-height: 150px !important;
        }
    }

    /* Form check label full width */
    .form-check-label {
        width: 100%;
        display: block;
    }

    /* Table responsive improvements */
    .table-responsive {
        overflow-x: auto;
    }
</style>