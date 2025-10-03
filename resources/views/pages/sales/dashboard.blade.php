@extends('layouts.sales')

@section('title')
  Sales Dashboard
@endsection

@section('content')
<div class="section-content section-dashboard-home" data-aos="fade-up">
  <div class="container-fluid">
    <div class="dashboard-heading mb-4">
      <h2 class="dashboard-title">Sales Dashboard</h2>
      <p class="dashboard-subtitle text-muted">
        Pantau pendaftaran berdasarkan hasil tryout
      </p>
    </div>

    <!-- Statistik -->
<div class="row g-3 mb-4">
  <div class="col-lg-4 col-md-6">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-3 d-flex align-items-center">
        <div class=" p-2 rounded me-3">
        </div>
        <div>
          <h6 class="text-muted mb-1">Total Pendaftaran</h6>
          <h4 class="fw-bold mb-0">{{ $totalRegistrations }}</h4>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-6">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-3 d-flex align-items-center">
        <div class=" p-2 rounded me-3">
        </div>
        <div>
          <h6 class="text-muted mb-1">Sudah Dihubungi</h6>
          <h4 class="fw-bold mb-0">{{ $contactedCount }}</h4>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-6">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-3 d-flex align-items-center">
        <div class=" p-2 rounded me-3">
        </div>
        <div>
          <h6 class="text-muted mb-1">Belum Dihubungi</h6>
          <h4 class="fw-bold mb-0">{{ $notContactedCount }}</h4>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Tabel Pendaftaran -->
<div class="card border-0 shadow-sm">
  <div class="card-header bg-white border-bottom-0 pb-0">
    <h5 class="mb-0">Pendaftaran Terbaru</h5>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-bordered table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Username</th>
            <th>Judul Ujian</th>
            <th>Program Studi</th>
            <th>Periode Akademik</th>
            <th>Status Kontak</th>
            <th>Waktu Daftar</th>
          </tr>
        </thead>
        <tbody>
          @forelse($registrations as $key => $reg)
          <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $reg->result->user->name ?? '-' }}</td>
            <td>{{ $reg->result->user->username ?? '-' }}</td>
            <td>{{ $reg->result->exam->title ?? '-' }}</td>
            <td>{{ $reg->program_studi }}</td>
            <td>{{ $reg->periode_akademik }}</td>
            <td>
              <span class="badge 
                @if($reg->status == 'sudah dihubungi') bg-success
                @else bg-warning
                @endif">
                {{ ucfirst($reg->status) }}
              </span>
            </td>
            <td>{{ $reg->created_at->diffForHumans() }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center text-muted py-4">
              Belum ada data pendaftaran.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
  </div>
</div>
@endsection
