@extends('layouts.admin')

@section('title')
  blog
@endsection

@section('content')
<div
class="section-content section-dashboard-home"
data-aos="fade-up"
>
<div class="container-fluid">
  <div class="dashboard-heading">
    <h2 class="dashboard-title">blog</h2>
    <p class="dashboard-subtitle">
      Create New blog
    </p>
  </div>
  <div class="dashboard-content">
   <div class="row">
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
        <div class="card">
            <div class="card-body">
                <form action="{{ route('blog.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Judul Blog</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Cover</label>
                                <input type="file" name="cover" class="form-control" required>
                            </div>
                        </div>
                    <div class="col-md-12">
                                        <div class="form-group">
                                    <label for="description">Konten</label>
                                    <textarea name="content" id="content" rows="3" class="form-control" >{{ old('content') }}</textarea>
                                </div>
                        </div>
                          <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Kategori</label>
                                <select name="kategori" class="form-control"  required>
                                    <option value="panduan">Panduan</option>
                                    <option value="artikel">Artikel</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col text-right">
                            <button type="submit" class="btn btn-success px-5">Save Now</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

   </div>
</div>
</div>

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
    const editorIds = ['content'];

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

@endsection

