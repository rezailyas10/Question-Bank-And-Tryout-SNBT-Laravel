<?php

namespace App\Services;

use App\Models\Major;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIEvaluationService
{
    private $groqApiKey;
    private $groqUrl;

    public function __construct()
    {
        // Set API key dari env
        $this->groqApiKey = env('GROQ_API_KEY');
        $this->groqUrl = 'https://api.groq.com/openai/v1/chat/completions';
    }

    /**
     * Evaluasi mendalam per soal dengan lesson yang spesifik
     */
    public function evaluatePerformance($wrongQuestions, $correctQuestions, $score)
{
    // Bentuk analisis array sederhana
    $wrong = $this->buildWrongQuestionsAnalysis($wrongQuestions);
    $correct = $this->buildWrongQuestionsAnalysis($correctQuestions);

    // Buat prompt
    $prompt = $this->buildDetailedEvaluationPrompt($wrong, $correct, $score);

    return $this->callGroqAPI($prompt);
}



    /**
     * Analisis soal yang salah dengan lesson spesifik
     */
    /**
 * Konversi Collection<Question> jadi array[] dengan keys yg dibutuhkan
 */
private function buildWrongQuestionsAnalysis($questions)
{
    $out = [];
    foreach ($questions as $q) {
        $out[] = [
            'lesson'       => $q->lesson ?? 'Umum',
            'description'  => $this->truncateText(strip_tags($q->question_text), 80),
            'explanation'  => $q->explanation ?? '-'
        ];
    }
    return $out;
}

   /**
 * Prompt evaluasi yang fokus pada perbaikan per soal dengan analisis kekuatan dan kelemahan
 */
/**
 * Prompt sederhana: fokus ke sub-bab pembahasan, ambil data dari array $wrong/$correct
 */
   private function buildDetailedEvaluationPrompt(array $wrong, array $correct, int $score)
{
    $lessonStats = [];

    // 1) Hitung jumlah soal benar per lesson
    foreach ($correct as $q) {
        $lesson = $q['lesson'];
        if (!isset($lessonStats[$lesson])) {
            $lessonStats[$lesson] = ['correct' => 0, 'wrong' => 0, 'description' => $q['description']];
        }
        $lessonStats[$lesson]['correct']++;
    }

    // 2) Hitung jumlah soal salah per lesson
    foreach ($wrong as $q) {
        $lesson = $q['lesson'];
        if (!isset($lessonStats[$lesson])) {
            $lessonStats[$lesson] = ['correct' => 0, 'wrong' => 0, 'description' => $q['description']];
        }
        $lessonStats[$lesson]['wrong']++;
    }

    // 3) Kelompokkan ke kekuatan atau kelemahan
    $strengthLessons = [];
    $weakLessons = [];

    foreach ($lessonStats as $lesson => $data) {
        $benar = $data['correct'];
        $salah = $data['wrong'];
        $desc = $data['description'];

        if ($benar > $salah) {
            $strengthLessons[$lesson] = ['count' => $benar, 'description' => $desc];
        } else {
            $weakLessons[$lesson] = ['count' => $salah, 'description' => $desc];
        }
    }

    // 4) Format KEKUATAN
    $str = "💪 **KEKUATAN SUBBAB PEMBAHASAN**\n";
    if (count($strengthLessons)) {
        foreach ($strengthLessons as $lesson => $data) {
            $str .= "- {$lesson} ({$data['count']} soal benar): {$data['description']}\n";
        }
    } else {
        $str .= "- Tidak ada subbab yang berhasil dikuasai.\n";
    }

    // 5) Format KELEMAHAN
    $weakSection = "⚠️ **KELEMAHAN SUBBAB PEMBAHASAN**\n";
    if (count($weakLessons)) {
        foreach ($weakLessons as $lesson => $data) {
            $wrongCount = $data['count'];
            $desc = $data['description'];
            $wrongText = $wrongCount > 0 ? "({$wrongCount} soal salah)" : "(tidak dijawab)";
            $weakSection .= "- {$lesson} {$wrongText}: {$desc}\n";
        }
    } else {
        $weakSection .= "- Tidak ada kelemahan; semua soal dikerjakan dengan benar.\n";
    }

    // 6) Strategi Belajar
    $strategy = "🎯 **STRATEGI PEMBELAJARAN**\n";
    if (count($weakLessons)) {
        foreach ($weakLessons as $lesson => $_) {
            $strategy .= "- Latih soal pada subbab **{$lesson}** setiap hari.\n";
            $strategy .= "- Tinjau ulang konsep di subbab **{$lesson}** (video atau ringkasan).\n";
        }
    } else {
        $strategy .= "- Pertahankan konsistensi belajar.\n";
    }

    // 7) Gabung jadi satu
    return <<<EOD
Kamu adalah tutor yang mengevaluasi kompetensi siswa berdasarkan latihan soal.

📊 Nilai: {$score}/100

{$str}

{$weakSection}

{$strategy}

Tulis ringkas, jelas, dan langsung ke poin (maks 1500 kata).
Gunakan kata Anda, bukan mereka.
EOD;
}

//  /**
//      * Prompt rekomendasi kampus
//      */
// public function recommendMajors(int $score, array $subjectStats)
//     {
//         $prompt = $this->buildAcademicRecommendationPrompt($score, $subjectStats);
//         return $this->callGroqAPI($prompt);
//     }

//     /**
//      * Prompt rekomendasi khusus untuk tes kemampuan akademik
//      * Berbeda dengan TPS yang lebih umum
//      */
//   private function buildAcademicRecommendationPrompt(int $score, array $subjectStats)
//     {
//         // Bangun teks performance mata pelajaran
//         $subText = '';
//         foreach ($subjectStats as $subject => $stat) {
//             $subText .= "• {$subject}: {$stat['correct']}/{$stat['total']} benar\n";
//         }

//         return <<<PROMPT
// Kamu adalah konselor akademik untuk Tes Kemampuan Akademik (TKA).  
// Berdasarkan data berikut:

// 🎯 Nilai Total: {$score}/100  
// 📊 Performance per Mata Pelajaran:  
// {$subText}

// Tugasmu:
// 1. **Analisis**: Ringkas kekuatan siswa di mata pelajaran yang paling tinggi akurasinya, dan kelemahan di yang terendah.
// 2. **Rekomendasi Jurusan**: Pilih 2–3 jurusan yang paling cocok dengan profil akademik siswa.  
// 3. **Alasan**: Jelaskan secara singkat kenapa jurusan tersebut sesuai (misal: “Kamu unggul di Matematika, jadi jurusan Statistika cocok karena…”.)  
// 4. **Langkah Selanjutnya**: Beri 1–2 tips belajar untuk memperkuat kelemahan.

// Ketentuan:
// - Hanya gunakan nama jurusan umum (Informatika, Statistika, Teknik Elektro, dst).
// - Jangan sebut passing score atau data lain yang tidak ada.
// - Jawaban dalam 300–400 kata, bahasa ringkas dan mudah dipahami.
// PROMPT;
//     }


  /**
     * Helper untuk memotong teks panjang
     */
    private function truncateText($text, $length = 100)
    {
        return strlen($text) > $length ? substr($text, 0, $length) . '...' : $text;
    }


    /**
     * Panggil Groq API
     */
   private function callGroqAPI($prompt)
{
    try {
        $prompt = mb_convert_encoding($prompt, 'UTF-8', 'UTF-8');

        Log::info('Calling Groq API with prompt length: ' . strlen($prompt));

        $start = microtime(true);

        try {
            $response = Http::retry(2, 3000)
                ->timeout(90)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->groqApiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->groqUrl, [
                    'model' => 'llama-3.1-8b-instant',
                    'messages' => [[
                        'role' => 'user',
                        'content' => $prompt
                    ]],
                    'temperature' => 0.7,
                    'max_tokens' => 5000,
                    'top_p' => 0.9
                ]);
        } catch (\Illuminate\Http\Client\RequestException $e) {
            if ($e->getCode() === 28 || str_contains($e->getMessage(), 'timeout')) {
                // Timeout terjadi, potong prompt agar lebih ringan
                Log::warning('Timeout terjadi. Prompt dipotong jadi 1500 karakter.');
                $prompt = substr($prompt, 0, 1500);

                $response = Http::retry(1, 3000)
                    ->timeout(60)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $this->groqApiKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->post($this->groqUrl, [
                        'model' => 'llama-3.1-8b-instant',
                        'messages' => [[
                            'role' => 'user',
                            'content' => $prompt
                        ]],
                        'temperature' => 0.7,
                        'max_tokens' => 5000,
                        'top_p' => 0.9
                    ]);
            } else {
                throw $e;
            }
        }

        $duration = microtime(true) - $start;
        Log::info("Groq response time: {$duration} seconds");

        if ($response->successful()) {
            $data = $response->json();
            return $data['choices'][0]['message']['content'] ?? 'AI tidak memberikan respons';
        } else {
            Log::error('Groq API Error: ' . $response->body());
            return 'Maaf, AI sedang sibuk. Coba lagi dalam beberapa menit ya! 😊';
        }
    } catch (\Exception $e) {
        Log::error('Groq API Exception: ' . $e->getMessage());
        return 'Ups, ada kendala teknis. Tim kami sedang memperbaikinya! 🔧';
    }
}


}