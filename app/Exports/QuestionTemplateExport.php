<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;

class QuestionTemplateExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
     public function collection()
    {
        return new Collection([
            [
                'question_text',
                'sub_category',
                'question_type',
                'lesson',
                'explanation',
                'option1',
                'option2',
                'option3',
                'option4',
                'option5',
                'correct_answer',
                'multiple1',
                'yes/no1',
                'multiple2',
                'yes/no2',
                'multiple3',
                'yes/no3',
                'multiple4',
                'yes/no4',
                'multiple5',
                'yes/no5',
                'text'
            ]
        ]);
    }
}
