<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran ITI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/style/main.css') }}?v={{ time() }}">
    <style>
    .registration-container {
        background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);
        min-height: 100vh;
        padding: 2rem 0;
    }

    .registration-card {
        max-width: 700px;
        margin: 0 auto;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.2);
        overflow: hidden;
    }

    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        text-align: center;
        border: none;
    }

    .card-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.5rem;
        letter-spacing: 0.5px;
    }

    .card-body {
        padding: 2.5rem;
    }

    .info-section {
        background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);
        border: 1px solid #e3e6f0;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        position: relative;
    }

    .info-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px 12px 0 0;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e3e6f0;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #5a5c69;
        font-size: 0.9rem;
    }

    .info-value {
        color: #3a3b45;
        font-weight: 500;
        text-align: right;
        flex: 1;
        margin-left: 1rem;
    }

    .recommendations-section {
        background: #f8f9fc;
        border: 1px solid #e3e6f0;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .recommendations-title {
        font-weight: 600;
        color: #5a5c69;
        margin-bottom: 1rem;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
    }

    .recommendations-title::before {
        content: '🎓';
        margin-right: 0.5rem;
    }

    .recommendation-item {
        background: white;
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        color: #5a5c69;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .recommendation-item:hover {
        border-color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
    }

    .recommendation-item:last-child {
        margin-bottom: 0;
    }

    .form-section {
        background: #ffffff;
    }

    .form-label {
        font-weight: 600;
        color: #5a5c69;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .form-select, .form-control {
        border: 2px solid #e3e6f0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background-color: #ffffff;
    }

    .form-select:focus, .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        outline: none;
    }

    .form-check-section {
        background: #f8f9fc;
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        padding: 1.25rem;
        margin: 1.5rem 0;
    }

    .form-check-input {
        width: 1.25rem;
        height: 1.25rem;
        margin-right: 0.75rem;
        accent-color: #667eea;
    }

    .form-check-label {
        font-weight: 500;
        color: #5a5c69;
        cursor: pointer;
        line-height: 1.4;
    }

    .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 8px;
        padding: 1rem 2rem;
        font-weight: 600;
        font-size: 1rem;
        color: white;
        transition: all 0.3s ease;
        width: 100%;
        letter-spacing: 0.5px;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        background: linear-gradient(135deg, #5a67d8 0%, #667eea 100%);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .registration-container {
            padding: 1rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .info-row {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .info-value {
            text-align: left;
            margin-left: 0;
            margin-top: 0.25rem;
        }
    }
</style>
</head>
<body>
<div class="registration-container">
    <div class="registration-card">
        <div class="card-header">
            <h4>Ajukan Pendaftaran ITI</h4>
        </div>
        <div class="card-body">
            {{-- Informasi hasil tryout --}}
            <div class="info-section">
                <div class="info-row">
                    <span class="info-label">Nama Lengkap</span>
                    <span class="info-value">{{ $result->user->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email</span>
                    <span class="info-value">{{ $result->user->email }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Skor Ujian</span>
                    <span class="info-value">{{ $result->score }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Ujian</span>
                    <span class="info-value">{{ $result->exam->title }}</span>
                </div>
            </div>

            {{-- Rekomendasi jurusan --}}
            <div class="recommendations-section">
                <div class="recommendations-title">
                    Program Studi yang Direkomendasikan oleh AI
                </div>
                @foreach($recommendedMajors as $major)
                    @php
                        $cleaned = trim(strip_tags(is_array($major) ? implode('', $major) : $major));
                    @endphp
                    @if($cleaned !== '')
                        <div class="recommendation-item">
                            {{ $cleaned }}
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Form pendaftaran --}}
            <div class="form-section">
                <form action="{{ route('register-iti.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="result_id" value="{{ $result->id }}">

                    <div class="form-group">
                        <label for="periode_akademik" class="form-label">Periode Akademik</label>
                        <select id="periode_akademik" name="periode_akademik" class="form-select" required>
                            <option hidden value="">Pilih Periode Akademik...</option>
                            @foreach($periods as $period)
                                <option value="{{ $period }}">{{ $period }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="program_studi" class="form-label">Program Studi</label>
                        <select id="program_studi" name="program_studi" class="form-select" required>
                            <option hidden value="">Pilih Program Studi...</option>
                            @foreach($majors as $major)
                                <option value="{{ $major }}">{{ $major }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-check-section">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="agree_to_contact" name="agree_to_contact" value="1">
                            <label class="form-check-label" for="agree_to_contact">
                                Saya bersedia dihubungi oleh pihak ITI untuk informasi lebih lanjut mengenai pendaftaran dan program studi.
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        Kirim Pendaftaran
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>