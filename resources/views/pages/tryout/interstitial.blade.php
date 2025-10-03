@extends('layouts.app')

@section('title','Subtest Selesai')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
      
      @if(isset($error))
        <div class="alert alert-danger text-center">
          <i class="fas fa-exclamation-triangle mb-2"></i>
          <div>{{ $error }}</div>
        </div>
      @endif

      @if(!isset($error))
        <div class="card shadow-sm border-0">
          <div class="card-body text-center py-5">
            
            <!-- Success Icon -->
            <div class="mb-4">
              <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center" 
                   style="width: 80px; height: 80px;">
                <i class="fas fa-check text-white" style="font-size: 2rem;"></i>
              </div>
            </div>

            <!-- Completion Message -->
            <h4 class="text-dark mb-3">Subtest Selesai!</h4>
            <p class="text-muted mb-4">
              Anda telah menyelesaikan subtest<br>
              <strong>"{{ $subCategory->name }}"</strong>
            </p>

            <!-- Progress Indicator (optional - if you want to show progress) -->
            <div class="mb-4">
              <small class="text-muted">Bagus! Lanjutkan ke subtest selanjutnya</small>
            </div>

            <!-- Action Buttons -->
            @if($next)
              <div class="d-grid gap-2">
                <a href="{{ route('tryout-subtest', ['exam'=>$exam->slug,'subtest'=>$next->id]) }}" 
                   class="btn btn-primary ">
                  <i class="fas fa-arrow-right me-2"></i>
                  Lanjut ke "{{ $next->name }}"
                </a>
              </div>
            @else
              <div class="mb-3">
                <div class="alert alert-info">
                  <i class="fas fa-trophy me-2"></i>
                  Selamat! Anda telah menyelesaikan semua subtest
                </div>
              </div>
              <a href="{{ route('tryout')}}" class="btn btn-success ">
                <i class="fas fa-home me-2"></i>
                Kembali ke Daftar Tryout
              </a>
            @endif

          </div>
        </div>

        <!-- Additional Info Card -->
        <div class="card mt-3 border-0 bg-light">
          <div class="card-body text-center py-3">
            <small class="text-muted">
              <i class="fas fa-info-circle me-1"></i>
              Pastikan koneksi internet stabil untuk melanjutkan
            </small>
          </div>
        </div>
      @endif

    </div>
  </div>
</div>

<style>
.card {
  transition: transform 0.2s ease-in-out;
}

.card:hover {
  transform: translateY(-2px);
}

.btn {
  transition: all 0.2s ease-in-out;
}

.btn:hover {
  transform: translateY(-1px);
}

.bg-success {
  background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
}

.btn-primary {
  background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
  border: none;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
  box-shadow: 0 4px 8px rgba(0,123,255,0.3);
}
</style>
@endsection