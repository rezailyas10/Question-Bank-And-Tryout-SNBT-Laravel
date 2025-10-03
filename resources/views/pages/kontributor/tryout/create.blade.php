@extends('layouts.kontributor')

@section('title')
  Buat Bank Soal Baru
@endsection

@section('content')
<div class="col-md-12">
   <!-- Notifikasi sukses -->
     @if(session('success'))
     <div class="alert alert-success">
         {{ session('success') }}
     </div>
 @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="container">
        <h1>Buat Tryout Baru</h1>

        <form action="{{ route('tryout.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="title">Nama Tryout</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea name="description" id="description" rows="3" class="form-control" >{{ old('description') }}</textarea>
            </div>
            <input type="hidden" name="exam_type" id="exam_type" value="tryout">
                         
    
                            {{-- tanggal dibuka/ditutup masuk exam --}}
                            <div class="form-group">
                                <label for="tanggal_dibuka">Tanggal Dibuka</label>
                                <input type="date"
                                    name="tanggal_dibuka"
                                    id="tanggal_dibuka"
                                    class="form-control"
                                    value="{{ old('tanggal_dibuka', $exam->tanggal_dibuka ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="tanggal_ditutup">Tanggal Ditutup</label>
                                <input type="date"
                                    name="tanggal_ditutup"
                                    id="tanggal_ditutup"
                                    class="form-control"
                                    value="{{ old('tanggal_ditutup', $exam->tanggal_ditutup ?? '') }}">
                            </div>
                      

            <div class="form-group">
                <label for="is_published">Published</label>
                <select name="is_published" id="is_published" class="form-control">
                    <option value="0" {{ old('is_published') == 0 ? 'selected' : '' }}>Tidak</option>
                    <option value="1" {{ old('is_published') == 1 ? 'selected' : '' }}>Ya</option>
                </select>
            </div>

            <!-- Data user secara otomatis berdasarkan user yang login -->
            <input type="hidden" name="created_by" value="{{ auth()->user()->id }}">
            <div class="form-group">
                <label for="user_name">Nama User</label>
                <input type="text" id="user_name" class="form-control" value="{{ auth()->user()->name }}" disabled>
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
        </form>
    </div>
</div>
@endsection

@push('addon-script')
<script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create( document.querySelector( '#description' ) )
        .then( editor => {
            console.log( editor );
        } )
        .catch( error => {
            console.error( error );
        } );
</script>

<script>
    const typeEl = document.getElementById('exam_type');
    const tryoutSec = document.getElementById('tryout-section');
 
    // function toggleSections() {
    //   if (typeEl.value === 'tryout') {
    //     // Sembunyikan field mata pelajaran untuk tryout
    //     subCategoryField.style.display = 'none';
    //     tryoutSec.style.display = 'block';
    //   } else {
    //     // Tampilkan field mata pelajaran untuk latihan soal
    //     subCategoryField.style.display = 'block';
    //     tryoutSec.style.display = 'none';
    //   }
    // }
  
    // // initial & on-change
    // typeEl.addEventListener('change', toggleSections);
    // toggleSections();
  
    // repeater subtest
    document.getElementById('add-row').addEventListener('click', () => {
      const list = document.getElementById('subtest-list');
      const item = list.querySelector('.subtest-item').cloneNode(true);
      // reset values
      item.querySelector('select').value = '';
      item.querySelector('input').value = '';
      list.appendChild(item);
      attachRemove(item);
    });
  
    function attachRemove(row) {
      row.querySelector('.remove-row').addEventListener('click', () => {
        const all = document.querySelectorAll('.subtest-item');
        if (all.length > 1) row.remove();
      });
    }
    // attach to initial row
    attachRemove(document.querySelector('.subtest-item'));
</script>
@endpush
