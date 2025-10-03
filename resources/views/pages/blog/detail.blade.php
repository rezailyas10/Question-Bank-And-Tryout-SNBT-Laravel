{{-- resources/views/blog/show.blade.php --}}
@extends('layouts.app')

@section('title', $blog->title)

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
    /* Wrapper yang melebar penuh dengan padding kiri-kanan */
   
    /* Batasi max-width agar teks tidak terlalu panjang di layar besar, tapi opsional */
    .content-inner {
        max-width: 1200px;
        margin: 0 auto;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    /* Breadcrumb */
    .breadcrumb {
        background: #ffffff;
    }
    .breadcrumb-item + .breadcrumb-item::before {
        content: "→";
        color: #6c757d;
    }

    /* Header artikel */
    .article-header {
        padding: 30px 40px;
        border-bottom: 1px solid #eee;
    }
    .article-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 16px;
        line-height: 1.2;
    }
    .article-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px; /* jarak antar item metadata */
        color: #666;
        font-size: 0.9rem;
    }
    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px; /* jarak icon dan teks */
    }
    .meta-item i {
        color: #007bff;
        font-size: 1rem;
    }

    /* Featured image */
    .article-image {
        width: 100%;
        height: auto;
        max-height: 500px;
        object-fit: cover;
        display: block;
    }
    .image-overlay {
        background: linear-gradient(transparent, rgba(0,0,0,0.7));
    }

    /* Konten artikel */
    .article-content {
        padding: 30px 40px;
    }
    .content-body p {
        margin-bottom: 1.5rem;
        font-size: 1rem;
        line-height: 1.8;
        color: #444;
        text-align: justify;
    }
    .content-body h2,
    .content-body h3,
    .content-body h4 {
        margin: 2rem 0 1rem;
        color: #2c3e50;
        font-weight: 600;
    }

    /* Footer artikel: tags & share */
    .article-footer {
        padding: 30px 40px;
        background: #f8f9fa;
        border-top: 1px solid #eee;
    }
    .article-tags {
        margin-bottom: 25px;
    }
    .article-tags h6 {
        font-size: 1rem;
        margin-bottom: 12px;
        color: #333;
        font-weight: 600;
    }
    .article-tags .badge {
        margin-right: 8px;
        margin-bottom: 8px;
        font-size: 0.875rem;
    }
    .share-section {
        margin-top: 20px;
    }
    .share-section .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        font-size: 0.9rem;
    }
    .btn-facebook {
        background: #1877f2; border-color: #1877f2; color: white;
    }
    .btn-facebook:hover {
        background: #166fe5; border-color: #166fe5;
    }
    .btn-twitter {
        background: #1da1f2; border-color: #1da1f2; color: white;
    }
    .btn-twitter:hover {
        background: #0d8bd9; border-color: #0d8bd9;
    }
    .btn-linkedin {
        background: #0077b5; border-color: #0077b5; color: white;
    }
    .btn-linkedin:hover {
        background: #005885; border-color: #005885;
    }
    .btn-whatsapp {
        background: #25d366; border-color: #25d366; color: white;
    }
    .btn-whatsapp:hover {
        background: #1ab851; border-color: #1ab851;
    }
    .btn-copy {
        background: #6c757d; color: white;
    }
    .btn-copy:hover {
        background: #5a6268;
    }

    /* Navigation bawah artikel */
    .article-navigation {
        padding: 30px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .article-navigation .btn {
        padding: 8px 20px;
    }
    .scroll-top button {
        width: 48px; height: 48px;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s, visibility 0.3s;
    }
    .scroll-top button.show {
        opacity: 1;
        visibility: visible;
    }
    .scroll-top button:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    /* Related Articles section */
    .related-section {
        padding: 30px 40px;
    }
    .related-section h5 {
        font-weight: 600;
        margin-bottom: 20px;
    }
    .related-article {
        margin-bottom: 24px;
    }
    .related-article:last-child {
        margin-bottom: 0;
    }
    .related-article .related-image img {
        width: 100%;
        object-fit: cover;
    }
    .related-article .related-title {
        transition: color 0.3s ease;
    }
    .related-article .related-title:hover {
        color: #0d6efd !important;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .content-wrapper {
            padding: 20px 5%;
        }
        .article-header, .article-content, .article-footer, .article-navigation, .related-section {
            padding: 20px;
        }
        .article-title {
            font-size: 2rem;
        }
        .article-image {
            max-height: 300px;
        }
        .meta-item {
            gap: 6px;
        }
        .share-section .btn {
            padding: 6px 12px;
            font-size: 0.85rem;
        }
        .scroll-top button {
            width: 40px; height: 40px;
        }
    }
</style>

@section('content')
<div class="container" style="margin-top: 100px">
    <div class="content-wrapper">
    <div class="content-inner">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4 px-3">
            <ol class="breadcrumb rounded-pill px-3 py-2 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="text-decoration-none">
                        <i class="fas fa-home me-2"></i>Home
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('blog') }}" class="text-decoration-none">Blog</a>
                </li>
                <li class="breadcrumb-item active fw-semibold" aria-current="page">
                    {{ Str::limit($blog->title, 30) }}
                </li>
            </ol>
        </nav>

        <!-- Main Content penuh lebar dalam content-inner -->
        <header class="article-header">
            <div class="mb-3">
                <span class="badge {{ $blog->category == 'panduan' ? 'bg-success' : 'bg-primary' }}">
                    {{ $blog->category == 'panduan' ? '📚 Panduan' : '📰 Artikel' }}
                </span>
            </div>
            <h1 class="article-title">{{ $blog->title }}</h1>
            <div class="article-meta">
                <div class="meta-item">
                    <i class="fas fa-user"></i>
                    <span class="fw-semibold text-dark">{{ $blog->author }}</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-calendar"></i>
                    <span class="text-muted">{{ $blog->formatted_date }}</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-clock"></i>
                    <span class="text-muted">{{ ceil(str_word_count(strip_tags($blog->content)) / 200) }} min read</span>
                </div>
                {{-- <div class="meta-item">
                    <i class="fas fa-eye"></i>
                    <span class="text-muted">{{ number_format($blog->views ?? rand(100, 1000)) }} views</span>
                </div> --}}
            </div>
        </header>


        <!-- Article Content -->
        <article class="article-content">
            <div class="content-body">
                {{-- Tampilkan konten yang sudah disanitasi di controller --}}
                {!! $blog->content !!}
            </div>
        </article>

        <!-- Article Footer: Tags & Share -->
        <footer class="article-footer">
            <!-- Tags -->
            @if(isset($blog->tags) && $blog->tags->isNotEmpty())
                <div class="article-tags mb-4">
                    <h6 class="fw-semibold mb-3">Tags:</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($blog->tags as $tag)
                            <a href="{{ route('blog', ['tag' => $tag->slug]) }}"
                               class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                                {{ $tag->nama }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Share Buttons -->
            <div class="share-section">
                <div class="card border-0 bg-light rounded-4">
                    <div class="card-body p-4">
                        <h6 class="fw-semibold mb-3">
                            <i class="fas fa-share-alt me-2 text-primary"></i>
                            Bagikan Artikel Ini
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
                               target="_blank" class="btn btn-facebook btn-sm rounded-pill">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($blog->title) }}"
                               target="_blank" class="btn btn-twitter btn-sm rounded-pill">
                                <i class="fab fa-twitter"></i> Twitter
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}"
                               target="_blank" class="btn btn-linkedin btn-sm rounded-pill">
                                <i class="fab fa-linkedin"></i> LinkedIn
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($blog->title . ' - ' . request()->fullUrl()) }}"
                               target="_blank" class="btn btn-whatsapp btn-sm rounded-pill">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                            <button onclick="copyToClipboard('{{ request()->fullUrl() }}')"
                                    class="btn btn-copy btn-sm rounded-pill">
                                <i class="fas fa-link"></i> Copy Link
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Related Articles (di bawah konten utama) -->
        @if(isset($relatedBlogs) && $relatedBlogs->count() > 0)
            <section class="related-section">
                <h5>Artikel Terkait</h5>
                @foreach($relatedBlogs as $related)
                    <div class="related-article row g-3 mb-4 {{ $loop->last ? '' : 'border-bottom pb-4' }}">
                        <div class="col-md-3 related-image">
                            <img src="{{ $related->cover_url }}" alt="{{ $related->title }}"
                                 class="img-fluid w-100" style="height: 80px; object-fit: cover; border-radius:4px;">
                        </div>
                        <div class="col-md-9">
                            <h6 class="mb-1 lh-base">
                                <a href="{{ route('blog-show', $related->slug) }}"
                                   class="text-decoration-none text-dark related-title">
                                    {{ Str::limit($related->title, 80) }}
                                </a>
                            </h6>
                            <div class="related-meta d-flex flex-wrap gap-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $related->formatted_date }}
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i>
                                    {{ $related->author }}
                                </small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </section>
        @endif

        <!-- Navigation bawah: kembali dan scroll to top -->
        <div class="article-navigation">
            <a href="{{ route('blog') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Blog
            </a>
            <div class="scroll-top">
                <button onclick="scrollToTop()" class="btn btn-light">
                    <i class="fas fa-arrow-up"></i>
                </button>
            </div>
        </div>
    </div> <!-- end content-inner -->
</div> <!-- end content-wrapper -->
</div>


<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            showToast('Link berhasil disalin!', 'success');
        }).catch(function(err) {
            console.error('Could not copy text: ', err);
            showToast('Gagal menyalin link', 'error');
        });
    }

    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `toast-notification ${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                <span>${message}</span>
            </div>
        `;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            padding: 12px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            ${type === 'success' ? 'background: #28a745;' : 'background: #dc3545;'}
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 100);
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }

    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    window.addEventListener('scroll', function() {
        const btn = document.querySelector('.scroll-top button');
        if (!btn) return;
        if (window.pageYOffset > 300) {
            btn.classList.add('show');
        } else {
            btn.classList.remove('show');
        }
    });
</script>
@endsection