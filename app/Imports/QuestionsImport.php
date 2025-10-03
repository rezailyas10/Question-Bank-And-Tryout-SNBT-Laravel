<?php

namespace App\Imports;

use App\Models\Exam;
use App\Models\Essay;
use App\Models\Question;
use App\Models\SubCategory;
use Illuminate\Support\Str;
use App\Models\MultipleChoice;
use App\Models\MultipleOption;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;

class QuestionsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */


     protected $examId;

   public function __construct($examId)
    {
        $this->examId = $examId;
    }

     public function model(array $row)
    {
        // dd($row);
         $headers = [
        'question_text', 'sub_category', 'question_type', 'lesson', 'explanation',
        'option1', 'option2', 'option3', 'option4', 'option5', 'correct_answer',
        'multiple1', 'yes_no1', 'multiple2', 'yes_no2', 'multiple3', 'yes_no3',
        'multiple4', 'yes_no4', 'multiple5', 'yes_no5', 'text'
    ];
    
    // Kalau row pertama (isinya sama kayak headers), langsung skip
    if ($row[0] === 'soal' || $row[0] === 'question_text') {
        return null;
    }

    $data = array_combine($headers, $row);

    // 1. cari sub_category_id
    $subCategoryId = null;
    if (!empty($data['sub_category'])) {
        $subCategory = SubCategory::where('name', $data['sub_category'])->first();
        $subCategoryId = $subCategory ? $subCategory->id : null;
    }

    // buat question utama
    $question = Question::create([
    'exam_id'        => $this->examId,
    'question_text'  => $data['question_text'] ?? null,
    'sub_category_id'=> $subCategoryId,
    'question_type'  => $data['question_type'] ?? null,
    'lesson'         => $data['lesson'] ?? null,
    'explanation'    => $data['explanation'] ?? null,
    'user_id'        => Auth::id(),
    'status'         => Auth::user()->is_validator == 1 ? 'Diterima' : 'Ditinjau',
]);

    // masukkan ke tabel sesuai tipe soal
    if ($data['question_type'] === 'multiple_choice' || $data['question_type'] === 'pilihan_ganda') {
        MultipleChoice::create([
            'question_id'    => $question->id,
            'option1'        => $data['option1'] ?? null,
            'option2'        => $data['option2'] ?? null,
            'option3'        => $data['option3'] ?? null,
            'option4'        => $data['option4'] ?? null,
            'option5'        => $data['option5'] ?? null,
            'correct_answer' => $data['correct_answer'] ?? null,
        ]);
    } elseif ($data['question_type'] === 'multiple_option' || $data['question_type'] === 'pilihan_majemuk') {
   MultipleOption::create([
    'question_id' => $question->id,
    'multiple1'   => $data['multiple1'] ?? null,
    'yes\/no1'    => $data['yes/no1'] ?? null, // pakai escape
    'multiple2'   => $data['multiple2'] ?? null,
    'yes\/no2'    => $data['yes/no2'] ?? null,
    'multiple3'   => $data['multiple3'] ?? null,
    'yes\/no3'    => $data['yes/no3'] ?? null,
    'multiple4'   => $data['multiple4'] ?? null,
    'yes\/no4'    => $data['yes/no4'] ?? null,
    'multiple5'   => $data['multiple5'] ?? null,
    'yes\/no5'    => $data['yes/no5'] ?? null,
]);
    } elseif ($data['question_type'] === 'isian') {
        Essay::create([
            'question_id' => $question->id,
            'text'        => $data['text'] ?? null,
        ]);
    }

    return $question;
}
}
