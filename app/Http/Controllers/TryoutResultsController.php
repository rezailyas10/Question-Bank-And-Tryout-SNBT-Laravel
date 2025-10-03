<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Exam;
use App\Models\Result;
use App\Models\UserMajor;
use Illuminate\Http\Request;
use App\Models\ResultDetails;
use App\Models\ResultsEvaluation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\AIEvaluationService1;

class TryoutResultsController extends Controller
{
      /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

     private $aiService;

    public function __construct(AIEvaluationService1 $aiService)
    {
        // Inject AI service untuk evaluasi
        $this->aiService = $aiService;
    }
   

public function leaderboard(Request $request, $examId, $resultId)
{
    // Ambil exam & result utama
    $exam   = Exam::findOrFail($examId);
    $result = Result::with('user.userMajor.major.university')
                    ->findOrFail($resultId);

    // **HANYA** jurusan user ini:
    $userMajors = UserMajor::with('major.university')
        ->where('user_id', $result->user_id)
        ->get();

    // Tangkap filter
    $majorId = $request->query('major_id');
    $search  = $request->query('search');

    // Bangun query leaderboard
    $qb = Result::with('user.userMajor.major.university')
        ->where('exam_id', $examId);

    // Filter by selected jurusan
    if ($majorId) {
        $qb->whereHas('user.userMajor', function($q) use($majorId) {
            $q->where('major_id', $majorId);
        });
    }

    // Filter by search kata kunci
    if ($search) {
        $qb->where(function($q) use($search) {
            $q->whereHas('user', fn($u) =>
                    $u->where('name','like',"%{$search}%")
                )
                ->orWhereHas('user.userMajor.major', fn($m) =>
                    $m->where('name','like',"%{$search}%")
                )
                ->orWhereHas('user.userMajor.major.university', fn($u) =>
                    $u->where('name','like',"%{$search}%")
                );
        });
    }

    // Get total count for current filter
    $totalFiltered = $qb->count();

    // Paginate & pertahankan query string
    $results = $qb->orderByDesc('score')
                  ->paginate(10)
                  ->withQueryString();

    // Hitung peringkat user saat ini dalam konteks filter yang sama
    $userRankQuery = Result::where('exam_id', $examId);
    
    if ($majorId) {
        $userRankQuery->whereHas('user.userMajor', function($q) use($majorId) {
            $q->where('major_id', $majorId);
        });
    }
    
    if ($search) {
        $userRankQuery->where(function($q) use($search) {
            $q->whereHas('user', fn($u) =>
                    $u->where('name','like',"%{$search}%")
                )
                ->orWhereHas('user.userMajor.major', fn($m) =>
                    $m->where('name','like',"%{$search}%")
                )
                ->orWhereHas('user.userMajor.major.university', fn($u) =>
                    $u->where('name','like',"%{$search}%")
                );
        });
    }

    // Hitung berapa user yang skornya lebih tinggi + 1
    $userRank = $userRankQuery->where('score', '>', $result->score)->count() + 1;

    // Informasi penerimaan berdasarkan jurusan yang dipilih
    $acceptanceInfo = null;
    $selectedMajor = null;
    
    if ($majorId) {
        $selectedMajor = $userMajors->firstWhere('major_id', $majorId);
        if ($selectedMajor && $selectedMajor->major) {
            $quota = $selectedMajor->major->quota ?? 0;
            $acceptanceInfo = [
                'major_name' => $selectedMajor->major->name,
                'university_name' => $selectedMajor->major->university->name ?? '',
                'quota' => $quota,
                'user_rank' => $userRank,
                'is_accepted' => $userRank <= $quota && $quota > 0,
                'total_participants' => $totalFiltered
            ];
        }
    }

    return view('pages.tryout.leaderboard', compact(
        'exam', 'result', 'results', 'userMajors', 'majorId', 'search',
        'userRank', 'totalFiltered', 'acceptanceInfo'
    ));
}
    /**
     * Show the form for creating a new resource.
     */

      /**
     * Halaman evaluasi tryout dengan AI
     */
      // public function evaluation($examId, $resultId)
    // {
    //     // 1. Ambil data Exam
    //     $exam = Exam::findOrFail($examId);

    //     // 2. Ambil data Result, validasi milik user
    //     $result = Result::findOrFail($resultId);
    //     // if ($result->user_id !== Auth::id()) {
    //     //     abort(403, 'Unauthorized access');
    //     // }

    //     // 3. Ambil semua ResultDetails (eager-load question & subCategory)
    //     $resultDetails = ResultDetails::with([
    //             'question.subCategory',
    //             'question' => function($q) {
    //                 $q->select('id', 'question_text', 'explanation', 'lesson', 'sub_category_id');
    //             }
    //         ])
    //         ->where('result_id', $resultId)
    //         ->get();

    //     // 4. Statistik dasar: jumlah benar & total soal
    //     $correctCount   = $resultDetails->where('correct', 1)->count();
    //     $totalQuestions = $resultDetails->count();

    //     // 5. Hitung ranking & total peserta (opsional, tampilan di view)
    //     $allScores = Result::where('exam_id', $examId)
    //         ->orderByDesc('score')
    //         ->pluck('score', 'id');
    //     $ranking           = $allScores->keys()->search($result->id);
    //     $ranking           = ($ranking !== false) ? $ranking + 1 : null;
    //     $totalParticipants = $allScores->count();

    //     // 6. Pisahkan ResultDetails menjadi yang salah dan yang benar, sertakan subCategory
    //     $wrongQuestions = $resultDetails->where('correct', 0)
    //         ->map(function($detail) {
    //             return (object)[
    //                 'lesson'       => $detail->question->lesson       ?? 'Umum',
    //                 'question_text'=> $detail->question->question_text ?? '',
    //                 'explanation'  => $detail->question->explanation   ?? '-',
    //                 'subCategory'  => $detail->question->subCategory   ?? null,
    //             ];
    //         });
    //     $correctQuestions = $resultDetails->where('correct', 1)
    //         ->map(function($detail) {
    //             return (object)[
    //                 'lesson'       => $detail->question->lesson       ?? 'Umum',
    //                 'question_text'=> $detail->question->question_text ?? '',
    //                 'explanation'  => $detail->question->explanation   ?? '-',
    //                 'subCategory'  => $detail->question->subCategory   ?? null,
    //             ];
    //         });

    //     // 7. Cari record ResultsEvaluation yang ada untuk resultId ini (record terbaru)
    //     $existingEvaluation = ResultsEvaluation::where('result_id', $resultId)
    //         ->orderByDesc('created_at')
    //         ->first();

    //     if (! $existingEvaluation || is_null($existingEvaluation->evaluation)) {
    //         // 7a. Kalau belum ada record sama sekali, OR ada record tapi kolom 'evaluation' masih null:
    //         //    Hitung statistik per subcategory untuk AI:
    //         $perSubcategory = DB::table('result_details')
    //             ->join('questions', 'result_details.question_id', '=', 'questions.id')
    //             ->join('sub_categories', 'questions.sub_category_id', '=', 'sub_categories.id')
    //             ->select([
    //                 'questions.sub_category_id AS id',
    //                 'sub_categories.name AS name',
    //                 DB::raw('SUM(CASE WHEN result_details.correct = 1 THEN 1 ELSE 0 END) AS correct'),
    //                 DB::raw('COUNT(*) AS total'),
    //                 DB::raw('(SUM(CASE WHEN result_details.correct = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100) AS percentage'),
    //                 DB::raw('AVG(result_details.score) AS average_score'),
    //             ])
    //             ->where('result_details.result_id', $resultId)
    //             ->groupBy('questions.sub_category_id', 'sub_categories.name')
    //             ->get()
    //             ->map(function($row) {
    //                 return [
    //                     'id'            => $row->id,
    //                     'name'          => $row->name ?: 'Umum',
    //                     'correct'       => (int) $row->correct,
    //                     'total'         => (int) $row->total,
    //                     'percentage'    => (float) $row->percentage,
    //                     'average_score' => (float) $row->average_score,
    //                 ];
    //             })->toArray();

    //         try {
    //             // Panggil AI dengan tambahan param subCategoryStats
    //             $rawEvaluation  = $this->aiService->evaluatePerformance(
    //                 $wrongQuestions,
    //                 $correctQuestions,
    //                 $result->score,
    //                 $perSubcategory
    //             );
    //             $Parsedown      = new \Parsedown();
    //             $evaluationHtml = $Parsedown->text($rawEvaluation);
    //             $aiStatus       = 'success';
    //         } catch (\Exception $e) {
    //             $evaluationHtml = 'AI tidak tersedia saat ini. Silakan coba lagi nanti.';
    //             $aiStatus       = 'error';
    //         }

    //         // 8. Simpan atau perbarui record (updateOrCreate agar kolom lain tidak ter‐overwrite)
    //         $existingEvaluation = ResultsEvaluation::updateOrCreate(
    //             ['result_id' => $resultId],
    //             ['evaluation' => $evaluationHtml]
    //         );
    //     } else {
    //         // 7b. Kalau sudah ada record dan kolom 'evaluation' sudah berisi:
    //         $evaluationHtml = $existingEvaluation->evaluation;
    //         $aiStatus       = 'success';
    //     }

    //     // 8. Rekap per subcategory untuk tampilan di view (tanpa rata-rata skor detail AI)
    //     $perSubcategoryForView = $resultDetails
    //         ->groupBy(fn($detail) => $detail->question->subCategory->name)
    //         ->map(function($group, $name) {
    //             $correct = $group->where('correct', 1)->count();
    //             $total   = $group->count();
    //             return (object)[
    //                 'name'       => $name ?: 'Umum',
    //                 'correct'    => $correct,
    //                 'total'      => $total,
    //                 'percentage' => $total > 0 ? ($correct / $total) * 100 : 0,
    //             ];
    //         });

    //     // 8b. Rekap per lesson (sub bab pembahasan) untuk tampilan
    //    $perLesson = $resultDetails
    // ->groupBy(fn($detail) => $detail->question->subCategory->name ?? 'Umum')
    // ->map(function ($groupedBySubCategory, $subCategoryName) {
    //     return (object)[
    //         'name'    => $subCategoryName,
    //         'lessons' => $groupedBySubCategory
    //             ->groupBy(fn($detail) => $detail->question->lesson ?? 'Umum')
    //             ->map(function ($lessonGroup, $lessonName) {
    //                 $correct = $lessonGroup->where('correct', 1)->count();
    //                 $total   = $lessonGroup->count();
    //                 return (object)[
    //                     'name'       => $lessonName,
    //                     'correct'    => $correct,
    //                     'total'      => $total,
    //                     'percentage' => $total > 0 ? ($correct / $total) * 100 : 0,
    //                 ];
    //             })->values(),
    //     ];
    // })->values();

    //     // 9. Render view
    //     return view('pages.tryout.evaluation', [
    //         'exam'               => $exam,
    //         'result'             => $result,
    //         'correctCount'       => $correctCount,
    //         'totalQuestions'     => $totalQuestions,
    //         'ranking'            => $ranking,
    //         'totalParticipants'  => $totalParticipants,
    //         'perSubcategory'     => $perSubcategoryForView,
    //         'wrongQuestions'     => $wrongQuestions,
    //         'correctQuestions'   => $correctQuestions,
    //         'aiEvaluation'       => $evaluationHtml,
    //         'aiStatus'           => $aiStatus,
    //         'existingEvaluation' => $existingEvaluation,
    //         'perLesson'          => $perLesson,
    //     ]);
    // }

  public function evaluation($examId, $resultId)
{
    // 1. Ambil data Exam
    $exam = Exam::findOrFail($examId);

    // 2. Ambil data Result
    $result = Result::findOrFail($resultId);

    // 3. Ambil semua ResultDetails dengan relasi
    $resultDetails = ResultDetails::with([
            'question.subCategory',
            'question' => function($q) {
                $q->select('id', 'question_text', 'explanation', 'lesson', 'sub_category_id');
            }
        ])
        ->where('result_id', $resultId)
        ->get();

    // 4. Statistik dasar
    $correctCount = $resultDetails->where('correct', 1)->count();
    $totalQuestions = $resultDetails->count();

    // 5. Hitung ranking (opsional)
    $allScores = Result::where('exam_id', $examId)
        ->orderByDesc('score')
        ->pluck('score', 'id');
    $ranking = $allScores->keys()->search($result->id);
    $ranking = ($ranking !== false) ? $ranking + 1 : null;
    $totalParticipants = $allScores->count();

    // 6. Pisahkan soal benar dan salah dengan informasi subCategory
    $wrongQuestions = $resultDetails->where('correct', 0)
        ->map(function($detail) {
            return [
                'sub_category' => $detail->question->subCategory->name ?? 'Umum',
                'question_text' => $detail->question->question_text ?? '',
                'explanation' => $detail->question->explanation ?? '-',
            ];
        })->toArray();
        
    $correctQuestions = $resultDetails->where('correct', 1)
        ->map(function($detail) {
            return [
                'sub_category' => $detail->question->subCategory->name ?? 'Umum',
                'question_text' => $detail->question->question_text ?? '',
                'explanation' => $detail->question->explanation ?? '-',
            ];
        })->toArray();

    // 7. Cek evaluasi yang sudah ada
    $existingEvaluation = ResultsEvaluation::where('result_id', $resultId)
        ->orderByDesc('created_at')
        ->first();

    if (!$existingEvaluation || is_null($existingEvaluation->evaluation)) {
        // 7a. Generate evaluasi baru dengan statistik per subcategory
        $perSubcategory = DB::table('result_details')
            ->join('questions', 'result_details.question_id', '=', 'questions.id')
            ->join('sub_categories', 'questions.sub_category_id', '=', 'sub_categories.id')
            ->select([
                'sub_categories.name AS name',
                DB::raw('SUM(CASE WHEN result_details.correct = 1 THEN 1 ELSE 0 END) AS correct'),
                DB::raw('COUNT(*) AS total'),
                DB::raw('(SUM(CASE WHEN result_details.correct = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100) AS percentage'),
                DB::raw('AVG(result_details.score) AS average_score'),
            ])
            ->where('result_details.result_id', $resultId)
            ->groupBy('sub_categories.name')
            ->get()
            ->keyBy('name')
            ->map(function($row) {
                return [
                    'correct' => (int) $row->correct,
                    'total' => (int) $row->total,
                    'percentage' => (float) $row->percentage,
                    'average_score' => (float) $row->average_score,
                ];
            })->toArray();

        try {
            // Panggil AI dengan evaluasi fokus mata pelajaran
            $rawEvaluation = $this->aiService->evaluatePerformance(
                $wrongQuestions,
                $correctQuestions,
                $result->score,
                $perSubcategory
            );
            
            $Parsedown = new \Parsedown();
            $evaluationHtml = $Parsedown->text($rawEvaluation);
            $aiStatus = 'success';
        } catch (\Exception $e) {
            $evaluationHtml = 'AI tidak tersedia saat ini. Silakan coba lagi nanti.';
            $aiStatus = 'error';
        }

        // 8. Simpan evaluasi
        $existingEvaluation = ResultsEvaluation::updateOrCreate(
            ['result_id' => $resultId],
            ['evaluation' => $evaluationHtml]
        );
    } else {
        // 7b. Gunakan evaluasi yang sudah ada
        $evaluationHtml = $existingEvaluation->evaluation;
        $aiStatus = 'success';
    }

    // 9. Rekap per subcategory untuk tampilan
    $perSubcategoryForView = $resultDetails
        ->groupBy(fn($detail) => $detail->question->subCategory->name ?? 'Umum')
        ->map(function($group, $name) {
            $correct = $group->where('correct', 1)->count();
            $total = $group->count();
            return (object)[
                'name' => $name,
                'correct' => $correct,
                'total' => $total,
                'percentage' => $total > 0 ? ($correct / $total) * 100 : 0,
            ];
        });

    // 10. Return view
    return view('pages.tryout.evaluation', [
        'exam' => $exam,
        'result' => $result,
        'correctCount' => $correctCount,
        'totalQuestions' => $totalQuestions,
        'ranking' => $ranking,
        'totalParticipants' => $totalParticipants,
        'perSubcategory' => $perSubcategoryForView,
        'aiEvaluation' => $evaluationHtml,
        'aiStatus' => $aiStatus,
        'existingEvaluation' => $existingEvaluation,
    ]);
}

    /**
     * Rekomendasi jurusan berbasis nilai
     */
    public function recommendation($examId, $resultId)
    {
        // Ambil data exam
        $exam = Exam::findOrFail($examId);
        
        // Ambil data result
        $result = Result::with('user')->findOrFail($resultId);

        // Validasi user
        if ($result->user_id != auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        // Ambil jurusan pilihan user
        $userMajors = UserMajor::with('major.university')
            ->where('user_id', $result->user_id)
            ->get();

        // Hitung data jurusan dengan passing score
        $majorRankings = [];
        foreach ($userMajors as $userMajor) {
            $majorRankings[] = [
                'major_name' => $userMajor->major->name ?? '-',
                'university' => $userMajor->major->university->name ?? '',
                'passing_score' => $userMajor->major->passing_score ?? 0,
                'quota' => $userMajor->major->quota ?? 0,
            ];
        }

        // Cek apakah tryout ini punya mata pelajaran spesifik
        $hasSubjectCategories = $this->checkHasSubjectCategories($examId);

        // Panggil AI untuk rekomendasi berbasis nilai
        $aiRecommendation = null;
        $aiStatus = 'loading';
        
       // 5. Ambil existingRecommendation (model ResultsEvaluation) terbaru berdasarkan result_id
    $existingRecommendation = ResultsEvaluation::where('result_id', $resultId)
        ->orderByDesc('created_at')
        ->first();

    // 6. Jika belum ada record sama sekali, buat baru. 
    //    Jika sudah ada tetapi kolom recommendation masih NULL, perbarui kolom recommendation.
    if (! $existingRecommendation) {
        // Belum pernah ada record ⇒ create baru dengan kolom recommendation
        try {
            $rawRec = $this->aiService->recommendMajors(
                $result->score,
                $majorRankings,
                $hasSubjectCategories
            );
            $Parsedown          = new \Parsedown();
            $recommendationHtml = $Parsedown->text($rawRec);
            $aiStatus           = 'success';
        } catch (\Exception $e) {
            $recommendationHtml = 'AI tidak tersedia saat ini. Silakan coba lagi nanti.';
            $aiStatus           = 'error';
        }

        // Simpan ke database
        $existingRecommendation = ResultsEvaluation::create([
            'result_id'     => $resultId,       
            'recommendation'=> $recommendationHtml,
        ]);

        $aiRecommendation = $recommendationHtml;
    }
    elseif (is_null($existingRecommendation->recommendation)) {
        // Sudah ada record tetapi kolom recommendation belum diisi ⇒ update hanya kolom recommendation
        try {
            $rawRec = $this->aiService->recommendMajors(
                $result->score,
                $majorRankings,
                $hasSubjectCategories
            );
            $Parsedown          = new \Parsedown();
            $recommendationHtml = $Parsedown->text($rawRec);
            $aiStatus           = 'success';
        } catch (\Exception $e) {
            $recommendationHtml = 'AI tidak tersedia saat ini. Silakan coba lagi nanti.';
            $aiStatus           = 'error';
        }

        // Update kolom recommendation pada objek existingRecommendation
        $existingRecommendation->recommendation = $recommendationHtml;
        $existingRecommendation->save();

        $aiRecommendation = $recommendationHtml;
    }
    else {
        // Record sudah ada dan kolom recommendation sudah terisi ⇒ langsung pakai data yg ada
        $recommendationHtml = $existingRecommendation->recommendation;
        $aiRecommendation   = $recommendationHtml;
        $aiStatus           = 'success';
    }

    // 5. Periksa flash message (jika baru saja klik generate ulang)
    if (session()->has('ai_recommendation_status')) {
        $status = session('ai_recommendation_status');
        if ($status === 'success') {
            // Bila status sukses, muat ulang rekomendasi terkini
            $existingRecommendation = ResultsEvaluation::where('result_id', $resultId)
                ->orderByDesc('created_at')
                ->first();

            $aiRecommendation = $existingRecommendation
                ? $existingRecommendation->recommendation
                : null;
        }
        $aiStatus = $status;
    }
    

        return view('pages.tryout.recommendation', [
            'exam' => $exam,
            'result' => $result,
            'majorRankings' => $majorRankings,
            'aiRecommendation' => $aiRecommendation,
            'aiStatus' => $aiStatus, // Status AI call
        ]);
    }

    public function generateRecommendation($examId, $resultId)
{
    // 1. Ambil data Exam & Result, validasi kepemilikan user
    $exam   = Exam::findOrFail($examId);
    $result = Result::with('user')->findOrFail($resultId);

    if ($result->user_id != auth()->id()) {
        abort(403, 'Unauthorized access');
    }

    // 2. Ambil record ResultsEvaluation yang sudah ada untuk result_id ini
    $existing = ResultsEvaluation::where('result_id', $resultId)
        ->orderByDesc('created_at')
        ->first();

    if (! $existing) {
        abort(404, 'Record evaluasi tidak ditemukan.');
    }

    // 3. Siapkan data jurusan seperti biasa
    $userMajors = UserMajor::with('major.university')
        ->where('user_id', $result->user_id)
        ->get();

    $majorRankings = [];
    foreach ($userMajors as $userMajor) {
        $majorRankings[] = [
            'major_name'    => $userMajor->major->name ?? '-',
            'university'    => $userMajor->major->university->name ?? '',
            'passing_score' => $userMajor->major->passing_score ?? 0,
            'quota'         => $userMajor->major->quota ?? 0,
        ];
    }

    $hasSubjectCategories = $this->checkHasSubjectCategories($examId);

    // 4. Panggil AI untuk rekomendasi baru (mau pakai result->score, majorRankings, dll.)
    try {
        $rawRec = $this->aiService->recommendMajors(
            $result->score,
            $majorRankings,
            $hasSubjectCategories
        );
        $Parsedown          = new \Parsedown();
        $recommendationHtml = $Parsedown->text($rawRec);
        $aiStatus           = 'success';
    } catch (\Exception $e) {
        $recommendationHtml = 'AI tidak tersedia saat ini. Silakan coba lagi nanti.';
        $aiStatus           = 'error';
    }

    // 5. Update kolom recommendation pada record yang sudah ada
    $existing->recommendation = $recommendationHtml;
    $existing->save();

    // 6. Redirect kembali ke halaman recommendation dengan flash status
    return redirect()
        ->route('tryout-recommendation', ['exam' => $examId, 'id' => $resultId])
        ->with('ai_recommendation_status', $aiStatus);
}

public function generateEvaluation(Request $request, $examId, $resultId)
    {
        // 1. Ambil data Exam & Result, validasi kepemilikan user
        $exam   = Exam::findOrFail($examId);
        $result = Result::with('user')->findOrFail($resultId);
        if ($result->user_id != auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        // 2. Ambil semua ResultDetails (eager-load question & subCategory)
        $resultDetails = ResultDetails::with([
                'question.subCategory',
                'question' => function($q) {
                    $q->select('id', 'question_text', 'explanation', 'lesson', 'sub_category_id');
                }
            ])
            ->where('result_id', $resultId)
            ->get();

        // 3. Siapkan data pertanyaan salah dan benar, sertakan subCategory
        $wrongQuestions = $resultDetails->where('correct', 0)
            ->map(function($detail) {
                return (object)[
                    'lesson'       => $detail->question->lesson       ?? 'Umum',
                    'question_text'=> $detail->question->question_text ?? '',
                    'explanation'  => $detail->question->explanation   ?? '-',
                    'subCategory'  => $detail->question->subCategory   ?? null,
                ];
            });
        $correctQuestions = $resultDetails->where('correct', 1)
            ->map(function($detail) {
                return (object)[
                    'lesson'       => $detail->question->lesson       ?? 'Umum',
                    'question_text'=> $detail->question->question_text ?? '',
                    'explanation'  => $detail->question->explanation   ?? '-',
                    'subCategory'  => $detail->question->subCategory   ?? null,
                ];
            });

        // 4. Ambil atau buat record ResultsEvaluation terbaru
        $existing = ResultsEvaluation::where('result_id', $resultId)
            ->orderByDesc('created_at')
            ->first();

        // 5. Hitung statistik per subcategory untuk AI
        $perSubcategory = DB::table('result_details')
            ->join('questions', 'result_details.question_id', '=', 'questions.id')
            ->join('sub_categories', 'questions.sub_category_id', '=', 'sub_categories.id')
            ->select([
                'questions.sub_category_id AS id',
                'sub_categories.name AS name',
                DB::raw('SUM(CASE WHEN result_details.correct = 1 THEN 1 ELSE 0 END) AS correct'),
                DB::raw('COUNT(*) AS total'),
                DB::raw('(SUM(CASE WHEN result_details.correct = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100) AS percentage'),
                DB::raw('AVG(result_details.score) AS average_score'),
            ])
            ->where('result_details.result_id', $resultId)
            ->groupBy('questions.sub_category_id', 'sub_categories.name')
            ->get()
            ->map(function($row) {
                return [
                    'id'            => $row->id,
                    'name'          => $row->name ?: 'Umum',
                    'correct'       => (int) $row->correct,
                    'total'         => (int) $row->total,
                    'percentage'    => (float) $row->percentage,
                    'average_score' => (float) $row->average_score,
                ];
            })->toArray();

        // 6. Panggil AI (generate ulang selalu)
        try {
            $rawEvaluation  = $this->aiService->evaluatePerformance(
                $wrongQuestions,
                $correctQuestions,
                $result->score,
                $perSubcategory
            );
            $Parsedown      = new \Parsedown();
            $evaluationHtml = $Parsedown->text($rawEvaluation);
            $aiStatus       = 'success';
        } catch (\Exception $e) {
            $evaluationHtml = 'AI tidak tersedia saat ini. Silakan coba lagi nanti.';
            $aiStatus       = 'error';
        }

        // 7. Simpan atau update record: jika belum ada, create; kalau sudah ada, update kolom evaluation
        if ($existing) {
            $existing->evaluation = $evaluationHtml;
            $existing->save();
        } else {
            ResultsEvaluation::create([
                'result_id'  => $resultId,
                'evaluation' => $evaluationHtml,
            ]);
        }

        // 8. Redirect ke halaman evaluasi dengan status
        return redirect()
            ->route('tryout-evaluation', ['exam' => $examId, 'id' => $resultId])
            ->with('ai_evaluation_status', $aiStatus);
    }



    /**
     * Cek apakah exam ini punya mata pelajaran spesifik (Matematika, Fisika, dll)
     */
    private function checkHasSubjectCategories($examId)
    {
        $subjectKeywords = ['matematika', 'fisika', 'kimia', 'biologi', 'ekonomi', 'sosiologi', 'geografi', 'sejarah'];
        
        $hasSubjects = DB::table('questions')
            ->join('sub_categories', 'questions.sub_category_id', '=', 'sub_categories.id')
            ->where('questions.exam_id', $examId)
            ->where(function($query) use ($subjectKeywords) {
                foreach ($subjectKeywords as $keyword) {
                    $query->orWhere('sub_categories.name', 'like', '%' . $keyword . '%');
                }
            })
            ->exists();
            
        return $hasSubjects;
    }
   

}





