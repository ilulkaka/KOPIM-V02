<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Login - Kopim</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #ffffff, #ffffff);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-box {
            background-color: #fff;
            padding: 2.5rem;
            border-radius: 1rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .login-box h3 {
            margin-bottom: 1.5rem;
            font-weight: bold;
            color: #343a40;
        }

        .input-group-text {
            background-color: #f1f1f1;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #6610f2;
        }

        .btn-primary {
            background-color: #6610f2;
            border: none;
        }

        .btn-primary:hover {
            background-color: #5a0ee2;
        }

        #errorMsg {
            font-size: 0.9rem;
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <form method="POST" id="login" class="bg-white p-4 rounded shadow" style="width: 100%; max-width: 400px;">
        @csrf
        <h3 class="text-center mb-4">Login</h3>
        {{-- @error('email')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror --}}
        <div id="error"></div>
        @if (Session::has('alert'))
            <div class="alert alert-danger">
                <div>{{ Session::get('alert') }}</div>
            </div>
        @endif

        {{-- @if ($errors->any())
            <p style="color:red">{{ $errors->first() }}</p>
        @endif --}}

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                <input type="text" class="form-control" id="email" name="email" required
                    placeholder="Enter username">
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                <input type="password" class="form-control" id="password" name="password" required
                    placeholder="Enter password">
            </div>
        </div>

        <div class="d-grid mb-2">
            <button type="submit" class="btn btn-primary">Login</button>
        </div>
    </form>
</body>

</html>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/login.js') }}"></script>
