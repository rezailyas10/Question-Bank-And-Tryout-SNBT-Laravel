@php
    $layout = auth()->user()->roles == 'ADMIN' ? 'layouts.admin' : 'layouts.kontributor';
@endphp

@extends($layout)

@section('title')
    Buat Pertanyaan
@endsection

@section('content')
<div class="container mt-5">
     <!-- Notifikasi sukses -->
     @if(session('success'))
     <div class="alert alert-success">
         {{ session('success') }}
     </div>
 @endif
    <h1>Buat Pertanyaan Baru</h1>
    @if($errors->any())
       <div class="alert alert-danger">
           <ul>
             @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
             @endforeach
           </ul>
       </div>
    @endif

    

    <!-- exam_id dikirim melalui query string misalnya ?exam_id= -->
    <form action="{{ route('question.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <!-- Hidden exam_id -->
        <input type="hidden" name="exam_id" value="{{ request()->get('exam_id') }}">

        <div class="form-group">
            <label for="text">Pertanyaan</label>
             <textarea name="question_text" id="question_text" rows="3" class="form-control tiny-editor" >{{ old('question_text') }}</textarea>
        </div>


        <div class="form-group">
            <label for="question_type">Tipe Soal</label>
            <select name="question_type" id="question_type" class="form-control" onchange="changeQuestionType()" required>
                <option value="">-- Pilih Tipe Soal --</option>
                <option value="pilihan_ganda" {{ old('question_type') == 'pilihan_ganda' ? 'selected' : '' }}>Pilihan Ganda</option>
                <option value="pilihan_majemuk" {{ old('question_type') == 'pilihan_majemuk' ? 'selected' : '' }}>Pilihan Majemuk</option>
                <option value="isian" {{ old('question_type') == 'isian' ? 'selected' : '' }}>Isian</option>
            </select>
        </div>

        <div class="form-group">
            <label for="lesson">Materi atau Subbab Topik</label>
            <input type="text" name="lesson" id="lesson" class="form-control" value="{{ old('lesson') }}" required>
        </div>

        <div class="form-group">
            <label for="sub_category_id">Mata Pelajaran</label>
            <select name="sub_category_id" id="sub_category_id" class="form-control" required>
                <option value="">-- Pilih Mata Pelajaran --</option>
                @foreach($sub_categories as $subCat)
                    <option value="{{ $subCat->id }}" {{ old('sub_category_id') == $subCat->id ? 'selected' : '' }}>
                        {{ $subCat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <!-- Section untuk Pilihan Ganda -->
        <div id="section-pilihan_ganda" style="display: none;">
            <h4>Pilihan Ganda</h4>
        
            <div class="form-group">
                <label for="option1">Opsi A</label>
                <textarea name="option1" id="option1" class="form-control tiny-editor" value="{{ old('option1') }}"></textarea>
                <div class="form-check">
                    <input type="radio" name="correct_answer" class="form-check-input" id="correct_option1" value="option1">
                    <label class="form-check-label" for="correct_option1">Benar</label>
                </div>
            </div>
            <div class="form-group">
                <label for="option2">Opsi B</label>
                <textarea name="option2" id="option2" class="form-control tiny-editor" value="{{ old('option2') }}"></textarea>
                <div class="form-check">
                    <input type="radio" name="correct_answer" class="form-check-input" id="correct_option2" value="option2">
                    <label class="form-check-label" for="correct_option2">Benar</label>
                </div>
            </div>
            <div class="form-group">
                <label for="option3">Opsi C</label>
                <textarea name="option3" id="option3" class="form-control tiny-editor" value="{{ old('option3') }}"></textarea>
                <div class="form-check">
                    <input type="radio" name="correct_answer" class="form-check-input" id="correct_option3" value="option3">
                    <label class="form-check-label" for="correct_option3">Benar</label>
                </div>
            </div>
            <div class="form-group">
                <label for="option4">Opsi D</label>
                <textarea name="option4" id="option4" class="form-control tiny-editor" value="{{ old('option4') }}"></textarea>
                <div class="form-check">
                    <input type="radio" name="correct_answer" class="form-check-input" id="correct_option4" value="option4">
                    <label class="form-check-label" for="correct_option4">Benar</label>
                </div>
            </div>
            <div class="form-group">
                <label for="option5">Opsi E</label>
                <textarea name="option5" id="option5" class="form-control tiny-editor" value="{{ old('option5') }}"></textarea>
                <div class="form-check">
                    <input type="radio" name="correct_answer" class="form-check-input" id="correct_option5" value="option5">
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
        <input type="text" name="multiple1" id="multiple1" class="form-control" value="{{ old('multiple1') }}">
        <div class="form-check">
            <input type="radio" name="yes/no1" id="yes_no1_yes" value="yes" class="form-check-input" {{ old('yes/no1')=='yes' ? 'checked' : '' }}>
            <label class="form-check-label" for="yes_no1_yes">Yes</label>
        </div>
        <div class="form-check">
            <input type="radio" name="yes/no1" id="yes_no1_no" value="no" class="form-check-input" {{ old('yes/no1')=='no' ? 'checked' : '' }}>
            <label class="form-check-label" for="yes_no1_no">No</label>
        </div>
    </div>
    <!-- Opsi B -->
    <div class="form-group">
        <label for="multiple2">Pernyataan 2</label>
        <input type="text" name="multiple2" id="multiple2" class="form-control" value="{{ old('multiple2') }}">
        <div class="form-check">
            <input type="radio" name="yes/no2" id="yes_no2_yes" value="yes" class="form-check-input" {{ old('yes/no2')=='yes' ? 'checked' : '' }}>
            <label class="form-check-label" for="yes_no2_yes">Yes</label>
        </div>
        <div class="form-check">
            <input type="radio" name="yes/no2" id="yes_no2_no" value="no" class="form-check-input" {{ old('yes/no2')=='no' ? 'checked' : '' }}>
            <label class="form-check-label" for="yes_no2_no">No</label>
        </div>
    </div>
    <!-- Opsi C -->
    <div class="form-group">
        <label for="multiple3">Pernyataan 3</label>
        <input type="text" name="multiple3" id="multiple3" class="form-control" value="{{ old('multiple3') }}">
        <div class="form-check">
            <input type="radio" name="yes/no3" id="yes_no3_yes" value="yes" class="form-check-input" {{ old('yes/no3')=='yes' ? 'checked' : '' }}>
            <label class="form-check-label" for="yes_no3_yes">Yes</label>
        </div>
        <div class="form-check">
            <input type="radio" name="yes/no3" id="yes_no3_no" value="no" class="form-check-input" {{ old('yes/no3')=='no' ? 'checked' : '' }}>
            <label class="form-check-label" for="yes_no3_no">No</label>
        </div>
    </div>
    <!-- Opsi D -->
    <div class="form-group">
        <label for="multiple4">Pernyataan 4</label>
        <input type="text" name="multiple4" id="multiple4" class="form-control" value="{{ old('multiple4') }}">
        <div class="form-check">
            <input type="radio" name="yes/no4" id="yes_no4_yes" value="yes" class="form-check-input" {{ old('yes/no4')=='yes' ? 'checked' : '' }}>
            <label class="form-check-label" for="yes_no4_yes">Yes</label>
        </div>
        <div class="form-check">
            <input type="radio" name="yes/no4" id="yes_no4_no" value="no" class="form-check-input" {{ old('yes/no4')=='no' ? 'checked' : '' }}>
            <label class="form-check-label" for="yes_no4_no">No</label>
        </div>
    </div>
    <!-- Opsi E -->
    <div class="form-group">
        <label for="multiple5">Pernyataan 5</label>
        <input type="text" name="multiple5" id="multiple5" class="form-control" value="{{ old('multiple5') }}">
        <div class="form-check">
            <input type="radio" name="yes/no5" id="yes_no5_yes" value="yes" class="form-check-input" {{ old('yes/no5')=='yes' ? 'checked' : '' }}>
            <label class="form-check-label" for="yes_no5_yes">Yes</label>
        </div>
        <div class="form-check">
            <input type="radio" name="yes/no5" id="yes_no5_no" value="no" class="form-check-input" {{ old('yes/no5')=='no' ? 'checked' : '' }}>
            <label class="form-check-label" for="yes_no5_no">No</label>
        </div>
    </div>
</div>


        <!-- Section untuk Soal Isian (Essay) -->
        <div id="section-isian" style="display: none;">
            <h4>Soal Isian</h4>
            <div class="form-group">
                <label for="essay_text">Jawaban Benar</label>
                <input type="text" name="text" id="essay_text" class="form-control" value="{{ old('text') }}">
            </div>
        </div>

         <div class="form-group">
            <label for="explanation">Penjelasan</label>
            <textarea name="explanation" id="explanation" class="form-control tiny-editor">{{ old('explanation') }}</textarea>
        </div>


        {{-- <div class="form-group">
    <label for="difficulty">Tingkat Kesulitan</label>
    <select name="difficulty" id="difficulty" class="form-control" required>
        <option value="">-- Pilih Kesulitan --</option>
        <option value="Mudah"  {{ old('difficulty')=='Mudah'  ? 'selected':'' }}>Mudah</option>
        <option value="Sedang" {{ old('difficulty')=='Sedang' ? 'selected':'' }}>Sedang</option>
        <option value="Sulit"  {{ old('difficulty')=='Sulit'  ? 'selected':'' }}>Sulit</option>
    </select>
    @error('difficulty')
      <small class="text-danger">{{ $message }}</small>
    @enderror
</div> --}}





       
</div>

        <button type="submit" class="btn btn-success">Simpan Pertanyaan</button>
    </form>
</div>
@endsection

@push('addon-script')
{{-- <script src="https://cdn.tiny.cloud/1/p5gi92z4l60ecwn8741x16fryd8qwwwadeojxxh3t1s1uh40/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '.tiny-editor',
        height: 300,
        plugins: 'image code mathjax',
        toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | image | code | mathjax',
        images_upload_url: '/upload-image', // endpoint laravel kamu
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
                    cb(result.location); // URL gambar dari server
                })
                .catch(error => {
                    console.error('Upload error:', error);
                });
            };

            input.click();
        },
        mathjax: {
            lib: 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js',
            symbols: { start: '\\(', end: '\\)' }
        },
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
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
