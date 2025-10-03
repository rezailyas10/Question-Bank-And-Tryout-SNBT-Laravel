@extends('layouts.admin')

@section('title')
  Update Latihan Soal
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
        <h1>Update Bank Soal</h1>

        <form action="{{ route('exam.update', $exam->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title">Nama Latihan Soal</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $exam->title) }}" required>
            </div>

            <div class="form-group">
    <label for="">Mata Pelajaran</label>
   <select name="sub_category_id" class="form-control" required>
    <option value="" disabled {{ is_null($exam->sub_category_id) ? 'selected' : '' }}>
        -- Pilih Mata Pelajaran --
    </option>
    @foreach ($subcategories as $sub_category)
        <option value="{{ $sub_category->id }}" 
            {{ $sub_category->id == $exam->sub_category_id ? 'selected' : '' }}>
            {{ $sub_category->name }}
        </option>
    @endforeach
</select>
</div>

            <div class="form-group">
                <label for="is_published">Published</label>
                <select name="is_published" id="is_published" class="form-control">
                    <option value="0" {{ old('is_published', $exam->is_published) == 0 ? 'selected' : '' }}>Tidak</option>
                    <option value="1" {{ old('is_published', $exam->is_published) == 1 ? 'selected' : '' }}>Ya</option>
                </select>
            </div>

            <!-- Data user otomatis, diset melalui input hidden -->
            <input type="hidden" name="created_by" value="{{ auth()->user()->name }}">
            <div class="form-group">
                <label for="user_name">Nama User</label>
                <input type="text" id="user_name" class="form-control" value="{{ auth()->user()->name }}" disabled>
            </div>

            <button type="submit" class="btn btn-success">Update</button>
        </form>
    </div>
</div>
@endsection

@push('addon-script')
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

    ClassicEditor
        .create(document.querySelector('#description'), {
            extraPlugins: [MyCustomUploadAdapterPlugin],
        })
        .then(editor => {
            console.log('CKEditor is ready');

            // Simpan gambar saat pertama kali
            previousImages = getImageSrcs(editor.getData());

            editor.model.document.on('change:data', () => {
                const currentImages = getImageSrcs(editor.getData());
                const deletedImages = previousImages.filter(src => !currentImages.includes(src));

                deletedImages.forEach(src => {
                    fetch('{{ route("ckeditor.delete") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ src })
                    }).then(res => res.json())
                      .then(data => console.log('Deleted:', data))
                      .catch(err => console.error('Delete failed', err));
                });

                previousImages = currentImages;
            });
        })
        .catch(error => {
            console.error('CKEditor init error:', error);
        });
</script>

<script>
    // const typeEl   = document.getElementById('exam_type');
    // const pracSec  = document.getElementById('practice-section');
    // const tryoutSec= document.getElementById('tryout-section');
  
    // function toggleSections() {
    //   if (typeEl.value === 'tryout') {
    //     pracSec.style.display   = 'none';
    //     tryoutSec.style.display = 'block';
    //   } else {
    //     pracSec.style.display   = 'block';
    //     tryoutSec.style.display = 'none';
    //   }
    // }
  
    document.getElementById('add-row').addEventListener('click', () => {
  const list = document.getElementById('subtest-list');
  const item = list.querySelector('.subtest-item').cloneNode(true);
  // reset values
  item.querySelector('select').value = '';
  item.querySelector('input').value = '';
  list.appendChild(item);
  attachRemove(item);
});

// attach to ALL initial rows, not just the first one
document.querySelectorAll('.subtest-item').forEach(item => {
  attachRemove(item);
});
  </script>

@endpush
