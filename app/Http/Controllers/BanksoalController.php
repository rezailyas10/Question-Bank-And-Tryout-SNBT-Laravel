<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Exam;
use App\Models\Result;
use App\Models\Category;
use App\Models\Question;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Models\ResultDetails;
use App\Models\ResultsEvaluation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\AIEvaluationService;

class BanksoalController extends Controller
{
     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

       private $aiService;

    public function __construct(AIEvaluationService $aiService)
    {
        // Inject AI service
        $this->aiService = $aiService;
    }
     public function index(Request $request)
    {
        $categories = Category::with('subCategory')->get();
        
        // Filter berdasarkan category_id jika ada
        if ($request->has('category_id') && $request->category_id != '') {
    if ($request->category_id == 'snbt') {
        $snbtCategoryIds = [1, 2, 6]; // ID kategori untuk SNBT
        $subCategories = SubCategory::whereIn('category_id', $snbtCategoryIds)->get();
    } elseif ($request->category_id == 'mandiri') {
        $subCategories = SubCategory::where('category_id', 7)->get(); // ID kategori untuk Mandiri
    } else {
        $subCategories = SubCategory::where('category_id', $request->category_id)->get();
    }
    } else {
        $subCategories = SubCategory::all();
    }


        $weeklyStats = null;
        $subCategoryStats = collect();
        $results = collect();

        // Jika user login, hitung statistik
        if (Auth::check()) {
               $userId = Auth::id();
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            
            // Hitung jumlah soal dikerjakan minggu ini
            $weeklyQuestions = Result::join('exams', 'results.exam_id', '=', 'exams.id')
                ->where('results.user_id', Auth::id())
                ->where('exams.exam_type', 'latihan soal')
                ->whereBetween('results.created_at', [$startOfWeek, $endOfWeek])
                ->count();

            // Hitung rata-rata score keseluruhan
            $overallAvgScore = Result::join('exams', 'results.exam_id', '=', 'exams.id')
                ->where('results.user_id', Auth::id())
                ->where('exams.exam_type', 'latihan soal')
                ->avg('results.score') ?? 0;

            // Hitung total pengerjaan
            $totalAttempts = Result::join('exams', 'results.exam_id', '=', 'exams.id')
                ->where('results.user_id', Auth::id())
                ->where('exams.exam_type', 'latihan soal')
                ->count();

            $weeklyStats = [
                'questions_answered' => $weeklyQuestions,
                'avg_score' => round($overallAvgScore, 1),
                'total_attempts' => $totalAttempts
            ];

            

            // Statistik per subcategory
            $subCategoryStats = DB::table('results_evaluations')
            ->join('results', 'results_evaluations.result_id', '=', 'results.id')
            ->join('exams', 'results.exam_id', '=', 'exams.id')
            ->join('sub_categories', 'exams.sub_category_id', '=', 'sub_categories.id')
            ->where('results.user_id', $userId)
            ->where('exams.exam_type', 'latihan soal') // sesuaikan string exam_type Anda
            ->selectRaw('
                sub_categories.id as id,
                sub_categories.name as name,
                COUNT(results_evaluations.id) as total_attempts,
                AVG(results_evaluations.score) as avg_score,
                MAX(results_evaluations.score) as best_score
            ')
            ->groupBy('sub_categories.id', 'sub_categories.name')
            ->orderByDesc('total_attempts')
            ->get();

            // Riwayat pengerjaan, hanya latihan
            // Ambil 10 riwayat terbaru dari results_evaluation, filter user dan exam_type
        $results = ResultsEvaluation::whereHas('result', function($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->whereHas('exam', function($q2) {
                      $q2->where('exam_type', 'latihan soal'); // sesuaikan exact string exam_type Anda
                  });
            })
            ->with(['result.exam']) // eager load untuk akses exam->title
            ->orderByDesc('created_at') // atau orderByDesc('updated_at') jika Anda update evaluation
            ->limit(10)
            ->get();
    }

        return view('pages.bank-soal.bank-soal', [
            'categories' => $categories,
            'subCategories' => $subCategories,
            'weeklyStats' => $weeklyStats,
            'subCategoryStats' => $subCategoryStats,
            'results' => $results,
            'selectedCategoryId' => $request->category_id
        ]);
    }
   public function detail($id)
{
    $subCategories = SubCategory::with([
    'exam' => function($query) {
        $query->where('exam_type', 'latihan soal')->where('is_published', true);
    }
])->where('slug', $id)->firstOrFail();

$exams = $subCategories->exam;

    $userResults = [];
     $examParticipants = []; 

    foreach ($exams as $exam) {
        $userResults[$exam->id] = Result::where('user_id', Auth::id())
            ->where('exam_id', $exam->id)
            ->latest()   
            ->first();    

            // Get total participants count for this exam
        $examParticipants[$exam->id] = Result::where('exam_id', $exam->id)
            ->distinct('user_id')
            ->count('user_id');
    }

    // 3. Kirim ke view
    return view('pages.bank-soal.bank-soal-detail', compact('subCategories', 'exams', 'userResults','examParticipants'));
}
     public function result($examId, $resultId)
            {
                $user = Auth::user();

        if ($user->roles != 'USER') {
            return redirect()->back();
        }
        // Ambil data exam dengan relasi
        $exam = Exam::with('questions', 'results')->findOrFail($examId);
        
        // Ambil data result dengan relasi
        $result = Result::with('user', 'exam', 'aiEvaluation')->findOrFail($resultId);
        
        // Ambil detail hasil dengan relasi question dan subCategory
        $resultDetails = ResultDetails::with(['question.subCategory'])
            ->where('result_id', $resultId)
            ->get();

            $details = ResultDetails::with('question.subCategory')
            ->where('result_id', $resultId)
            ->whereHas('question', function ($query) {
                $query->where('status', 'Diterima');
            })
            ->get();


        // Hitung statistik dasar
        $correctCount = $resultDetails->where('correct', true)->count();
//         $totalQuestions = $resultDetails->filter(function ($detail) {
//     return $detail->question && $detail->question->status === 'Diterima';
// })->count();
        $totalQuestions = $resultDetails->count();
        
        // Dapatkan soal yang salah untuk evaluasi AI
        $wrongQuestions = $resultDetails->where('correct', false)
            ->map(function($detail) {
                return $detail->question;
            });

        // Hitung ranking jika ada data peserta lain
        $ranking = null;
        $totalParticipants = null;
        $allResults = Result::where('exam_id', $examId)
            ->orderBy('score', 'desc')
            ->get();
        
        if ($allResults->count() > 1) {
            $ranking = $allResults->search(function($item) use ($resultId) {
                return $item->id == $resultId;
            }) + 1;
            $totalParticipants = $allResults->count();
        }

        // Cari apakah sudah ada record ResultsEvaluations untuk result ini
        $existingEvaluation = ResultsEvaluation::where('result_id', $resultId)
    ->latest()   // alias orderBy('created_at', 'desc')
    ->first();

       // Jika belum ada, generate satu kali secara otomatis
        if ($existingEvaluation) {
    // Jika kolom evaluation masih null atau kosong, lakukan generate dan update
    if (is_null($existingEvaluation->evaluation) || $existingEvaluation->evaluation === '') {
        // Kumpulkan soal salah & benar
        $wrongQuestions   = $resultDetails->where('correct', false)
                             ->map(fn($d) => $d->question);
        $correctQuestions = $resultDetails->where('correct', true)
                             ->map(fn($d) => $d->question);

        // Panggil service AI untuk evaluasi
        $rawEvaluation = $this->aiService->evaluatePerformance(
            $wrongQuestions,
            $correctQuestions,
            $result->score
        );

        // Convert Markdown → HTML
        $Parsedown      = new \Parsedown();
        $evaluationHtml = $Parsedown->text($rawEvaluation);

        // Update record existing dengan evaluation baru
        $existingEvaluation->update([
            'evaluation' => $evaluationHtml,
        ]);
    }
}

        // Ambil semua record evaluasi untuk riwayat (urut terbaru pertama)
        $allEvaluations = $result->aiEvaluation()->orderByDesc('created_at')->paginate(5);

// // Siapkan performance per subCategory
//     $subjectStats = [];
//     foreach ($details->groupBy(fn($d)=> $d->question->subCategory->name) as $sub => $group) {
//         $subjectStats[$sub] = [
//             'correct' => $group->where('correct',true)->count(),
//             'total'   => $group->count(),
//         ];
//     }

//         // Hanya rekomendasi jurusan untuk kategori TKA
//     $needsMajorRecommendation = $exam->subCategory->category->name === 'Tes Kemampuan Akademik';
//     $majorRecommendation = null;
//     if ($needsMajorRecommendation) {
//         // Biarkan AI memilih jurusan sendiri
//         $majorRecommendation = $this->aiService->recommendMajors(
//             $result->score,
//             $subjectStats
//         );
//     }

//     $Parsedown = new \Parsedown();
// $majorRecommendation = $Parsedown->text($majorRecommendation); // Konversi Markdown ke HTML

    return view('pages.bank-soal.results', [
        'result' => $result,
        'resultDetails' => $details,
        'correctCount' => $correctCount,
        'totalQuestions' => $totalQuestions,
        'allEvaluations' => $allEvaluations,
        'existingEvaluation' => $existingEvaluation
        // 'needsMajorRecommendation' => $needsMajorRecommendation,
        // 'majorRecommendation' => $majorRecommendation,
    ]);
    }

    public function generateAI($examId, $resultId)
    {
        // Ambil data result & detail soal
        $result = Result::findOrFail($resultId);
        $resultDetails = ResultDetails::with('question')
            ->where('result_id', $resultId)
            ->get();

        $wrongQuestions   = $resultDetails->where('correct', false)->map(fn($d) => $d->question);
        $correctQuestions = $resultDetails->where('correct', true)->map(fn($d) => $d->question);

        // Panggil service AI → hanya evaluasi
        $rawEvaluation = $this->aiService->evaluatePerformance(
            $wrongQuestions,
            $correctQuestions,
            $result->score
        );

        $Parsedown      = new \Parsedown();
        $evaluationHtml = $Parsedown->text($rawEvaluation);

       $existingEvaluation = ResultsEvaluation::where('result_id', $resultId)
        ->latest()   // alias orderBy('created_at', 'desc')
        ->first();

    if ($existingEvaluation) {
        // Update record yang sudah ada
        $existingEvaluation->update([
            'evaluation' => $evaluationHtml,
        ]);
    }
        return redirect()
            ->route('bank-soal-result', ['exam' => $examId, 'id' => $resultId])
            ->with('success', 'Evaluasi AI baru telah berhasil digenerate.');
    }

    public function review($examId, $questionId)
    {
        $exam = Exam::with('questions')->findOrFail($examId);
        $question = Question::with(['multipleChoice', 'multipleOption', 'essay'])->where('status','Diterima')->findOrFail($questionId);
    
        // ambil jawaban user dari result_detail
        $userResultDetail = ResultDetails::with('result')
            ->whereHas('result', fn($query) => $query->where('exam_id', $examId)->where('user_id', auth()->id()))
            ->where('question_id', $questionId)
            ->latest()
            ->first();
    
        $result = $userResultDetail?->result; // ambil relasi result dari result_detail

        $userAnswersProcessed = [];

        if (
            $question->question_type === 'pilihan_majemuk' &&
            $userResultDetail &&
            $userResultDetail->answer &&
            $question->multipleOption
        ) {
            $userAnswers = json_decode($userResultDetail->answer, true);
            $options = $question->multipleOption;
        
            $statements = [];
            $correctMap = [];
        
            for ($i = 1; $i <= 5; $i++) {
                $statement = $options->{"multiple$i"} ?? null;
                $correct = $options->{"yes/no$i"} ?? 'no';
        
                if (!empty($statement)) {
                    $statements[] = $statement;
                    $correctMap[$statement] = $correct;
                }
            }
        
            foreach ($userAnswers as $statement => $userValue) {
                $correctValue = $correctMap[$statement] ?? 'no';
        
                $userAnswersProcessed[] = [
                    'statement' => $statement,
                    'user_answer' => ucfirst($userValue),
                    'is_correct' => strtolower($userValue) === strtolower($correctValue),
                ];
            }
        }

        $resultDetails = ResultDetails::whereHas('result', function ($q) use ($examId) {
    $q->where('exam_id', $examId)->where('user_id', auth()->id());
})->get();

    
return view('pages.bank-soal.result-details', compact('exam', 'question', 'userResultDetail', 'result', 'userAnswersProcessed','resultDetails'));

    }




}
