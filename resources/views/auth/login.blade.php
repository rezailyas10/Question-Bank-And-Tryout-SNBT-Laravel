@extends('layouts.auth')

@section('title')
    Login
@endsection
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> 
 <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<style>
    img {
        border-top-left-radius: 0;
    border-bottom-left-radius: 0;
    border-top-right-radius: 24px;
    border-bottom-right-radius: 24px;
    }

   .toggle-password {
    position: absolute;
    top: 50px;
    right: 15px; /* Geser sedikit ke kiri agar benar-benar di dalam input */
    transform: translateY(-50%);
    cursor: pointer;
    z-index: 2;
    color: #6c757d;
    font-size: 1rem;
}

@media (max-width: 760px) {
  /* Sembunyikan gambar */
  .row-login .col-lg-6 {
    display: none !important;
  }

  /* Center form */
  .row-login .col-lg-5 {
    margin: 0 auto;
  }

  /* Buat input, form-group, dan button full width */
  .row-login .col-lg-5 .form-group,
  .row-login .col-lg-5 .form-control,
  .row-login .col-lg-5 .btn {
    width: 100% !important;
  }

  /* Tambahkan padding kanan di input supaya ikon mata gak niban teks */
  .row-login .col-lg-5 .form-control.pe-5 {
    padding-right: 2.5rem !important;
  }

}

</style>
@section('content')
    <!-- Page Content -->
    <div class="page-content page-auth">
        <div class="section-store-auth" data-aos="fade-up">
          <div class="container">
            <div class="row align-items-center row-login">
              <div class="col-lg-6 text-center">
                <img
                  src="/images/login.png"
                  alt=""
                  class="w-50 mb-4 mb-lg-none"
                />
              </div>
              <div class="col-lg-5">
                <h2>
                 Belajar Lebih Mudah,  <br />
                  Mulai dari Login
                </h2>
                <form method="POST" action="{{ route('login', ['redirect_to' => url()->current()]) }}" class="mt-3">
                  @csrf
                  <div class="form-group">
                    <label>Email address</label>
                    <input id="email" type="email" class="form-control w-75 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                  </div>
                  <div class="form-group w-75 position-relative">
                    <label>Password</label>
                    <input id="password" type="password"
                            class="form-control pe-5 @error('password') is-invalid @enderror"
                            name="password" required autocomplete="current-password">
                    <span class="fa fa-fw fa-eye toggle-password" data-target="password"></span>
                    @error('password')
                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                     <div class="mt-2">
                        <a href="{{ route('password.request') }}" class="small text-primary">Lupa password?</a>
                    </div>
                    </div>


                   <button
                  type="submit"
                    class="btn btn-block w-75 mt-4 text-light"
                    style="background-color: #1A4F80;"
                  >
                    Masuk
                  </button>
                  <a class="btn btn-signup w-75 mt-2" href="{{ route('register') }}">
                    Daftar
                  </a>
                  {{-- <a href="{{ route('redirectToGoogle') }}" 
                    class="btn btn-light w-75 mt-2 d-flex align-items-center justify-content-center border"
                    style="gap: 8px;"> --}}
                        {{-- Ikon Google full color (SVG) --}}
                        {{-- <svg width="20" height="20" viewBox="0 0 533.5 544.3">
                            <path fill="#4285f4" d="M533.5 278.4c0-17.4-1.6-34.1-4.7-50.4H272v95.4h146.9c-6.3 33.9-25.5 62.6-54.4 81.7l87.7 68.1c51.3-47.3 81.3-117 81.3-194.8z"/>
                            <path fill="#34a853" d="M272 544.3c73.8 0 135.7-24.5 180.9-66.5l-87.7-68.1c-24.4 16.3-55.5 25.8-93.2 25.8-71.5 0-132-48.2-153.7-112.8l-89.7 69.3c42.7 84.3 130.5 142.3 243.4 142.3z"/>
                            <path fill="#fbbc04" d="M118.3 322.7c-10.2-30.1-10.2-62.5 0-92.6L28.6 160.8c-30.5 59.9-30.5 130.5 0 190.4l89.7-69.3z"/>
                            <path fill="#ea4335" d="M272 107.7c39.9-.6 78.2 14 107.5 40.9l80.2-80.2C411.4 24.3 344.1-1.3 272 0 159.1 0 71.3 57.9 28.6 142.2l89.7 69.3c21.7-64.6 82.2-112.8 153.7-112.8z"/>
                        </svg> --}}
                        {{-- Masuk Dengan Google
                    </a> --}}
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
<div class="container" style="display: none">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login', ['redirect_to' => url()->current()]) }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                            <div class="form-group">
                    <label>Password</label>
                    <input id="password" type="password" class="form-control w-75 @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                  </div>

                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('addon-script')
<script>
    document.querySelectorAll('.toggle-password').forEach(function (element) {
        element.addEventListener('click', function () {
            let targetId = this.getAttribute('data-target');
            let input = document.getElementById(targetId);

            if (input) {
                if (input.type === "password") {
                    input.type = "text";
                    this.classList.remove("fa-eye");
                    this.classList.add("fa-eye-slash");
                } else {
                    input.type = "password";
                    this.classList.remove("fa-eye-slash");
                    this.classList.add("fa-eye");
                }
            }
        });
    });
</script>
@endpush
