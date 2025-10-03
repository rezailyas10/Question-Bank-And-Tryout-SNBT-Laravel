@php
    $layout = auth()->user()->roles == 'ADMIN' ? 'layouts.admin' : 'layouts.kontributor';
@endphp

@extends($layout)

@section('title')
    Edit Pertanyaan
@endsection

@section('content')
<div class="container">
    <!-- Notifikasi sukses -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <h1>Edit Pertanyaan</h1>

    @if($errors->any())
       <div class="alert alert-danger">
           <ul>
             @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
             @endforeach
           </ul>
       </div>
    @endif

    <form action="{{ route('question.update', $question->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <!-- Hidden exam_id -->
        <input type="hidden" name="exam_id" value="{{ $question->exam_id }}">

       @php
function fixImageUrls($html) {
    return preg_replace_callback(
        '/src="([^"]+)"/i',
        function ($matches) {
            $url = $matches[1];
            if (preg_match('/^http(s)?:\/\//', $url)) {
                return 'src="' . $url . '"';
            } else {
                return 'src="' . asset($url) . '"';
            }
        },
        $html
    );
}

$questionTextFixed = old('question_text', isset($question->question_text) ? fixImageUrls($question->question_text) : '');
@endphp

<div class="form-group">
    <label for="question_text">Pertanyaan</label>
    <textarea name="question_text" id="question_text" class="form-control tiny-editor">{!! $questionTextFixed !!}</textarea>
</div>


        <div class="form-group">
            <label for="question_type">Tipe Soal</label>
            <select name="question_type" id="question_type" class="form-control" onchange="changeQuestionType()" required>
                <option value="">-- Pilih Tipe Soal --</option>
                <option value="pilihan_ganda" {{ old('question_type', $question->question_type)=='pilihan_ganda' ? 'selected' : '' }}>Pilihan Ganda</option>
                <option value="pilihan_majemuk" {{ old('question_type', $question->question_type)=='pilihan_majemuk' ? 'selected' : '' }}>Pilihan Majemuk</option>
                <option value="isian" {{ old('question_type', $question->question_type)=='isian' ? 'selected' : '' }}>Isian</option>
            </select>
        </div>


        <div class="form-group">
            <label for="lesson">Materi atau Subbab Topik</label>
            <input type="text" name="lesson" id="lesson" class="form-control" value="{{ old('lesson', $question->lesson) }}" required>
        </div>

        <div class="form-group">
            <label for="sub_category_id">Sub Category</label>
            <select name="sub_category_id" id="sub_category_id" class="form-control" required>
                <option value="">-- Pilih Sub Category --</option>
                @foreach($sub_categories as $subCat)
                    <option value="{{ $subCat->id }}" {{ old('sub_category_id', $question->sub_category_id)==$subCat->id ? 'selected' : '' }}>
                        {{ $subCat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Section untuk Pilihan Ganda -->
        @php
function fixMultipleChoice($html) {
    return preg_replace_callback(
        '/src="([^"]+)"/i',
        function ($matches) {
            $url = $matches[1];
            if (preg_match('/^http(s)?:\/\//', $url)) {
                return 'src="' . $url . '"';
            } else {
                return 'src="' . asset($url) . '"';
            }
        },
        $html
    );
}

// Proses tiap opsi agar URL gambarnya diperbaiki
$option1 = old('option1', isset($question->multipleChoice->option1) ? fixMultipleChoice($question->multipleChoice->option1) : '');
$option2 = old('option2', isset($question->multipleChoice->option2) ? fixMultipleChoice($question->multipleChoice->option2) : '');
$option3 = old('option3', isset($question->multipleChoice->option3) ? fixMultipleChoice($question->multipleChoice->option3) : '');
$option4 = old('option4', isset($question->multipleChoice->option4) ? fixMultipleChoice($question->multipleChoice->option4) : '');
$option5 = old('option5', isset($question->multipleChoice->option5) ? fixMultipleChoice($question->multipleChoice->option5) : '');

// Untuk nilai jawaban benar tetap pakai langsung dari database/old
$correctAnswer = old('correct_answer', $question->multipleChoice->correct_answer ?? '');
@endphp

<div id="section-pilihan_ganda" >
    <h4>Pilihan Ganda</h4>
    <!-- Opsi A -->
    <div class="form-group">
        <label for="option1">Opsi A</label>
        <textarea name="option1" id="option1" class="form-control tiny-editor">{!! $option1 !!}</textarea>
        <div class="form-check">
            <input type="radio" name="correct_answer" value="option1" class="form-check-input" id="correct_option1"
                {{ $correctAnswer == ($question->multipleChoice->option1 ?? '') ? 'checked' : '' }}>
            <label class="form-check-label" for="correct_option1">Benar</label>
        </div>
    </div>

    <!-- Opsi B -->
    <div class="form-group">
        <label for="option2">Opsi B</label>
        <textarea name="option2" id="option2" class="form-control tiny-editor">{!! $option2 !!}</textarea>
        <div class="form-check">
            <input type="radio" name="correct_answer" value="option2" class="form-check-input" id="correct_option2"
                {{ $correctAnswer == ($question->multipleChoice->option2 ?? '') ? 'checked' : '' }}>
            <label class="form-check-label" for="correct_option2">Benar</label>
        </div>
    </div>

    <!-- Opsi C -->
    <div class="form-group">
        <label for="option3">Opsi C</label>
        <textarea name="option3" id="option3" class="form-control tiny-editor">{!! $option3 !!}</textarea>
        <div class="form-check">
            <input type="radio" name="correct_answer" value="option3" class="form-check-input" id="correct_option3"
                {{ $correctAnswer == ($question->multipleChoice->option3 ?? '') ? 'checked' : '' }}>
            <label class="form-check-label" for="correct_option3">Benar</label>
        </div>
    </div>

    <!-- Opsi D -->
    <div class="form-group">
        <label for="option4">Opsi D</label>
        <textarea name="option4" id="option4" class="form-control tiny-editor">{!! $option4 !!}</textarea>
        <div class="form-check">
            <input type="radio" name="correct_answer" value="option4" class="form-check-input" id="correct_option4"
                {{ $correctAnswer == ($question->multipleChoice->option4 ?? '') ? 'checked' : '' }}>
            <label class="form-check-label" for="correct_option4">Benar</label>
        </div>
    </div>

    <!-- Opsi E -->
    <div class="form-group">
        <label for="option5">Opsi E</label>
        <textarea name="option5" id="option5" class="form-control tiny-editor">{!! $option5 !!}</textarea>
        <div class="form-check">
            <input type="radio" name="correct_answer" value="option5" class="form-check-input" id="correct_option5"
                {{ $correctAnswer == ($question->multipleChoice->option5 ?? '') ? 'checked' : '' }}>
            <label class="form-check-label" for="correct_option5">Benar</label>
        </div>
    </div>
</div>

           


        <!-- Section untuk Pilihan Majemuk -->
        <div id="section-pilihan_majemuk" style="display: none;">
            <h4>Pilihan Majemuk</h4>
            <!-- Opsi A -->
            <div class="form-group">
                <label for="multiple1">Pernyataan 1</label>
                <input type="text" name="multiple1" id="multiple1" class="form-control" value="{{ old('multiple1', $question->multipleOption->multiple1 ?? '') }}">
                <div class="form-check">
                    <input type="radio" name="yes/no1" id="yes_no1_yes" value="yes" class="form-check-input" 
                        {{ old('yes_no1', $question->multipleOption->{'yes/no1'} ?? '') == 'yes' ? 'checked' : '' }}>
                    <label class="form-check-label" for="yes_no1_yes">Yes</label>
                </div>
                <div class="form-check">
                    <input type="radio" name="yes/no1" id="yes_no1_no" value="no" class="form-check-input" 
                        {{ old('yes_no1', $question->multipleOption->{'yes/no1'} ?? '') == 'no' ? 'checked' : '' }}>
                    <label class="form-check-label" for="yes_no1_no">No</label>
                </div>
            </div>
            <!-- Opsi B -->
            <div class="form-group">
                <label for="multiple2">Pernyataan 2</label>
                <input type="text" name="multiple2" id="multiple2" class="form-control" value="{{ old('multiple2', $question->multipleOption->multiple2 ?? '') }}">
                <div class="form-check">
                    <input type="radio" name="yes/no2" id="yes_no2_yes" value="yes" class="form-check-input" 
                        {{ old('yes/no2', $question->multipleOption->{'yes/no2'} ?? '') == 'yes' ? 'checked' : '' }}>
                    <label class="form-check-label" for="yes_no2_yes">Yes</label>
                </div>
                <div class="form-check">
                    <input type="radio" name="yes/no2" id="yes_no2_no" value="no" class="form-check-input" 
                        {{ old('yes/no2', $question->multipleOption->{'yes/no2'} ?? '') == 'no' ? 'checked' : '' }}>
                    <label class="form-check-label" for="yes_no2_no">No</label>
                </div>
            </div>
            <!-- Opsi C -->
            <div class="form-group">
                <label for="multiple3">Pernyataan 3</label>
                <input type="text" name="multiple3" id="multiple3" class="form-control" value="{{ old('multiple3', $question->multipleOption->multiple3 ?? '') }}">
                <div class="form-check">
                    <input type="radio" name="yes/no3" id="yes_no3_yes" value="yes" class="form-check-input" 
                        {{ old('yes/no3', $question->multipleOption->{'yes/no3'} ?? '') == 'yes' ? 'checked' : '' }}>
                    <label class="form-check-label" for="yes_no3_yes">Yes</label>
                </div>
                <div class="form-check">
                    <input type="radio" name="yes/no3" id="yes_no3_no" value="no" class="form-check-input" 
                        {{ old('yes/no3', $question->multipleOption->{'yes/no3'} ?? '') == 'no' ? 'checked' : '' }}>
                    <label class="form-check-label" for="yes_no3_no">No</label>
                </div>
            </div>
            <!-- Opsi D -->
            <div class="form-group">
                <label for="multiple4">Pernyataan 4</label>
                <input type="text" name="multiple4" id="multiple4" class="form-control" value="{{ old('multiple4', $question->multipleOption->multiple4 ?? '') }}">
                <div class="form-check">
                    <input type="radio" name="yes/no4" id="yes_no4_yes" value="yes" class="form-check-input" 
                        {{ old('yes/no4', $question->multipleOption->{'yes/no4'} ?? '') == 'yes' ? 'checked' : '' }}>
                    <label class="form-check-label" for="yes_no4_yes">Yes</label>
                </div>
                <div class="form-check">
                    <input type="radio" name="yes/no4" id="yes_no4_no" value="no" class="form-check-input" 
                        {{ old('yes/no4', $question->multipleOption->{'yes/no4'} ?? '') == 'no' ? 'checked' : '' }}>
                    <label class="form-check-label" for="yes_no4_no">No</label>
                </div>
            </div>
            <!-- Opsi E -->
            <div class="form-group">
                <label for="multiple5">Pernyataan 5</label>
                <input type="text" name="multiple5" id="multiple5" class="form-control" value="{{ old('multiple5', $question->multipleOption->multiple5 ?? '') }}">
                <div class="form-check">
                    <input type="radio" name="yes/no5" id="yes_no5_yes" value="yes" class="form-check-input" 
                        {{ old('yes/no5', $question->multipleOption->{'yes/no5'} ?? '') == 'yes' ? 'checked' : '' }}>
                    <label class="form-check-label" for="yes_no5_yes">Yes</label>
                </div>
                <div class="form-check">
                    <input type="radio" name="yes/no5" id="yes_no5_no" value="no" class="form-check-input" 
                        {{ old('yes/no5', $question->multipleOption->{'yes/no5'} ?? '') == 'no' ? 'checked' : '' }}>
                    <label class="form-check-label" for="yes_no5_no">No</label>
                </div>
            </div>
        </div>

        <!-- Section untuk Soal Isian (Essay) -->
        <div id="section-isian" style="display: none;">
            <h4>Soal Isian</h4>
            <div class="form-group">
                <label for="essay_text">Jawaban Benar</label>
                <input type="text" name="text" id="essay_text" class="form-control" value="{{ old('text', $question->essay->text ?? '') }}">
            </div>
        </div>

              @php
$explanationFixed = old('explanation', isset($question->explanation) ? fixImageUrls($question->explanation) : '');
@endphp

        <div class="form-group">
    <label for="explanation">Penjelasan</label>
    <textarea name="explanation" id="explanation" class="form-control tiny-editor">{!! $explanationFixed !!}</textarea>
</div>

        {{-- <div class="form-group">
            <label for="difficulty">Tingkat Kesulitan</label>
            <select name="difficulty" id="difficulty" class="form-control" required>
                <option value="Mudah"  {{ old('difficulty', $question->difficulty)=='Mudah'  ? 'selected':'' }}>Mudah</option>
                <option value="Sedang" {{ old('difficulty', $question->difficulty)=='Sedang' ? 'selected':'' }}>Sedang</option>
                <option value="Sulit"  {{ old('difficulty', $question->difficulty)=='Sulit'  ? 'selected':'' }}>Sulit</option>
            </select>
            @error('difficulty')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div> --}}

       @php
    $user = Auth::user();
@endphp

   @if (auth()->user()->roles === 'ADMIN' || (Auth::user()->roles === 'KONTRIBUTOR' && Auth::user()->is_validator == 1))
        {{-- Hanya tampilkan bila role user bukan admin --}}
    <div class="form-group">
        <label for="status">Status Review</label>
        <select name="status" id="status" class="form-control" required>
            <option value="Ditinjau"  {{ old('status', $question->status)=='Ditinjau'  ? 'selected':'' }}>Ditinjau</option>
            <option value="Diterima"  {{ old('status', $question->status)=='Diterima'  ? 'selected':'' }}>Diterima</option>
            <option value="Ditolak"   {{ old('status', $question->status)=='Ditolak'   ? 'selected':'' }}>Ditolak</option>
        </select>
    </div>
@else
<div class="form-group d-none">
        <label for="status">Status Review</label>
        <select name="status" id="status" class="form-control" required>
            <option value="Ditinjau"  {{ old('status', $question->status)=='Ditinjau'  ? 'selected':'' }}>Ditinjau</option>
            <option value="Diterima"  {{ old('status', $question->status)=='Diterima'  ? 'selected':'' }}>Diterima</option>
            <option value="Ditolak"   {{ old('status', $question->status)=='Ditolak'   ? 'selected':'' }}>Ditolak</option>
        </select>
    </div>
   @endif
   <div class="form-group">
    <label for="note">Catatan / Komentar</label>
    <textarea name="note" id="note" class="form-control" rows="10">{{ old('note', $question->note ?? '') }}</textarea>
</div>
  
        @if ($question->exam->exam_type == 'latihan soal')
            <div class="d-flex justify-content-between align-items-center">
        <a href="{{ route('exam.show', $question->exam->slug) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Ujian
        </a>
         <button type="submit" class="btn btn-success">Simpan Perubahan</button>
    </div>
        @else
             <div class="d-flex justify-content-between align-items-center">
        <a href="{{ route('tryout.show', $question->exam->slug) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Ujian
        </a>
         <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        @endif

       
    </form>
     
</div>
@endsection

@push('addon-script')

<!-- Konfigurasi MathJax -->
<script>
    window.MathJax = {
        tex: {
            inlineMath: [['$', '$'], ['\\(', '\\)']],
            displayMath: [['$$', '$$'], ['\\[', '\\]']]
        },
        svg: { fontCache: 'global' }
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (window.MathJax) {
            MathJax.typeset(); // Render semua rumus di halaman
        }
    });
</script>

{{-- tinyMCE --}}
{{-- <script src="https://cdn.tiny.cloud/1/p5gi92z4l60ecwn8741x16fryd8qwwwadeojxxh3t1s1uh40/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
  selector: '.tiny-editor',
  height: 300,
  plugins: 'image code',
  toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | image | code',
  images_upload_url: '/upload-image',
  automatic_uploads: true,
  file_picker_types: 'image',
  file_picker_callback: function (cb, value, meta) {
    const input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', 'image/*');

    input.onchange = function () {
      const file = this.files[0];
      const formData = new FormData();
      formData.append('file', file);

      fetch('/upload-image', {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
      })
      .then(response => response.json())
      .then(result => {
        cb(result.location);
      })
      .catch(error => {
        console.error('Upload error:', error);
      });
    };

    input.click();
  },
  content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
  setup: function (editor) {
    // Simpan daftar gambar saat ini di editor
    let currentImages = [];

    // Fungsi untuk mendapatkan semua src gambar dalam editor
    function getEditorImages() {
      const imgs = editor.getBody().querySelectorAll('img');
      return Array.from(imgs).map(img => img.getAttribute('src'));
    }

    // Saat editor diinisialisasi, simpan gambar awal
    editor.on('init', function () {
      currentImages = getEditorImages();
    });

    // Pada setiap perubahan konten, cek apakah ada gambar yang dihapus
    editor.on('NodeChange Change KeyUp', function () {
      const newImages = getEditorImages();

      // Cari gambar yang ada di currentImages tapi sudah hilang di newImages
      const deletedImages = currentImages.filter(src => !newImages.includes(src));

      // Update currentImages
      currentImages = newImages;

      // Kirim permintaan hapus ke backend untuk gambar yang dihapus
      deletedImages.forEach(src => {
        let imagePath = src;
        try {
          const url = new URL(src);
          // Asumsikan URL berbentuk .../storage/...
          // kita ekstrak path relatif setelah /storage/
          if (url.pathname.startsWith('/storage/')) {
            imagePath = url.pathname.replace('/storage/', '');
          }
        } catch (e) {
          // src bukan URL, gunakan langsung
        }

        fetch('/delete-image', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
          },
          body: JSON.stringify({ image_path: imagePath }),
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            console.log('Image deleted:', imagePath);
          } else {
            console.warn('Failed to delete image:', imagePath);
          }
        })
        .catch(console.error);
      });
    });

    // Opsional: support MathJax jika kamu pakai
    editor.on('Change KeyUp', function () {
      if (window.MathJax) {
        MathJax.typesetPromise([editor.getBody()]).catch(err => {
          console.error('MathJax typeset error:', err);
        });
      }
    });
  }
});
</script> --}}

<script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>


<script>
    class MyUploadAdapter {
        constructor(loader) {
            this.loader = loader;
        }

        upload() {
            return this.loader.file
                .then(file => new Promise((resolve, reject) => {
                    const data = new FormData();
                    data.append('upload', file);

                    fetch('{{ route("ckeditor.upload") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: data
                    })
                    .then(response => response.json())
                    .then(result => {
                        resolve({
                            default: result.url
                        });
                    })
                    .catch(error => {
                        reject('Upload failed');
                    });
                }));
        }

        abort() {
            // Optional
        }
    }

    function MyCustomUploadAdapterPlugin(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
            return new MyUploadAdapter(loader);
        };
    }

     function getImageSrcs(html) {
        const doc = new DOMParser().parseFromString(html, 'text/html');
        return Array.from(doc.querySelectorAll('img')).map(img => img.getAttribute('src'));
    }

    // List ID textarea yang ingin kamu jadikan CKEditor
    const editorIds = ['question_text', 'explanation', 'option1', 'option2', 'option3', 'option4', 'option5'];

    // Menyimpan instance editor dan previousImages per textarea
    const editors = {};

    editorIds.forEach(id => {
        const textarea = document.querySelector(`#${id}`);
        if (!textarea) return; // skip kalau elemen tidak ditemukan

        ClassicEditor
            .create(textarea, {
                extraPlugins: [MyCustomUploadAdapterPlugin],
            })
            .then(editor => {
                console.log(`CKEditor ready on #${id}`);

                editors[id] = {
                    instance: editor,
                    previousImages: getImageSrcs(editor.getData())
                };

                editor.model.document.on('change:data', () => {
                    const currentImages = getImageSrcs(editor.getData());
                    const deletedImages = editors[id].previousImages.filter(src => !currentImages.includes(src));

                    deletedImages.forEach(src => {
                        fetch('{{ route("ckeditor.delete") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ src })
                        })
                        .then(res => res.json())
                        .then(data => console.log('Deleted:', data))
                        .catch(err => console.error('Delete failed', err));
                    });

                    editors[id].previousImages = currentImages;
                });
            })
            .catch(error => {
                console.error(`CKEditor init error on #${id}:`, error);
            });
    });
</script>
<script>
    function changeQuestionType() {
        let type = document.getElementById("question_type").value;
        document.getElementById("section-pilihan_ganda").style.display = type === "pilihan_ganda" ? "block" : "none";
        document.getElementById("section-pilihan_majemuk").style.display = type === "pilihan_majemuk" ? "block" : "none";
        document.getElementById("section-isian").style.display = type === "isian" ? "block" : "none";
    }

    // Panggil saat halaman dimuat untuk menampilkan opsi yang sesuai dengan pilihan sebelumnya
    window.onload = function() {
        changeQuestionType();
    };
</script>
@endpush