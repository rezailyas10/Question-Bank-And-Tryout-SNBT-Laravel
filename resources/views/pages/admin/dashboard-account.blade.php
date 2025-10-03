@extends('layouts.admin')

@section('title')
  Account Setting
@endsection

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@section('content')
<div class="section-content section-dashboard-home" data-aos="fade-up">
  <div class="container-fluid">
    <div class="dashboard-heading">
      <h2 class="dashboard-title">My Account</h2>
      <p class="dashboard-subtitle">
        Update your current profile
      </p>
    </div>
    <div class="dashboard-content">
      <div class="row">
        <div class="col-10">
          <form action="{{ route('dashboard-settings-redirect', 'dashboard-settings-account') }}" method="POST" enctype="multipart/form-data" id="locations">
            @csrf
            <div class="card">
              <div class="card-body">
                <!-- Your Name, Username & Email -->
                <div class="row mb-2">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="name">Your Name</label>
                      <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" />
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="username">Username</label>
                      <input type="text" class="form-control" id="username" name="username" value="{{ $user->username }}" />
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="email">Your Email</label>
                      <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" disabled />
                    </div>
                  </div>
                </div>

                <!-- Mobile -->
                <div class="row mb-2">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="mobile">Mobile</label>
                      <input type="text" class="form-control" id="mobile" name="phone_number" value="{{ $user->phone_number }}" />
                    </div>
                  </div>
                </div>

                <!-- Instansi -->
                <div class="row mb-2">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="instansi">Instansi</label>
                      <input type="text" class="form-control" id="instansi" name="instansi" value="{{ old('instansi', $user->instansi) }}" />
                    </div>
                  </div>
                </div>

                <!-- Social Media -->
                <div class="row mb-2">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="instagram">Instagram (opsional)</label>
                      <input type="text" class="form-control" id="instagram" name="instagram" value="{{ old('instagram', $user->instagram) }}" placeholder="username tanpa @" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="facebook">Facebook (opsional)</label>
                      <input type="text" class="form-control" id="facebook" name="facebook" value="{{ old('facebook', $user->facebook) }}" placeholder="username atau URL profil" />
                    </div>
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="twitter">Twitter (opsional)</label>
                      <input type="text" class="form-control" id="twitter" name="twitter" value="{{ old('twitter', $user->twitter) }}" placeholder="username tanpa @" />
                    </div>
                  </div>
                </div>

                <!-- Submit Button -->
                <div class="row">
                  <div class="col text-right">
                    <button type="submit" class="btn btn-success px-5">
                      Save Now
                    </button>
                  </div>
                </div>

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

  
</script>

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
</script>
@endpush
