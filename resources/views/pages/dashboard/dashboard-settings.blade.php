@php
    switch (auth()->user()->roles) {
        case 'ADMIN':
            $layout = 'layouts.admin';
            break;
        case 'KONTRIBUTOR':
            $layout = 'layouts.kontributor';
            break;
        case 'SALES':
            $layout = 'layouts.sales';
            break;
        case 'VALIDATOR':
            $layout = 'layouts.validator';
            break;
        case 'USER':
            $layout = 'layouts.dashboard';
            break;
        default:
            $layout = 'layouts.dasboard'; // fallback layout kalau role tidak dikenali
            break;
    }
@endphp

@extends($layout)

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> 
 <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">

 <style>
  .toggle-password {
  cursor: pointer;
  position: absolute;
  right: 10px;
  top: 43px;
  z-index: 2;
    color: #6c757d;
}

</style>

@section('title')
  Change Password
@endsection

@section('content')
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
<div class="section-content section-dashboard-home" data-aos="fade-up">
  <div class="container-fluid">
    <div class="dashboard-heading">
      <h2 class="dashboard-title">Change Password</h2>
      <p class="dashboard-subtitle">Change your password</p>
    </div>
    <div class="dashboard-content">
      <div class="row">
        <div class="col-8 mx-auto">
         <form action="{{ route('dashboard-settings-update') }}" method="POST">
            @csrf
            <div class="card">
              <div class="card-body">
                <!-- Old Password -->
                  <div class="form-group position-relative">
                    <label for="oldPassword">Old Password</label>
                    <input type="password" class="form-control" id="oldPassword" name="current_password" placeholder="Enter your old password" required />
                    <span class="fa fa-fw fa-eye toggle-password" data-target="oldPassword"></span>
                    <small id="oldPasswordFeedback" class="form-text text-muted"></small>
                    @error('current_password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                  </div>

                  <!-- New Password -->
                  <div class="form-group position-relative">
                    <label for="newPassword">New Password</label>
                    <input type="password" class="form-control" id="newPassword" name="password" placeholder="Enter your new password" required />
                    <span class="fa fa-fw fa-eye toggle-password" data-target="newPassword"></span>
                    <small id="newPasswordFeedback" class="form-text text-muted">
                      Your password must be at least 8 characters long, contain letters and numbers.
                    </small>
                  </div>

                  <!-- Confirm New Password -->
                  <div class="form-group position-relative">
                    <label for="confirmPassword">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirmPassword" name="password_confirmation" placeholder="Confirm your new password" required />
                    <span class="fa fa-fw fa-eye toggle-password" data-target="confirmPassword"></span>
                    <small id="confirmPasswordFeedback" class="form-text text-muted"></small>
                  </div>

                <!-- Submit Button -->
                <div class="row">
                  <div class="col text-right">
                    <button type="submit" class="btn btn-success px-5">Save Now</button>
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

  // Validasi Password
  document.getElementById('newPassword').addEventListener('input', function () {
    let feedback = document.getElementById('newPasswordFeedback');
    let password = this.value;
    let regex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;
    
    if (password.length === 0) {
    feedback.classList.remove("text-muted");
      feedback.innerText = "Your password must be at least 8 characters long, contain letters and numbers.";
      feedback.style.color = "gray";
    } else if (!regex.test(password)) {
    feedback.classList.remove("text-muted");
      feedback.innerText = "Password must contain at least 8 characters with letters and numbers.";
      feedback.style.color = "red";
    } else {
    feedback.classList.remove("text-muted");
      feedback.innerText = "Strong password!";
      feedback.style.color = "green";
    }
  });

  document.getElementById('confirmPassword').addEventListener('input', function () {
    let feedback = document.getElementById('confirmPasswordFeedback');
    let newPassword = document.getElementById('newPassword').value;
    let confirmPassword = this.value;

    if (confirmPassword.length === 0) {
      feedback.innerText = "";
    } else if (newPassword !== confirmPassword) {
    feedback.classList.remove("text-muted");
      feedback.innerText = "Passwords do not match!";
      feedback.style.color = "red";
    } else {
    feedback.classList.remove("text-muted");
      feedback.innerText = "Passwords match!";
      feedback.style.color = "green";
    }
  });

   document.getElementById('oldPassword').addEventListener('input', function () {
  let feedback = document.getElementById('oldPasswordFeedback');
  let currentPassword = this.value;

  if (currentPassword.length === 0) {
    feedback.innerText = "";
    return;
  }

  feedback.innerText = "Checking old password...";
  feedback.style.color = "gray";

  fetch("{{ route('password.check') }}", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-TOKEN": "{{ csrf_token() }}"
    },
    body: JSON.stringify({ current_password: currentPassword })
  })
  .then(response => response.json())
  .then(data => {
    if (data.valid) {
      feedback.classList.remove("text-muted");
      feedback.innerText = "Old password is correct.";
      feedback.style.color = "green";
    } else {
    feedback.classList.remove("text-muted");
      feedback.innerText = "Old password is incorrect.";
      feedback.style.color = "red";
    }
  })
  .catch(error => {
  feedback.classList.remove("text-muted");
    feedback.innerText = "Error checking password.";
    feedback.style.color = "red";
  });
});
</script>
@endpush
