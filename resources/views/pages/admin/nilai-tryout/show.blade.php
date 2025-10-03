@extends('layouts.admin')

@section('title')
Analisis Soal - {{ $exam->title }}
@endsection
<link rel="stylesheet" href="{{ asset('/style/irt.css') }}?v={{ time() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.27.2/axios.min.js"></script>


    @section('content')
<div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📊 Analisis Soal dan Nilai</h1>
            <p>{{ $exam->title }}</p>
        </div>
         @php
    if (!function_exists('fixImageUrls')) {
        function fixImageUrls($html) {
            return preg_replace_callback(
                '/src="([^"]+)"/i',
                function ($matches) {
                    $url = $matches[1];
                    if (preg_match('/^http(s)?:\/\//', $url)) {
                        return 'src="' . $url . '"';
                    } else {
                        return 'src="' . asset($url) . '"';
                    }
                },
                $html
            );
        }
    }
    @endphp

        <!-- Action Section -->
        <div class="action-section">
            <div id="alertSuccess" class="alert alert-success">
                <span id="successMessage"></span>
            </div>
            
            <div id="alertError" class="alert alert-error">
                <span id="errorMessage"></span>
            </div>

            <div class="action-buttons">
                <button id="calculateBtn" class="btn btn-primary">
                    🧮 Hitung Analisis IRT
                </button>
                <button id="resetBtn" class="btn btn-danger">
                    🗑️ Reset Semua Nilai
                </button>
            </div>

            <div id="loadingIndicator" class="loading">
                <span class="spinner"></span>
                Sedang memproses analisis...
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Soal</h3>
                <div class="stat-value">{{ $exam->questions->count() }}</div>
            </div>
            <div class="stat-card">
                <h3>Total Peserta</h3>
                <div class="stat-value">{{ $totalParticipants }}</div>
            </div>
            <div class="stat-card">
                <h3>Mata Pelajaran</h3>
                <div class="stat-value">{{ $questionsBySubCategory->count() }}</div>
            </div>
        </div>

        <!-- Main Content Tabs -->
        <div class="tabs">
            <div class="tab-header">
                <button class="tab-button active" onclick="showTab('questions')">📝 Analisis Soal</button>
                <button class="tab-button" onclick="showTab('participants')">📝 Hasil Peserta</button>
                <button class="tab-button" onclick="showTab('results')">🎯 Hasil IRT</button>
                <button class="tab-button" onclick="showTab('responses')">📊 Data Jawaban</button>
            </div>

            <!-- Questions Analysis Tab -->
            <div id="questions" class="tab-content active">
                @foreach($questionsBySubCategory as $subCategoryName => $questions)
                <div style="margin-bottom: 30px;">
                    <h3 style="color: #3b82f6; margin-bottom: 15px; font-size: 1.3rem;">📚 {{ $subCategoryName }}</h3>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>No. Soal</th>
                                    <th>Pertanyaan</th>
                                    <th>Total Peserta</th>
                                    <th>Jawaban Benar</th>
                                    <th>Persentase Benar</th>
                                    <th>Tingkat Kesulitan</th>
                                    <th>Detail Soal</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($questions as $index => $question)
                                @php
                            $rawHtml = old('question_text', $question->question_text ?? '');
                            $fixedHtml = fixImageUrls($rawHtml);
                            $textOnly = strip_tags($fixedHtml);
                            $isOnlyImage = trim($textOnly) === '';
                            // untuk data attribute, gunakan lowercase untuk subcategory & status
                            $subCatValue = strtolower($question->subCategory->name ?? '');
                            $statusValue = strtolower($question->status ?? 'ditinjau');
                        @endphp
                                <tr>
                                    <td><strong>{{ $index + 1 }}</strong></td>
                                    <td style="max-width: 300px;">
                                       @if ($isOnlyImage)
                                        {{-- Wrap gambar dengan div agar bisa di‐CSS --}}
                                        <div class="image-only-wrapper">
                                            {!! preg_replace(
                                                '/<img\s+([^>]+)>/i',
                                                '<img $1 class="img-fluid image-only" />',
                                                $fixedHtml
                                            ) !!}
                                        </div>
                                    @else
                                        {!! Str::limit(strip_tags($fixedHtml), 50) !!}
                                    @endif
                                    </td>
                                    <td>{{ $question->total_participants }}</td>
                                    <td class="correct">{{ $question->correct_answers }}</td>
                                    <td><strong>{{ $question->correct_percentage }}%</strong></td>
                                    <td>
                                        <span class="difficulty-{{ strtolower($question->difficulty_category) === 'mudah' ? 'easy' : (strtolower($question->difficulty_category) === 'sedang' ? 'medium' : 'hard') }}">
                                            {{ $question->difficulty_category }}
                                        </span>
                                    </td>
                                    <td>
                                         <a href="{{ route('question.show', $question->id) }}" class="btn btn-sm btn-warning float-right">Detail</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
            <!-- Results Analysis Tab -->
           <div id="participants" class="tab-content active">
    @foreach($questionsBySubCategory as $subCategoryName => $questions)
    <div style="margin-bottom: 30px;">
        <h3 style="color: #3b82f6; margin-bottom: 15px; font-size: 1.3rem;">📚 {{ $subCategoryName }}</h3>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Peserta</th>
                        <th>Nilai Rata-Rata</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($exam->results as $result)
                        @php
                            // Ambil rata-rata skor peserta untuk subcategory ini
                            $avgScore = $averageScoresByResult[$result->id][$subCategoryName] ?? null;
                        @endphp

                        @if($avgScore !== null)
                        <tr>
                            <td><strong>{{ $no++ }}</strong></td>
                            <td>{{ $result->user->name ?? 'Nama tidak ditemukan' }}</td>
                            <td>{{ number_format($avgScore, 2) }}</td>
                        </tr>
                        @endif
                    @endforeach

                    @if($no === 1)
                    <tr>
                        <td colspan="3" style="text-align: center;">Tidak ada data peserta pada subcategory ini.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>


            <!-- Results Tab -->
            <div id="results" class="tab-content">
                <div class="category-selector">
                    <label for="resultCategory" style="font-weight: 600; margin-right: 10px;">Pilih Mata Pelajaran:</label>
                    <select id="resultCategory" onchange="showResults()">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($questionsBySubCategory->keys() as $subCategory)
                        <option value="{{ $subCategory }}">{{ $subCategory }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="resultsContent">
                    <div class="no-data">
                        <div class="no-data-icon">📊</div>
                        <div>Pilih mata pelajaran dan jalankan analisis IRT untuk melihat hasil</div>
                    </div>
                </div>
            </div>

            <!-- Response Data Tab -->
            <div id="responses" class="tab-content">
                <div class="category-selector">
                    <label for="responseCategory" style="font-weight: 600; margin-right: 10px;">Pilih Mata Pelajaran:</label>
                    <select id="responseCategory" onchange="showResponses()">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($questionsBySubCategory->keys() as $subCategory)
                        <option value="{{ $subCategory }}">{{ $subCategory }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="responsesContent">
                    <div class="no-data">
                        <div class="no-data-icon">📝</div>
                        <div>Pilih mata pelajaran untuk melihat data jawaban peserta</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Global variables
        let irtData = null;
        const examSlug = '{{ $exam->slug }}';

        // Setup CSRF token
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Tab switching
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });
            
            document.getElementById(tabId).classList.add('active');
            event.target.classList.add('active');
        }

        // Show alerts
        function showAlert(type, message) {
            const alertElement = document.getElementById(type === 'success' ? 'alertSuccess' : 'alertError');
            const messageElement = document.getElementById(type === 'success' ? 'successMessage' : 'errorMessage');
            
            messageElement.textContent = message;
            alertElement.style.display = 'block';
            
            setTimeout(() => {
                alertElement.style.display = 'none';
            }, 5000);
        }

        // Calculate IRT
        document.getElementById('calculateBtn').addEventListener('click', async function() {
            const btn = this;
            const loading = document.getElementById('loadingIndicator');
            
            btn.disabled = true;
            loading.style.display = 'block';
            
            try {
                const response = await axios.post(`/admin/nilai-tryout/${examSlug}/calculate-irt`);
                
                if (response.data.success) {
                    irtData = response.data.data;
                    console.log('IRT Data received:', irtData); // Debug log
                    showAlert('success', 'Analisis IRT berhasil dihitung!');
                    
                    // Refresh active views
                    if (document.getElementById('resultCategory').value) {
                        showResults();
                    }
                } else {
                    showAlert('error', response.data.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'Error: ' + (error.response?.data?.message || error.message));
            } finally {
                btn.disabled = false;
                loading.style.display = 'none';
            }
        });

        // Reset scores
        document.getElementById('resetBtn').addEventListener('click', async function() {
            if (!confirm('Yakin ingin mereset semua nilai ke 0? Tindakan ini tidak dapat dibatalkan.')) {
                return;
            }

            const btn = this;
            btn.disabled = true;
            
            try {
                const response = await axios.post(`/admin/nilai-tryout/${examSlug}/reset-scores`);
                
                if (response.data.success) {
                    irtData = null;
                    showAlert('success', 'Semua nilai berhasil direset!');
                    
                    // Reset selectors
                    document.getElementById('resultCategory').value = '';
                    document.getElementById('responseCategory').value = '';
                    
                    // Clear content
                    document.getElementById('resultsContent').innerHTML = `
                        <div class="no-data">
                            <div class="no-data-icon">📊</div>
                            <div>Nilai telah direset. Pilih mata pelajaran dan jalankan analisis untuk melihat hasil baru</div>
                        </div>
                    `;
                    document.getElementById('responsesContent').innerHTML = `
                        <div class="no-data">
                            <div class="no-data-icon">📝</div>
                            <div>Pilih mata pelajaran untuk melihat data jawaban peserta</div>
                        </div>
                    `;
                } else {
                    showAlert('error', response.data.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'Error: ' + (error.response?.data?.message || error.message));
            } finally {
                btn.disabled = false;
            }
        });

        // Show results for selected category - FIXED VERSION
       function showResults() {
    const selectedCategory = document.getElementById('resultCategory').value;
    const contentDiv = document.getElementById('resultsContent');
    
    if (!selectedCategory) {
        contentDiv.innerHTML = `
            <div class="no-data">
                <div class="no-data-icon">📊</div>
                <div>Pilih mata pelajaran untuk melihat hasil</div>
            </div>
        `;
        return;
    }

    console.log('Selected category:', selectedCategory);
    console.log('Available IRT data:', irtData);

    if (!irtData || !irtData[selectedCategory]) {
        contentDiv.innerHTML = `
            <div class="no-data">
                <div class="no-data-icon">⚠️</div>
                <div>Silakan jalankan analisis IRT terlebih dahulu</div>
            </div>
        `;
        return;
    }

    // Extract data from the correct structure
    const categoryData = irtData[selectedCategory];
    console.log('Category data:', categoryData);
    
    // Check if irt_results exists
    if (!categoryData.irt_results) {
        contentDiv.innerHTML = `
            <div class="no-data">
                <div class="no-data-icon">❌</div>
                <div>Data IRT tidak valid untuk mata pelajaran ini</div>
            </div>
        `;
        return;
    }

    const results = categoryData.irt_results.person_results;
    const stats = categoryData.irt_results.summary_stats;
    
    console.log('Results:', results);
    console.log('Stats:', stats);

    // Check if required data exists
    if (!results || !stats) {
        contentDiv.innerHTML = `
            <div class="no-data">
                <div class="no-data-icon">❌</div>
                <div>Data hasil tidak lengkap</div>
            </div>
        `;
        return;
    }

    // Fetch detailed scores per subcategory for each user
    fetchDetailedScores(selectedCategory, results, stats, contentDiv);
}

// New function to fetch detailed scores per subcategory
// Fixed fetchDetailedScores function
async function fetchDetailedScores(selectedCategory, results, stats, contentDiv) {
    try {
        // Make sure the URL matches your route exactly
        // If you're in admin panel, the URL should include /admin prefix
        const response = await axios.get(`/admin/nilai-tryout/${examSlug}/detailed-scores/${encodeURIComponent(selectedCategory)}`);
        
        if (!response.data.success) {
            throw new Error('Gagal memuat data skor detail');
        }

        const detailedScores = response.data.data;
        console.log('Detailed scores:', detailedScores);

        // Rest of your code remains the same...
        const utbkStats = stats.utbk_score || {};

        contentDiv.innerHTML = `
         <!-- Summary Stats -->
            <div class="stats-grid" style="margin-bottom: 25px;">
                <div class="stat-card">
                    <h3>Rata-rata Nilai</h3>
                    <div class="stat-value">${Math.round(response.data.utbk_score.mean || 0)}</div>
                </div>
                <div class="stat-card">
                    <h3>Nilai Tertinggi</h3>
                  <div class="stat-value">${Math.round(response.data.utbk_score.max || 0)}</div>
                </div>
                <div class="stat-card">
                    <h3>Nilai Terendah</h3>
                   <div class="stat-value">${Math.round(response.data.utbk_score.min || 0)}</div>
                </div>
                <div class="stat-card">
                    <h3>Std Deviasi</h3>
                    <div class="stat-value">${Math.round(response.data.utbk_score.std || 0)}</div>
                </div>
            </div>

            <!-- Results Table -->
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Peserta</th>
                            <th>Jumlah Benar</th>
                            <th>Rata-rata Skor ${selectedCategory}</th>
                            <th>Kategori</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${results.map(result => {
                            const userDetailScore = detailedScores.find(ds => ds.user_id == result.user_id);
                            const averageScore = userDetailScore ? userDetailScore.average_score : 0;
                            
                            let category = 'Kurang';
                            let scoreClass = 'score-low';
                            
                            if (averageScore >= 650) {
                                category = 'Sangat Baik';
                                scoreClass = 'score-high';
                            } else if (averageScore >= 550) {
                                category = 'Baik';
                                scoreClass = 'score-high';
                            } else if (averageScore >= 450) {
                                category = 'Cukup';
                                scoreClass = 'score-medium';
                            }
                            
                            return `
                                <tr>
                                    <td><strong>${result.user_name || 'N/A'}</strong></td>
                                    <td>${result.raw_score || 0}</td>
                                    <td class="${scoreClass}">${averageScore.toFixed(2)}</td>
                                    <td>${category}</td>
                                </tr>
                            `;
                        }).join('')}
                    </tbody>
                </table>
            </div>
        `;

    } catch (error) {
        console.error('Error fetching detailed scores:', error);
        
        // More detailed error logging
        if (error.response) {
            console.log('Error status:', error.response.status);
            console.log('Error data:', error.response.data);
        }
        
        contentDiv.innerHTML = `
            <div class="no-data">
                <div class="no-data-icon">❌</div>
                <div>Error memuat data skor: ${error.message}</div>
                <div style="font-size: 12px; color: #666; margin-top: 5px;">
                    Status: ${error.response?.status || 'Unknown'}
                </div>
            </div>
        `;
    }
}

        // Show responses for selected category
        function showResponses() {
            const selectedCategory = document.getElementById('responseCategory').value;
            const contentDiv = document.getElementById('responsesContent');

            if (!selectedCategory) {
                contentDiv.innerHTML = `
                    <div class="no-data">
                        <div class="no-data-icon">📝</div>
                        <div>Pilih mata pelajaran untuk melihat data jawaban</div>
                    </div>
                `;
                return;
            }

            // Fetch response data
            fetchResponseData(selectedCategory);
        }

        // Fetch response data
        async function fetchResponseData(category) {
            const contentDiv = document.getElementById('responsesContent');

            try {
                const response = await axios.get(`/admin/nilai-tryout/${examSlug}/response-data/${encodeURIComponent(category)}`);
                
                if (!response.data.success) {
                    throw new Error('Gagal memuat data jawaban');
                }

                const { questions, responses } = response.data.data;

                if (questions.length === 0) {
                    contentDiv.innerHTML = `
                        <div class="no-data">
                            <div class="no-data-icon">❌</div>
                            <div>Tidak ada soal ditemukan untuk mata pelajaran ini</div>
                        </div>
                    `;
                    return;
                }

                contentDiv.innerHTML = `
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nama Peserta</th>
                                   ${questions.map((_, index) => `<th>Soal ${index + 1}</th>`).join('')}
                                    <th>Total Benar</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${responses.map(user => `
                                    <tr>
                                        <td><strong>${user.name}</strong></td>
                                        ${user.answers.map(answer => `
                                            <td class="${answer ? 'correct' : 'incorrect'}">
                                                ${answer ? 1 : 0}
                                            </td>
                                        `).join('')}
                                        <td><strong>${user.total_correct}</strong></td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            } catch (error) {
                console.error('Error:', error);
                contentDiv.innerHTML = `
                    <div class="no-data">
                        <div class="no-data-icon">❌</div>
                        <div>Error: ${error.response?.data?.message || error.message}</div>
                    </div>
                `;
            }
        }
    </script>
    @endsection
    

