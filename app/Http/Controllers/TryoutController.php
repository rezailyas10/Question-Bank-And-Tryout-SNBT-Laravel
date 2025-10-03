<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Exam;
use App\Models\Major;
use App\Models\Result;
use App\Models\Product;
use App\Models\Category;
use App\Models\Question;
use App\Models\UserMajor;
use App\Models\University;
use App\Models\SubCategory;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ResultDetails;
use App\Models\ResultsEvaluation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TryoutController extends Controller
{
     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
{
    $currentTime = Carbon::now();
    $search = $request->get('search');
    
    // Base query dengan search
    $baseQuery = Exam::where('exam_type', 'tryout')
        ->where('is_published', true);
    
    if ($search) {
        $baseQuery->where('title', 'LIKE', '%' . $search . '%');
    }
    
    // Upcoming tryouts
    $upcoming = (clone $baseQuery)
        ->where('tanggal_dibuka', '>', $currentTime)
        ->orderBy('tanggal_dibuka')
        ->get();

    // Ongoing tryouts
    $ongoing = (clone $baseQuery)
        ->where('tanggal_dibuka', '<=', $currentTime)
        ->where('tanggal_ditutup', '>=', $currentTime)
        ->orderBy('tanggal_dibuka')
        ->get();

    // Past tryouts
    $past = (clone $baseQuery)
        ->where('tanggal_ditutup', '<', $currentTime)
        ->orderBy('tanggal_dibuka', 'desc')
        ->get();

    // Merge all exams untuk get user results
    $allExams = $upcoming->merge($ongoing)->merge($past);

    $userId = Auth::id();

    // Get user results
    $userResults = [];
    $myTryouts = collect(); // Tryout yang sudah dikerjakan user

       if (Auth::check()) {
        // Ambil semua result user dengan exam yang sesuai kriteria search
        $resultsQuery = Result::where('user_id', Auth::id())
            ->whereHas('exam', function($query) use ($search) {
                $query->where('exam_type', 'tryout')
                      ->where('is_published', true);
                if ($search) {
                    $query->where('title', 'LIKE', '%' . $search . '%');
                }
            })
            ->with(['exam' => function($query) {
                $query->select('id', 'title', 'slug', 'tanggal_dibuka', 'tanggal_ditutup');
            }])
            ->latest('created_at') // URUTKAN BERDASARKAN CREATED_AT RESULT
            ->get();

        foreach ($resultsQuery as $result) {
            if ($result->exam) {
                $userResults[$result->exam_id] = $result;
                $myTryouts->push($result->exam);
            }
        }
    }

   $examParticipants = [];
    
    if ($allExams->isNotEmpty()) {
        $examIds = $allExams->pluck('id')->unique()->toArray();
        
        // Query untuk mendapatkan jumlah peserta per exam dalam satu query
        $participantCounts = Result::whereIn('exam_id', $examIds)
            ->select('exam_id', DB::raw('COUNT(DISTINCT user_id) as participant_count'))
            ->groupBy('exam_id')
            ->get()
            ->keyBy('exam_id');
        
        // Populate examParticipants array
        foreach ($examIds as $examId) {
            $examParticipants[$examId] = $participantCounts->get($examId)->participant_count ?? 0;
        }
    }


    // Remove tryouts yang sudah dikerjakan dari kategori lain
    $completedExamIds = $myTryouts->pluck('id')->toArray();

    // Remove tryouts yang sudah dikerjakan dari kategori lain
    $upcoming = $upcoming->filter(function($exam) use ($userResults) {
        return !isset($userResults[$exam->id]);
    });
    
    $ongoing = $ongoing->filter(function($exam) use ($userResults) {
        return !isset($userResults[$exam->id]);
    });
    
    $past = $past->filter(function($exam) use ($userResults) {
        return !isset($userResults[$exam->id]);
    });

    // Statistik per subcategory berdasarkan result_details
$subCategoryStats = DB::table('result_details')
    ->join('results', 'result_details.result_id', '=', 'results.id')
    ->join('questions', 'result_details.question_id', '=', 'questions.id')
    ->join('sub_categories', 'questions.sub_category_id', '=', 'sub_categories.id')
    ->join('exams', 'results.exam_id', '=', 'exams.id')
    ->where('results.user_id', $userId)
    ->where('exams.exam_type', 'tryout')
    ->selectRaw('
        sub_categories.id as id,
        sub_categories.name as name,
        COUNT(result_details.id) as total_questions,
        SUM(CASE WHEN result_details.correct = 1 THEN 1 ELSE 0 END) as correct_answers,
        SUM(CASE WHEN result_details.correct = 0 THEN 1 ELSE 0 END) as wrong_answers,
        SUM(CASE WHEN result_details.correct IS NULL THEN 1 ELSE 0 END) as empty_answers,
        ROUND((SUM(CASE WHEN result_details.correct = 1 THEN 1 ELSE 0 END) * 100.0 / COUNT(result_details.id)), 1) as accuracy_percentage
    ')
    ->groupBy('sub_categories.id', 'sub_categories.name')
    ->orderByDesc('total_questions')
    ->get();

     // Ambil 10 riwayat terbaru dari results_evaluation, filter user dan exam_type
        $results = ResultsEvaluation::whereHas('result', function($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->whereHas('exam', function($q2) {
                      $q2->where('exam_type', 'tryout'); // sesuaikan exact string exam_type Anda
                  });
            })
            ->with(['result.exam']) // eager load untuk akses exam->title
            ->orderByDesc('created_at') // atau orderByDesc('updated_at') jika Anda update evaluation
            ->limit(10)
            ->get();
    

    return view('pages.tryout.tryout', [
        'myTryouts' => $myTryouts,
        'upcoming' => $upcoming,
        'ongoing' => $ongoing,
        'past' => $past,
        'userResults' => $userResults,
        'search' => $search,
        'examParticipants' => $examParticipants, // Tambahkan data peserta
        'subCategoryStats' => $subCategoryStats,
            'results' => $results,
    ]);
}
    public function test()
    {
       $subCategories = SubCategory::all();
        return view('pages.tryout.tryout1', compact('subCategories'));
    }

   public function show($slug)
{
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    $exam = Exam::where('slug', $slug)->firstOrFail();

    $questions = Question::with('subCategory.category')
    ->where('exam_id', $exam->id)->where('status','Diterima')
    ->get();

    $userExamResult = auth()->user()->results()
    ->where('exam_id', $exam->id)
    ->latest()
    ->first();

$universities = University::all();
$majors = Major::all();
      $userMajors = UserMajor::with('major')
        ->where('user_id', auth()->id())
        ->get();

    return view('pages.tryout.tryoutDetail', compact('exam','userExamResult','userMajors','universities','majors','questions'));
}

 public function result($examId, $resultId)
        {
            $user = Auth::user();

    if ($user->roles !== 'USER') {
        return redirect()->back();
    }
        $exam = Exam::with('questions','results')->findOrFail($examId);
        $result = Result::with('user','exam')->findOrFail($resultId);
    
        $resultDetails = ResultDetails::with('question.subCategory')
            ->where('result_id', $resultId)
            ->get();
    
        $correctCount = $resultDetails->filter(fn($d) => $d->correct == 1)->count();
        $inCorrectCount = $resultDetails->filter(fn($d) => $d->correct == 0 && !is_null($d->correct))->count();
        $nullCount = $resultDetails->filter(fn($d) => is_null($d->correct))->count();
        $totalQuestions = $resultDetails->count();

        // Total valid = benar + salah (tanpa null)
        $totalValid = $correctCount + $inCorrectCount;

        // Hitung akurasi
        $accuracy = $totalValid > 0 ? ($correctCount / $totalValid) * 100 : 0;

    // Ambil semua hasil ujian exam ini, urut descending score
$allScores = Result::where('exam_id', $examId)
    ->orderByDesc('score')
    ->pluck('score', 'id'); // collection [result_id => score]

$totalParticipants = $allScores->count();

// Cari ranking user ini untuk tryout secara keseluruhan
$ranking = $allScores->keys()->search($result->id);
if ($ranking === false) {
    $ranking = null;
} else {
    $ranking += 1; // dari index 0 ke ranking mulai 1
}

// Hitung persentase Top X%
$topPercentage = $totalParticipants > 0 ? (1 - ($ranking - 1) / $totalParticipants) * 100 : 0;

// Ambil jurusan user
$userId = $result->user_id;
$userMajors = UserMajor::with('major.university')
    ->where('user_id', $userId)
    ->get();

// Query untuk ranking per universitas dan per major
$universityRankings = [];
$majorRankings = [];

foreach ($userMajors as $userMajor) {
    // Ranking berdasarkan universitas
    $universityRank = DB::table('results')
        ->join('user_majors', 'results.user_id', '=', 'user_majors.user_id')
        ->join('majors', 'user_majors.major_id', '=', 'majors.id')
        ->where('results.exam_id', $examId)
        ->where('majors.university_id', $userMajor->major->university_id)
        ->where('results.score', '>', $result->score)
        ->count() + 1;

    $totalUniversityParticipants = DB::table('results')
        ->join('user_majors', 'results.user_id', '=', 'user_majors.user_id')
        ->join('majors', 'user_majors.major_id', '=', 'majors.id')
        ->where('results.exam_id', $examId)
        ->where('majors.university_id', $userMajor->major->university_id)
        ->count();

    // Ranking berdasarkan major/jurusan
    $majorRank = DB::table('results')
        ->join('user_majors', 'results.user_id', '=', 'user_majors.user_id')
        ->where('results.exam_id', $examId)
        ->where('user_majors.major_id', $userMajor->major_id)
        ->where('results.score', '>', $result->score)
        ->count() + 1;

    $totalMajorParticipants = DB::table('results')
        ->join('user_majors', 'results.user_id', '=', 'user_majors.user_id')
        ->where('results.exam_id', $examId)
        ->where('user_majors.major_id', $userMajor->major_id)
        ->count();

    // Simpan ranking universitas (hindari duplikasi)
    $universityKey = $userMajor->major->university_id;
    if (!isset($universityRankings[$universityKey])) {
        $universityRankings[$universityKey] = [
            'university_name' => $userMajor->major->university->name ?? '-',
            'rank' => $universityRank,
            'total' => $totalUniversityParticipants,
        ];
    }

    // Simpan ranking major
    $majorRankings[] = [
        'major_name' => $userMajor->major->name ?? '-',
        'university' => $userMajor->major->university->name ?? '',
        'rank' => $majorRank,
        'total' => $totalMajorParticipants,
        'quota' => $userMajor->major->quota ?? 0,
        'is_accepted' => $majorRank <= ($userMajor->major->quota ?? 0) && ($userMajor->major->quota ?? 0) > 0,
    ];
}

// Convert university rankings to indexed array
$universityRankings = array_values($universityRankings);
    // Rekap berdasarkan subkategori
$perSubcategory = DB::table('result_details')
    ->join('questions', function ($join) {
        $join->on('result_details.question_id', '=', 'questions.id')
             ->where('questions.status', '=', 'Diterima'); // Filter status
    })
    ->join('sub_categories', 'questions.sub_category_id', '=', 'sub_categories.id')
    ->join('result_subject_scores', function ($join) use ($resultId) {
        $join->on('result_subject_scores.sub_category_id', '=', 'questions.sub_category_id')
             ->where('result_subject_scores.result_id', '=', $resultId);
    })
    ->select([
        'questions.sub_category_id AS id',
        'sub_categories.name AS name',
        DB::raw('SUM(CASE WHEN result_details.correct = 1 THEN 1 ELSE 0 END) AS correct'),
        DB::raw('SUM(CASE WHEN result_details.correct = 0 AND (result_details.answer IS NOT NULL AND result_details.answer <> \'\') THEN 1 ELSE 0 END) AS wrong'),
        DB::raw('SUM(CASE WHEN result_details.answer IS NULL OR result_details.answer = \'\' THEN 1 ELSE 0 END) AS `empty`'),
        DB::raw('COUNT(*) AS total'),
        DB::raw('(SUM(CASE WHEN result_details.correct = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100) AS percentage'),
        DB::raw('result_subject_scores.irt_score AS average_score'), // Ambil dari result_subject_scores
        DB::raw('MIN(result_details.question_id) AS firstQId'),
    ])
    ->where('result_details.result_id', $resultId)
    ->groupBy(
        'questions.sub_category_id',
        'sub_categories.name',
        'result_subject_scores.irt_score'
    )
    ->get();
    
        return view('pages.tryout.results', [
            'exam'   => $exam,
            'result' => $result,
            'resultDetails' => $resultDetails,
            'correctCount' => $correctCount,
            'totalQuestions' => $totalQuestions,
            'inCorrectCount' => $inCorrectCount,
            'nullCount' => $nullCount,
            'totalValid' => $totalValid,
            'accuracy' => $accuracy,
            'ranking' => $ranking,
            'totalParticipants' => $totalParticipants,
            'topPercentage' => round($topPercentage, 2),
            'majorRankings' => $majorRankings,
            'perSubcategory' => $perSubcategory,
        ]);
    }

 public function review($examId, $subCategoryId, $questionId)
    {
        $exam = Exam::with('questions')->findOrFail($examId);
        $question = Question::with(['multipleChoice', 'multipleOption', 'essay'])->where('sub_category_id', $subCategoryId)->where('status','Diterima')->findOrFail($questionId);
    
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

        // Hitung total peserta yang ikut exam ini
            $totalParticipants = Result::where('exam_id', $examId)->count();

            // Hitung berapa peserta yang sempat menjawab soal ini
            $totalAnswers = ResultDetails::whereHas('result', function($q) use ($examId) {
                    $q->where('exam_id', $examId);
                })
                ->where('question_id', $questionId)
                ->count();

            // Hitung yang menjawab benar
            $correctAnswers = ResultDetails::whereHas('result', function ($q) use ($examId) {
                    $q->where('exam_id', $examId);
                })
                ->where('question_id', $questionId)
                ->where('correct', true)
                ->count();

            // P‐value: proporsi peserta yang benar
            $difficultyLevel = $totalParticipants > 0
                ? round($correctAnswers / $totalParticipants, 2)
                : 0;

            // Kategori Kesulitan
            if ($difficultyLevel <= 0.30) {
                $difficultyCategory = 'Sukar';
            } elseif ($difficultyLevel <= 0.70) {
                $difficultyCategory = 'Sedang';
            } else {
                $difficultyCategory = 'Mudah';
            }

    $resultDetails = ResultDetails::whereHas('result', function ($q) use ($examId) {
    $q->where('exam_id', $examId)->where('user_id', auth()->id());
})->get();

    
return view('pages.tryout.result-details', compact('exam', 'question', 'userResultDetail', 'result', 'userAnswersProcessed',   'difficultyLevel',
        'difficultyCategory','resultDetails'));

    }

public function downloadResultPdf($examId, $resultId)
{
    // Ambil View dari fungsi result() yang sudah ada
    $view = $this->result($examId, $resultId);
    $data = $view->getData(); // Ambil array data dari View

    if (!$data) {
        return redirect()->back();
    }

    // Ambil evaluasi dan rekomendasi dari DB
    $resultEvaluations = ResultsEvaluation::where('result_id', $resultId)->get();

    // Inisialisasi Parsedown
    $Parsedown = new \Parsedown();

    // Parsing ke HTML satu per satu
    $data['evaluations'] = $resultEvaluations->pluck('evaluation')->map(function ($rawEvaluation) use ($Parsedown) {
        return $Parsedown->text($rawEvaluation);
    });

    $data['recommendations'] = $resultEvaluations->pluck('recommendation')->map(function ($rawRecommendation) use ($Parsedown) {
        return $Parsedown->text($rawRecommendation);
    });

    // Generate PDF
    $pdf = PDF::loadView('pages.tryout.result-pdf', $data);
    $pdf->setPaper('A4', 'portrait');

    $fileName = 'Hasil_Tryout_' . $data['result']->user->name . '_' . date('Y-m-d') . '.pdf';
    return $pdf->download($fileName);
}
}