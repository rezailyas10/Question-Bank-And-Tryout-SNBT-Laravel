<?php

namespace App\Services;

use App\Models\Major;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIEvaluationService1
{
    private $groqApiKey;
    private $groqUrl;

    public function __construct()
    {
        // Set API key dan URL yang benar
        $this->groqApiKey = env('GROQ_API_KEY');
        $this->groqUrl = 'https://api.groq.com/openai/v1/chat/completions';
    }

    /**
     * Evaluasi mendalam per soal dengan lesson yang spesifik.
     *
     * @param  \Illuminate\Support\Collection  $wrongQuestions   Koleksi soal yang salah
     * @param  \Illuminate\Support\Collection  $correctQuestions Koleksi soal yang benar
     * @param  int                             $score            Nilai total (misal 750 dari 1000)
     * @return string                                              Teks respons dari Groq/OpenAI
     */
     public function evaluatePerformance($wrongQuestions, $correctQuestions, int $score, array $subCategoryStats = [])
    {
        // Jika diberikan Collection, convert ke array of objects sesuai buildWrongQuestionsAnalysis
        // Namun kita langsung panggil buildWrongQuestionsAnalysis yang menerima Collection.
        $wrongArr   = $this->buildWrongQuestionsAnalysis($wrongQuestions);
        $correctArr = $this->buildWrongQuestionsAnalysis($correctQuestions);

        // Build prompt dengan array hasil analisis
        $prompt = $this->buildDetailedEvaluationPrompt($wrongArr, $correctArr, $score, $subCategoryStats);

        // Panggil API Groq/OpenAI (sesuaikan implementasi callGroqAPI Anda)
        return $this->callGroqAPI($prompt);
    }


    /**
     * Rekomendasi jurusan murni berdasarkan nilai dan passing score
     */
    public function recommendMajors($score, $majorRankings, $hasSubjectCategories = false)
    {
        // Buat prompt rekomendasi berbasis nilai
        $prompt = $this->buildScoreBasedRecommendationPrompt($score, $majorRankings, $hasSubjectCategories);
        
        // Panggil API Groq
        return $this->callGroqAPI($prompt);
    }

    /**
     
     * Konversi Collection<Question> (atau objek serupa) 
     * menjadi array[] dengan keys yang dibutuhkan.
     *
     * @param  \Illuminate\Support\Collection  $questions
     * @return array[]
     */
     private function buildWrongQuestionsAnalysis($questions)
    {
        $out = [];
        // Jika array diberikan, ubah ke koleksi agar konsisten
        if (is_array($questions)) {
            $questions = collect($questions);
        }
        foreach ($questions as $q) {
            // $q diasumsikan objek dengan properti:
            // - lesson
            // - question_text
            // - explanation
            // - dan relasi subCategory (object) dengan properti name, jika eager-loaded
            $subcatName = null;
            if (isset($q->subCategory) && isset($q->subCategory->name)) {
                $subcatName = $q->subCategory->name;
            }
            // Jika $q->subCategory adalah array atau koleksi, sesuaikan:
            if (!$subcatName && isset($q->sub_category) && is_string($q->sub_category)) {
                $subcatName = $q->sub_category;
            }
            $out[] = [
                'sub_category' => $subcatName ?: 'Umum',
                'lesson'       => $q->lesson       ?? 'Umum',
                'description'  => $this->truncateText(strip_tags($q->question_text ?? ''), 80),
                'explanation'  => $q->explanation  ?? '-',
            ];
        }
        return $out;
    }


    /**
     * Prompt sederhana: fokus ke sub-bab pembahasan, 
     * ambil data dari array $wrong dan $correct.
     *
     * @param  array[]  $wrong   Array hasil buildWrongQuestionsAnalysis untuk soal salah
     * @param  array[]  $correct Array hasil buildWrongQuestionsAnalysis untuk soal benar
     * @param  int      $score
     * @return string  Prompt untuk AI
     */
// private function buildDetailedEvaluationPrompt(array $wrongArr, array $correctArr, int $score, array $subCategoryStats = [])
// {
//     // 1. Kumpulkan statistik per lesson dalam setiap sub_category
//     $lessonStats = [];
//     foreach ($correctArr as $q) {
//         $sub = $q['sub_category'] ?? 'Umum';
//         $lesson = $q['lesson'] ?? 'Umum';
//         $lessonStats[$sub][$lesson]['correct'] = ($lessonStats[$sub][$lesson]['correct'] ?? 0) + 1;
//         $desc = trim($q['description'] ?? '');
//         if ($desc) {
//             $lessonStats[$sub][$lesson]['descriptions'][] = $desc;
//         }
//     }
//     foreach ($wrongArr as $q) {
//         $sub = $q['sub_category'] ?? 'Umum';
//         $lesson = $q['lesson'] ?? 'Umum';
//         $lessonStats[$sub][$lesson]['wrong'] = ($lessonStats[$sub][$lesson]['wrong'] ?? 0) + 1;
//         $desc = trim($q['description'] ?? '');
//         if ($desc) {
//             $lessonStats[$sub][$lesson]['descriptions'][] = $desc;
//         }
//     }
//     // Buat summary deskripsi tiap lesson (maks 2)
//     foreach ($lessonStats as $sub => $lessons) {
//         foreach ($lessons as $lesson => $stat) {
//             $unique = array_unique($stat['descriptions'] ?? []);
//             $sample = array_slice($unique, 0, 2);
//             $lessonStats[$sub][$lesson]['summary'] = $sample ? implode(' ... ', $sample) : '-';
//         }
//     }

//     // 2. Format Analisa Per Materi (Lesson)
//     $materiSection = "🔍 **Analisa Per Materi (Lesson)**\n\n";
//     $materiSection .= "Kekuatan\n";
//     $foundStrength = false;
//     foreach ($lessonStats as $subcat => $lessons) {
//         $lines = [];
//         foreach ($lessons as $lesson => $stat) {
//             $c = $stat['correct'] ?? 0;
//             $w = $stat['wrong'] ?? 0;
//             if ($c + $w > 0 && $c >= $w) {
//                 $foundStrength = true;
//                 $lines[] = "- {$lesson}: {$stat['summary']} ({$c} benar, {$w} salah)";
//             }
//         }
//         if ($lines) {
//             $materiSection .= "Mata Pelajaran: {$subcat}\n";
//             foreach ($lines as $l) {
//                 $materiSection .= "{$l}\n";
//             }
//         }
//     }
//     if (!$foundStrength) {
//         $materiSection .= "- Tidak ada materi dengan jumlah benar ≥ salah.\n";
//     }

//     $materiSection .= "\nKelemahan\n";
//     $foundWeak = false;
//     foreach ($lessonStats as $subcat => $lessons) {
//         $lines = [];
//         foreach ($lessons as $lesson => $stat) {
//             $c = $stat['correct'] ?? 0;
//             $w = $stat['wrong'] ?? 0;
//             if ($w > $c) {
//                 $foundWeak = true;
//                 $lines[] = "- {$lesson}: {$stat['summary']} ({$c} benar, {$w} salah)";
//             }
//         }
//         if ($lines) {
//             $materiSection .= "Mata Pelajaran: {$subcat}\n";
//             foreach ($lines as $l) {
//                 $materiSection .= "{$l}\n";
//             }
//         }
//     }
//     if (!$foundWeak) {
//         $materiSection .= "- Tidak ada materi dengan jumlah salah > benar.\n";
//     }

//     // 3. Format Analisa Per Mata Pelajaran berdasarkan benar vs salah
//    // 3. Hitung ulang statistik per subCategory langsung dari lessonStats
// $subStats = [];  // ['subcat' => ['correct'=>int,'wrong'=>int]]
// foreach ($lessonStats as $subcat => $lessons) {
//     $sc = 0; $sw = 0;
//     foreach ($lessons as $stat) {
//         $sc += $stat['correct'] ?? 0;
//         $sw += $stat['wrong']   ?? 0;
//     }
//     $subStats[$subcat] = ['correct'=>$sc, 'wrong'=>$sw];
// }

// // 4. Format Analisa Per Mata Pelajaran berdasarkan $subStats
// $subcatSection = "\n📊 **Analisa Per Mata Pelajaran (SubCategory)**\n\n";

// // Kekuatan: benar ≥ salah
// $strengths = [];
// // Kelemahan: salah > benar
// $weaknesses = [];

// foreach ($subStats as $subcat => $stat) {
//     if ($stat['correct'] >= $stat['wrong']) {
//         $strengths[] = sprintf(
//             "%s (%d benar, %d salah%s)",
//             $subcat,
//             $stat['correct'],
//             $stat['wrong'],
//             !empty($subCategoryStats[$subcat]['average_score'])
//                 ? sprintf(", Skor: %.2f", $subCategoryStats[$subcat]['average_score'])
//                 : ''
//         );
//     } else {
//         $weaknesses[] = sprintf(
//             "%s (%d benar, %d salah%s)",
//             $subcat,
//             $stat['correct'],
//             $stat['wrong'],
//             !empty($subCategoryStats[$subcat]['average_score'])
//                 ? sprintf(", Skor: %.2f", $subCategoryStats[$subcat]['average_score'])
//                 : ''
//         );
//     }
// }

// $subcatSection .= "Kekuatan: " . (!empty($strengths) ? implode('; ', $strengths) : '–') . "\n";
// $subcatSection .= "Kelemahan: " . (!empty($weaknesses) ? implode('; ', $weaknesses) : '–') . "\n";


//     // 4. Gabungkan menjadi prompt
//     $prompt = <<<EOD
// Kamu adalah tutor yang mengevaluasi kompetensi siswa berdasarkan latihan soal.

// 📊 Nilai Total: {$score}/1000

// {$materiSection}

// {$subcatSection}

// Tulis ringkas, jelas, dan langsung ke poin minimal 600 kata.
// EOD;

//     return $prompt;
// }



private function buildDetailedEvaluationPrompt(array $wrongArr, array $correctArr, int $score, array $subCategoryStats = [])
{
    // 1. Hitung statistik per subCategory dari data benar dan salah
    $subStats = [];
    
    foreach ($correctArr as $q) {
        $subcat = $q['sub_category'] ?? 'Umum';
        $subStats[$subcat]['correct'] = ($subStats[$subcat]['correct'] ?? 0) + 1;
    }
    foreach ($wrongArr as $q) {
        $subcat = $q['sub_category'] ?? 'Umum';
        $subStats[$subcat]['wrong'] = ($subStats[$subcat]['wrong'] ?? 0) + 1;
    }

    // 2. Format analisa per mata pelajaran
    $subcatSection = "📊 **Analisa Per Mata Pelajaran**\n\n";
    $excellent = []; // >=80%
    $good = [];      // 70-79%
    $needsWork = []; // 50-69%
    $critical = [];  // <50%

    foreach ($subStats as $subcat => $stat) {
        $correct = $stat['correct'] ?? 0;
        $wrong = $stat['wrong'] ?? 0;
        $total = $correct + $wrong;
        $percentage = $total > 0 ? ($correct / $total) * 100 : 0;

        $displayText = sprintf(
            "%s (%d/%d soal, %.1f%%%s)",
            $subcat,
            $correct,
            $total,
            $percentage,
            !empty($subCategoryStats[$subcat]['average_score'])
                ? sprintf(", Skor IRT: %.2f", $subCategoryStats[$subcat]['average_score'])
                : ''
        );

        if ($percentage >= 80) {
            $excellent[] = $displayText;
        } elseif ($percentage >= 70) {
            $good[] = $displayText;
        } elseif ($percentage >= 50) {
            $needsWork[] = $displayText;
        } else {
            $critical[] = $displayText;
        }
    }

    if (!empty($excellent)) {
        $subcatSection .= "**🎯 Sangat Baik (≥80%):** " . implode('; ', $excellent) . "\n\n";
    }
    if (!empty($good)) {
        $subcatSection .= "**✅ Baik (70-79%):** " . implode('; ', $good) . "\n\n";
    }
    if (!empty($needsWork)) {
        $subcatSection .= "**⚠️ Perlu Perbaikan (50-69%):** " . implode('; ', $needsWork) . "\n\n";
    }
    if (!empty($critical)) {
        $subcatSection .= "**🚨 Perlu Perhatian Khusus (<50%):** " . implode('; ', $critical) . "\n\n";
    }

    // 3. Prompt yang fokus strategi relevan dengan ujian
    $prompt = <<<EOD
Kamu adalah tutor yang mengevaluasi kompetensi tryout siswa berdasarkan hasil ujian mereka.

📊 **Nilai Total:** {$score}/1000

{$subcatSection}

**Kriteria Evaluasi:**
- Sangat Baik (≥80%): Sudah menguasai dengan baik, pertahankan dan tingkatkan
- Baik (70-79%): Cukup menguasai, masih ada ruang improvement
- Perlu Perbaikan (50-69%): Butuh fokus belajar lebih intensif
- Perlu Perhatian Khusus (<50%): Prioritas utama untuk diperbaiki

**Instruksi Evaluasi:**
Berdasarkan kategorisasi mata pelajaran di atas, berikan analisis yang:

1. **Analisis Kekuatan & Kelemahan:** Sebutkan mata pelajaran mana yang sudah dikuasai dan mana yang perlu diperbaiki, sesuai dengan persentase yang tercatat.
2. **Prioritas Belajar:** Urutkan mata pelajaran berdasarkan tingkat urgensi perbaikan.
3. **Strategi Spesifik:** Berikan rekomendasi belajar yang **langsung relevan dengan mata pelajaran tersebut dan persiapan ujian**, seperti:
   - Mengerjakan latihan soal sesuai format ujian untuk mata pelajaran itu.
   - Menonton video pembahasan soal dari sumber terpercaya.
   - Mengulang dan memperbaiki kesalahan pada soal yang salah di percobaan sebelumnya.
   - Membuat rangkuman rumus, konsep, atau pola soal yang sering muncul di ujian.
   - Untuk kategori "Sangat Baik": strategi mempertahankan dan mencoba soal dengan tingkat kesulitan lebih tinggi.
   - Untuk kategori "Baik": tips agar naik level ke "Sangat Baik" dengan fokus pada tipe soal yang masih salah.
   - Untuk kategori "Perlu Perbaikan": metode drilling soal dan pembahasan intensif.
   - Untuk kategori "Perlu Perhatian Khusus": langkah intensive back-to-basic dan latihan bertahap.
4. **Action Plan:** Berikan jadwal dan target konkret untuk 2–4 minggu ke depan, termasuk jumlah soal yang harus dikerjakan per minggu, jadwal menonton video pembahasan, dan sesi evaluasi ulang.
5. **Motivasi:** Berikan dorongan positif dan perspektif yang membangun agar siswa percaya diri untuk memperbaiki hasilnya.

Tulislah evaluasi yang personal, actionable, dan memotivasi minimal 500 kata. Gunakan bahasa yang mudah dipahami siswa.
EOD;

    return $prompt;
}






    /**
     * Potong teks panjang, tambahkan "..." jika lebih dari $length karakter.
     *
     * @param  string  $text
     * @param  int     $length
     * @return string
     */
    private function truncateText(string $text, int $length = 100): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . '...';
    }


/**
 * Prompt rekomendasi berbasis nilai dengan alternatif meski sudah memenuhi passing score
 */
private function buildScoreBasedRecommendationPrompt($score, $majorRankings, $hasSubjects)
{
    // 1. Buat teks status untuk setiap jurusan pilihan user
    $majorText = '';
    foreach ($majorRankings as $major) {
        $gap = round($major['passing_score'] - $score, 2);
        $line = "• {$major['major_name']} ({$major['university']})\n";
        $line .= "  Passing Score: {$major['passing_score']} | ";

        if ($score >= $major['passing_score']) {
            $safeMargin = round($score - $major['passing_score'], 2);
            if ($safeMargin >= 50) {
                $line .= "✅ Sangat Aman (selisih +{$safeMargin} poin)\n";
            } else {
                $line .= "⚠️ Aman tapi tipis (selisih +{$safeMargin} poin) – peluang ada, tapi perlu hati-hati\n";
            }
        } else {
            $line .= "❌ Kurang {$gap} poin – masih perlu peningkatan\n dan tidak direkomendasikan";
        }

        $majorText .= $line;
    }

    $minSafeGap = 20; // ubah sesuai kebutuhan

$safeMajors = Major::with('university')
    ->where('passing_score', '<=', $score - $minSafeGap)
    ->orderByDesc('passing_score')
    ->limit(15)
    ->get();

    // Jika masih kosong, berarti nilai user sangat rendah
    if ($safeMajors->count() == 0) {
        // Cari jurusan dengan PS paling rendah
        $safeMajors = Major::with('university')
            ->orderBy('passing_score')
            ->limit(10)
            ->get();
    }

    // 3. FIXED: Cari jurusan TARGET STRETCH dengan range yang realistis
    $stretchMajors = Major::with('university')
        ->where('passing_score', '>', $score)
        ->where('passing_score', '<=', $score + 200) // Maksimal 200 poin di atas
        ->orderBy('passing_score')
        ->limit(12)
        ->get();

    // Jika kosong, ambil yang terdekat di atas score
    if ($stretchMajors->count() == 0) {
        $stretchMajors = Major::with('university')
            ->where('passing_score', '>', $score)
            ->orderBy('passing_score')
            ->limit(8)
            ->get();
    }

    // 4. FIXED: Format data alternatif – Pilihan Aman (SELALU ADA)
    $safeAlternativeText = '';
    if ($safeMajors->count() > 0) {
        // Randomize untuk variasi jika ada jurusan dengan passing score sama
        $safeMajorsArray = $safeMajors->shuffle()->take(10)->toArray();
        
        foreach ($safeMajorsArray as $major) {
            $scoreDifference = $score - $major['passing_score'];
            // HANYA yang PS <= score user yang masuk "Pilihan Aman"
            if ($scoreDifference >= 0) {
                $safeAlternativeText .= "• {$major['name']} ({$major['university']['name']}) - PS: {$major['passing_score']} (Aman: +{$scoreDifference} poin)\n";
            }
        }
        
        // Jika setelah filter ternyata kosong, ambil yang benar-benar di bawah score
        if (empty($safeAlternativeText)) {
            $trueSafeMajors = Major::with('university')
                ->where('passing_score', '<', $score)
                ->orderByDesc('passing_score')
                ->limit(10)
                ->get();
            
            foreach ($trueSafeMajors as $major) {
                $scoreDifference = $score - $major->passing_score;
                $safeAlternativeText .= "• {$major->name} ({$major->university->name}) - PS: {$major->passing_score} (Aman: +{$scoreDifference} poin)\n";
            }
        }
    } else {
        // Fallback - ambil jurusan dengan PS di bawah user saja
        $fallbackMajors = Major::with('university')
            ->where('passing_score', '<', $score)
            ->orderByDesc('passing_score')
            ->limit(10)
            ->get();
        
        if ($fallbackMajors->count() > 0) {
            foreach ($fallbackMajors as $major) {
                $scoreDifference = $score - $major->passing_score;
                $safeAlternativeText .= "• {$major->name} ({$major->university->name}) - PS: {$major->passing_score} (Aman: +{$scoreDifference} poin)\n";
            }
        } else {
            $safeAlternativeText = "Nilai Anda sudah sangat tinggi! Semua jurusan bisa dimasuki dengan aman.\n";
        }
    }

    // 5. FIXED: Format data alternatif – Target Stretch (SELALU ADA)
    $stretchAlternativeText = '';
    if ($stretchMajors->count() > 0) {
        // Randomize untuk variasi
        $stretchMajorsArray = $stretchMajors->shuffle()->take(10)->toArray();
        
        foreach ($stretchMajorsArray as $major) {
            $scoreDifference = $major['passing_score'] - $score;
            $stretchAlternativeText .= "• {$major['name']} ({$major['university']['name']}) - PS: {$major['passing_score']} (Target: +{$scoreDifference})\n";
        }
    } else {
        // Fallback - ambil jurusan dengan PS di atas user
        $fallbackStretch = Major::with('university')
            ->where('passing_score', '>', $score)
            ->orderBy('passing_score')
            ->limit(8)
            ->get();
        
        if ($fallbackStretch->count() > 0) {
            foreach ($fallbackStretch as $major) {
                $scoreDifference = $major->passing_score - $score;
                $stretchAlternativeText .= "• {$major->name} ({$major->university->name}) - PS: {$major->passing_score} (Target: +{$scoreDifference})\n";
            }
        } else {
            $stretchAlternativeText = "Nilai Anda sudah sangat tinggi! Fokus pada pilihan aman saja.\n";
        }
    }

    // 6. Catatan mata pelajaran
    $subjectNote = $hasSubjects
        ? "💡 Catatan: Karena ini tryout dengan mata pelajaran spesifik, pertimbangkan juga kesesuaian minat jurusan dengan kemampuan pada mapel tersebut."
        : "💡 Catatan: Rekomendasi berdasarkan nilai TPS dan kemampuan umum.";

    // 7. Buat bagian analisis jurusan user (semua pilihan)
    $evaluationText = "🎓 ANALISIS PELUANG BERDASARKAN DATA REAL:\n";
    $evaluationText .= "- Evaluasi gap nilai terhadap semua pilihan jurusan:\n\n";

    foreach ($majorRankings as $major) {
        $gap = round($major['passing_score'] - $score, 2);
        $line = "• {$major['major_name']} ({$major['university']})\n";
        $line .= "  Passing Score: {$major['passing_score']} | ";

        if ($score >= $major['passing_score']) {
            $safeMargin = round($score - $major['passing_score'], 2);
            if ($safeMargin >= 50) {
                $line .= "✅ Sangat Aman (selisih +{$safeMargin} poin)\n";
            } else {
                $line .= "⚠️ Aman tapi tipis (selisih +{$safeMargin} poin) – peluang ada, tapi perlu hati-hati\n";
            }
        } else {
            $line .= "❌ Kurang {$gap} poin – masih perlu peningkatan\n";
        }

        $evaluationText .= $line . "\n";
    }

    // 8. FIXED: Rekomendasi jurusan di Institut Teknologi Indonesia (ITI) - lebih fleksibel
    $itiMajors = [
        ['name' => 'Teknik Elektro', 'score_range' => [350, 1000]],
        ['name' => 'Teknik Mesin', 'score_range' => [350, 1000]],
        ['name' => 'Teknik Sipil', 'score_range' => [450, 1000]],
        ['name' => 'Arsitektur', 'score_range' => [500, 1000]],
        ['name' => 'Teknik Kimia', 'score_range' => [480, 1000]],
        ['name' => 'Teknik Industri', 'score_range' => [420, 1000]],
        ['name' => 'Perencanaan Wilayah Dan Kota', 'score_range' => [470, 1000]],
        ['name' => 'Teknologi Industri Pertanian', 'score_range' => [400, 1000]],
        ['name' => 'Teknik Informatika', 'score_range' => [480, 1000]],
        ['name' => 'Manajemen', 'score_range' => [430, 1000]],
        ['name' => 'Sistem Informasi', 'score_range' => [450, 1000]],
        ['name' => 'Teknik Lingkungan', 'score_range' => [400, 1000]],
    ];

    // Shuffle array ITI untuk variasi rekomendasi
    shuffle($itiMajors);
    
    $itiRecommendations = [];
    foreach ($itiMajors as $major) {
        [$minScore, $maxScore] = $major['score_range'];
        if ($score >= $minScore && $score <= $maxScore) {
            $confidence = $score >= ($minScore + 100) ? "Sangat Aman" : "Aman";
            $itiRecommendations[] = "• {$major['name']} (S1 - Institut Teknologi Indonesia) - {$confidence}";
        }
    }

    // Jika tidak ada yang cocok, berikan rekomendasi terdekat
    if (empty($itiRecommendations)) {
        $closestIti = [];
        foreach ($itiMajors as $major) {
            $distance = abs($major['score_range'][0] - $score);
            $closestIti[] = ['major' => $major, 'distance' => $distance];
        }
        
        // Sort berdasarkan jarak terdekat
        usort($closestIti, function($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });
        
        // Ambil 3 terdekat
        for ($i = 0; $i < min(3, count($closestIti)); $i++) {
            $major = $closestIti[$i]['major'];
            $needed = $major['score_range'][0] - $score;
            if ($needed > 0) {
                $itiRecommendations[] = "• {$major['name']} (S1 - Institut Teknologi Indonesia) - Butuh +{$needed} poin";
            }
        }
    }

    $itiText = count($itiRecommendations) > 0
        ? implode("\n", $itiRecommendations)
        : "Saat ini belum ada jurusan ITI yang sesuai dengan nilai Anda, fokus tingkatkan nilai terlebih dahulu.";

    // 9. Susun prompt akhir
    $prompt = <<<EOD
Kamu adalah konselor akademik yang REALISTIS dan HANYA menggunakan data jurusan yang tersedia di database.

🎯 Nilai Tryout: {$score}/1000

📋 Status Jurusan Pilihan:
{$majorText}

📚 DATA ALTERNATIF (SELALU TERSEDIA):

🟢 PILIHAN AMAN & REALISTIS:
{$safeAlternativeText}

🟡 TARGET STRETCH (Tantangan Realistis):
{$stretchAlternativeText}

{$subjectNote}

ATURAN KETAT:
- WAJIB HANYA menggunakan nama jurusan dan universitas yang tertera dalam "Pilihan Aman" atau "Target Stretch".
- DILARANG membuat nama jurusan atau universitas baru.
- Fokus pada analisis mendalam .

{$evaluationText}

💡 REKOMENDASI ALTERNATIF SISTEM:
- Dari "Pilihan Aman": Pilih 8-12 jurusan terbaik yang paling cocok dengan profil nilai JARAK NILAI MINIMAL 20.
- Dari "Target Stretch": Pilih 5-8 jurusan yang realistis dicapai dalam 2-4 bulan.
- Sertakan proyeksi prospek kerja (ringkas) dan alasan pemilihan.


🏫 REKOMENDASI KHUSUS ITI (Institut Teknologi Indonesia):
Jurusan-jurusan di ITI yang realistis sesuai nilai tryout:

{$itiText}

PENTING:
- Gunakan HANYA data yang benar-benar tersedia di database.
- Maksimal 5000 kata, bahasa praktis dan jujur.
- Berikan analisis mendalam dengan data konkret.
- Setiap rekomendasi harus disertai alasan karena jarak passing score dan nilai yang sedikit dan prospek kerjanya.
EOD;

    return $prompt;
}



    /**
     * Panggil Groq API dengan error handling yang baik
     */
    private function callGroqAPI($prompt)
    {
        try {
        // Pastikan prompt valid UTF-8
        $prompt = mb_convert_encoding($prompt, 'UTF-8', 'UTF-8');

        // Log untuk debugging
        Log::info('Calling Groq API with prompt length: ' . strlen($prompt));
        
        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->groqApiKey,
                'Content-Type' => 'application/json',
            ])
            ->post($this->groqUrl, [
                'model' => 'llama-3.1-8b-instant',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 99999,
                'top_p' => 0.9
            ]);

            // Log response untuk debugging
            Log::info('Groq API Response Status: ' . $response->status());
            
            if ($response->successful()) {
                $data = $response->json();
                $aiResponse = $data['choices'][0]['message']['content'] ?? 'AI tidak memberikan respons';
                
                // Log sukses
                Log::info('AI Response received successfully');
                
                return $aiResponse;
            } else {
                // Log error response
                Log::error('Groq API Error: ' . $response->body());
                return 'Maaf, AI sedang sibuk. Coba lagi dalam beberapa menit ya! 😊';
            }
            
        } catch (\Exception $e) {
            // Log exception
            Log::error('Groq API Exception: ' . $e->getMessage());
            return 'Ups, ada kendala teknis. Tim kami sedang memperbaikinya! 🔧';
        }
    }
}