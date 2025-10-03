@extends('layouts.auth')

@section('title')
    Update Profile
@endsection
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
  <div class="page-content page-auth mt-5" id="profile">
    <div class="section-store-auth" data-aos="fade-up">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-6">
            <h2>
              Update Profil Anda Sebelum Mengakses Apliksai
            </h2>

            <form method="POST" action="{{ route('profile.update', ['redirect' => 'profile.edit']) }}" class="mt-4">
              @csrf
              @if (auth()->user()->google_id != null)
              <div class="form-group position-relative mt-3">
                <label>Password</label>
                <input id="password" type="password"
                      class="form-control pe-5 @error('password') is-invalid @enderror"
                      name="password" required autocomplete="new-password">
                <span class="fa fa-fw fa-eye toggle-password" data-target="password"></span>
                <small id="newPasswordFeedback" class="form-text"></small>
              </div>

              <div class="form-group position-relative mt-3">
                <label>Confirm Password</label>
                <input id="password-confirm" type="password"
                      class="form-control pe-5 @error('password_confirmation') is-invalid @enderror"
                      name="password_confirmation" required autocomplete="new-password">
                <span class="fa fa-fw fa-eye toggle-password" data-target="password-confirm"></span>
                <small id="confirmPasswordFeedback" class="form-text"></small>
              </div>
            @endif

              {{-- Username --}}
              <div class="form-group">
                <label for="username">Username</label>
                <input
                  id="username"
                  name="username"
                  v-model="username"
                  @change="checkUsername"
                  type="text"
                  class="form-control @error('username') is-invalid @enderror"
                  :class="{'is-invalid': username_unavailable}"
                  value="{{ old('username', auth()->user()->username) }}"
                  required
                >
                @error('username')
                  <span class="invalid-feedback">{{ $message }}</span>
                @enderror
              </div>

              {{-- Phone Number --}}
              <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input
                  id="phone_number"
                  name="phone_number"
                  type="text"
                  class="form-control @error('phone_number') is-invalid @enderror"
                  value="{{ old('phone_number', auth()->user()->phone_number) }}"
                >
                @error('phone_number')
                  <span class="invalid-feedback">{{ $message }}</span>
                @enderror
              </div>

              {{-- Jenjang Pendidikan --}}
              <div class="form-group">
                <label for="jenjang">Jenjang Pendidikan</label>
                <select
                  id="jenjang"
                  name="jenjang"
                  class="form-control @error('jenjang') is-invalid @enderror"
                >
                  @php
                    $levels = ['SD','SMP','SMA','Kuliah'];
                    $current = old('jenjang', auth()->user()->jenjang);
                  @endphp
                  <option value="">— Pilih Jenjang —</option>
                  @foreach($levels as $lvl)
                    <option value="{{ $lvl }}" {{ $current === $lvl ? 'selected' : '' }}>
                      {{ $lvl }}
                    </option>
                  @endforeach
                </select>
                @error('jenjang')
                  <span class="invalid-feedback">{{ $message }}</span>
                @enderror
              </div>

              {{-- Kelas --}}
              <div class="form-group">
                <label for="kelas">Kelas</label>
                <select
                  id="kelas"
                  name="kelas"
                  class="form-control @error('kelas') is-invalid @enderror"
                >
                  @php
                    $classes = ['12','11','10','9','8','7'];
                    $currentK = old('kelas', auth()->user()->kelas);
                  @endphp
                  <option value="">— Pilih Kelas —</option>
                  @foreach($classes as $cls)
                    <option value="{{ $cls }}" {{ $currentK === $cls ? 'selected' : '' }}>
                      {{ $cls }}
                    </option>
                  @endforeach
                </select>
                @error('kelas')
                  <span class="invalid-feedback">{{ $message }}</span>
                @enderror
              </div>

              {{-- Nama Sekolah --}}
              <div class="form-group">
                <label for="sekolah">Nama Sekolah</label>
                <input
                  id="sekolah"
                  name="sekolah"
                  type="text"
                  class="form-control @error('sekolah') is-invalid @enderror"
                  value="{{ old('sekolah', auth()->user()->sekolah) }}"
                >
                @error('sekolah')
                  <span class="invalid-feedback">{{ $message }}</span>
                @enderror
              </div>

              {{-- Instagram --}}
              <div class="form-group">
                <label for="instagram">Instagram (opsional)</label>
                <input
                  id="instagram"
                  name="instagram"
                  type="text"
                  class="form-control @error('instagram') is-invalid @enderror"
                  value="{{ old('instagram', auth()->user()->instagram) }}"
                  placeholder="username tanpa @"
                >
                @error('instagram')
                  <span class="invalid-feedback">{{ $message }}</span>
                @enderror
              </div>

              {{-- Facebook --}}
              <div class="form-group">
                <label for="facebook">Facebook (opsional)</label>
                <input
                  id="facebook"
                  name="facebook"
                  type="text"
                  class="form-control @error('facebook') is-invalid @enderror"
                  value="{{ old('facebook', auth()->user()->facebook) }}"
                  placeholder="username atau URL profil"
                >
                @error('facebook')
                  <span class="invalid-feedback">{{ $message }}</span>
                @enderror
              </div>

              {{-- Twitter --}}
              <div class="form-group">
                <label for="twitter">Twitter (opsional)</label>
                <input
                  id="twitter"
                  name="twitter"
                  type="text"
                  class="form-control @error('twitter') is-invalid @enderror"
                  value="{{ old('twitter', auth()->user()->twitter) }}"
                  placeholder="username tanpa @"
                >
                @error('twitter')
                  <span class="invalid-feedback">{{ $message }}</span>
                @enderror
              </div>

              <button type="submit" :disabled="username_unavailable" class="btn btn-block mt-4 text-light" style="background-color: #1A4F80;">
                Simpan Perubahan
              </button>
              <a href="{{ route('home') }}" class="btn btn-secondary btn-block mt-2">
                Batal
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

  var profile = new Vue({
    el: "#profile",
    mounted() {
      AOS.init();
    },
    data() {
      return {
        username: "{{ auth()->user()->username }}",
        username_unavailable: false,
      }
    },
    methods: {
      checkUsername() {
        var self = this;
        axios.get('{{ route('api-username-check') }}', {
          params: { username: this.username }
        })
        .then(function (response) {
          if (response.data == 'Available') {
            self.$toasted.show("Username tersedia", { position: "top-center", className: "rounded", duration: 1000 });
            self.username_unavailable = false;
          } else {
            self.$toasted.error("Maaf, username sudah dipakai.", { position: "top-center", className: "rounded", duration: 1000 });
            self.username_unavailable = true;
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
