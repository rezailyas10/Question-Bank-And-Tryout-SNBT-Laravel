@extends('layouts.quiz')

@section('title')
  Pembahasan Soal
@endsection

@section('content')
<div class="container mt-4">
  <div class="row">
    <!-- Panel Navigasi Soal -->
    <div class="col-md-3" data-aos="fade-right" data-aos-duration="1000">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Daftar Soal</h5>
        </div>
        <div class="card-body">
          <div class="numbers-container d-flex flex-wrap justify-content-center">
         @foreach($exam->questions->where('status', 'Diterima') as $q)
          @php
              $detail = $resultDetails->firstWhere('question_id', $q->id) ?? null;

              if ($detail === null || $detail->correct === null) {
                  $btnClass = 'bg-white text-dark'; // belum dijawab
              } elseif ($detail->correct) {
                  $btnClass = 'bg-success text-white'; // benar
              } else {
                  $btnClass = 'bg-danger text-white'; // salah
              }
          @endphp

          <a href="{{ route('exam.review', ['exam' => $exam->id, 'question' => $q->id]) }}" 
            class="btn question-number-btn {{ $btnClass }} {{ $q->id == $question->id ? 'active' : '' }}">
              {{ $loop->iteration }}
          </a>
      @endforeach
          </div>
          <div class="mt-4">
            <a href="{{ route('bank-soal-result', ['exam' => $exam->id, 'id' => $result->id]) }}" class="btn btn-primary w-100">
              Kembali ke Hasil
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Konten Pembahasan -->
    <div class="col-md-9" data-aos="fade-left" data-aos-duration="1000">
      <div class="card">
        <div class="card-header">
          <h5>Pembahasan Soal No. {{ $exam->questions->search(function($item) use ($question) {
            return $item->id == $question->id;
          }) + 1 }}</h5>
           <h6>Materi: {{ $question->lesson }}</h6>
        </div>

        {{-- untuk menampilkan data gambar --}}
                        @php
            $fixedquestiontext = preg_replace_callback(
    '/<img\s+[^>]*src="([^"]+)"[^>]*>/i',
    function ($matches) {
        $url = $matches[1];
        if (!preg_match('/^http(s)?:\/\//', $url)) {
            $url = asset($url);
        }
        // Tambahkan style max-width agar tidak terlalu besar
        return '<img src="' . $url . '" style="max-width: 100%; height: auto;" />';
    },
    $question->question_text
);

                        @endphp
        <div class="card-body">
          <!-- Pertanyaan -->
          <div class="question-box mb-4">
            <h6 class="fw-bold">Pertanyaan:</h6>
            @php
                $hasTable = str_contains($question->question_text, '<table');
            @endphp
             <p>@if ($hasTable)
            {{-- Tambahkan wrapper styling untuk tabel --}}
            <div class="table-responsive">
                {{-- Tambahkan style table agar rapi --}}
                <style>
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 10px;
                    }
                    th, td {
                        border: 1px solid #ccc;
                        padding: 8px;
                        text-align: center;
                    }
                    th {
                        background-color: #f8f9fa;
                    }
                </style>
                {!! $question->question_text !!}
            </div>
        @else
            {!! $fixedquestiontext !!}
        @endif</p>
            
          </div>

          <!-- Jawaban User dan Jawaban Benar -->
          <div class="row">
            @if ($question->question_type === 'pilihan_ganda')
            <div class="col-md-12">
              @else
              <div class="col-md-6">
            @endif
              <div class="answer-box user-answer p-3 mb-3" style="background-color: #f8f9fa; border-radius: 8px;">
                <h6 class="fw-bold">Jawaban Anda:</h6>
                
                @if($question->question_type === 'pilihan_ganda')
                <div class="user-mc-answer">
                  @if($question->multipleChoice)
                    @php
                      $userAnswer = $userResultDetail?->answer;
                      $isAnswered = $userAnswer !== null && $userAnswer !== 'null';
                      $correctAnswer = $question->multipleChoice->correct_answer;
                      $isCorrect = $userResultDetail?->correct;
                    @endphp

                    <ul class="list-group">
                      @for($i = 1; $i <= 5; $i++)
                        @php
                          $optionField = 'option' . $i;
                          $optionText = $question->multipleChoice->$optionField;

                          $isUserAnswer = $userAnswer === $optionText;
                          $isCorrectAnswerOption = $correctAnswer === $optionText;

                          $class = '';

                          if ($isAnswered) {
                            if ($isUserAnswer && $isCorrect) {
                              $class = 'bg-success text-white'; // benar
                            } elseif ($isUserAnswer && !$isCorrect) {
                              $class = 'bg-danger text-white'; // salah
                            } elseif (!$isCorrect && $isCorrectAnswerOption) {
                              $class = 'bg-success text-white'; // highlight jawaban benar
                            }
                          }
                        @endphp

                        {{-- untuk menampilkan data gambar --}}
                         @php
$fixedOptionText = preg_replace_callback(
    '/<img\s+[^>]*src="([^"]+)"[^>]*>/i',
    function ($matches) {
        $url = $matches[1];

        // Jika sudah http atau https, langsung pakai
        if (preg_match('/^http(s)?:\/\//', $url)) {
            $finalUrl = $url;
        } else {
            // Buat path storage untuk akses publik
            $cleanPath = ltrim($url, '/');
            if (!str_starts_with($cleanPath, 'storage/')) {
                $cleanPath = 'storage/' . $cleanPath;
            }
            $finalUrl = asset($cleanPath);
        }

        return '<img src="' . $finalUrl . '" style="max-width:100%; height:auto; max-height:200px;" />';
    },
    $optionText
);
@endphp

                        @if(!empty($optionText))
                          <li class="list-group-item {{ $class }}">
                            {!! $fixedOptionText !!}
                            @if($isAnswered && $isUserAnswer)
                              <i class="bi {{ $isCorrect ? 'bi-check-circle' : 'bi-x-circle' }}"></i>
                            @endif
                          </li>
                        @endif
                      @endfor
                    </ul>
                  @else
                    <p class="text-muted">Data opsi tidak tersedia</p>
                  @endif
                </div>
              
                  @elseif($question->question_type === 'pilihan_majemuk')
                  <div class="user-multiple-answer">
                    @if(!empty($userAnswersProcessed))
                      <table class="table table-sm">
                        <thead>
                          <tr>
                            <th>Pernyataan</th>
                            <th>Jawaban Anda</th>
                          </tr>
                        </thead>
                        <tbody>
                           @php
                        $plainText = strtolower(strip_tags($question->question_text));
                        $defaultWords = ['mendukung', 'memperlemah', 'memperkuat', 'menguatkan', 'melemahkan'];

                        $matchedWord = null;

                        foreach ($defaultWords as $keyword) {
                            if (str_contains($plainText, $keyword)) {
                                $matchedWord = ucfirst($keyword);
                                break;
                            }
                        }

                        if ($matchedWord) {
                            $yesLabel = $matchedWord;
                            $noLabel  = 'Tidak ' . $matchedWord;
                            $isStatementValid = false;
                        } else {
                            $yesLabel = 'Benar';
                            $noLabel  = 'Salah';
                            $isStatementValid = true;
                        }
                    @endphp
                          @foreach($userAnswersProcessed as $answer)
                            @php
                                $userAnswer = strtolower(trim($answer['user_answer']));
                            @endphp
                            <tr>
                                <td>{{ $answer['statement'] }}</td>
                                <td class="{{ $answer['is_correct'] ? 'text-success' : 'text-danger' }}">
                                    @if($userAnswer === 'yes')
                                        {{ $yesLabel }}
                                    @elseif($userAnswer === 'no')
                                        {{ $noLabel }}
                                    @else
                                        {{ $answer['user_answer'] }}
                                    @endif
                                    <i class="bi {{ $answer['is_correct'] ? 'bi-check-circle' : 'bi-x-circle' }}"></i>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                      </table>
                    @else
                      <p class="text-muted">Tidak menjawab</p>
                    @endif
                  </div>
                
                @elseif($question->question_type === 'isian')
                <div class="user-essay-answer">
                  @php
                    $ans = $userResultDetail?->answer;
                  @endphp

                  {{-- Cek jawaban ada dan bukan string 'null' --}}
                  @if($ans !== null && $ans !== 'null')
                    <p class="{{ $userResultDetail->correct ? 'text-success' : 'text-danger' }}">
                      {{ $ans }}
                      <i class="bi {{ $userResultDetail->correct ? 'bi-check-circle' : 'bi-x-circle' }}"></i>
                    </p>
                  @else
                    <p class="text-muted">Tidak menjawab</p>
                  @endif
                  </div>
                @endif
              </div>
            </div>

            <div class="col-md-6">
              <div class="answer-box correct-answer p-3 mb-3" style="background-color: #e8f5e9; border-radius: 8px;">
                <h6 class="fw-bold">Jawaban Benar:</h6>
                
                @if($question->question_type === 'pilihan_ganda')
                   {{-- untuk menampilkan data gambar --}}
                       @php
    $fixedCorrectAnswer = '';

    if (!empty($question->multipleChoice->correct_answer)) {
        $fixedCorrectAnswer = preg_replace_callback(
            '/<img\s+[^>]*src="([^"]+)"[^>]*>/i',
            function ($matches) {
                $url = $matches[1];

                if (preg_match('/^http(s)?:\/\//', $url)) {
                    // URL eksternal, biarkan
                    $finalUrl = $url;
                } else {
                    // URL lokal, pastikan diawali dengan storage/
                    $cleanPath = ltrim($url, '/');
                    if (!str_starts_with($cleanPath, 'storage/')) {
                        $cleanPath = 'storage/' . $cleanPath;
                    }
                    $finalUrl = asset($cleanPath);
                }

                return '<img src="' . $finalUrl . '" style="max-width:100%; height:auto; max-height:200px;" />';
            },
            $question->multipleChoice->correct_answer
        );
    }
@endphp
                      <p>{!! $fixedCorrectAnswer !!}</p>
                
                  @elseif($question->question_type === 'pilihan_majemuk')
                  <div class="correct-multiple-answer">
                    @php
                      $userAnswers = json_decode($question->answer, true); // decode jawaban user
                      $option = $question->multipleOption;
                    @endphp
                
                    @if($option)
                      <table class="table table-sm">
                        <thead>
                          <tr>
                            <th>Pernyataan</th>
                            <th>Jawaban Benar</th>
                          </tr>
                        </thead>
                        <tbody>
                          @for($i = 1; $i <= 5; $i++)
                            @php
                                $statement = $option->{'multiple' . $i};
                                $correct = strtolower($option->{'yes/no' . $i} ?? 'no');
                            @endphp

                            @if(!empty($statement))
                                <tr>
                                    <td>{{ $statement }}</td>
                                    <td>
                                        @if($correct === 'yes')
                                            {{ $yesLabel }}
                                        @elseif($correct === 'no')
                                            {{ $noLabel }}
                                        @else
                                            {{ ucfirst($correct) }}
                                        @endif
                                    </td>
                                </tr>
                            @endif

                          @endfor
                        </tbody>
                      </table>
                    @else
                      <p class="text-muted">Data jawaban tidak tersedia</p>
                    @endif
                  </div>
                
                @elseif($question->question_type === 'isian')
                  <div class="correct-essay-answer">
                    @if($question->essay)
                      <p>{{ $question->essay->text }}</p>
                    @else
                      <p class="text-muted">Data jawaban tidak tersedia</p>
                    @endif
                  </div>
              @endif
              </div>
            </div>
          </div>

          <!-- Penjelasan -->
          <div class="explanation-box p-3 mt-3" style="background-color: #e3f2fd; border-radius: 8px;">
            <h6 class="fw-bold">Pembahasan:</h6>
            <div class="explanation-content">

              @php
                 $fixedexplanation = preg_replace_callback(
                  '/<img\s+[^>]*src="([^"]+)"[^>]*>/i',
                  function ($matches) {
                      $url = $matches[1];
                      if (!preg_match('/^http(s)?:\/\//', $url)) {
                          $url = asset($url);
                      }
                      // Tambahkan style max-width agar tidak terlalu besar
                      return '<img src="' . $url . '" style="max-width: 100%; height: auto;" />';
                  },
                  $question->explanation
              );
              @endphp
              @if($question->explanation)
                <p>{!! $fixedexplanation !!}</p>
              @else
                <p class="text-muted">Tidak ada pembahasan untuk soal ini.</p>
              @endif
            </div>
          </div>

          <!-- Navigasi antar soal -->
          <div class="navigation mt-4 d-flex justify-content-between">
            @php
              $currentIndex = $exam->questions->search(function($item) use ($question) {
                return $item->id == $question->id;
              });
              $prevIndex = $currentIndex - 1;
              $nextIndex = $currentIndex + 1;
            @endphp

            @if($prevIndex >= 0)
            <a href="{{ route('exam.review', ['exam' => $exam->id, 'question' => $exam->questions[$prevIndex]->id]) }}"
              class="btn btn-outline-primary">
             <i class="bi bi-arrow-left"></i> Soal Sebelumnya
           </a>
            @else
              <div></div>
            @endif

            @if($nextIndex < $exam->questions->count())
              <a href="{{ route('exam.review', ['exam' => $exam->id, 'question' => $exam->questions[$nextIndex]->id]) }}" 
                class="btn btn-outline-primary">
                Soal Berikutnya <i class="bi bi-arrow-right"></i>
              </a>
            @else
              <div></div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('addon-style')
<style>
  .question-number-btn {
    margin: 5px;
    width: 40px;
    height: 40px;
    text-align: center;
    line-height: 40px;
    padding: 0;
    border-radius: 50%;
    font-weight: 500;
  }
  
  .question-number-btn.active {
    background-color: #3b82f6;
    color: #ffffff;
    font-weight: bold;
  }
  
  .explanation-box {
    border-left: 4px solid #2196f3;
  }
  
  .user-answer {
    border-left: 4px solid #f44336;
  }
  
  .correct-answer {
    border-left: 4px solid #4caf50;
  }
</style>
@endpush

@push('addon-script')
<script>
  AOS.init();

  $(function () {
    $(document).scroll(function () {
      var $nav = $(".header");
      $nav.toggleClass("scrolled", $(this).scrollTop() > $nav.height());
    });
  });
</script>
@endpush