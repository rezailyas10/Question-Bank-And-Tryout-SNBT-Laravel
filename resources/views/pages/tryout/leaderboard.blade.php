@extends('layouts.app')

@section('title','Leaderboard Tryout')
 <!-- Bootstrap 5 CSS -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
        />
        <!-- Font Awesome -->
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        />
        <!-- Google Fonts -->
        <link
            href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
            rel="stylesheet"
        />

        <style>
            :root {
                --primary-color: #10b981;
                --secondary-color: #6b7280;
                --warning-color: #f59e0b;
                --success-color: #10b981;
                --danger-color: #ef4444;
                --background-color: #f9fafb;
                --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1),
                    0 1px 2px 0 rgba(0, 0, 0, 0.06);
                --card-shadow-hover: 0 4px 8px -2px rgba(0, 0, 0, 0.1),
                    0 2px 4px -1px rgba(0, 0, 0, 0.05);
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: "Inter", sans-serif;
                background-color: var(--background-color);
                color: #374151;
                line-height: 1.5;
                font-size: 14px;
            }

            /* Header Styles */
            .navbar {
                background: white;
                box-shadow: var(--card-shadow);
                padding: 0.5rem 0;
            }

            .navbar-brand {
                display: flex;
                align-items: center;
                font-weight: 700;
                font-size: 1.25rem;
                color: #111827 !important;
                text-decoration: none;
            }

            .brand-icon {
                width: 32px;
                height: 32px;
                background: linear-gradient(
                    135deg,
                    var(--primary-color),
                    #059669
                );
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 8px;
            }

            .brand-icon i {
                font-size: 14px;
            }

            .nav-link {
                color: var(--secondary-color) !important;
                font-weight: 500;
                padding: 0.25rem 0.75rem !important;
                transition: all 0.3s ease;
                font-size: 0.9rem;
            }

            .nav-link:hover {
                color: var(--primary-color) !important;
            }

            .profile-avatar {
                width: 28px;
                height: 28px;
                border-radius: 50%;
                object-fit: cover;
                border: 2px solid #e5e7eb;
            }

            /* Main Content */
            .main-container {
                max-width: 1000px;
                margin: 0 auto;
                padding: 1.5rem 1rem;
            }

            .page-header {
                margin-bottom: 1.5rem;
            }

            .page-title {
                font-size: 1.5rem;
                font-weight: 700;
                color: #111827;
                margin-bottom: 0.25rem;
            }

            .page-subtitle {
                color: var(--secondary-color);
                font-size: 0.9rem;
            }

            /* User Stats Card */
            .user-stats-card {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 12px;
                padding: 1.25rem;
                margin-bottom: 1.5rem;
                color: white;
                box-shadow: var(--card-shadow-hover);
            }

            .user-stats-title {
                font-size: 1.1rem;
                font-weight: 600;
                margin-bottom: 0.75rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .user-stats-content {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1rem;
            }

            .stat-item {
                background: rgba(255, 255, 255, 0.1);
                border-radius: 8px;
                padding: 0.75rem;
                backdrop-filter: blur(10px);
            }

            .stat-label {
                font-size: 0.8rem;
                opacity: 0.8;
                margin-bottom: 0.25rem;
            }

            .stat-value {
                font-size: 1.1rem;
                font-weight: 600;
            }

            /* Acceptance Info Card */
            .acceptance-card {
                border-radius: 12px;
                padding: 1rem;
                margin-bottom: 1.5rem;
                box-shadow: var(--card-shadow);
                border-left: 4px solid;
            }

            .acceptance-card.accepted {
                background: linear-gradient(135deg, #d1fae5 0%, #ffffff 100%);
                border-left-color: var(--success-color);
            }

            .acceptance-card.rejected {
                background: linear-gradient(135deg, #fee2e2 0%, #ffffff 100%);
                border-left-color: var(--danger-color);
            }

            .acceptance-title {
                font-weight: 600;
                margin-bottom: 0.5rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .acceptance-title.accepted {
                color: #059669;
            }

            .acceptance-title.rejected {
                color: #dc2626;
            }

            .acceptance-details {
                font-size: 0.9rem;
                color: var(--secondary-color);
                line-height: 1.4;
            }

            /* Search Bar */
            .search-container {
                margin-bottom: 1.5rem;
            }

            .search-input {
                border: 1px solid #d1d5db;
                border-radius: 8px;
                padding: 0.5rem 0.75rem;
                font-size: 0.9rem;
                background: white;
                box-shadow: var(--card-shadow);
                transition: all 0.3s ease;
            }

            .search-input:focus {
                border-color: var(--primary-color);
                box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.1);
                outline: none;
            }

            .search-btn {
                background: var(--primary-color);
                border: 1px solid var(--primary-color);
                border-radius: 8px;
                color: white;
                padding: 0.5rem 1rem;
                transition: all 0.3s ease;
                font-size: 0.9rem;
            }

            .search-btn:hover {
                background: #059669;
                transform: translateY(-1px);
            }

            /* Leaderboard Styles */
            .leaderboard-item {
                background: white;
                border-radius: 12px;
                padding: 1rem;
                margin-bottom: 0.75rem;
                box-shadow: var(--card-shadow);
                transition: all 0.3s ease;
                border: 1px solid #f3f4f6;
            }

            .leaderboard-item:hover {
                box-shadow: var(--card-shadow-hover);
                transform: translateY(-1px);
            }

            .leaderboard-item.top-3 {
                background: linear-gradient(135deg, #fef3c7 0%, #ffffff 100%);
                border: 2px solid var(--warning-color);
            }

            .rank-container {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 36px;
                height: 36px;
                margin-right: 1rem;
                flex-shrink: 0;
            }

            .rank-badge {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 0.8rem;
            }

            .rank-badge.gold {
                background: linear-gradient(135deg, #fbbf24, #f59e0b);
                color: white;
            }

            .rank-badge.silver {
                background: linear-gradient(135deg, #9ca3af, #6b7280);
                color: white;
            }

            .rank-badge.bronze {
                background: linear-gradient(135deg, #d97706, #b45309);
                color: white;
            }

            .rank-badge.default {
                background: #f3f4f6;
                color: var(--secondary-color);
            }

            .participant-info {
                flex: 1;
                min-width: 0;
            }

            .participant-name {
                font-size: 1rem;
                font-weight: 600;
                color: #111827;
                margin-bottom: 0.15rem;
            }

            .participant-details {
                color: var(--secondary-color);
                font-size: 0.75rem;
                line-height: 1.3;
            }

            .score-container {
                text-align: right;
                margin-left: 1rem;
            }

            .score-value {
                font-size: 1.25rem;
                font-weight: 700;
                color: #111827;
                line-height: 1;
            }

            .top-3 .score-value {
                color: var(--warning-color);
                font-size: 1.4rem;
            }

            .score-label {
                color: var(--secondary-color);
                font-size: 0.7rem;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            /* Pagination */
            .pagination-container {
                margin-top: 2rem;
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 0.75rem;
            }

            .pagination {
                margin: 0;
            }

            .page-link {
                border: 1px solid #d1d5db;
                color: var(--secondary-color);
                padding: 0.5rem 0.75rem;
                margin: 0 1px;
                border-radius: 6px;
                font-weight: 500;
                transition: all 0.3s ease;
                font-size: 0.85rem;
            }

            .page-link:hover {
                background: var(--primary-color);
                border-color: var(--primary-color);
                color: white;
                transform: translateY(-1px);
            }

            .page-item.active .page-link {
                background: var(--primary-color);
                border-color: var(--primary-color);
                color: white;
            }

            .results-info {
                color: var(--secondary-color);
                font-size: 0.8rem;
                text-align: center;
                margin-top: 1rem;
            }

            /* Animations */
            .fade-in {
                animation: fadeIn 0.6s ease-out forwards;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .stagger-animation {
                opacity: 0;
                transform: translateY(20px);
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .main-container {
                    padding: 1rem 0.75rem;
                }

                .page-title {
                    font-size: 1.25rem;
                }

                .user-stats-content {
                    grid-template-columns: 1fr;
                }

                .leaderboard-item {
                    padding: 0.75rem;
                }

                .rank-container {
                    width: 32px;
                    height: 32px;
                    margin-right: 0.75rem;
                }

                .rank-badge {
                    width: 28px;
                    height: 28px;
                    font-size: 0.75rem;
                }

                .participant-name {
                    font-size: 0.95rem;
                }

                .participant-details {
                    font-size: 0.7rem;
                }

                .score-value {
                    font-size: 1.1rem;
                }

                .top-3 .score-value {
                    font-size: 1.25rem;
                }

                .navbar-nav {
                    margin-top: 0.5rem;
                }
            }

            @media (max-width: 576px) {
                .leaderboard-item {
                    padding: 0.6rem;
                    margin-bottom: 0.6rem;
                }

                .participant-details {
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }

                .rank-container {
                    margin-right: 0.6rem;
                }

                .score-container {
                    margin-left: 0.6rem;
                }
            }
        </style>

@section('content')
<div class="main-container" style="margin-top:100px">
  <!-- Header -->
  <div class="page-header mb-4">
    <h1 class="page-title">Leaderboard {{ $exam->title }}</h1>
    <p class="page-subtitle">
      Total Peserta: <strong>{{ $totalFiltered ?? $results->total() }}</strong>
    </p>
  </div>

  <!-- User Stats Card -->
  <div class="user-stats-card">
    <div class="user-stats-title">
      <i class="fas fa-user-circle"></i>
      Posisi Anda: {{ $result->user->name }}
    </div>
    <div class="user-stats-content">
      <div class="stat-item">
        <div class="stat-label">Peringkat Anda</div>
        <div class="stat-value">#{{ $userRank }} dari {{ $totalFiltered ?? $results->total() }} peserta</div>
      </div>
      <div class="stat-item">
        <div class="stat-label">Skor Anda</div>
        <div class="stat-value">{{ number_format($result->score, 2) }}</div>
      </div>
      @if($majorId)
      <div class="stat-item">
        <div class="stat-label">Filter Aktif</div>
        <div class="stat-value">{{ $userMajors->firstWhere('major_id', $majorId)->major->name ?? 'Jurusan Dipilih' }}</div>
      </div>
      @endif
    </div>
  </div>

  <!-- Acceptance Info Card (only show when major is selected) -->
  @if($acceptanceInfo)
  <div class="acceptance-card {{ $acceptanceInfo['is_accepted'] ? 'accepted' : 'rejected' }}">
    <div class="acceptance-title {{ $acceptanceInfo['is_accepted'] ? 'accepted' : 'rejected' }}">
      <i class="fas {{ $acceptanceInfo['is_accepted'] ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
      {{ $acceptanceInfo['is_accepted'] ? 'DITERIMA' : 'TIDAK DITERIMA' }}
    </div>
    <div class="acceptance-details">
      <strong>{{ $acceptanceInfo['major_name'] }}</strong> - {{ $acceptanceInfo['university_name'] }}<br>
      Kuota: {{ number_format($acceptanceInfo['quota']) }} peserta | 
      Peringkat Anda: #{{ $acceptanceInfo['user_rank'] }} dari {{ $acceptanceInfo['total_participants'] }} peserta<br>
      @if($acceptanceInfo['is_accepted'])
        <span class="text-success fw-semibold">✓ Anda berada dalam kuota penerimaan jurusan ini</span>
      @else
        <span class="text-danger fw-semibold">✗ Anda berada di luar kuota penerimaan jurusan ini</span>
      @endif
    </div>
  </div>
  @endif

  <!-- Filter Form -->
  <form id="filterForm" method="get" class="row g-3 mb-4">
    <!-- Pilih Jurusan: auto submit on change -->
    <div class="col-md-4">
      <select 
        name="major_id" 
        class="form-select"
        onchange="document.getElementById('filterForm').submit()"
      >
        <option value="">-- Semua Jurusan --</option>
        @foreach($userMajors as $um)
          <option 
            value="{{ $um->major_id }}"
            @selected($majorId == $um->major_id)
          >
            {{ $um->major->university->name }} • {{ $um->major->name }}
          </option>
        @endforeach
      </select>
    </div>

    <!-- Search (Enter atau click button akan submit) -->
    <div class="col-md-8">
      <div class="input-group">
        <input
          type="text"
          name="search"
          value="{{ $search }}"
          class="form-control"
          placeholder="Cari nama, jurusan, universitas..."
          onkeypress="if(event.key==='Enter'){ document.getElementById('filterForm').submit(); }"
        />
        <button 
          class="btn btn-outline-secondary" 
          type="button" 
          onclick="document.getElementById('filterForm').submit()"
        >
          <i class="fas fa-search"></i>
        </button>
      </div>
    </div>
  </form>

  <!-- Leaderboard List -->
  <div class="leaderboard-container mb-4">
  @foreach($results as $idx => $r)
    @php
        $absRank = ($results->currentPage() - 1) * $results->perPage() + $idx + 1;
        $user    = $r->user;

        // Cari UserMajor yang sesuai dengan major_id pilihan (jika ada)
        if (isset($majorId) && $majorId) {
            $um2 = $user->userMajor->firstWhere('major_id', $majorId);
        }

        // Jika tidak ada yang cocok, fallback ke yang pertama
        $um2 = $um2 ?? $user->userMajor->first() ?? null;

        // Ambil nama jurusan dan universitas
        $major = $um2 && $um2->major ? $um2->major->name : '-';
        $univ  = $um2 && $um2->major && $um2->major->university
                 ? $um2->major->university->name
                 : '-';

        // Tandai jika ini adalah hasil yang sedang dilihat user
        $highlight = $r->id === $result->id;
    @endphp

    <div
        id="result-{{ $r->id }}"
        class="leaderboard-item d-flex align-items-center py-3 px-2 border-bottom
               {{ $highlight ? 'bg-warning bg-opacity-25' : '' }}
               {{ $absRank <= 3 ? 'top-3' : '' }}"
    >
        <div class="rank-container me-3">
            @if($absRank === 1)
                <div class="rank-badge gold"><i class="fas fa-crown"></i></div>
            @elseif($absRank === 2)
                <div class="rank-badge silver">2</div>
            @elseif($absRank === 3)
                <div class="rank-badge bronze">3</div>
            @else
                <div class="rank-badge default">{{ $absRank }}</div>
            @endif
        </div>

        <div class="participant-info flex-grow-1">
            <div class="participant-name fw-semibold">
              {{ $user->name }}
              @if($highlight)
                <span class="badge bg-primary ms-2">Anda</span>
              @endif
            </div>
            <div class="participant-details text-muted small">
                {{ $univ }} • {{ $major }}
            </div>
        </div>

        <div class="score-container text-end">
            <div class="score-value fw-bold">{{ number_format($r->score, 2) }}</div>
            <div class="score-label small text-muted">Score</div>
        </div>
    </div>
@endforeach

  </div>

  <!-- Pagination -->
  <div class="pagination-container">
    {{ $results->links('pagination::bootstrap-5') }}
  </div>
  <div class="results-info text-center mt-2">
    Menampilkan {{ $results->firstItem() }}–{{ $results->lastItem() }}
    dari {{ $totalFiltered ?? $results->total() }} peserta
    @if($majorId || $search)
      <span class="text-muted">(dengan filter)</span>
    @endif
  </div>
</div>

<script>
  // Scroll ke hasil highlight
  document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('result-{{ $result->id }}');
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
  });
</script>
@endsection