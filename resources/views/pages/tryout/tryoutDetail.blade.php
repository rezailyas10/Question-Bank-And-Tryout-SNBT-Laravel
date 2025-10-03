@extends('layouts.app')

@section('title')
  Try Out
@endsection

<link rel="stylesheet" href="{{ asset('/style/tryout.css') }}?v={{ time() }}">

@section('content')
    <div class="container" id="detail-header">
      <!-- Judul & rentang tanggal -->
      <div class="detail-title">{{ $exam->title }}</div>
      <div class="detail-subtitle">
        {{ $exam->tanggal_dibuka->translatedFormat('j F Y') }}
        @if($exam->tanggal_ditutup)
          &nbsp;–&nbsp;{{ $exam->tanggal_ditutup->translatedFormat('j F Y') }}
        @endif
      </div>

      <div class="row stats-container">
        {{-- Status --}}
        <div class="col-6 col-md-3 stat-item">
          <div class="stat-value">
            @if(now()->lt($exam->tanggal_dibuka)) Belum Mulai
            @elseif(now()->between($exam->tanggal_dibuka, $exam->tanggal_ditutup)) Sedang Berlangsung
            @else Selesai
            @endif
          </div>
          <div class="stat-label">Status</div>
        </div>

        {{-- Total Waktu --}}
        <div class="col-6 col-md-3 stat-item">
          <div class="stat-value">
       {{ $questions->pluck('subCategory')->unique('id')->pluck('timer')->sum() }} menit
          </div>
          <div class="stat-label">Total Waktu</div>
        </div>

        {{-- Jumlah Soal --}}
        <div class="col-6 col-md-3 stat-item">
          <div class="stat-value">
            <div class="stat-value">
  {{ $questions->count() }} soal
</div>
          </div>
          <div class="stat-label">Jumlah Soal</div>
        </div>
      </div>
    </div>
  <div class="container py-4">
    {{-- mengkelompokan pertanyaan berdasarkan mata pelajaran --}}
    @php
  $questionsGrouped = $questions->groupBy(fn($q) => $q->subCategory->category->name);
        @endphp

  @foreach($questionsGrouped as $categoryName => $groupedQuestions)
  <div class="subject-container">
    <div class="subject-header">{{ $categoryName }}</div>

    @php
      $bySubCategory = $groupedQuestions->groupBy('sub_category_id');
    @endphp

    @foreach($bySubCategory as $subCategoryId => $subQuestions)
      @php
        $subCategory = $subQuestions->first()->subCategory;
      @endphp
      <div class="subject-item">
        <div>{{ $subCategory->name }}</div>
        <div class="subject-duration">
          <div class="duration-box">
            <i class="fas fa-clock duration-icon"></i>
            {{ $subCategory->timer ?? '-' }} menit
          </div>
          <div class="duration-box">
            <i class="fas fa-list duration-icon"></i>
            {{ $subQuestions->count() }} soal
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endforeach

   <!-- University Selection -->
<div class="university-selection">
    <div class="university-title">Target Jurusan</div>
    <div class="university-description">
        Klik untuk menentukan Jurusan Yang Dipilih
    </div>

   @if($userMajors->isNotEmpty())
    @foreach($userMajors as $index => $userMajor)
        <div class="mb-3">
            <h6>Pilihan {{ $index + 1 }}</h6>

            <div class="mb-2">
                <label for="university_{{ $index }}">Universitas</label>
                <input type="text" class="form-control" id="university_{{ $index }}" 
                    value="{{ $userMajor->major->university->name ?? 'Universitas Tidak Diketahui' }}" readonly>
            </div>

            <div>
                <label for="major_{{ $index }}">Program Studi</label>
                <input type="text" class="form-control" id="major_{{ $index }}" 
                    value="{{ $userMajor->major->name ?? 'Jurusan Tidak Diketahui' }}" readonly>
            </div>
        </div>
    @endforeach
      <a href="{{ route('user.majors.index') }}" class="btn btn-primary mt-2">
            Tambah Pilihan Jurusan
        </a>
@else
        
        

    <div class="mascot-container mt-3">
    <div class="alert alert-warning">
        Kamu belum memilih jurusan ini.
    </div>
    <a href="{{ route('user.majors.index') }}" class="btn btn-primary mt-2">
        Pilih Jurusan
    </a>
</div>
    @endif

   
</div>

    
@if (now()->lt($exam->tanggal_dibuka))
    {{-- Jika tryout belum dibuka --}}
    <div class="text-center mb-3 text-red-600 font-semibold">
        Tryout ini belum dibuka.
    </div>
    <a href="{{ route('tryout') }}" class="register-button">
        Kembali ke Halaman Tryout
    </a>

@elseif ($userExamResult)
    {{-- Jika user sudah mengerjakan --}}
    <div class="footer-note" >
        Anda telah mengerjakan tryout ini pada tanggal
        <br />
        <strong>{{ $userExamResult->created_at->format('d M Y H:i') }}</strong>
    </div>
    
    <a href="{{ route('tryout') }}" class="register-button">
        Kembali ke Halaman Tryout
    </a>

@elseif (now()->between($exam->tanggal_dibuka, $exam->tanggal_ditutup))
    {{-- Jika sekarang dalam rentang pengerjaan dan user belum mengerjakan --}}
   @auth
                        @if (Auth::check())
                           <a href="{{ route('tryout-subtest', ['exam' => $exam->slug, 'subtest' => $questions->pluck('subCategory')->filter()->first()?->id]) }}"
                            class="register-button">
                            Kerjakan Sekarang
                            </a>
                        @else
                            <a href="{{ route('login', ['redirect_to' => url()->current()]) }}" class="btn btn-warning">
                                Login untuk Mulai
                            </a>
                        @endif
                    @endauth

@else
    {{-- Jika tryout sudah ditutup dan user belum mengerjakan --}}
    <div class="text-center mb-3 text-gray-600 font-semibold">
        Tryout ini telah ditutup.
    </div>
    <a href="{{ route('tryout') }}" class="register-button">
        Kembali ke Halaman Tryout
    </a>
@endif

   
</div>


@endsection

@push('addon-script')
<script
src="https://kit.fontawesome.com/a076d05399.js"
crossorigin="anonymous"
></script>
<script src="/vendor/jquery/jquery.slim.min.js"></script>
<script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>

</script>
<script src="/script/navbar-scroll.js"></script>
@endpush