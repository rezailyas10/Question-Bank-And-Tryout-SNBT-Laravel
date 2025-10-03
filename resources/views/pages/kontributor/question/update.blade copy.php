@extends('layouts.admin')

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

        <div class="form-group">
            <label for="text">Pertanyaan</label>
            <textarea name="question_text" id="question_text" class="form-control">{{ old('question_text', $question->question_text) }}</textarea>
        </div>

        <div class="form-group">
            <label for="photos">Foto (Opsional)</label>
            @if($question->photo)
                <div class="mb-2">
                    <img src="{{ asset('storage/'.$question->photo) }}" alt="Foto Pertanyaan" width="200">
                </div>
                <div class="form-check">
                    <!-- Checkbox untuk menghapus foto -->
                    <input type="checkbox" name="remove_photo" id="remove_photo" class="form-check-input" value="true">
                    <label class="form-check-label" for="remove_photo">Hapus Foto</label>
                </div>
            @endif
            <input type="file" name="photo" id="photos" class="form-control">
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
            <label for="explanation">Penjelasan</label>
            <textarea name="explanation" id="explanation" class="form-control">{{ old('explanation', $question->explanation) }}</textarea>
        </div>

        <div class="form-group">
            <label for="lesson">Pelajaran (Lesson)</label>
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
        <div id="section-pilihan_ganda" >
            <h4>Pilihan Ganda</h4>
            <!-- Opsi A -->
                <div class="form-group">
                    <label for="option1">Opsi A</label>
                    <input type="text" name="option1" id="option1" class="form-control" 
                        value="{{ old('option1', $question->multipleChoice->option1 ?? '') }}">
                    <div class="form-check">
                        <input type="radio" name="correct_answer" value="option1" class="form-check-input" id="correct_option1"
                            {{ old('correct_answer', $question->multipleChoice->correct_answer ?? '') == ($question->multipleChoice->option1 ?? '') ? 'checked' : '' }}>
                        <label class="form-check-label" for="correct_option1">Benar</label>
                    </div>
                </div>
    
                <!-- Opsi B -->
                <div class="form-group">
                    <label for="option2">Opsi B</label>
                    <input type="text" name="option2" id="option2" class="form-control" 
                        value="{{ old('option2', $question->multipleChoice->option2 ?? '') }}">
                    <div class="form-check">
                        <input type="radio" name="correct_answer" value="option2" class="form-check-input" id="correct_option2"
                            {{ old('correct_answer', $question->multipleChoice->correct_answer ?? '') == ($question->multipleChoice->option2 ?? '') ? 'checked' : '' }}>
                        <label class="form-check-label" for="correct_option2">Benar</label>
                    </div>
                </div>
    
                <!-- Opsi C -->
                <div class="form-group">
                    <label for="option3">Opsi C</label>
                    <input type="text" name="option3" id="option3" class="form-control" 
                        value="{{ old('option3', $question->multipleChoice->option3 ?? '') }}">
                    <div class="form-check">
                        <input type="radio" name="correct_answer" value="option3" class="form-check-input" id="correct_option3"
                            {{ old('correct_answer', $question->multipleChoice->correct_answer ?? '') == ($question->multipleChoice->option3 ?? '') ? 'checked' : '' }}>
                        <label class="form-check-label" for="correct_option3">Benar</label>
                    </div>
                </div>
    
                <!-- Opsi D -->
                <div class="form-group">
                    <label for="option4">Opsi D</label>
                    <input type="text" name="option4" id="option4" class="form-control" 
                        value="{{ old('option4', $question->multipleChoice->option4 ?? '') }}">
                    <div class="form-check">
                        <input type="radio" name="correct_answer" value="option4" class="form-check-input" id="correct_option4"
                            {{ old('correct_answer', $question->multipleChoice->correct_answer ?? '') == ($question->multipleChoice->option4 ?? '') ? 'checked' : '' }}>
                        <label class="form-check-label" for="correct_option4">Benar</label>
                    </div>
                </div>
    
                <!-- Opsi E -->
                <div class="form-group">
                    <label for="option5">Opsi E</label>
                    <input type="text" name="option5" id="option5" class="form-control" 
                        value="{{ old('option5', $question->multipleChoice->option5 ?? '') }}">
                    <div class="form-check">
                        <input type="radio" name="correct_answer" value="option5" class="form-check-input" id="correct_option5"
                            {{ old('correct_answer', $question->multipleChoice->correct_answer ?? '') == ($question->multipleChoice->option5 ?? '') ? 'checked' : '' }}>
                        <label class="form-check-label" for="correct_option5">Benar</label>
                    </div>
                </div>
            </div>
           


        <!-- Section untuk Pilihan Majemuk -->
        <div id="section-pilihan_majemuk" style="display: none;">
            <h4>Pilihan Majemuk</h4>
            <!-- Opsi A -->
            <div class="form-group">
                <label for="multiple1">Opsi A</label>
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
                <label for="multiple2">Opsi B</label>
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
                <label for="multiple3">Opsi C</label>
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
                <label for="multiple4">Opsi D</label>
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
                <label for="multiple5">Opsi E</label>
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

        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
    </form>
</div>
@endsection

@push('addon-script')
<script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create( document.querySelector( '#question_text' ) )
        .then( editor => {
            console.log( editor );
        } )
        .catch( error => {
            console.error( error );
        } );
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
