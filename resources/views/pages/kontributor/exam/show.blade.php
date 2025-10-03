@extends('layouts.kontributor')

@section('title')
  Detail Bank Soal
@endsection

@section('content')
<div class="container">
    <!-- Notifikasi sukses -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
@php
    if (!function_exists('fixImageUrls')) {
        function fixImageUrls($html) {
            return preg_replace_callback(
                '/src="([^"]+)"/i',
                function ($matches) {
                    $url = $matches[1];
                    if (preg_match('/^http(s)?:\/\//', $url)) {
                        return 'src="' . $url . '"';
                    } else {
                        return 'src="' . asset($url) . '"';
                    }
                },
                $html
            );
        }
    }
@endphp

    <h1>Detail Bank Soal: {{ $exam->title }}</h1>
    
    <div class="card mb-3">
        <div class="card-body">

            <p><strong>Tipe Ujian:</strong> {{ $exam->exam_type }}</p>
            <p><strong>Published:</strong> {{ $exam->is_published ? 'Ya' : 'Tidak' }}</p>
            <p><strong>Dibuat Oleh:</strong> {{ $exam->created_by }}</p>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <a href="{{ route('question.create', ['exam_id' => $exam->id]) }}" class="btn btn-primary">Tambah Pertanyaan</a>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <a href="{{ route('questions.import.form', ['exam_id' => $exam->id]) }}" class="btn btn-primary">Import Pertanyaan</a>
        </div>
    </div>



    <hr>

    <h3>Pertanyaan Saya</h3>
    
    <!-- @php
        $userQuestions = $exam->questions->where('user_id', auth()->id());
    @endphp -->

     @php
     if (auth()->user()->roles === 'KONTRIBUTOR' && auth()->user()->is_validator == 1) {
        // Kontributor Validator -> bisa lihat semua soal
        $userQuestions = $exam->questions;
    } elseif (auth()->user()->roles === 'KONTRIBUTOR' && auth()->user()->is_validator == 0) {
        // Kontributor biasa -> hanya lihat soal miliknya sendiri
        $userQuestions = $exam->questions->where('user_id', auth()->id());
    }
@endphp
    
    @if($userQuestions->count() > 0)
        <!-- Filter berdasarkan mata pelajaran -->
        <div class="row mb-3 align-items-end">
            {{-- <div class="col-md-4">
                <label for="difficultyFilter">Filter berdasarkan Tingkat Kesulitan:</label>
                <select id="difficultyFilter" class="form-control">
                    <option value="">Semua Tingkat</option>
                    <option value="mudah">Mudah</option>
                    <option value="sedang">Sedang</option>
                    <option value="susah">Susah</option>
                </select>
            </div> --}}

            <div class="col-md-4">
                <label for="statusFilter">Filter berdasarkan Status:</label>
                <select id="statusFilter" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="ditinjau">Ditinjau</option>
                    <option value="diterima">Diterima</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>

            <div class="col-md-4">
                <form method="GET" class="d-flex gap-2">
                    <div class="flex-grow-1">
                        <label for="search">Cari Pertanyaan:</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control" placeholder="Cari pertanyaan...">
                    </div>
                    <div class="pt-4">
                        <button type="submit" class="btn btn-primary mt-1">Cari</button>
                    </div>
                </form>
            </div>
        </div>


        <!-- Tabel pertanyaan -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="questionsTable">
                <thead class="thead-dark">
                    <tr>
                        <th>No</th>
                        <th>Pertanyaan</th>
                        <th>Mata Pelajaran</th>
                        <th>Tanggal Dibuat</th>
                        <th>Pembuat</th>
                        {{-- <th>Tingkat Kesulitan</th> --}}
                        <th>Tipe Soal</th>
                      <th>Status</th>
                      <th>Komentar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                   @foreach($userQuestions as $index => $question)
    @php
        $rawHtml = old('question_text', isset($question->question_text) ? $question->question_text : '');
        $fixedHtml = fixImageUrls($rawHtml);
        $textOnly = strip_tags($fixedHtml);
        $isOnlyImage = trim($textOnly) === '';
    @endphp

    <tr class="question-row" 
        data-subcategory="{{ $question->subCategory->name ?? '' }}"
        {{-- data-difficulty="{{ strtolower($question->difficulty ?? '') }}" --}}
        data-status="{{ strtolower($question->status ?? 'ditinjau') }}">
        <td>{{ $loop->iteration }}</td>
        <td>
            <div class="question-text" style="max-height: 80px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                @if ($isOnlyImage)
                    {!! $fixedHtml !!}
                @else
                    {!! Str::limit(strip_tags($fixedHtml), 80) !!}
                @endif
            </div>
        </td>
        <td>
            <span class="badge badge-info">
                {{ $question->subCategory->name ?? 'Tidak Ditentukan' }}
            </span>
        </td>
        <td>
            <small>
                {{ $question->created_at->format('d/m/Y H:i') }}
                @if($question->updated_at != $question->created_at)
                    <br>
                    <em class="text-muted">Diupdate: {{ $question->updated_at->format('d/m/Y H:i') }}</em>
                @endif
            </small>
        </td>
        <td>
            <small>
             {{ $question->user->name}}
            </small>
        </td>
        {{-- <td>
            @if(strtolower($question->difficulty ?? '') == 'mudah')
                <span class="badge badge-success">Mudah</span>
            @elseif(strtolower($question->difficulty ?? '') == 'sedang')
                <span class="badge badge-warning">Sedang</span>
            @elseif(strtolower($question->difficulty ?? '') == 'sulit')
                <span class="badge badge-danger">Susah</span>
            @else
                <span class="badge badge-secondary">Tidak Ditentukan</span>
            @endif
        </td> --}}
        <td>
            <span class="badge badge-primary">{{ ucfirst($question->question_type) }}</span>
        </td>
        <td>
            @if(strtolower($question->status ?? 'Ditinjau') == 'diterima')
                <span class="badge badge-success">
                    <i class="fas fa-check-circle"></i> Diterima
                </span>
            @elseif(strtolower($question->status ?? 'Ditinjau') == 'ditolak')
                <span class="badge badge-danger">
                    <i class="fas fa-times-circle"></i> Ditolak
                </span>
            @elseif(strtolower($question->status ?? 'Ditinjau') == 'ditinjau')
                <span class="badge badge-warning">
                    <i class="fas fa-clock"></i> Ditinjau
                </span>
            @else
                <span class="badge badge-secondary">Tidak Diketahui</span>
            @endif
        </td> 
              <td>
    <small>
        {!! \Illuminate\Support\Str::limit($question->note ?? '-', 50, '...') !!}
    </small>
</td>
        <td>
            <div class="btn-group" role="group">
                <a href="{{ route('question.show', $question->id) }}" 
                   class="btn btn-sm btn-info" 
                   title="Detail">
                    <i class="fas fa-eye"></i> Detail
                </a>
                {{-- <a href="{{ route('question.edit', $question->id) }}" 
                    class="btn btn-sm btn-warning" 
                    title="Edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('question.destroy', $question->id) }}" 
                          method="POST" 
                          class="d-inline-block" 
                          onsubmit="return confirm('Anda yakin ingin menghapus pertanyaan ini?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" title="Hapus">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form> --}}

                       {{-- Kalau soal milik user sendiri --}}
                @if($question->user_id == auth()->id())
                    {{-- Kontributor biasa & validator sama-sama bisa edit & hapus --}}
                    @if(auth()->user()->is_validator == 0)
                        {{-- Kontributor biasa: tombol edit hanya jika status bukan diterima/ditolak --}}
                        @if(!in_array(strtolower($question->status ?? 'ditinjau'), ['diterima', 'ditolak']))
                            <a href="{{ route('question.edit', $question->id) }}" 
                                class="btn btn-sm btn-warning" 
                                title="Edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endif

                        {{-- Hapus kalau status ditinjau/ditolak --}}
                        @if(in_array(strtolower($question->status ?? 'ditinjau'), ['ditinjau', 'ditolak']))
                            <form action="{{ route('question.destroy', $question->id) }}" 
                                method="POST" 
                                class="d-inline-block" 
                                onsubmit="return confirm('Anda yakin ingin menghapus pertanyaan ini?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        @endif
                    @elseif((auth()->user()->is_validator == 1))
                        {{-- Kontributor validator: selalu bisa edit & hapus soalnya sendiri --}}
                        <a href="{{ route('question.edit', $question->id) }}" 
                            class="btn btn-sm btn-warning" 
                            title="Edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('question.destroy', $question->id) }}" 
                            method="POST" 
                            class="d-inline-block" 
                            onsubmit="return confirm('Anda yakin ingin menghapus pertanyaan ini?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" title="Hapus">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    @endif

                {{-- Kalau soal bukan milik user --}}
                @elseif($question->sub_category_id == auth()->user()->sub_category_id && auth()->user()->is_validator == 1)
                    {{-- Validator kontributor: bisa Review (edit tapi tanpa hapus) --}}
                    <a href="{{ route('question.edit', $question->id) }}" 
                        class="btn btn-sm btn-warning" 
                        title="Review">
                        <i class="fas fa-edit"></i> Review
                    </a>
                @endif


            </div>
        </td>
    </tr>
@endforeach

                </tbody>
            </table>
        </div>

        <!-- Informasi total dan status breakdown -->
        <div class="row mt-3">
            <div class="col-md-8">
                <p class="text-muted">
                    Total pertanyaan saya: <span id="totalQuestions">{{ $userQuestions->count() }}</span>
                    | Ditampilkan: <span id="visibleQuestions">{{ $userQuestions->count() }}</span>
                </p>
            </div>
            <div class="col-md-4 text-right">
                <small class="text-muted">
                    <span class="badge badge-warning">{{ $userQuestions->where('status', 'Ditinjau')->count() }}</span> Ditinjau |
                    <span class="badge badge-success">{{ $userQuestions->where('status', 'Diterima')->count() }}</span> Diterima |
                    <span class="badge badge-danger">{{ $userQuestions->where('status', 'Ditolak')->count() }}</span> Ditolak
                </small>
            </div>
        </div>

        <!-- Alert informasi status -->
        @if($userQuestions->where('status', 'Ditolak')->count() > 0)
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle"></i>
                <strong>Info:</strong> Pertanyaan yang ditolak dapat diedit dan diajukan kembali untuk review.
            </div>
        @endif
    @else
        <div class="alert alert-info">
            <h5><i class="fas fa-info-circle"></i> Belum Ada Pertanyaan</h5>
            <p>Anda belum membuat pertanyaan untuk bank soal ini. 
               <a href="{{ route('question.create', ['exam_id' => $exam->id]) }}" class="btn btn-primary btn-sm">
                   <i class="fas fa-plus"></i> Buat pertanyaan pertama Anda
               </a>
            </p>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // const difficultyFilter = document.getElementById('difficultyFilter');
    const statusFilter = document.getElementById('statusFilter');
    const questionRows = document.querySelectorAll('.question-row');
    const visibleQuestionsSpan = document.getElementById('visibleQuestions');

    function filterQuestions() {
        // const difficultyValue = difficultyFilter.value.toLowerCase();
        const statusValue = statusFilter.value.toLowerCase();
        
        let visibleCount = 0;

        questionRows.forEach(row => {
            // const rowDifficulty = row.dataset.difficulty.toLowerCase();
            const rowStatus = row.dataset.status.toLowerCase();

            // const matchDifficulty = !difficultyValue || rowDifficulty === difficultyValue;
            const matchStatus = !statusValue || rowStatus === statusValue;

            if (  matchStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        visibleQuestionsSpan.textContent = visibleCount;
    }

    // difficultyFilter.addEventListener('change', filterQuestions);
    statusFilter.addEventListener('change', filterQuestions);
});
</script>

<style>
.question-text {
    max-width: 300px;
    word-wrap: break-word;
}

.table th {
    vertical-align: middle;
    white-space: nowrap;
}

.table td {
    vertical-align: middle;
}

.btn-group .btn {
    margin-right: 2px;
}

.badge {
    font-size: 0.8em;
}

.badge i {
    margin-right: 3px;
}

.alert-info {
    border-left: 4px solid #17a2b8;
}
</style>
@endsection