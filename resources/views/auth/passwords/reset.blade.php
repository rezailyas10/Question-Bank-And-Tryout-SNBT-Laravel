<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
         <link rel="stylesheet" href="{{ asset('/style/main.css') }}?v={{ time() }}">
    <style>

        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h2 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #666;
            font-size: 14px;
        }
        
        .alert {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            padding-right: 40px;
            border: 2px solid #e1e5e9;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #007bff;
        }
        
          .toggle-password {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(10%);
            cursor: pointer;
            z-index: 2;
            color: #6c757d;
            font-size: 1rem;
        }
        
        .btn {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background: #0056b3;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Rubah Password</h2>
            <p>Masukkan password baru untuk akun Anda</p>
        </div>

        <!-- Success Message -->
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="form-group">
                <label for="password">Password Baru</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password baru" required>
                <span class="fa fa-fw fa-eye toggle-password" data-target="password"></span>
            </div>
            
            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi password baru" required>
                <span class="fa fa-fw fa-eye toggle-password" data-target="password_confirmation"></span>
            </div>
            
            <button type="submit" class="btn">Rubah Password</button>
        </form>
        
        <div class="back-link">
            <a href="{{ route('login') }}">Kembali ke Login</a>
        </div>
    </div>

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
</body>
</html>