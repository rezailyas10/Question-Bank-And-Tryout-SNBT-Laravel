@extends('layouts.app')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> 
@section('title', 'Blog - Artikel & Panduan')
<style> 
.blog-image {
    height: 250px;
    object-fit: cover;
    width: 100%;
}

@media (max-width: 576px) {
    .blog-image {
        height: 180px;
    }
}

</style>
@section('content')
<div class="container" style="margin-top: 100px">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="hero-section text-center py-5 px-4 rounded-4 mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h1 class="display-4 fw-bold mb-3">Blog & Artikel</h1>
                <p class="lead mb-4">Temukan panduan lengkap dan artikel menarik untuk menambah wawasan Anda</p>
                <div class="d-flex justify-content-center">
                    <div class="bg-white bg-opacity-20 backdrop-blur rounded-pill px-4 py-2">
                        <span style="color: black">{{ $blogs->total() }} Total Artikel</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('blog') }}" class="row g-3">
                        <div class="col-lg-5 col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 ps-0" name="search" 
                                       value="{{ request('search') }}" placeholder="Cari artikel, panduan...">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4">
                            <select class="form-select" name="category">
                                <option value="">🗂️ Semua Kategori</option>
                                <option value="panduan" {{ request('category') == 'panduan' ? 'selected' : '' }}>
                                    📚 Panduan
                                </option>
                                <option value="artikel" {{ request('category') == 'artikel' ? 'selected' : '' }}>
                                    📰 Artikel
                                </option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-2">
                            <button type="submit" class="btn btn-primary w-100 fw-semibold">
                                Cari
                            </button>
                        </div>
                        <div class="col-lg-2 col-md-12">
                            <a href="{{ route('blog') }}" class="btn btn-outline-secondary w-100">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Blog Grid -->
    @if($blogs->count() > 0)
        <div class="row g-4 mb-5">
            @foreach($blogs as $blog)
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <article class="blog-card h-100">
                        <div class="card border-0 shadow-sm h-100 overflow-hidden">
                            <!-- Cover Image -->
                            <div class="position-relative overflow-hidden">
                                <img src="{{ asset('storage/' . $blog->cover) }}" alt="{{ $blog->title }}" 
                                     class="card-img-top blog-image" 
                                     alt="{{ $blog->title }}"
                                     loading="lazy">
                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="badge badge-category {{ $blog->category == 'panduan' ? 'bg-success' : 'bg-primary' }}">
                                        {{ $blog->category == 'panduan' ? '📚 Panduan' : '📰 Artikel' }}
                                    </span>
                                </div>
                                <div class="blog-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                    <a href="{{ route('blog-show', $blog->slug) }}" 
                                       class="btn btn-light btn-lg rounded-circle">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="card-body p-4 d-flex flex-column">
                                <!-- Title -->
                                <h5 class="card-title fw-bold mb-3 lh-base">
                                    <a href="{{ route('blog-show', $blog->slug) }}" 
                                       class="text-decoration-none text-dark blog-title">
                                        {{ $blog->title }}
                                    </a>
                                </h5>

                                <!-- Excerpt -->
                                <p class="card-text text-muted mb-4 flex-grow-1">
                                    {{ $blog->excerpt }}
                                </p>

                                <!-- Meta Info -->
                                <div class="blog-meta d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="author-avatar me-2">
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" 
                                                 style="width: 32px; height: 32px;">
                                                <span class="text-white fw-bold small">
                                                    {{ substr($blog->author, 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div>
                                            <small class="text-dark fw-semibold d-block"> {{ $blog->author }}</small>
                                        </div>
                                    </div>
                                    <div class="reading-time text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                       <small class="text-muted">{{ $blog->formatted_date }}</small>
                                    </div>
                                </div>

                                <!-- Read More Button -->
                                <a href="{{ route('blog-show', $blog->slug) }}" 
                                   class="btn btn-outline-primary btn-sm fw-semibold mt-auto">
                                    Baca Selengkapnya
                                    <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    <nav aria-label="Blog pagination">
                        {{ $blogs->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </nav>
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="empty-state mb-4">
                        <div class="empty-icon mb-4">
                            <i class="fas fa-search fa-4x text-muted opacity-50"></i>
                        </div>
                        <h3 class="text-muted mb-3">Tidak ada artikel ditemukan</h3>
                        <p class="text-muted mb-4">
                            Coba ubah kata kunci pencarian atau pilih kategori yang berbeda
                        </p>
                        <a href="{{ route('blog') }}" class="btn btn-primary">
                            <i class="fas fa-refresh me-2"></i>
                            Lihat Semua Artikel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .hero-section {
        backdrop-filter: blur(10px);
    }
    
    .blog-card {
        transition: all 0.3s ease;
    }
    
    .blog-card:hover {
        transform: translateY(-8px);
    }
    
    .blog-image {
        height: 220px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .blog-card:hover .blog-image {
        transform: scale(1.05);
    }
    
    .blog-overlay {
        background: rgba(0,0,0,0.5);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .blog-card:hover .blog-overlay {
        opacity: 1;
    }
    
    .blog-title {
        transition: color 0.3s ease;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .blog-title:hover {
        color: #0d6efd !important;
    }
    
    .badge-category {
        font-size: 0.75rem;
        padding: 0.5rem 0.75rem;
        border-radius: 20px;
        backdrop-filter: blur(10px);
    }
    
    .card {
        border-radius: 16px !important;
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
    }
    
    .author-avatar {
        transition: transform 0.3s ease;
    }
    
    .blog-card:hover .author-avatar {
        transform: scale(1.1);
    }
    
    .btn {
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-2px);
    }
    
    .form-control, .form-select {
        border-radius: 10px;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
        transform: translateY(-2px);
    }
    
    .input-group-text {
        border-radius: 10px 0 0 10px;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-right: none;
    }
    
    .empty-state {
        max-width: 400px;
        margin: 0 auto;
    }
    
    .empty-icon {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .backdrop-blur {
        backdrop-filter: blur(10px);
    }
    
    @media (max-width: 768px) {
        .hero-section {
            padding: 3rem 1rem !important;
        }
        
        .display-4 {
            font-size: 2.5rem;
        }
    }
</style>
@endpush