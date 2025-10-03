<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />
    <meta name="description" content="" />
    <meta name="author" content="" />

    <title>@yield('title')</title>

   {{-- css --}}
   @stack('prepend-style')
   @include('includes.style')
   @stack('addon-style')
   <style>
    .header {
    background-color: #4285f4;
    color: white;
    padding: 20px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.question-container {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 20px;
    /* Hapus height fixed agar bisa menyesuaikan konten */
    min-height: 400px;
    display: flex;
    flex-direction: column;
}

.question-number {
    font-weight: bold;
    color: #555;
}

.question-content {
    margin-top: 15px;
    font-size: 16px;
    flex: 1; /* Mengambil ruang yang tersedia */
    overflow-wrap: break-word;
    word-wrap: break-word;
}

/* Batasi ukuran gambar di dalam pertanyaan */
.question-content .option-text img,
.question-content p img {
    max-width: 100% !important;
    max-height: 500px !important;
    height: auto !important;
    width: auto !important;
    object-fit: contain;
    display: block;
    border-radius: 4px;
}

/* Batasi ukuran gambar di dalam opsi jawaban */
.option-item .option-text img {
    max-width: 100% !important;
    max-height: 120px !important;
    height: auto !important;
    width: auto !important;
    object-fit: contain;
    display: inline-block;
    vertical-align: middle;
    margin: 5px;
    border-radius: 4px;
}

/* Pastikan option-text dapat menampung konten dengan baik */
.option-text {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    line-height: 1.4;
    word-break: break-word;
}


.page-link {
    color: #4285f4;
}

.page-item.active .page-link {
    background-color: #4285f4;
    border-color: #4285f4;
}

.question-number-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s ease;
    background-color: #f1f5fe;
    border: 1px solid #e1e5f0;
}

.question-number-btn.active {
    background-color: #4285f4;
    color: white;
    border: none;
}

.option-item {
    background-color: #f1f5fe;
    border-radius: 6px;
    padding: 12px 16px;
    margin-bottom: 13px;
    cursor: pointer;
    transition: all 0.3s ease;
    min-height: 48px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    overflow: hidden;
}

.option-item:hover {
    background-color: #e3eaf9;
    transform: translateY(-2px);
}

.option-item .fw-bold {
    flex-shrink: 0;
    min-width: 20px;
    margin-top: 2px;
}

.option-item .option-text {
    flex: 1;
    min-width: 0; /* Memungkinkan text wrapping */
}

.math-formula {
    font-style: italic;
    margin: 15px 0;
    font-size: 1.1em;
}

.submit-btn {
    background-color: #f1f5fe;
    color: #4285f4;
    border: 1px solid #e1e5f0;
    padding: 10px 20px;
    border-radius: 5px;
    font-weight: bold;
    transition: all 0.3s ease;
    text-align: center;
    width: 100%;
}

.submit-btn:hover {
    background-color: #4285f4;
    color: white;
}

.navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
    margin-bottom: 20px;
    background-color: white;
    padding: 10px 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    flex-shrink: 0;
}

.pagination-info {
    font-weight: 500;
    color: #555;
}

.nav-button {
    color: #4285f4;
    background-color: white;
    border: 1px solid #4285f4;
    padding: 8px 16px;
    border-radius: 5px;
    transition: all 0.3s ease;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 44px;
    min-height: 44px;
    justify-content: center;
}

.nav-button:hover:not(:disabled) {
    background-color: #4285f4;
    color: white;
}

.nav-button:disabled {
    color: #aaa;
    border-color: #ddd;
    cursor: not-allowed;
}

.nav-button i {
    font-size: 1rem;
    display: inline-block;
}

.nav-button-text {
    display: inline;
}

.card {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #eaeaea;
    padding: 15px;
    font-weight: 600;
}

/* Styling untuk reset button agar tidak tertutup */
.reset-btn {
    margin-top: 20px;
    margin-bottom: 10px;
    flex-shrink: 0;
}

/* Styling untuk container options agar lebih fleksibel */
.options {
    margin-bottom: 20px;
}

/* Styling untuk essay answer */
.essay-answer {
    margin-bottom: 20px;
}

.essay-answer .form-control {
    min-height: 30px;
    resize: vertical;
}

/* Styling untuk tabel pilihan majemuk */
.table {
    margin-bottom: 20px;
}

.table td, .table th {
    vertical-align: middle;
    padding: 12px;
}

.table td:first-child {
    word-break: break-word;
    max-width: 300px;
}

/* Responsive styles */
@media (max-width: 1000px) {
    .numbers-container {
        justify-content: flex-start;
        padding-right: 10px;
    }

    .question-number-btn {
        margin: 0 3px;
        transition: all 0.3s ease;
        flex: 0 0 auto;
    }

    .numbers-container {
        display: flex;
        flex-wrap: nowrap !important;
        overflow-x: auto;
        overflow-y: hidden;
        padding-bottom: 8px;
        padding-top: 4px;
        white-space: nowrap;
        width: 100%;
        justify-content: flex-start !important;
    }

    .nav-button-text {
        display: none;
    }

    .nav-button {
        padding: 8px 10px;
    }

    .nav-button i {
        font-size: 1.2rem;
    }

    /* Pada mobile, gambar lebih kecil */
    .question-content .option-text img,
    .question-content p img {
        max-height: 200px !important;
    }

    .option-item .option-text img {
        max-height: 80px !important;
    }
}

@media (max-width: 767px) {
    .header h2 {
        font-size: 1.5rem;
    }

    .question-container {
        padding: 15px;
    }


    /* Pada mobile yang sangat kecil, gambar lebih kecil lagi */
    .question-content .option-text img,
    .question-content p img {
        max-height: 150px !important;
    }

    .option-item .option-text img {
        max-height: 60px !important;
    }

    .table-responsive {
        font-size: 14px;
    }
      .navigation {
        padding: 8px 12px;
    }
    
    .nav-button {
        padding: 10px 12px;
        font-size: 14px;
        min-width: 48px;
        min-height: 48px;
    }
    
    .nav-button-text {
        display: none;
    }
    
    .nav-button i {
        font-size: 1.2rem;
        display: inline-block !important;
    }
    
    .pagination-info {
        font-size: 14px;
        font-weight: 600;
    }
    
    /* Option items responsive */
    .option-item {
        padding: 8px 12px;
        gap: 8px;
        margin-bottom: 10px;
    }
    
    .option-item .fw-bold {
        min-width: 18px;
        font-size: 14px;
    }
    
    .option-item .option-text {
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .navigation {
        padding: 6px 10px;
    }
    
    .nav-button {
        padding: 8px 10px;
        min-width: 40px;
        min-height: 40px;
    }
    
    .pagination-info {
        font-size: 13px;
    }
    
    /* Option items untuk mobile kecil */
    .option-item {
        padding: 6px 10px;
        gap: 6px;
        margin-bottom: 8px;
    }
    
    .option-item .fw-bold {
        min-width: 16px;
        font-size: 13px;
    }
    
    .option-item .option-text {
        font-size: 13px;
    }
}

/* Tambahan untuk mengatur spacing yang lebih baik */
.question-box {
    display: flex;
    flex-direction: column;
    min-height: 100%;
}

.question-box > *:last-child {
    margin-bottom: 0;
}

/* Pastikan gambar dalam HTML yang di-render tidak overflow */
img {
    max-width: 100%;
    height: auto;
}

/* Style khusus untuk konten yang memiliki gambar */
.option-text p {
    margin: 0;
    line-height: 1.4;
}

.option-text p img {
    margin: 5px 0;
}
</style>
  </head>

  <body>
  

    @yield('content')

   


    <!-- Bootstrap core JavaScript -->
    {{-- js --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    @stack('prepend-script')
    @include('includes.script')
    @stack('addon-script')
  </body>
</html>
