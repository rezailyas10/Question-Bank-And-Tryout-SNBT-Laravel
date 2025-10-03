@extends('layouts.auth')

@section('title')
    Register
@endsection
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
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
</style>
@section('content')

  <!-- Page Content -->
  <div class="page-content page-auth mt-5" id="register">
    <div class="section-store-auth" data-aos="fade-up">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-4">
            <h2>
              Daftar Sekarang & <br />
              Mulai Persiapan SNBT-mu!
            </h2>
            <form method="POST" class="mt-3" action="{{ route('register') }}">
              @csrf

              <div class="form-group">
                <label>Full Name</label>
                <input id="name" v-model="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
              </div>
              <div class="form-group">
                <label>Email</label>
                <input id="email" type="email" v-model="email" @change="checkEmail" class="form-control @error('email') is-invalid @enderror" :class="{'is-invalid': email_unavailable}" name="email" value="{{ old('email') }}" required autocomplete="email">
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
              </div>
              <div class="form-group position-relative mt-3">
                <label>Password</label>
                <input id="password" type="password"
                       class="form-control pe-5 @error('password') is-invalid @enderror"
                       name="password" required autocomplete="new-password">
                <span class="fa fa-fw fa-eye toggle-password" data-target="password"></span>
                <small id="newPasswordFeedback" class="form-text"></small>
                @error('password')
                  <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>

              <div class="form-group position-relative mt-3">
                <label>Confirm Password</label>
                <input id="password-confirm" type="password"
                       class="form-control pe-5 @error('password_confirmation') is-invalid @enderror"
                       name="password_confirmation" required autocomplete="new-password">
                <span class="fa fa-fw fa-eye toggle-password" data-target="password-confirm"></span>
                <small id="confirmPasswordFeedback" class="form-text"></small>
                @error('password_confirmation')
                  <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>

              
              <button type="submit" :disabled="email_unavailable" class="btn btn-block mt-4 text-light" style="background-color: #1A4F80;">
                Daftar Sekarang
              </button>
               {{-- <a href="{{ route('redirectToGoogle') }}" 
                    class="btn btn-light w-100 mt-2 d-flex align-items-center justify-content-center border"
                    style="gap: 8px;"> --}}
                        {{-- Ikon Google full color (SVG) --}}
                      {{-- <svg width="20" height="20" viewBox="0 0 533.5 544.3">
                        <path fill="#4285f4" d="M533.5 278.4c0-17.4-1.6-34.1-4.7-50.4H272v95.4h146.9c-6.3 33.9-25.5 62.6-54.4 81.7l87.7 68.1c51.3-47.3 81.3-117 81.3-194.8z"/>
                        <path fill="#34a853" d="M272 544.3c73.8 0 135.7-24.5 180.9-66.5l-87.7-68.1c-24.4 16.3-55.5 25.8-93.2 25.8-71.5 0-132-48.2-153.7-112.8l-89.7 69.3c42.7 84.3 130.5 142.3 243.4 142.3z"/>
                        <path fill="#fbbc04" d="M118.3 322.7c-10.2-30.1-10.2-62.5 0-92.6L28.6 160.8c-30.5 59.9-30.5 130.5 0 190.4l89.7-69.3z"/>
                        <path fill="#ea4335" d="M272 107.7c39.9-.6 78.2 14 107.5 40.9l80.2-80.2C411.4 24.3 344.1-1.3 272 0 159.1 0 71.3 57.9 28.6 142.2l89.7 69.3c21.7-64.6 82.2-112.8 153.7-112.8z"/>
                      </svg>
                        Daftar Dengan Google
                    </a> --}}
              <a href="{{ route('login', ['redirect_to' => url()->current()]) }}" class="btn btn-signup btn-block mt-2">
                Kembali ke Login
              </a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@push('addon-script')
<script src="/vendor/vue/vue.js"></script>
<script src="https://unpkg.com/vue-toasted"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
  Vue.use(Toasted);

  var register = new Vue({
    el: "#register",
    mounted() {
      AOS.init();
    },
    data() {
      return {
        name: "",
        username: "",
        email: "",
        password: "",
        email_unavailable: false,
      }
    },
    methods: {
      checkEmail() {
        var self = this;
        axios.get('{{ route('api-register-check') }}', {
          params: {
            email: this.email
          }
        })
        .then(function (response) {
          if (response.data == 'Unavailable') {
            self.$toasted.show(
              "Email bisa didaftar",
              {
                position: "top-center",
                className: "rounded",
                duration: 1000,
              }
            );
            self.email_unavailable = false;
          } else {
            self.$toasted.error(
              "Maaf, tampaknya email sudah terdaftar pada sistem kami.",
              {
                position: "top-center",
                className: "rounded",
                duration: 1000,
              }
            );
            self.email_unavailable = true;
          }
        })
        .catch(function (error) {
          console.log(error);
        });
      }
    }
  });

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
// === Password strength & confirmation validation ===
  (function(){
    const pwd        = document.getElementById('password');
    const pwdFeed    = document.getElementById('newPasswordFeedback');
    const confirm    = document.getElementById('password-confirm');
    const confFeed   = document.getElementById('confirmPasswordFeedback');
    const regex      = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;

    if (pwd && pwdFeed) {
      pwd.addEventListener('input', function() {
        if (this.value.length === 0) {
          pwdFeed.innerText = "Password minimal 8 karakter, huruf & angka.";
          pwdFeed.style.color = "gray";
        } else if (!regex.test(this.value)) {
          pwdFeed.innerText = "Password harus 8+ karakter dengan huruf & angka.";
          pwdFeed.style.color = "red";
        } else {
          pwdFeed.innerText = "Password kuat!";
          pwdFeed.style.color = "green";
        }
      });
    }

    if (confirm && confFeed) {
      confirm.addEventListener('input', function() {
        if (this.value.length === 0) {
          confFeed.innerText = "";
        } else if (pwd.value !== this.value) {
          confFeed.innerText = "Password tidak cocok!";
          confFeed.style.color = "red";
        } else {
          confFeed.innerText = "Password cocok!";
          confFeed.style.color = "green";
        }
      });
    }
  })();

</script>
@endpush
