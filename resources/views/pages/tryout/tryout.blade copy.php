@extends('layouts.app')

@section('title', 'Try Out')

@push('addon-style')
<style>
    /* —————— CSS Anda tetap sama —————— */
    .navbar-brand { color: white; font-weight: 600; }
    .navbar-icons { display: flex; align-items: center; }
    .navbar-icon { color: white; margin-left: 15px; font-size: 1.2rem; }
    .search-bar { background-color: #f0f0f0; border-radius: 50px; padding: 8px 15px; margin: 15px 0; }
    .search-bar input { border: none; background: transparent; width: 100%; }
    .search-bar input:focus { outline: none; }
    .task-card { border-radius: 12px; margin-bottom: 10px; padding: 15px; color: white; position: relative; }
    .task-card.yellow { background-color: var(--orange); }
    .task-card.green  { background-color: var(--green); }
    .task-card.purple { background-color: var(--purple); }
    .task-card-title { font-weight: 600; margin-bottom: 5px; }
    .task-card-button { background-color: rgba(255,255,255,0.3); border: none; border-radius: 50px; color: white; padding: 3px 12px; font-size: 0.8rem; }
    .section-title { font-weight: 600; font-size: 1.1rem; margin: 20px 0 15px 0; }
    .list-item { background-color: white; border-radius: 8px; padding: 12px; margin-bottom: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); position: relative; cursor: pointer; }
    .list-item-title { font-weight: 600; margin-bottom: 0; }
    .list-item-subtitle { color: #777; font-size: 0.8rem; }
    .toggle-icon { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: var(--primary); }
    .list-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
    @media (max-width: 768px) { .list-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')

  <div class="container py-3">
    <!-- Search Bar -->
    <div class="search-bar d-flex align-items-center" >
      <i class="fas fa-search me-2"></i>
      <input type="text" placeholder="Cari Bank Soal" />
    </div>

    <!-- Task Cards (tetap statis) -->
    <div class="row"  data-aos-delay="100">
      <div class="col-12">
        <div class="task-card yellow">
          <div class="task-card-title">Penting UTBK 27 Maret</div>
          <div class="d-flex justify-content-between align-items-center">
            <small>Countdown: 27d</small>
            <button class="task-card-button">Buka</button>
          </div>
        </div>
      </div>
    </div>
    <div class="row"  data-aos-delay="150">
      <div class="col-12">
        <div class="task-card green">
          <div class="task-card-title">UN Matematika IPA</div>
          <div class="d-flex justify-content-between align-items-center">
            <small>Countdown: 5d</small>
            <button class="task-card-button">Mulai</button>
          </div>
        </div>
      </div>
    </div>
    <div class="row"  data-aos-delay="200">
      <div class="col-12">
        <div class="task-card purple">
          <div class="task-card-title">Pembahasan UN Matematika</div>
          <div class="d-flex justify-content-between align-items-center">
            <small>26 Maret</small>
            <button class="task-card-button">Lihat</button>
          </div>
        </div>
      </div>
    </div>

    <!-- —————— Upcoming Try Out —————— -->
    @if($upcoming->isNotEmpty())
    <div class="section-title"  >
      Try Out Segera Dimulai
    </div>
    <div class="row" >
      @foreach($upcoming as $exam)
        <div class="col-12 mb-3">
          <a href="{{ route('tryout-detail', $exam->slug) }}" class="text-decoration-none">
            <div class="list-item">
              <div class="list-item-title">{{ $exam->title }}</div>
              <div class="list-item-subtitle">
                {{ \Carbon\Carbon::parse($exam->tanggal_dibuka)->translatedFormat('j F Y, H:i') }}
                @if($exam->tanggal_ditutup)
                  – {{ \Carbon\Carbon::parse($exam->tanggal_ditutup)->format('H:i') }}
                @endif
              </div>
              <div class="toggle-icon">
                <i class="fas fa-chevron-right"></i>
              </div>
            </div>
          </a>
        </div>
      @endforeach
    </div>
  @endif
  

  <!-- —————— Ongoing Try Out —————— -->
  @if($ongoing->isNotEmpty())
    <div class="section-title"  >
      Sedang Berlangsung
    </div>
    <div class="row"  >
      @foreach($ongoing as $exam)
        <div class="col-12 mb-3">
          <a href="{{ route('tryout-detail', $exam->slug) }}" class="text-decoration-none">
            <div class="list-item">
              <div class="list-item-title">{{ $exam->title }}</div>
              @if($exam->description)
                <div class="list-item-subtitle">{{ $exam->description }}</div>
              @endif
              <div class="toggle-icon">
                <i class="fas fa-chevron-right"></i>
              </div>
            </div>
          </a>
        </div>
      @endforeach
    </div>
  @endif

  <!-- —————— Past Try Out —————— -->
  @if($past->isNotEmpty())
    <div class="section-title"  >
     Try Out Sebelumnya
    </div>
    <div class="list-grid"  >
     @foreach($past as $exam)
    <div class="col-12 mb-3">
        @if(! empty($userResults[$exam->id]))
            {{-- Sudah pernah mengerjakan --}}
            <a href="{{ route('tryout-result', ['exam' => $exam->id, 'id' => $userResults[$exam->id]->id]) }}" class="text-decoration-none">
                <div class="list-item">
                    <div class="list-item-title">{{ $exam->title }}</div>
                    <div class="list-item-subtitle">
                        {{ \Carbon\Carbon::parse($exam->tanggal_dibuka)->translatedFormat('j F Y, H:i') }}
                        @if($exam->tanggal_ditutup)
                            – {{ \Carbon\Carbon::parse($exam->tanggal_ditutup)->format('H:i') }}
                        @endif
                    </div>
                    <div class="toggle-icon">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            </a>
        @else
            {{-- Belum mengerjakan --}}
            <div class="list-item">
                <a href="{{ route('tryout-detail', $exam->slug) }}" class="text-decoration-none">
                <div class="list-item-title">{{ $exam->title }}</div>
                <div class="list-item-subtitle">
                    {{ \Carbon\Carbon::parse($exam->tanggal_dibuka)->translatedFormat('j F Y, H:i') }}
                    @if($exam->tanggal_ditutup)
                        – {{ \Carbon\Carbon::parse($exam->tanggal_ditutup)->format('H:i') }}
                    @endif
                </div>
                <div class="toggle-icon">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
        @endif
    </div>
@endforeach

    </div>
  @endif

  </div>
@endsection

@push('addon-script')
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="/vendor/jquery/jquery.slim.min.js"></script>
  <script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({ duration: 800, once: true });
  </script>
  <script src="/script/navbar-scroll.js"></script>
@endpush
