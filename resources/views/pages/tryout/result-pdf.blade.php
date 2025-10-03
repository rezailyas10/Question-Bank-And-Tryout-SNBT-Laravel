<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hasil Tryout - {{ $result->exam->title }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #3369a7;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #3369a7;
            margin: 0;
            font-size: 24px;
        }
        
        .header h2 {
            color: #666;
            margin: 5px 0;
            font-size: 16px;
        }
        
        .main-info {
            background: #f8f9fa;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: center;
        }
        
        .score-big {
            font-size: 48px;
            font-weight: bold;
            color: #3369a7;
            margin: 10px 0;
        }
        
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .stats-table th,
        .stats-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        
        .stats-table th {
            background-color: #3369a7;
            color: white;
        }
        
        .section-title {
            color: #3369a7;
            font-size: 16px;
            font-weight: bold;
            margin: 25px 0 15px 0;
            border-bottom: 1px solid #3369a7;
            padding-bottom: 5px;
        }
        
        .ranking-item {
            background: #f8f9fa;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #3369a7;
            border-radius: 4px;
        }
        
        .ranking-item.accepted {
            border-left-color: #28a745;
            background: #d4edda;
        }
        
        .ranking-item.rejected {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        
        .subcat-item {
            background: white;
            border: 1px solid #ddd;
            padding: 12px;
            margin: 8px 0;
            border-radius: 4px;
        }
        
        .progress-bar {
            background: #e9ecef;
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            margin: 5px 0;
        }
        
        .progress-fill {
            height: 100%;
            border-radius: 4px;
        }
        
        .progress-fill.good { background: #28a745; }
        .progress-fill.average { background: #fd7e14; }
        .progress-fill.poor { background: #dc3545; }
        
        .evaluation-section {
            background: #e3f2fd;
            padding: 15px;
            margin: 15px 0;
            border-radius: 6px;
        }
        
        .recommendation-section {
            background: #f3e5f5;
            padding: 15px;
            margin: 15px 0;
            border-radius: 6px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>HASIL TRYOUT</h1>
        <h2>{{ $result->exam->title }}</h2>
        <p><strong>Nama:</strong> {{ $result->user->name }}</p>
        <p><strong>Tanggal:</strong> {{ date('d F Y', strtotime($result->created_at)) }}</p>
    </div>

    <!-- Informasi Utama -->
    <div class="main-info">
        <h3>NILAI RATA-RATA</h3>
        <div class="score-big">{{ $result->score }}</div>
        <p>Akurasi: {{ number_format($accuracy, 2) }}%</p>
    </div>

    <!-- Statistik -->
    <table class="stats-table">
        <thead>
            <tr>
                <th>Benar</th>
                <th>Salah</th>
                <th>Kosong</th>
                <th>Total Soal</th>
                <th>Ranking</th>
                <th>Total Peserta</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $correctCount }}</td>
                <td>{{ $inCorrectCount }}</td>
                <td>{{ $nullCount }}</td>
                <td>{{ $totalQuestions }}</td>
                <td>{{ $ranking ?? '-' }}</td>
                <td>{{ $totalParticipants }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Ranking Universitas -->
    @if(!empty($universityRankings))
    <h3 class="section-title">RANKING PER UNIVERSITAS</h3>
    @foreach($universityRankings as $university)
    <div class="ranking-item">
        <strong>{{ $university['university_name'] }}</strong><br>
        Ranking: {{ $university['rank'] }} dari {{ $university['total'] }} peserta
    </div>
    @endforeach
    @endif

    <!-- Ranking Jurusan -->
    @if(!empty($majorRankings))
    <h3 class="section-title">RANKING PER JURUSAN</h3>
    @foreach($majorRankings as $index => $major)
    <div class="ranking-item {{ $major['is_accepted'] ? 'accepted' : 'rejected' }}">
        <strong>Pilihan {{ $index + 1 }}: {{ $major['major_name'] }}</strong><br>
        <small>{{ $major['university'] }}</small><br>
        Ranking: {{ $major['rank'] }} dari {{ $major['total'] }} peserta<br>
        @if($major['quota'] > 0)
        Kuota: {{ number_format($major['quota']) }} | 
        Status: {{ $major['is_accepted'] ? 'DITERIMA' : 'TIDAK DITERIMA' }}
        @endif
    </div>
    @endforeach
    @endif

    <div class="page-break"></div>

    <!-- Rekap Per Subtes -->
    <h3 class="section-title">REKAP PER SUBTES</h3>
    @foreach($perSubcategory as $sub)
    @php
        $fillClass = $sub->average_score >= 300 ? 'good' : ($sub->average_score >= 150 ? 'average' : 'poor');
    @endphp
    <div class="subcat-item">
        <strong>{{ $sub->name }}</strong><br>
        Skor: {{ number_format($sub->average_score, 2) }}<br>
        Benar: {{ $sub->correct }} | Salah: {{ $sub->wrong }} | Kosong: {{ $sub->empty }} | Total: {{ $sub->total }}<br>
        <div class="progress-bar">
            <div class="progress-fill {{ $fillClass }}" style="width: {{ max(1, $sub->percentage) }}%"></div>
        </div>
        Persentase Benar: {{ number_format($sub->percentage, 2) }}%
    </div>
    @endforeach

    <!-- Evaluasi -->
    @if($evaluations->isNotEmpty())
    <h3 class="section-title">EVALUASI</h3>
    @foreach($evaluations as $evaluation)
    <div class="evaluation-section">
        {!! $evaluation !!}
    </div>
    @endforeach
    @endif

    <!-- Rekomendasi -->
    @if($recommendations->isNotEmpty())
    <h3 class="section-title">REKOMENDASI</h3>
    @foreach($recommendations as $recommendation)
    <div class="recommendation-section">
        {!! $recommendation!!}
    </div>
    @endforeach
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini dibuat secara otomatis pada {{ date('d F Y H:i:s') }}</p>
        <p>© {{ date('Y') }} - Sistem Tryout Online</p>
    </div>
</body>
</html>