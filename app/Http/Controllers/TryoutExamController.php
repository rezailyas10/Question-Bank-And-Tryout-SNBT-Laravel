<?php

namespace App\Http\Controllers;

use App\Models\cart;
use App\Models\Exam;
use App\Models\Result;
use App\Models\Product;
use App\Models\Question;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Models\ResultDetails;
use App\Models\TryoutSubtest;
use App\Models\ResultDetailss;
use App\Models\ResultsEvaluation;
use Illuminate\Support\Facades\Auth;

class TryoutExamController extends Controller
{
      /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function exam($examid, $subtestId)
    {
        // 1) Ambil exam
        $exam = Exam::where('slug', $examid)->firstOrFail();

        // 2) Ambil data pivot subtest
        $subtest = SubCategory::with('category')->findOrFail($subtestId);

        // 3) Ambil hanya questions untuk subcategory ini
        $questions = Question::where('exam_id', $exam->id)
                    ->where('sub_category_id', $subtestId)
                    ->where('status','Diterima')
                    ->with(['multipleChoice', 'multipleOption', 'essay'])
                    ->inRandomOrder()
                    ->get();

        // 4) Tampilkan view
        return view('pages.tryout.exam', [
            'exam'      => $exam,
            'questions' => $questions,
            'subtest'   => $subtest,
        ]);
    }


  public function submit(Request $request, $examSlug, $subtestId)
{
    // 1. Temukan exam dan subtest
    $exam = Exam::where('slug', $examSlug)->firstOrFail();
    $subtest = SubCategory::findOrFail($subtestId);

    // 2. Ambil semua questions untuk subCategory ini
    $questions = Question::where('exam_id', $exam->id)
                         ->where('sub_category_id', $subtest->id)
                         ->get();

    // 3. Siapkan Result (score default 0 jika baru dibuat)
    $result = Result::firstOrCreate(
        [
            'user_id' => Auth::id(),
            'exam_id' => $exam->id,
        ],
        [
            // jika baru, score di-set 0
            'score' => 0,
        ]
    );

   foreach ($questions as $question) {
    $input = $request->input("answer.{$question->id}", null);
    $answer  = null;
    $correct = null;

    if ($question->question_type === 'pilihan_ganda') {
        if (!empty($input)) {
            $answer  = $input;
            $correct = ($input === $question->multipleChoice->correct_answer) ? 1 : 0; // tetap 0 walaupun benar
        }
    }
    elseif ($question->question_type === 'pilihan_majemuk') {
        $keys      = ['multiple1','multiple2','multiple3','multiple4','multiple5'];
        $map       = [
            'multiple1'=>'yes/no1','multiple2'=>'yes/no2',
            'multiple3'=>'yes/no3','multiple4'=>'yes/no4',
            'multiple5'=>'yes/no5',
        ];
        $formatted = [];
        $benar     = 0;
        $total     = 0;
        $dijawab   = false;

        foreach ($keys as $k) {
            if ($opt = $question->multipleOption->{$k}) {
                $userAns = $input[$k] ?? null;
                if (!is_null($userAns)) $dijawab = true;

                $total++;
                $actual  = $question->multipleOption->{$map[$k]};
                $formatted[$opt] = $userAns;
                if ($userAns === $actual) {
                    $benar++;
                }
            }
        }

        if ($dijawab) {
        $answer  = $formatted;

        // ✅ Jawaban benar hanya jika semua benar
        $correct = ($benar === $total) ? 1 : 0;
    }
    }
    elseif ($question->question_type === 'isian') {
        if (!empty($input)) {
            $answer  = $input;
            $correct = 1; // tetap 0 walaupun benar
        }
    }

    // ResultDetails::create([
    //     'result_id'   => $result->id,
    //     'question_id' => $question->id,
    //     'answer'      => is_string($answer) || is_null($answer) ? $answer : json_encode($answer),
    //     'correct'     => $correct,
    // ]);

    // simpan detail
   // Simpan hanya jika soal berstatus 'Diterima'
if ($question->status === 'Diterima') {
    ResultDetails::updateOrCreate(
        [
            'result_id'   => $result->id,
            'question_id' => $question->id,
        ],
        [
            'answer'  => is_string($answer) || is_null($answer) ? $answer : json_encode($answer),
            'correct' => $correct,
        ]
    );
}
}

// Hitung jumlah jawaban benar dan salah dari resultDetails
$correctCount = ResultDetails::where('result_id', $result->id)
    ->where('correct', 1)
    ->count();

$wrongCount = ResultDetails::where('result_id', $result->id)
    ->where('correct', 0)
    ->count();

$zeroCount = ResultDetails::where('result_id', $result->id)
            ->where('correct', null)
            ->count();


 ResultsEvaluation::updateOrCreate(
        ['result_id' => $result->id],          // kondisi mencari record
        ['correct'   => $correctCount,         // kolom yang di-update jika record ada
         'wrong'     => $wrongCount,
         'empty' => $zeroCount]           // atau di-set jika record baru dibuat
    );


    // 6. Redirect ke interstitial subtest berikutnya
    return redirect()->route('subtest-interstitial', [
        'exam'    => $exam->slug,
        'subCategory' => $subtest->id,
    ]);
}


    // …



public function interstitial($examSlug, $subCategoryId)
{
    $exam = Exam::where('slug', $examSlug)->first();
    $error = null;

    if (!$exam) {
        $error = 'Exam tidak ditemukan.';
        return view('pages.tryout.interstitial', compact('error'));
    }

    $subCategory = SubCategory::find($subCategoryId);
    if (!$subCategory) {
        $error = 'Subkategori tidak ditemukan.';
        return view('pages.tryout.interstitial', compact('exam', 'error'));
    }

    $result = Result::where('user_id', Auth::id())
                    ->where('exam_id', $exam->id)
                    ->first();

    if (!$result) {
        $error = 'Result tidak ditemukan. Silakan kerjakan subkategori soal terlebih dahulu.';
        return view('pages.tryout.interstitial', compact('exam', 'subCategory', 'error'));
    }

    // Hitung skor untuk subkategori ini
    $qids = $exam->questions()
                ->where('sub_category_id', $subCategory->id)
                ->pluck('id');

    $correctCount = $result->details()
                           ->whereIn('question_id', $qids)
                           ->sum('correct');

    $totalQuestions = $qids->count();

    // Ambil semua subkategori dari questions yang tersedia dalam exam
    $allSubCategories = $exam->questions()
                             ->with('subCategory')
                             ->get()
                             ->pluck('subCategory')
                             ->unique('id')
                             ->values();

    $idx = $allSubCategories->search(fn($s) => $s->id == $subCategory->id);
    $next = $allSubCategories->get($idx + 1);

    return view('pages.tryout.interstitial', compact(
        'exam', 'subCategory', 'correctCount', 'totalQuestions', 'next', 'error'
    ));
}


    // Halaman akhir setelah subtest terakhir
    public function finish($examSlug)
    {
        $exam   = Exam::where('slug',$examSlug)->firstOrFail();
        return view('pages.tryout.finish',compact('exam'));
    }

}





