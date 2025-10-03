@extends('layouts.quiz')

@section('title')
  Quiz
@endsection
@section('content')
    <!-- Header -->
      <div class="header" data-aos="fade-down" data-aos-duration="800">
        <h2>{{ $exam->title }}</h2>
    </div>
<div class="container mt-4">
  <form action="{{ route('exam-submit') }}" method="POST">
    @csrf
    <div class="row g-4">
      <!-- Panel Nomor Soal -->
      <div class="col-md-3" data-aos="fade-right" data-aos-duration="1000">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Nomor Soal</h5>
          </div>
          <div class="card-body">
            <div class="numbers-container d-flex flex-wrap justify-content-center">
              @foreach($questions as $index => $question)
                <button type="button" class="btn question-number-btn {{ $index == 0 ? 'active' : '' }}" data-index="{{ $index }}">
                  {{ $index + 1 }}
                </button>
              @endforeach
            </div>
            <div class="mt-4">
              <!-- Tombol trigger modal -->
              <button type="button" class="submit-btn btn btn-primary" data-bs-toggle="modal" data-bs-target="#submitModal" data-aos="zoom-in" data-aos-delay="300">
                Selesaikan Pengerjaan
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Konten Pertanyaan -->
      <div class="col-md-9" data-aos="fade-left" data-aos-duration="1000">
        <div class="question-container">
          <h5 class="question-number">Pertanyaan no <span id="current-question-number">1</span></h5>

          @foreach($questions as $index => $question)
        {{-- buat masukin gambar --}}
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
            <div class="question-content question-box" style="{{ $index === 0 ? '' : 'display:none;' }}" data-index="{{ $index }}">
             
@php
    $hasTable = str_contains($question->question_text, '<table');
@endphp
            <p class="option-text">@if ($hasTable)
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
                <img src="{{ asset('storage/' . $question->photo) }}" alt="Question Image" class="img-fluid">
              @endif

              @if($question->question_type === 'pilihan_ganda')
              <div class="options mt-5" data-question-id="{{ $question->id }}">
                <h6>Pilihan Jawaban (pilihan ganda)</h6>
                @php
                  $options = collect([
                    'option1' => $question->multipleChoice->option1 ?? null,
                    'option2' => $question->multipleChoice->option2 ?? null,
                    'option3' => $question->multipleChoice->option3 ?? null,
                    'option4' => $question->multipleChoice->option4 ?? null,
                    'option5' => $question->multipleChoice->option5 ?? null,
                  ])->filter()->shuffle();
                  $labels = range('A', 'E');
                @endphp

                @if($options->isNotEmpty())
                  @foreach($options as $key => $text)
                    <div class="option-item mt-2" data-option-id="{{ $key }}">
                      <span class="fw-bold">{{ array_shift($labels) }}</span> &nbsp;&nbsp; <span class="option-text">{!! $text !!}</span>
                    </div>
                  @endforeach
                  <!-- Simpan jawaban pilihan ganda dalam input tersembunyi -->
                  <input type="hidden" name="answer[{{ $question->id }}]" id="answer-{{ $question->id }}">
                @else
                  <p>Tidak ada pilihan jawaban.</p>
                @endif
              </div>

              @elseif($question->question_type === 'pilihan_majemuk')
                <div class="options mt-5">
                  <h6>Pilihan Jawaban (pilihan majemuk)</h6>
                  @if($question->multipleOption)
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
                      $options = [
                        'multiple1' => $question->multipleOption->multiple1 ?? null,
                        'multiple2' => $question->multipleOption->multiple2 ?? null,
                        'multiple3' => $question->multipleOption->multiple3 ?? null,
                        'multiple4' => $question->multipleOption->multiple4 ?? null,
                        'multiple5' => $question->multipleOption->multiple5 ?? null,
                      ];
                      $filteredOptions = array_filter($options);
                    @endphp

                    @if(count($filteredOptions))
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th>Pernyataan</th>
                            <th class="text-center">{{ $yesLabel }}</th>
                            <th class="text-center"> {{ $noLabel }}</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($filteredOptions as $key => $text)
                            <tr>
                              <td>{{ $text }}</td>
                              <td class="text-center">
                                <input type="radio" name="answer[{{ $question->id }}][{{ $key }}]" value="yes">
                              </td>
                              <td class="text-center">
                                <input type="radio" name="answer[{{ $question->id }}][{{ $key }}]" value="no">
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    @else
                      <p>Tidak ada pilihan jawaban.</p>
                    @endif
                  @else
                    <p>Tidak ada pilihan jawaban.</p>
                  @endif
                </div>

              @elseif($question->question_type === 'isian')
                <div class="essay-answer mt-5">
                  <h6>Jawaban (isian)</h6>
                  <input class="form-control w-50" type="text" name="answer[{{ $question->id }}]" placeholder="Tulis jawabanmu di sini...">
                </div>

              @else
                <p>Jenis soal belum diimplementasikan.</p>
              @endif

              <input type="hidden" name="exam_id" value="{{ $exam->id }}">

              <!-- Tombol Reset Jawaban -->
              <button
                type="button"
                class="reset-btn btn btn-secondary mt-3"
                onclick="resetAnswer({{ $index }}, {{ $question->id }})">
                Reset Jawaban
              </button>
            </div>
          @endforeach

          <!-- Navigasi Soal -->
        </div>
        <div class="navigation mt-3">
          <button type="button" class="nav-button btn" id="prev-question" disabled>
            <i class="bi bi-arrow-left"></i>
            <span class="nav-button-text">Sebelumnya</span>
          </button>
          <div class="pagination-info" id="pagination-info">
            1 / {{ count($questions) }}
          </div>
          <button type="button" class="nav-button btn" id="next-question">
            <span class="nav-button-text">Selanjutnya</span>
            <i class="bi bi-arrow-right"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Konfirmasi Submit -->
    <div class="modal fade" id="submitModal" tabindex="-1" aria-labelledby="submitModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="submitModalLabel">Konfirmasi Pengerjaan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Apakah Anda yakin ingin menyelesaikan pengerjaan?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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

$(function () {
  $(document).scroll(function () {
    var $nav = $(".header");
    $nav.toggleClass("scrolled", $(this).scrollTop() > $nav.height());
  });
});
</script>


@endpush
