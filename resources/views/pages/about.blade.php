@extends('layouts.app')

@section('title', 'Tentang Aplikasi')

@section('content')
<div class="container py-5">
    <div class="card shadow rounded-4 border-0">
        <div class="card-body p-5">
            <h1 class="mb-4 text-primary fw-bold">Tentang Aplikasi</h1>
            <p class="fs-5">
                Aplikasi ini dibuat oleh <strong>Reza Nurfa Ilyas</strong>, seorang mahasiswa dari <strong>Institut Teknologi Indonesia</strong>, sebagai bagian dari penyusunan <strong>Tugas Akhir</strong>.
            </p>
            <p class="fs-5">
                Aplikasi ini berisi kumpulan soal-soal <strong>UTBK</strong> yang dikumpulkan dari <strong>Ruangguru</strong> dan <strong>Pahamify</strong> serta buku bank soal, dan dikemas dalam bentuk platform latihan soal yang interaktif dan mudah digunakan.
            </p>
            <p class="fs-5">
                Tujuan dari aplikasi ini adalah untuk membantu siswa dalam mempersiapkan diri menghadapi UTBK secara lebih efektif melalui pengalaman belajar digital.
            </p>
            <div class="text-muted mt-4">
                &copy; {{ date('Y') }} Reza Nurfa Ilyas – Institut Teknologi Indonesia. All rights reserved.
            </div>
        </div>
    </div>
</div>
@endsection
