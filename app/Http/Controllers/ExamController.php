<?php

namespace App\Http\Controllers;

use App\Models\cart;
use App\Models\Exam;
use App\Models\Result;
use App\Models\Product;
use App\Models\Question;
use App\Models\ResultLog;
use App\Models\ResultsEvaluation;
use Illuminate\Http\Request;
use App\Models\ResultDetails;
use App\Models\ResultDetailss;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
      /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function detail(Request $request, $id)
{
        $user = Auth::user();

    if ($user->roles !== 'USER') {
        return redirect()->back();
    }
    $exam = Exam::where('slug', $id)
            ->with(['questions.multipleChoice', 'questions.multipleOption', 'questions.essay'])
            ->firstOrFail();

    $questions = $exam->questions()->where('status','Diterima')->orderBy('id')->get();

    return view('pages.bank-soal.exam', [
        'exam'      => $exam,
        'questions' => $questions,
    ]);
}


public function submit(Request $request)
{
    $user     = Auth::user();
    $exam_id  = $request->input('exam_id');
    $questions = Question::where('exam_id', $exam_id)->where('status','Diterima')->get();

    // 1. Cari atau buat Result (per user & exam)
    $result = Result::firstOrCreate(
        [
            'user_id' => $user->id,
            'exam_id' => $exam_id,
        ],
        [
            'score' => 0, // default kalau baru dibuat
        ]
    );

    // Result::create([
    //     'user_id'   => $result->user_id,
    //     'exam_id' => $result->exam_id,
    //     'score'     => 0,
    // ]);

    // // 2. Hapus semua detail jawaban lama (jika ada)
    // $result->details()->delete();

    $score = 0;

    // 3. Simpan jawaban & hitung skor
    foreach ($questions as $question) {
    $answerInput = $request->input("answer.{$question->id}", null);
    $answer  = null;
    $correct = null;

    // pilihan ganda
    if ($question->question_type === 'pilihan_ganda') {
        if (!empty($answerInput)) {
            $answer  = $answerInput;
            $correct = ($answer === $question->multipleChoice->correct_answer) ? 1 : 0;
        }

    // pilihan majemuk
    } elseif ($question->question_type === 'pilihan_majemuk') {
        $keys = ['multiple1','multiple2','multiple3','multiple4','multiple5'];
        $map  = [
            'multiple1'=>'yes/no1','multiple2'=>'yes/no2',
            'multiple3'=>'yes/no3','multiple4'=>'yes/no4',
            'multiple5'=>'yes/no5',
        ];
        $formatted = []; $benar = 0; $total = 0; $dijawab = false;

        foreach ($keys as $key) {
            if ($opsi = $question->multipleOption->{$key}) {
                $userAns = $answerInput[$key] ?? null;
                if (!is_null($userAns)) $dijawab = true;

                $actual = $question->multipleOption->{$map[$key]};
                $formatted[$opsi] = $userAns;
                if ($userAns === $actual) $benar++;
                $total++;
            }
        }

        if ($dijawab) {
            $answer  = $formatted;
            $correct = ($total > 0 && $benar === $total) ? 1 : 0;
        }

    // isian
    } elseif ($question->question_type === 'isian') {
        if (!empty($answerInput)) {
            $answer  = $answerInput;
            $correct = strcasecmp($answer, $question->essay->text ?? '') === 0 ? 1 : 0;
        }
    }

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

// ResultDetails::create([
//         'result_id'   => $result->id,
//         'question_id' => $question->id,
//         'answer'      => is_string($answer) || is_null($answer) ? $answer : json_encode($answer),
//         'correct'     => $correct,
//     ]);

    // Hanya tambah skor kalau correct bernilai 1
    if ($correct === 1) {
        $score += 1;
    }
}


// hitung skor persentase
$totalQuestions = $questions->count();
$percentageScore = $totalQuestions > 0 ? ($score / $totalQuestions) * 100 : 0;

// update score dengan persentase
$result->update(['score' => $percentageScore]);

    //— redirect sesuai kebutuhan kamu —//
    // misal ambil subcategory pertama:
    $exam = Exam::with('subCategory')->findOrFail($exam_id);
    $subCategorySlug = $exam->subCategory->slug;

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

// Simpan ke tabel resultEvaluation
ResultsEvaluation::create([
    'result_id' => $result->id,
    'score'     => $percentageScore,
    'correct'   => $correctCount,
    'wrong'     => $wrongCount,
    'empty'     => $zeroCount
]);
    return redirect()
        ->route('bank-soal-detail', ['id' => $subCategorySlug])
        ->with('success', "Kuis berhasil disubmit! Skor Anda: {$score} / {$questions->count()}");
}


// public function submit(Request $request)
// {
//     $user = Auth::user();
//     $exam_id = $request->input('exam_id');
//     $questions = Question::where('exam_id', $exam_id)->get();

//     $result = Result::create([
//         'user_id' => $user->id,
//         'exam_id' => $exam_id,
//         'score'   => 0,
//     ]);

//     $score = 0;

//     foreach ($questions as $question) {
//         $answerInput = $request->input("answer.{$question->id}", []);
//         $answer = '';
//         $correct = 0;

//         if ($question->question_type === 'pilihan_ganda') {
//             $answer = $answerInput;
//             $correct = ($answer === $question->multipleChoice->correct_answer) ? 1 : 0;

//         } elseif ($question->question_type === 'pilihan_majemuk') {
//             $keys = ['multiple1','multiple2','multiple3','multiple4','multiple5'];
//             $map  = ['multiple1'=>'yes/no1','multiple2'=>'yes/no2','multiple3'=>'yes/no3','multiple4'=>'yes/no4','multiple5'=>'yes/no5'];
//             $formatted = []; $benar = 0; $total = 0;

//             foreach ($keys as $key) {
//                 if ($opsi = $question->multipleOption->{$key}) {
//                     $total++;
//                     $userAns = $answerInput[$key] ?? null;
//                     $actual  = $question->multipleOption->{$map[$key]};
//                     $formatted[$opsi] = $userAns;
//                     if ($userAns === $actual) $benar++;
//                 }
//             }

//             $answer = $formatted;
//             // $correct = $total > 0 ? $benar / $total : 0;
//             //jika kalo salah satu saja maka salah semua
//             $correct = ($total > 0 && $benar === $total) ? 1 : 0; 

//         } elseif ($question->question_type === 'isian') {
//             $answer = $answerInput;
//             $correct = strcasecmp($answer, $question->essay->text ?? '') === 0 ? 1 : 0;
//         }

//         ResultDetails::create([
//             'result_id'   => $result->id,
//             'question_id' => $question->id,
//             'answer'      => is_string($answer) ? $answer : json_encode($answer),
//             'correct'     => $correct,
//         ]);

//         $score += $correct;
//     }

//     $result->update(['score' => $score]);

//     // Get the exam with its tryoutSubtest relationships
// $exam = Exam::with('tryoutSubtest.subCategory')->findOrFail($exam_id);

// // Get the first tryoutSubtest's subCategory slug
// $subCategorySlug = $exam->tryoutSubtest->first()->subCategory->slug;

// return redirect()->route('bank-soal-detail', [
//     'id' => $subCategorySlug
// ])->with('success', "Kuis berhasil disubmit! Skor Anda: {$score} dari {$questions->count()}");
// }

}



