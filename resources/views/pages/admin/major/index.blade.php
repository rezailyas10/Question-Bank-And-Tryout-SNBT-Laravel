@extends('layouts.admin')

@section('title')
Program Studi
@endsection
<link rel="stylesheet" href="{{ asset('/style/kontributor-exam.css') }}?v={{ time() }}">
@section('content')
<div class="container">
    <!-- Stats Cards -->
    <div class="stats-row">
        <div class="stat-card">
            <p class="stat-number">{{ $majors->count() }}</p>
            <p class="stat-label">Total Program Studi</p>
        </div>
        <div class="stat-card">
            <p class="stat-number">{{ $sub_universities->count() }}</p>
            <p class="stat-label">Total Universitas</p>
        </div>
        <div class="stat-card">
            <p class="stat-number">{{ number_format($majors->avg('quota')) }}</p>
            <p class="stat-label">Rata-rata Daya Tampung</p>
        </div>
        <div class="stat-card">
            <p class="stat-number">{{ number_format($majors->avg('passing_score')) }}</p>
            <p class="stat-label">Rata-rata Skor UTBK</p>
        </div>
    </div>

    <!-- Success Notification -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Create Button -->
    <a href="{{ route('major.create') }}" class="btn-create">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Buat Program Studi Baru
    </a>

    <!-- Filter Section -->
    <div class="filter-container">
        <form method="GET" action="{{ route('major.index') }}" id="filterForm">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="university_id" class="filter-label">Filter Universitas</label>
                    <select name="university_id" id="university_id" class="filter-select">
                        <option value="">-- Semua Universitas --</option>
                        @foreach($sub_universities as $university)
                            <option value="{{ $university->id }}" {{ request('university_id') == $university->id ? 'selected' : '' }}>
                                {{ $university->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="search" class="filter-label">Cari Program Studi</label>
                    <input type="text" name="search" id="search" class="filter-input" value="{{ request('search') }}" placeholder="Masukkan nama program studi...">
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn-filter-apply">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>
                        Terapkan
                    </button>
                    <a href="{{ route('major.index') }}" class="btn-filter-reset">Reset</a>
                </div>
            </div>
        </form>
    </div>

    

    <!-- Table Container -->
    <div class="table-container">
        <div class="table-header">
            <h1>Daftar Program Studi</h1>
        </div>

        @if($majors->count() > 0)
        <table class="enhanced-table">
            <thead>
                <tr class="text-center">
                    <th>Nama Program Studi</th>
                    <th>Jenjang</th>
                    <th>Skor UTBK Rata-Rata</th>
                    <th>Daya Tampung</th>
                    <th>Peminat</th>
                    <th>Universitas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($majors as $major)
                <tr>
                    <td>
                        <strong>{{ $major->name }}</strong>
                    </td>
                    <td>
                        <span class="level-badge level-{{ strtolower($major->level) }}">{{ $major->level }}</span>
                    </td>
                    <td class="text-center">
                        <span class="score-badge">{{ $major->passing_score }}</span>
                    </td>
                    <td class="text-center">
                        <span class="quota-count">{{ number_format($major->quota) }}</span>
                    </td>
                    <td class="text-center">
                        <span class="peminat-count">{{ number_format($major->peminat) }}</span>
                    </td>
                    <td>
                        <div class="university-info">
                            <span>{{ $major->university->name }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('major.edit', $major->id) }}" class="btn-modern btn-edit">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708L10.5 8.207l-3-3L12.146.146zM11.207 9l-3-3-6.364 6.364a.5.5 0 0 0-.146.353V14.5a.5.5 0 0 0 .5.5h1.793a.5.5 0 0 0 .353-.146L11.207 9z"/>
                                </svg>
                                Update
                            </a>
                            <form action="{{ route('major.destroy', $major->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-modern btn-delete" onclick="return confirm('Apakah anda yakin ingin menghapus?')">
                                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">🎓</div>
            <h3>Belum ada Program Studi</h3>
            <p>Mulai dengan membuat program studi pertama Anda</p>
            <a href="{{ route('major.create') }}" class="btn-create" style="margin-top: 20px;">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                </svg>
                Buat Program Studi Pertama
            </a>
        </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($majors->hasPages())
    <div class="pagination-container">
        {{ $majors->links() }}
    </div>
    @endif
</div>

<style>
/* Filter Container Styles */
.filter-container {
    background: white;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
}

.filter-row {
    display: flex;
    gap: 20px;
    align-items: end;
    flex-wrap: wrap;
}

.filter-group {
    flex: 1;
    min-width: 200px;
}

.filter-label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    font-size: 0.9em;
}

.filter-select, .filter-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.95em;
    transition: all 0.2s ease;
    background: white;
}

.filter-select:focus, .filter-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filter-actions {
    display: flex;
    gap: 12px;
}

.btn-filter-apply {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-filter-apply:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-filter-reset {
    background: #f3f4f6;
    color: #6b7280;
    border: 2px solid #e5e7eb;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-filter-reset:hover {
    background: #e5e7eb;
    color: #374151;
}

/* Level Badge Styles */
.level-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8em;
    font-weight: 600;
    text-transform: uppercase;
}

.level-s1 { background: #dbeafe; color: #1e40af; }
.level-s2 { background: #dcfce7; color: #166534; }
.level-s3 { background: #fef3c7; color: #92400e; }
.level-d3 { background: #fce7f3; color: #be185d; }
.level-d4 { background: #e0e7ff; color: #3730a3; }

/* Score Badge */
.score-badge {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9em;
}

/* Count Styles */
.quota-count {
    background: #fef3c7;
    color: #92400e;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.85em;
    font-weight: 500;
}

.peminat-count {
    background: #ddd6fe;
    color: #6b46c1;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.85em;
    font-weight: 500;
}

/* University Info */
.university-info {
    display: flex;
    align-items: center;
    gap: 12px;
}


/* Pagination Container */
.pagination-container {
    margin-top: 32px;
    display: flex;
    justify-content: center;
}


@media (max-width: 768px) {
    .filter-row {
        flex-direction: column;
    }
    
    .filter-actions {
        width: 100%;
        justify-content: stretch;
    }
    
    .btn-filter-apply, .btn-filter-reset {
        flex: 1;
        justify-content: center;
    }
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.getElementById('filterForm');
        const selects = filterForm.querySelectorAll('select');

        selects.forEach(function(select) {
            select.addEventListener('change', function () {
                filterForm.submit();
            });
        });
    });
</script>
@endsection