@extends('layouts.kontributor')

@section('title')
  Buat Latihan Soal Baru
@endsection

@section('content')
<div class="col-md-12">
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
        <h1>Buat Bank Soal Baru</h1>

        <form action="{{ route('exam.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="title">Nama Latihan Soal</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
            </div>

                           <div id="tryout-section" >
                            <label class="d-block">mata Pelajaran</label>
                            <div id="subtest-list">
                                <div class="d-flex mb-2 subtest-item">
                                    <select name="sub_category_id" class="form-control mr-2">
                                        <option value="">-- Mata Pelajaran --</option>
                                        @foreach($sub_categories as $sub)
                                            <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
    
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

@endpush
