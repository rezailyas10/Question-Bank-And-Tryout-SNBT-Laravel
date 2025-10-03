<?php

namespace App\Http\Controllers\Admin;

use App\Models\Exam;
use App\Models\Result;
use App\Models\ResultSubjectScore;
use Illuminate\Http\Request;
use App\Models\ResultDetails;
use App\Models\ResultsEvaluation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class TryoutScoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{

    // query dasar: hanya tryout buatan user yang login
    $query = Exam::where('exam_type', 'tryout')
                 ->where('created_by', Auth::user()->name);

    // ── Filter SEARCH: judul (bisa ditambah kolom lain kalau perlu) ─
    if ($request->filled('search')) {
        $query->where('title', 'like', '%' . $request->search . '%');
    }

    // ambil data + hitung results + pagination + keep query string
    $exams = $query->withCount('results')
                   ->latest()
                   ->paginate(10)
                   ->withQueryString();

    return view('pages.admin.nilai-tryout.index', [
        'exams'          => $exams,
    ]);
}


    /**
     * Menampilkan detail exam dengan analisis statistik pertanyaan
     * Method ini menghitung tingkat kesukaran setiap soal dan menyiapkan data untuk IRT
     */
    public function show($slug)
    {
        // Ambil data exam berdasarkan slug
        $exam = Exam::where('slug', $slug)->firstOrFail();

        // Load relasi yang diperlukan untuk optimasi query
        $exam->load([
            'questions.subCategory',    // Data soal dan kategorinya
            'results.details.question', // Detail jawaban setiap peserta
            'results.user'              // Data peserta
        ]);

        // Kelompokkan pertanyaan berdasarkan mata pelajaran (subCategory)
        $questionsBySubCategory = $exam->questions->where('status', 'Diterima')->groupBy('subCategory.name')->map(function ($questions, $subCategoryName) use ($exam) {
            return $questions->map(function ($question) use ($exam) {
                
                // Hitung statistik dasar untuk setiap pertanyaan
                $totalParticipants = Result::where('exam_id', $exam->id)->count();
                
                // Hitung total jawaban untuk pertanyaan ini
                $totalAnswers = ResultDetails::whereHas('result', function ($query) use ($exam) {
                    $query->where('exam_id', $exam->id);
                })->where('question_id', $question->id)->count();

                // Hitung jawaban benar untuk pertanyaan ini
                $correctAnswers = ResultDetails::whereHas('result', function ($query) use ($exam) {
                    $query->where('exam_id', $exam->id);
                })->where('question_id', $question->id)
                  ->where('correct', true)->count();

                // Jawaban salah = total jawaban - jawaban benar
                $incorrectAnswers = $totalAnswers - $correctAnswers;

                // Hitung tingkat kesukaran (proportion correct)
                // Nilai 0-1, semakin tinggi = semakin mudah
                $difficultyLevel = $totalParticipants > 0 ? round($correctAnswers / $totalParticipants, 3) : 0;

                // Klasifikasi tingkat kesukaran berdasarkan standar psikometri
                if ($difficultyLevel <= 0.30) {
                    $difficultyCategory = 'Sukar';      // ≤30% yang benar
                } elseif ($difficultyLevel <= 0.70) {
                    $difficultyCategory = 'Sedang';     // 31-70% yang benar
                } else {
                    $difficultyCategory = 'Mudah';      // >70% yang benar
                }

              $firstScore = ResultDetails::whereHas('result', function ($query) use ($exam) {
                $query->where('exam_id', $exam->id);
            })->where('question_id', $question->id)
            ->where('correct', true) // hanya jawaban yang benar
            ->value('score');

                $question->question_score = $firstScore ?? 0;

                // Tambahkan statistik ke objek question
                $question->total_participants = $totalParticipants;
                $question->total_answers = $totalAnswers;
                $question->correct_answers = $correctAnswers;
                $question->incorrect_answers = $incorrectAnswers;
                $question->difficulty_level = $difficultyLevel;
                $question->difficulty_category = $difficultyCategory;
                $question->correct_percentage = $totalAnswers > 0 ? round(($correctAnswers / $totalAnswers) * 100, 2) : 0;

                return $question;
            });
        });
        

        // Hitung total peserta ujian
        $totalParticipants = $exam->results->count();

         // Buat array rata-rata skor peserta per subcategory
    $averageScoresByResult = [];

    foreach ($exam->results as $result) {
    $scores = DB::table('result_subject_scores')
        ->join('sub_categories', 'result_subject_scores.sub_category_id', '=', 'sub_categories.id')
        ->where('result_subject_scores.result_id', $result->id)
        ->select(
            'sub_categories.name as subcategory_name',
            'result_subject_scores.irt_score as average_score'
        )
        ->get();

    $averageScoresByResult[$result->id] = $scores->pluck('average_score', 'subcategory_name')->toArray();
}

    // Kirim data ke view
    return view('pages.admin.nilai-tryout.show', compact(
        'exam', 
        'questionsBySubCategory', 
        'totalParticipants',
        'averageScoresByResult'
    ));
    }

    /**
     * Endpoint untuk melakukan perhitungan IRT analysis
     * Dipanggil via AJAX saat tombol "Calculate IRT" diklik
     */
    public function calculateIRT(Request $request, $slug)
{
    Log::info("=== Memulai Perhitungan IRT untuk exam: $slug ===");
    
    $exam = Exam::where('slug', $slug)->firstOrFail();
    $exam->load(['questions.subCategory', 'results.details.question', 'results.user']);

    $allIRTData = [];

    // Kelompokkan pertanyaan berdasarkan mata pelajaran
    $questionsBySubCategory = $exam->questions->groupBy('subCategory.name');
    
    // Proses setiap mata pelajaran secara terpisah
    foreach ($questionsBySubCategory as $subCategoryName => $questions) {
        Log::info("Memproses mata pelajaran: $subCategoryName dengan " . count($questions) . " soal");
        
        $irtData = $this->prepareIRTData($exam, $questions);
        
        if (empty($irtData['users']) || empty($irtData['items'])) {
            Log::warning("Melewati $subCategoryName - tidak ada data valid");
            continue;
        }
        
        $irtResults = $this->calculateIRTAnalysis($irtData);
        
        $allIRTData[$subCategoryName] = [
            'irt_results' => $irtResults
        ];
        
        // Simpan skor IRT per mata pelajaran ke tabel terpisah
        $this->saveIRTScores($exam, $questions, $irtResults);
    }

    // Hitung skor akhir berdasarkan rata-rata per mata pelajaran
    $this->calculateFinalScores($exam);

    return response()->json([
        'success' => true,
        'message' => 'Analisis IRT dan perhitungan skor final selesai!',
        'data' => $allIRTData
    ]);
}

    /**
     * Melakukan perhitungan IRT menggunakan Rasch Model (1 Parameter Logistic)
     * Ini adalah inti dari analisis IRT yang menghasilkan skor UTBK
     */
    private function calculateIRTAnalysis($sampleData)
    {
        $users = $sampleData['users'];      // Data peserta dan jawaban mereka
        $items = $sampleData['items'];      // ID soal-soal
        $numUsers = count($users);          // Jumlah peserta
        $numItems = count($items);          // Jumlah soal

        Log::info("Analisis IRT: $numUsers peserta, $numItems soal");
        
        // LANGKAH 1: Estimasi tingkat kesukaran setiap soal (parameter b)
        $itemDifficulties = [];
        
        foreach ($items as $item) {
            $correct = 0;  // Jumlah yang menjawab benar
            $total = 0;    // Total yang mengerjakan soal ini
            
            // Hitung proporsi yang menjawab benar untuk soal ini
            foreach ($users as $user) {
                if (isset($user['responses'][$item])) {
                    $total++;
                    if ($user['responses'][$item] == 1) {
                        $correct++;
                    }
                }
            }
            
            // Proporsi yang menjawab benar (p-value)
            $proportion = $total > 0 ? $correct / $total : 0;
            
            // Hindari proporsi ekstrem (0 atau 1) yang menyebabkan masalah matematis
            $proportion = max(0.01, min(0.99, $proportion));
            
            // Konversi proporsi ke skala logit untuk mendapatkan parameter kesukaran
            // Formula: b = -logit(p) = -ln(p/(1-p))
            // Semakin mudah soal (p tinggi), semakin negatif nilai b
            $difficulty = -log($proportion / (1 - $proportion));
            
            $itemDifficulties[$item] = [
                'proportion_correct' => $proportion,
                'difficulty' => $difficulty,
                'total_responses' => $total,
                'correct_responses' => $correct
            ];
            
            Log::info("Soal $item: proporsi benar=$proportion, kesukaran=$difficulty");
        }

        // LANGKAH 2: Estimasi kemampuan setiap peserta (parameter theta)
        $personAbilities = [];
        
        foreach ($users as $user) {
            $userId = $user['user_id'];
            
            // Hitung skor mentah (total jawaban benar)
            $rawScore = array_sum(array_values($user['responses']));
            $totalItems = count($user['responses']);
            
            // Handle kasus ekstrem
            if ($rawScore == 0) {
                // Semua salah - kemampuan sangat rendah
                $theta = -3.0;
                Log::info("Peserta $userId: Semua salah, theta = -3.0");
            } elseif ($rawScore == $totalItems) {
                // Semua benar - kemampuan sangat tinggi
                $theta = 3.0;
                Log::info("Peserta $userId: Semua benar, theta = 3.0");
            } else {
                // Estimasi theta menggunakan algoritma Newton-Raphson
                $theta = $this->estimateTheta($user['responses'], $itemDifficulties);
                Log::info("Peserta $userId: Skor mentah=$rawScore/$totalItems, theta=$theta");
            }
            
            $personAbilities[$userId] = $theta;
        }

        // LANGKAH 3: Konversi theta ke skala UTBK (150-1000)
        $results = [];
        
        // Hitung statistik theta untuk normalisasi
        $allThetas = array_values($personAbilities);
        $thetaMean = array_sum($allThetas) / count($allThetas);
        $thetaStd = $this->calculateStandardDeviation($allThetas);
        
        Log::info("Statistik theta: rata-rata=$thetaMean, std=$thetaStd");
        
        // Konversi setiap peserta ke skor UTBK
        foreach ($users as $user) {
            $userId = $user['user_id'];
            $theta = $personAbilities[$userId];
            $rawScore = array_sum(array_values($user['responses']));
            
            // Standardisasi theta menjadi z-score
            $zScore = $thetaStd > 0 ? ($theta - $thetaMean) / $thetaStd : 0;
            
            // Konversi z-score ke skala UTBK
            // Rumus: UTBK = 500 + (100 × z-score)
            // Range dibatasi antara 150-1000
            if ($rawScore == 0) {
                $utbkScore = 150;  // Skor minimum untuk yang semua salah
            } else {
                $utbkScore = 500 + (100 * $zScore);
                $utbkScore = max(150, min(1000, $utbkScore));  // Batasi range
            }
            
            // Simpan hasil untuk peserta ini
            $results[] = [
                'user_id' => $userId,
                'result_id' => $user['result_id'],
                'user_name' => $user['name'],
                'responses' => $user['responses'],
                'raw_score' => $rawScore,
                'theta' => round($theta, 4),
                'z_score' => round($zScore, 4),
                'utbk_score' => round($utbkScore, 2)
            ];
            
            Log::info("Peserta $userId final: mentah=$rawScore, theta=$theta, z=$zScore, utbk=$utbkScore");
        }

        // Log ringkasan untuk validasi
        $utbkScores = array_column($results, 'utbk_score');
        $utbkMean = array_sum($utbkScores) / count($utbkScores);
        $utbkStd = $this->calculateStandardDeviation($utbkScores);
        
        Log::info("Skor UTBK final: rata-rata=$utbkMean, std=$utbkStd, min=" . min($utbkScores) . ", max=" . max($utbkScores));

        return [
            'item_difficulties' => $itemDifficulties,
            'person_results' => $results,
            'summary_stats' => $this->calculateSummaryStats($results),
            'theta_stats' => [
                'mean' => $thetaMean,
                'std' => $thetaStd
            ]
        ];
    }

    private function calculateFinalScoresFromSubjects($exam, $subjectScoresByResult)
{
    Log::info("Menghitung skor akhir dari rata-rata mata pelajaran untuk exam {$exam->id}");

    foreach ($subjectScoresByResult as $resultId => $subjectScores) {
        // Hitung rata-rata dari semua mata pelajaran
        if (count($subjectScores) > 0) {
            $totalScore = array_sum($subjectScores);
            $subjectCount = count($subjectScores);
            $finalScore = round($totalScore / $subjectCount, 2);
        } else {
            $finalScore = 0;
        }

        // Update skor akhir di tabel results
        Result::where('id', $resultId)->update(['score' => $finalScore]);

        Log::info("→ Result {$resultId} skor akhir (rata-rata dari {$subjectCount} mata pelajaran) = {$finalScore}");
        
        // Update ResultsEvaluation seperti kode asli
        $correctCount = ResultDetails::where('result_id', $resultId)
            ->where('correct', 1)
            ->count();
        $wrongCount = ResultDetails::where('result_id', $resultId)
            ->where('correct', 0)
            ->count();
        $zeroCount = ResultDetails::where('result_id', $resultId)
            ->where('correct', null)
            ->count();
            
        ResultsEvaluation::updateOrCreate(
            ['result_id' => $resultId],
            [
                'score'   => $finalScore,
                'correct' => $correctCount,
                'wrong'   => $wrongCount,
                'empty'   => $zeroCount
            ]
        );
    }

    Log::info("Perhitungan skor akhir selesai untuk exam {$exam->id}");
}


    /**
     * Estimasi kemampuan peserta (theta) menggunakan Maximum Likelihood Estimation
     * dengan algoritma Newton-Raphson untuk mencari nilai theta yang optimal
     */
    private function estimateTheta($responses, $itemDifficulties)
    {
        // Estimasi awal theta berdasarkan proporsi jawaban benar
        $rawScore = array_sum(array_values($responses));
        $totalItems = count($responses);
        $proportion = $rawScore / $totalItems;
        
        // Konversi proporsi ke logit sebagai starting point
        $proportion = max(0.01, min(0.99, $proportion));
        $theta = log($proportion / (1 - $proportion));
        
        // Parameter untuk iterasi Newton-Raphson
        $maxIterations = 100;    // Maksimal 100 iterasi
        $tolerance = 0.001;      // Batas konvergensi
        
        Log::info("Estimasi theta awal: $theta (skor mentah: $rawScore/$totalItems)");
        
        // Iterasi Newton-Raphson untuk mencari theta optimal
        for ($iter = 0; $iter < $maxIterations; $iter++) {
            $logLikelihood = 0;      // Log-likelihood function
            $firstDerivative = 0;    // Turunan pertama (score function)
            $secondDerivative = 0;   // Turunan kedua (information function)
            
            // Hitung untuk setiap soal yang dijawab
            foreach ($responses as $item => $response) {
                if (!isset($itemDifficulties[$item])) continue;
                
                $difficulty = $itemDifficulties[$item]['difficulty'];
                
                // Rasch Model: P(X=1|θ,b) = exp(θ-b) / (1 + exp(θ-b))
                $exponent = $theta - $difficulty;
                
                // Cegah overflow numerik
                $exponent = max(-50, min(50, $exponent));
                
                // Probabilitas menjawab benar
                $probability = exp($exponent) / (1 + exp($exponent));
                
                // Kontribusi ke log-likelihood
                if ($response == 1) {
                    $logLikelihood += log($probability);
                } else {
                    $logLikelihood += log(1 - $probability);
                }
                
                // Turunan pertama (Score function)
                $firstDerivative += $response - $probability;
                
                // Turunan kedua (Information function) - selalu negatif
                $secondDerivative -= $probability * (1 - $probability);
            }
            
            // Update Newton-Raphson: θ_new = θ_old - f'(θ)/f''(θ)
            if (abs($secondDerivative) > 0.0001) {
                $thetaNew = $theta - ($firstDerivative / $secondDerivative);
                
                // Batasi theta dalam range yang masuk akal (-6 sampai 6)
                $thetaNew = max(-6, min(6, $thetaNew));
            } else {
                // Matrix singular, hentikan iterasi
                Log::warning("Matrix singular pada iterasi $iter");
                break;
            }
            
            // Cek konvergensi
            $change = abs($thetaNew - $theta);
            if ($change < $tolerance) {
                Log::info("Konvergen pada iterasi $iter, perubahan=$change");
                $theta = $thetaNew;
                break;
            }
            
            $theta = $thetaNew;
        }
        
        return $theta;
    }

    /**
 * Menyimpan skor IRT ke tabel result_subject_scores
 * Setiap mata pelajaran mendapat skor terpisah per peserta
 */
private function saveIRTScores($exam, $questions, $irtResults)
{
    Log::info("Menyimpan skor IRT per mata pelajaran ke tabel result_subject_scores");

    // Ambil sub_category_id dari pertanyaan pertama (semua pertanyaan dalam group ini punya sub_category sama)
    $subCategoryId = $questions->first()->sub_category_id;
    $subCategoryName = $questions->first()->subCategory->name;
    
    Log::info("Sub Category ID: {$subCategoryId}, Name: {$subCategoryName}");

    // Simpan skor untuk setiap peserta
    foreach ($irtResults['person_results'] as $personResult) {
        $resultId = $personResult['result_id'];
        $irtScore = $personResult['utbk_score'];

        // Insert atau update skor per mata pelajaran
        DB::table('result_subject_scores')->updateOrInsert(
            [
                'result_id' => $resultId,
                'sub_category_id' => $subCategoryId
            ],
            [
                'irt_score' => $irtScore,
                'updated_at' => now()
            ]
        );

        Log::info("→ Result {$resultId}, SubCategory {$subCategoryId} ({$subCategoryName}) = {$irtScore}");
    }
}

    /**
 * Menghitung skor akhir per peserta berdasarkan rata-rata skor per mata pelajaran
 * MENGGUNAKAN DATA DARI TABEL result_subject_scores
 */
private function calculateFinalScores($exam)
{
    Log::info("Menghitung skor akhir dari tabel result_subject_scores untuk exam {$exam->id}");

    // Ambil semua result_id di exam ini
    $resultIds = Result::where('exam_id', $exam->id)->pluck('id')->toArray();

    foreach ($resultIds as $resultId) {
        // Hitung rata-rata skor dari tabel result_subject_scores
        $averageScore = DB::table('result_subject_scores')
            ->where('result_id', $resultId)
            ->avg('irt_score');

        $finalScore = $averageScore ? round($averageScore, 2) : 0;

        // Update skor akhir di tabel results
        Result::where('id', $resultId)->update(['score' => $finalScore]);

        // Hitung statistik jawaban benar/salah untuk ResultsEvaluation
        $correctCount = ResultDetails::where('result_id', $resultId)->where('correct', 1)->count();
        $wrongCount = ResultDetails::where('result_id', $resultId)->where('correct', 0)->count();
        $zeroCount = ResultDetails::where('result_id', $resultId)->where('correct', null)->count();

        // Update evaluasi
        ResultsEvaluation::updateOrCreate(
            ['result_id' => $resultId],
            [
                'score'   => $finalScore,
                'correct' => $correctCount,
                'wrong'   => $wrongCount,
                'empty'   => $zeroCount
            ]
        );

        Log::info("→ Result {$resultId} skor akhir = {$finalScore}");
    }

    Log::info("Perhitungan skor akhir selesai untuk exam {$exam->id}");
}


    /**
     * Menyiapkan data dari database ke format yang diperlukan untuk analisis IRT
     */
    private function prepareIRTData($exam, $questions)
    {
        $users = [];  // Data peserta dan jawaban mereka
        $items = [];  // ID soal dalam format IRT
        

        // Buat array item dari question IDs
        foreach ($questions as $question) {
            $items[] = "Item_" . $question->id;
        }

        // Ambil semua hasil ujian untuk exam ini
        $results = Result::where('exam_id', $exam->id)
                         ->with(['details', 'user'])
                         ->get();

        // Proses setiap peserta ujian
        foreach ($results as $result) {
            $responses = [];
            
            // Untuk setiap pertanyaan dalam mata pelajaran ini
            foreach ($questions as $question) {
                $itemKey = "Item_" . $question->id;
                
                // Cari jawaban peserta untuk pertanyaan ini
                $resultDetail = $result->details->firstWhere('question_id', $question->id);
                
                // Konversi ke format binary (0/1) berdasarkan kolom 'correct'
                if ($resultDetail) {
                    $responses[$itemKey] = $resultDetail->correct ? 1 : 0;
                } else {
                    $responses[$itemKey] = 0;  // Tidak dijawab = salah
                }
            }
            
            // Simpan data peserta
            $users[] = [
                'user_id' => $result->user_id,
                'result_id' => $result->id,
                'name' => $result->user->name ?? "User " . $result->user_id,
                'responses' => $responses
            ];
        }

        return [
            'users' => $users,
            'items' => $items
        ];
    }

    /**
     * Menghitung statistik deskriptif untuk hasil IRT
     */
    private function calculateSummaryStats($results)
    {
        $thetas = array_column($results, 'theta');
        $utbkScores = array_column($results, 'utbk_score');
        $rawScores = array_column($results, 'raw_score');

        return [
            'theta' => [
                'mean' => round(array_sum($thetas) / count($thetas), 4),
                'std'  => round($this->calculateStandardDeviation($thetas), 4),
                'min'  => round(min($thetas), 4),
                'max'  => round(max($thetas), 4),
            ],
            'utbk_score' => [
                'mean' => round(array_sum($utbkScores) / count($utbkScores), 2),
                'std'  => round($this->calculateStandardDeviation($utbkScores), 2),
                'min'  => round(min($utbkScores), 2),
                'max'  => round(max($utbkScores), 2),
            ],
            'raw_score' => [
                'mean' => round(array_sum($rawScores) / count($rawScores), 2),
                'std'  => round($this->calculateStandardDeviation($rawScores), 2),
                'min'  => min($rawScores),
                'max'  => max($rawScores),
            ],
        ];
    }

    /**
     * Menghitung standard deviation dengan formula yang benar
     */
    private function calculateStandardDeviation($values)
    {
        if (count($values) <= 1) return 0;
        
        $mean = array_sum($values) / count($values);
        $squaredDifferences = array_map(function($value) use ($mean) {
            return pow($value - $mean, 2);
        }, $values);
        
        // Menggunakan sample standard deviation (n-1)
        $variance = array_sum($squaredDifferences) / (count($values) - 1);
        return sqrt($variance);
    }

    /**
     * Endpoint untuk mendapatkan data response dalam format JSON
     * Digunakan untuk debugging atau analisis lebih lanjut
     */
    public function responseData($slug, $categoryName)
    {
        // Ambil exam
        $exam = Exam::where('slug', $slug)->firstOrFail();

        // Ambil pertanyaan untuk mata pelajaran tertentu
        $questions = $exam->questions
                          ->filter(function($q) use ($categoryName) {
                              return $q->subCategory && $q->subCategory->name === $categoryName;
                          })
                          ->values(); 

        // Siapkan header pertanyaan
        $questionsPayload = $questions->map(fn($q) => ['id' => $q->id]);

        // Ambil semua hasil dan jawaban
        $results = $exam->results()->with(['user','details'])->get();

        // Buat array responses
        $responses = $results->map(function($result) use ($questions) {
            $answers = [];
            $totalCorrect = 0;

            foreach ($questions as $question) {
                $detail = $result->details->firstWhere('question_id', $question->id);
                
                if ($detail) {
                    $ans = $detail->correct ? 1 : 0;
                } else {
                    $ans = 0;
                }
                
                $answers[] = $ans;
                $totalCorrect += $ans;
            }

            return [
                'name' => $result->user->name,
                'answers' => $answers,
                'total_correct' => $totalCorrect,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'questions' => $questionsPayload,
                'responses' => $responses
            ]
        ]);
    }

    /**
     * Reset semua skor ke 0
     * Berguna untuk testing atau reset data
     */
    public function resetScores($slug)
    {
        try {
            $exam = Exam::where('slug', $slug)->firstOrFail();
            
            // Reset score di ResultDetails menjadi 0
            ResultSubjectScore::whereHas('result', function ($query) use ($exam) {
                $query->where('exam_id', $exam->id);
            })->update(['irt_score' => 0]);
            
            // Reset score di Result menjadi 0
            Result::where('exam_id', $exam->id)->update(['score' => 0]);
            
            return response()->json([
                'success' => true,
                'message' => 'Semua skor telah direset ke 0!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error mereset skor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
 * Endpoint untuk mendapatkan rata-rata skor per subcategory untuk setiap peserta
 * Digunakan untuk menampilkan skor yang benar per mata pelajaran
 */
public function detailedScores($slug, $categoryName)
{
    try {
        $categoryName = urldecode($categoryName);
        $exam = Exam::where('slug', $slug)->firstOrFail();
        
        // Ambil sub_category_id berdasarkan nama
        $subCategory = DB::table('sub_categories')->where('name', $categoryName)->first();
        
        if (!$subCategory) {
            return response()->json([
                'success' => false,
                'message' => 'Sub category not found'
            ], 404);
        }

        $resultIds = Result::where('exam_id', $exam->id)->pluck('id')->toArray();
        
        if (empty($resultIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No results found for this exam'
            ], 404);
        }
        
        $detailedScores = [];
        
        foreach ($resultIds as $resultId) {
            // Ambil data user
            $result = Result::with('user')->find($resultId);
            
            if (!$result || !$result->user) {
                continue;
            }

            // Ambil skor IRT untuk mata pelajaran ini dari tabel result_subject_scores
            $subjectScore = DB::table('result_subject_scores')
                ->where('result_id', $resultId)
                ->where('sub_category_id', $subCategory->id)
                ->value('irt_score');

            // Hitung statistik jawaban benar/salah untuk mata pelajaran ini
            $stats = DB::table('result_details')
                ->join('questions', 'result_details.question_id', '=', 'questions.id')
                ->join('sub_categories', 'questions.sub_category_id', '=', 'sub_categories.id')
                ->select([
                    DB::raw('SUM(CASE WHEN result_details.correct = 1 THEN 1 ELSE 0 END) AS correct'),
                    DB::raw('COUNT(*) AS total'),
                    DB::raw('(SUM(CASE WHEN result_details.correct = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100) AS percentage'),
                ])
                ->where('result_details.result_id', $resultId)
                ->where('sub_categories.name', $categoryName)
                ->first();
            
            $detailedScores[] = [
                'user_id' => $result->user_id,
                'user_name' => $result->user->name,
                'result_id' => $resultId,
                'subcategory_name' => $categoryName,
                'correct_answers' => $stats->correct ?? 0,
                'total_questions' => $stats->total ?? 0,
                'percentage' => round($stats->percentage ?? 0, 2),
                'average_score' => round($subjectScore ?? 0, 2) // SKOR IRT DARI TABEL result_subject_scores
            ];
        }
        
        // Hitung statistik agregat dari skor IRT per mata pelajaran
        $irtScores = collect($detailedScores)->pluck('average_score')->filter();

        $stats = [
            'mean' => $irtScores->avg() ?? 0,
            'max' => $irtScores->max() ?? 0,
            'min' => $irtScores->min() ?? 0,
            'std' => $irtScores->count() > 1
                ? sqrt($irtScores->map(fn($x) => pow($x - $irtScores->avg(), 2))->sum() / ($irtScores->count() - 1))
                : 0
        ];

        return response()->json([
            'success' => true,
            'data' => $detailedScores,
            'utbk_score' => array_map(fn($v) => round($v, 2), $stats),
            'debug' => [
                'exam_slug' => $slug,
                'category_name' => $categoryName,
                'sub_category_id' => $subCategory->id,
                'results_count' => count($detailedScores)
            ]
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error getting detailed scores: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}
}

    





