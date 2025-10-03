@extends('layouts.admin')

@section('title')
  blog
@endsection

@section('content')
<div class="section-content section-dashboard-home" data-aos="fade-up">
  <div class="container-fluid">
    <div class="dashboard-heading">
      <h2 class="dashboard-title">blog</h2>
      <p class="dashboard-subtitle">Edit blog</p>
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
              <form action="{{ route('blog.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="title">Judul Blog</label>
                      <input type="text" id="title" name="title" class="form-control" value="{{ old('title', $item->title) }}" required>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="cover">Cover</label>
                      <div class="d-flex align-items-center">
                        @if($item->cover)
                          <img src="{{ Storage::url($item->cover) }}" alt="Current cover" class="img-thumbnail" style="max-width: 100px; margin-right: 10px; border:none">
                        @endif
                        <input type="file" id="cover" name="cover" class="form-control">
                      </div>
                    </div>
                  </div>

                  {{-- FixImageUrls di Blade: pastikan pakai $item --}}
                  @php
                    if (!function_exists('fixImageUrls')) {
                        function fixImageUrls($html) {
                            if (!$html) return '';
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
                    }
                    $contentFixed = old('content', isset($item->content) ? fixImageUrls($item->content) : '');
                  @endphp

                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="content">Konten</label>
                      {{-- Debug: <pre>{{ $contentFixed }}</pre> --}}
                      <textarea name="content" id="content" class="form-control tiny-editor">{!! $contentFixed !!}</textarea>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="category">Kategori</label>
                      <select name="category" id="category" class="form-control" required>
                        <option value="{{ old('category', $item->category) }}">Tidak diganti ({{ $item->category }})</option>
                        <option value="panduan">Panduan</option>
                        <option value="artikel">Artikel</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row mt-3">
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

  {{-- Load CKEditor setelah DOM siap --}}
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
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: data
                })
                .then(response => response.json())
                .then(result => {
                    resolve({ default: result.url });
                })
                .catch(error => reject('Upload failed'));
            }));
      }
      abort() {}
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
    document.addEventListener('DOMContentLoaded', function () {
      const editorIds = ['content'];
      const editors = {};
      editorIds.forEach(id => {
        console.log('Trying to get editor with ID:', id);
        const textarea = document.getElementById(id);
        if (!textarea) {
          console.warn(`Element with ID #${id} not found.`);
          return;
        }
        ClassicEditor
          .create(textarea, { extraPlugins: [MyCustomUploadAdapterPlugin] })
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
          .catch(error => console.error(`CKEditor init error on #${id}:`, error));
      });
    });
  </script>
</div>
@endsection
