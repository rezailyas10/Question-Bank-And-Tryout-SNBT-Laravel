@extends('layouts.quiz')

@section('title')
  Pengerjaan Tryout
@endsection
<style>
  .option-text img {
  max-width: 100%;
  height: auto;
}
</style>
@section('content')
 <div class="header" data-aos="fade-down" data-aos-duration="800">
        <h2>{{ $subtest->name }}</h2>
    </div>
<div class="container mt-4">

  <!-- TIMER DISPLAY -->
  <div class="d-flex justify-content-end mb-3">
    <span class="fw-bold">Waktu tersisa:</span>
    &nbsp;<span id="timer-display" class="fw-bold"></span>
  </div>

  <form id="subtest-form"
        action="{{ route('tryout-submit', ['exam'=>$exam->slug,'subtest'=>$subtest->id]) }}"
        method="POST">
    @csrf

    <!-- HIDDEN TIMER VALUE (detik) -->
    <input type="hidden" id="timer-value" value="{{ $subtest->timer * 60 }}">

    <div class="row g-4">
      <!-- Panel Nomor Soal -->
      <div class="col-md-3" data-aos="fade-right" data-aos-duration="1000">
        <!-- … panel nomor soal sama persis … -->
        <div class="card">
          <div class="card-header"><h5 class="mb-0">Nomor Soal</h5></div>
          <div class="card-body">
            <div class="numbers-container d-flex flex-wrap justify-content-center">
              @foreach($questions as $i => $q)
                <button type="button"
                        class="btn question-number-btn {{ $i==0?'active':'' }}"
                        data-index="{{ $i }}">
                  {{ $i+1 }}
                </button>
              @endforeach
            </div>
            <div class="mt-4">
              <button type="button"
                      class="submit-btn btn btn-primary"
                      data-bs-toggle="modal"
                      data-bs-target="#submitModal">
                Selesaikan Pengerjaan
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Konten Pertanyaan -->
      <div class="col-md-9" data-aos="fade-left" data-aos-duration="1000">
        <div class="question-container">
          <h5 class="question-number">
            Pertanyaan no <span id="current-question-number">1</span>
          </h5>

          @foreach($questions as $i => $question)
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
            <div class="question-content question-box"
                 style="{{ $i===0?'':'display:none;' }}"
                 data-index="{{ $i }}">
              <!-- … semua markup pertanyaan & opsi sama seperti semula … -->

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
              @if($question->photo)
                <img src="{{ asset('storage/'.$question->photo) }}"
                     class="img-fluid mb-3">
              @endif

              {{-- PILGAN --}}
              @if($question->question_type==='pilihan_ganda')
                <div class="options mt-5" data-question-id="{{ $question->id }}">
                  <h6>Pilihan Ganda</h6>
                  @php
                    $opts = collect([
                      'option1'=>$question->multipleChoice->option1,
                      'option2'=>$question->multipleChoice->option2,
                      'option3'=>$question->multipleChoice->option3,
                      'option4'=>$question->multipleChoice->option4,
                      'option5'=>$question->multipleChoice->option5,
                    ])->filter()->shuffle();
                    $labels = range('A','E');
                  @endphp
                  @foreach($opts as $key=>$text)
                    <div class="option-item mt-2" data-option-id="{{ $key }}">
                      <span class="fw-bold">{{ array_shift($labels) }}</span>
                      &nbsp;  <span class="option-text">{!! $text !!}</span>
                      {{-- <span>{!! $text !!}</span> --}}
                    </div>
                  @endforeach
                  <input type="hidden"
                         name="answer[{{ $question->id }}]"
                         id="answer-{{ $question->id }}">
                </div>

              {{-- PILMAJ --}}
              @elseif($question->question_type==='pilihan_majemuk')
                <div class="options mt-5">
                  <h6>Pilihan Majemuk</h6>
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
                  @php
                    $opts = [
                      'multiple1'=>$question->multipleOption->multiple1,
                      'multiple2'=>$question->multipleOption->multiple2,
                      'multiple3'=>$question->multipleOption->multiple3,
                      'multiple4'=>$question->multipleOption->multiple4,
                      'multiple5'=>$question->multipleOption->multiple5,
                    ];
                    $filtered = array_filter($opts);
                  @endphp
                  @if($filtered)
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Pernyataan</th>
                       <th class="text-center">{{ $yesLabel }}</th>
                            <th class="text-center">{{ $noLabel }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($filtered as $k=>$t)
                          <tr>
                            <td>{{ $t }}</td>
                            <td class="text-center">
                              <input type="radio"
                                     name="answer[{{ $question->id }}][{{ $k }}]"
                                     value="yes">
                            </td>
                            <td class="text-center">
                              <input type="radio"
                                     name="answer[{{ $question->id }}][{{ $k }}]"
                                     value="no">
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  @endif
                </div>

              {{-- ISIAN --}}
              @elseif($question->question_type==='isian')
                <div class="essay-answer mt-5">
                  <h6>Jawaban Isian</h6>
                  <input type="text"
                         class="form-control w-50"
                         name="answer[{{ $question->id }}]"
                         placeholder="Tulis jawaban…">
                </div>
              @endif

              <button
              type="button"
              class="reset-btn btn btn-secondary mt-3"
              onclick="resetAnswer({{ $i }}, {{ $question->id }})">
              Reset Jawaban
            </button>
            </div>
          @endforeach

          <input type="hidden" name="exam_id" value="{{ $exam->id }}">

          <!-- Nave Si Soal -->
          <div class="navigation mt-3">
            <button type="button" class="nav-button btn" id="prev-question" disabled>
              <i class="bi bi-arrow-left"></i> Sebelumnya
            </button>
            <div class="pagination-info" id="pagination-info">
              1 / {{ $questions->count() }}
            </div>
            <button type="button" class="nav-button btn" id="next-question">
              Selanjutnya <i class="bi bi-arrow-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Submit -->
    <div class="modal fade" id="submitModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Konfirmasi Pengerjaan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            Apakah Anda yakin ingin selesai?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              Batal
            </button>
            <button type="submit" class="btn btn-primary">Ya, Selesaikan</button>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection

@push('addon-style')
<style>
  .question-number-btn.answered {
    background-color: #3b82f6 !important;
    color: #ffffff;
    font-weight: bold;
    border: 1px solid #3b82f6;
  }

  .reset-btn {
    margin-top: 20px;
  }
</style>
@endpush

@push('addon-script')
<script>
    AOS.init();

let totalQuestions = {{ count($questions) }};
let currentQuestion = 0;

function showQuestion(index) {
  document.querySelectorAll('.question-box').forEach((box, i) => {
    box.style.display = i === index ? 'block' : 'none';
  });

  document.querySelectorAll('.question-number-btn').forEach((btn, i) => {
    btn.classList.toggle('active', i === index);
  });

  document.getElementById('current-question-number').innerText = index + 1;
  document.getElementById('pagination-info').innerText = (index + 1) + ' / ' + totalQuestions;

  document.getElementById('prev-question').disabled = index === 0;
  document.getElementById('next-question').disabled = index === totalQuestions - 1;
}

document.querySelectorAll('.question-number-btn').forEach((btn) => {
  btn.addEventListener('click', function () {
    currentQuestion = parseInt(this.dataset.index);
    showQuestion(currentQuestion);
  });
});

document.getElementById('prev-question').addEventListener('click', () => {
  if (currentQuestion > 0) {
    currentQuestion--;
    showQuestion(currentQuestion);
  }
});

document.getElementById('next-question').addEventListener('click', () => {
  if (currentQuestion < totalQuestions - 1) {
    currentQuestion++;
    showQuestion(currentQuestion);
  }
});

function markAnswered(index, isAnswered) {
const button = document.querySelector(`.question-number-btn[data-index="${index}"]`);
if (button) {
  if (isAnswered) {
    button.classList.add('answered');
  } else {
    button.classList.remove('answered');
  }
}
}

//reset jawaban
function resetAnswer(index, questionId) {
  const container = document.querySelector(`.question-box[data-index="${index}"]`);

  // Reset pilihan ganda
  container.querySelectorAll('.option-item').forEach(option => {
    option.style.backgroundColor = "";
    option.style.fontWeight = "normal";
  });

  // Reset pilihan majemuk
  container.querySelectorAll('input[type="radio"]').forEach(input => {
    input.checked = false;
  });

  // Reset isian
  const inputText = container.querySelector('.essay-answer input');
  if (inputText) inputText.value = '';

  // **Tambah**: Clear hidden input supaya controller melihat jawaban kosong
  const hidden = document.getElementById(`answer-${questionId}`);
  if (hidden) hidden.value = '';

  markAnswered(index, false);
}

// Event listener pilihan ganda
document.addEventListener('click', function(e) {
  if (e.target.closest('.option-item')) {
    let container = e.target.closest('.question-box');
    container.querySelectorAll('.option-item').forEach(o => {
      o.style.backgroundColor = "#f1f5fe";
      o.style.fontWeight = "normal";
    });

    let selected = e.target.closest('.option-item');
    selected.style.backgroundColor = "#d2e3fc";
    selected.style.fontWeight = "bold";

    let questionId = container.querySelector('input[type="hidden"]').name.match(/answer\[(\d+)\]/)[1];

    let clone = selected.cloneNode(true);
    clone.querySelector('span')?.remove(); // hapus label A/B/C/D
//fungsi untuk submit data gambar ke datbase
    let answerValue = '';
    const img = clone.querySelector('img');
    if (img) {
      answerValue = img.outerHTML;
    } else {
      answerValue = clone.innerText.trim();
    }

    document.getElementById('answer-' + questionId).value = answerValue;

    let questionIndex = container.dataset.index;
    markAnswered(questionIndex, true);
  }
});


// Event listener pilihan majemuk
document.querySelectorAll('.question-box').forEach(container => {
const questionIndex = container.dataset.index;
const radios = container.querySelectorAll('input[type="radio"]');

radios.forEach(radio => {
  radio.addEventListener('change', () => {
    const hasChecked = Array.from(radios).some(r => r.checked);
    markAnswered(questionIndex, hasChecked);
  });
});
});

// Event listener isian
document.querySelectorAll('.essay-answer input').forEach(input => {
const container = input.closest('.question-box');
const index = container.dataset.index;

input.addEventListener('input', () => {
  if (input.value.trim() !== '') {
    markAnswered(index, true);
  } else {
    markAnswered(index, false);
  }
});
});

 // ====== TIMER ======
 let secs = parseInt(document.getElementById('timer-value').value,10),
      disp= document.getElementById('timer-display');
  function fmt(t){ let m=Math.floor(t/60), s=t%60;
    return `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;}
  disp.innerText=fmt(secs);
  let ti = setInterval(()=>{
    secs--;
    if(secs<=0){
      clearInterval(ti);
      document.getElementById('subtest-form').submit();
    } else disp.innerText=fmt(secs);
  },1000);
  // ===== END TIMER ======

$(function () {
  $(document).scroll(function () {
    var $nav = $(".header");
    $nav.toggleClass("scrolled", $(this).scrollTop() > $nav.height());
  });
});
</script>
@endpush
