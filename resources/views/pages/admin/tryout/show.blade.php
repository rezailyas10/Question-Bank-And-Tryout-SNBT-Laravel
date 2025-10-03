@extends('layouts.admin')

@section('title')
  Detail tryout
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

    <h1>Detail tryout: {{ $exam->title }}</h1>
    
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

   @php
    use Illuminate\Support\Facades\Auth;
@endphp

@if ($exam->created_by === Auth::user()->name)
    <div class="row mb-3">
        <div class="col-md-6">
            <a href="{{ route('tryout.edit', $exam->id) }}" class="btn btn-secondary">Edit Tryout</a>
        </div>
        <div class="col-md-6 text-right">
            <form action="{{ route('tryout.destroy', $exam->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Anda yakin ingin menghapus tryout ini?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger">Hapus Tryout</button>
            </form>
        </div>
    </div>
@endif

    <hr>

    <h3>Pertanyaan</h3>
    
    @if($exam->questions->count() > 0)
        <!-- Filter: subCategory, status, dan pembuat -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="subCategoryFilter">Filter berdasarkan Mata Pelajaran:</label>
                <select id="subCategoryFilter" class="form-control">
                    <option value="">Semua Mata Pelajaran</option>
                    @foreach($exam->questions->groupBy('subCategory.name') as $subCategoryName => $questions)
                        <option value="{{ strtolower($subCategoryName) }}">{{ $subCategoryName }} ({{ $questions->count() }})</option>
                    @endforeach
                </select>
            </div>
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
                <label for="creatorFilter">Filter Pembuat:</label>
                <select id="creatorFilter" class="form-control">
                    <option value="">Semua</option>
                    <option value="mine">Hanya Saya</option>
                </select>
            </div>
             <div class="col-md-12">
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
                        <th>Pembuat</th>
                        <th>Tipe Soal</th>
                        <th>Status</th>
                        <th>Komentar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exam->questions as $index => $question)
                        @php
                            $rawHtml = old('question_text', $question->question_text ?? '');
                            $fixedHtml = fixImageUrls($rawHtml);
                            $textOnly = strip_tags($fixedHtml);
                            $isOnlyImage = trim($textOnly) === '';
                        @endphp

                        <tr class="question-row" 
                            data-subcategory="{{ strtolower($question->subCategory->name ?? '') }}"
                            data-status="{{ strtolower($question->status ?? 'ditinjau') }}"
                            data-user-id="{{ $question->user_id }}">
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
                                    {{ $question->user->name ?? 'Unknown' }}
                                    <br>
                                    <em>{{ $question->created_at->format('d/m/Y') }}</em>
                                </small>
                            </td>
                            <td>
                                <span class="badge badge-primary">{{ ucfirst($question->question_type) }}</span>
                            </td>
                            <td>
                                @if($question->status == 'Diterima')
                                    <span class="badge badge-success">Diterima</span>
                                @elseif($question->status == 'Ditolak')
                                    <span class="badge badge-danger">Ditolak</span>
                                @elseif($question->status == 'Ditinjau')
                                    <span class="badge badge-warning">Ditinjau</span>
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
                                    @if ($question->user_id == Auth::id())
                                        <a href="{{ route('question.edit', $question->id) }}" 
                                        class="btn btn-sm btn-warning" 
                                        title="Edit">
                                            <i class="fas fa-edit"></i> Update
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
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Informasi total -->
        <div class="row mt-3">
            <div class="col-md-12">
                <p class="text-muted">
                    Total: <span id="totalQuestions">{{ $exam->questions->count() }}</span> pertanyaan
                    | Ditampilkan: <span id="visibleQuestions">{{ $exam->questions->count() }}</span> pertanyaan
                </p>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <p>Tidak ada pertanyaan. <a href="{{ route('question.create', ['exam_id' => $exam->id]) }}">Buat pertanyaan baru</a></p>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const subCategoryFilter = document.getElementById('subCategoryFilter');
    const statusFilter = document.getElementById('statusFilter');
    const creatorFilter = document.getElementById('creatorFilter');
    const questionRows = document.querySelectorAll('.question-row');
    const visibleQuestionsSpan = document.getElementById('visibleQuestions');

    // ambil user id saat ini untuk filter "Hanya Saya"
    const currentUserId = @json(auth()->user()->id);

    function filterQuestions() {
        const subCategoryValue = subCategoryFilter.value.toLowerCase();
        const statusValue = statusFilter.value.toLowerCase();
        const creatorValue = creatorFilter.value; // "" atau "mine"
        
        let visibleCount = 0;

        questionRows.forEach(row => {
            const rowSubCategory = row.dataset.subcategory;
            const rowStatus = row.dataset.status;
            const rowUserId = row.dataset.userId;

            const matchSubCategory = !subCategoryValue || rowSubCategory.includes(subCategoryValue);
            const matchStatus = !statusValue || rowStatus === statusValue;
            let matchCreator = true;
            if (creatorValue === 'mine') {
                matchCreator = String(rowUserId) === String(currentUserId);
            }
            // jika semua cocok, tampilkan
            if (matchSubCategory && matchStatus && matchCreator) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        visibleQuestionsSpan.textContent = visibleCount;
    }

    subCategoryFilter.addEventListener('change', filterQuestions);
    statusFilter.addEventListener('change', filterQuestions);
    creatorFilter.addEventListener('change', filterQuestions);
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
</style>
@endsection
