@extends('layouts.app')

@section('title')
  Bank Soal Detail
@endsection

<link rel="stylesheet" href="{{ asset('/style/bank-soal.css') }}?v={{ time() }}">

<!-- Notifikasi sukses -->
@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@section('content')
   <!-- Navbar -->
   <nav class="navbar">
       <div class="container">
           <a href="{{ route('dashboard') }}" class="brand-text">Bank Soal</a>
       </div>
   </nav>

   <div class="container mt-4">
       <!-- Sub Kategori Header -->
       <div class="sub-category-header">
           <h3>{{ $subCategories->name }}</h3>
           @if($subCategories->photo)
              <img src="{{ asset('storage/' . $subCategories->photo) }}" alt="{{ $subCategories->name }}" style="max-width: 150px; border-radius: 6px;">
           @endif
       </div>

       @php
           // Pastikan $exams Collection; jika array, ubah ke collect()
           $collectionExams = $exams instanceof \Illuminate\Support\Collection ? $exams : collect($exams);
           [$doneExams, $notDoneExams] = $collectionExams->partition(function($exam) use ($userResults) {
               return ! empty($userResults[$exam->id]);
           });
       @endphp

       {{-- Bagian: Soal yang sudah dikerjakan --}}
       @if($doneExams->isNotEmpty())
           <h4 class="mb-3">Sudah Dikerjakan</h4>
           <div class="exam-grid">
               @foreach($doneExams as $index => $exam)
                   <div class="exam-card">
                       <div class="exam-number">
                           <div class="exam-badge">{{ $exam->exam_type }}</div>
                           <div class="exam-number-display">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                       </div>
                       <div class="exam-content">
                           <div class="exam-status">Sudah Dikerjakan</div>
                           <div class="exam-title">{{ $exam->title }}</div>
                           <div class="exam-meta">
                               <div class="exam-meta-item">
                                   <span>⭐</span>
                                   <span>Nilai: {{ $userResults[$exam->id]->score }}</span>
                               </div>
                               <div class="exam-meta-item">
                                   <span>👥</span>
                                   <span>{{ $examParticipants[$exam->id] ?? 0 }} Peserta</span>
                               </div>
                           </div>
                       </div>
                       <div class="exam-actions">
                           <div class="score-badge">Score: {{ $userResults[$exam->id]->score }}</div>
                           <a href="{{ route('bank-soal-result', ['exam' => $exam->id, 'id' => $userResults[$exam->id]->id]) }}" class="btn btn-secondary">
                               Lihat Detail
                           </a>
                       </div>
                   </div>
               @endforeach
           </div>
       @endif

       {{-- Bagian: Soal yang belum dikerjakan --}}
       @if($notDoneExams->isNotEmpty())
           <h4 class="mt-4 mb-3">Belum Dikerjakan</h4>
           <div class="exam-grid">
               @foreach($notDoneExams as $index => $exam)
                   <div class="exam-card">
                       <div class="exam-number">
                           <div class="exam-badge">{{ $exam->exam_type }}</div>
                           <div class="exam-number-display">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                       </div>
                       <div class="exam-content">
                           <div class="exam-status">Belum Dikerjakan</div>
                           <div class="exam-title">{{ $exam->title }}</div>
                           <div class="exam-meta">
                               <div class="exam-meta-item">
                                   <span>⭐</span>
                                   <span>Belum Dikerjakan</span>
                               </div>
                               <div class="exam-meta-item">
                                   <span>👥</span>
                                   <span>{{ $examParticipants[$exam->id] ?? 0 }} Peserta</span>
                               </div>
                           </div>
                       </div>
                       <div class="exam-actions">
                           @auth
                               <a href="{{ route('exam', $exam->slug) }}" class="btn btn-primary">
                                   Mulai Latihan
                               </a>
                           @else
                               <a href="{{ route('login', ['redirect_to' => url()->current()]) }}" class="btn btn-warning">
                                   Login Dulu
                               </a>
                           @endauth
                       </div>
                   </div>
               @endforeach
           </div>
       @endif

       {{-- Jika tidak ada sama sekali --}}
       @if($doneExams->isEmpty() && $notDoneExams->isEmpty())
           <div class="empty-message">
               Belum ada latihan soal untuk sub kategori ini.
           </div>
       @endif

   </div>
@endsection
@push('addon-script')
<script>
 
</script>
@endpush