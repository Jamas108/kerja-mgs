<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PTGM - Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #1E90FF;
            --secondary-blue: #003f82;
            --navy-blue: #4f5fda;
            --light-blue: #E3F2FD;
            --white: #ffffff;
            --light-gray: #f8f9fa;
            --gray: #6c757d;
            --dark-gray: #343a40;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--light-blue);
            background-image: linear-gradient(135deg, var(--light-blue) 0%, var(--white) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            display: flex;
            max-width: 900px;
            width: 100%;
            background-color: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .login-branding {
            background-color: var(--white);
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .login-form {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-image: linear-gradient(135deg, var(--primary-blue) 0%, var(--navy-blue) 100%);
            color: var(--white);
        }

        .logo {
            max-width: 180px;
            margin-bottom: 30px;
        }

        .welcome-text {
            text-align: center;
            margin-bottom: 40px;
        }

        .welcome-text h1 {
            font-weight: 600;
            font-size: 28px;
            margin-bottom: 12px;
        }

        .welcome-text p {
            color: rgba(255, 255, 255, 0.85);
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            padding-left: 40px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s;
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--white);
        }

        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .form-group input:focus {
            border-color: var(--white);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2);
            outline: none;
            background-color: rgba(255, 255, 255, 0.15);
        }

        .form-group i {
            position: absolute;
            left: 15px;
            top: 40px;
            color: rgba(255, 255, 255, 0.7);
        }

        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
        }

        .form-check input {
            margin-right: 8px;
        }

        .btn {
            padding: 12px 24px;
            background-color: var(--white);
            color: var(--primary-blue);
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }

        .btn:hover {
            background-color: rgba(255, 255, 255, 0.9);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 24px;
        }

        .form-footer a {
            color: var(--white);
            text-decoration: none;
            transition: all 0.3s;
            opacity: 0.8;
        }

        .form-footer a:hover {
            opacity: 1;
            text-decoration: underline;
        }

        .branding-content {
            text-align: center;
            z-index: 1;
        }

        .branding-content h2 {
            font-size: 32px;
            margin-bottom: 20px;
            color: var(--dark-gray);
        }

        .branding-content p {
            font-size: 16px;
            opacity: 0.8;
            margin-bottom: 30px;
            line-height: 1.6;
            color: var(--gray);
        }

        .decorative-shape {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--light-blue) 0%, #c5e3ff 100%);
            opacity: 0.6;
            z-index: 0;
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            top: -150px;
            right: -150px;
        }

        .shape-2 {
            width: 200px;
            height: 200px;
            bottom: -100px;
            left: -100px;
        }

        .invalid-feedback {
            color: var(--white);
            font-size: 14px;
            margin-top: 5px;
            background-color: rgba(220, 53, 69, 0.2);
            padding: 5px 10px;
            border-radius: 4px;
            border-left: 3px solid rgba(220, 53, 69, 0.6);
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 90%;
            }

            .login-branding {
                padding: 30px;
            }

            .login-form {
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-branding">
            <div class="decorative-shape shape-1"></div>
            <div class="decorative-shape shape-2"></div>
            <div class="branding-content">
                <img src="{{Storage::url('images/migaslogo.png')}}" alt="PTGM Logo" class="logo">
                <h2>Welcome to PTGM</h2>
                <p>Sign in to access your account and manage your resources</p>
            </div>
        </div>
        <div class="login-form">
            <div class="welcome-text">
                <h1>Sign In</h1>
                <p>Enter your credentials to access your account</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <i class="fas fa-envelope"></i>
                    <input id="email" type="email" name="email" value="" required autocomplete="email" autofocus placeholder="your@email.com">
                    <div class="invalid-feedback" style="display: none;">
                        <strong>Invalid email address</strong>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <i class="fas fa-lock"></i>
                    <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                    <div class="invalid-feedback" style="display: none;">
                        <strong>Password is required</strong>
                    </div>
                </div>


                <button type="submit" class="btn">Sign In</button>


            </form>
        </div>
    </div>

    <script>
        // Simple form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            let valid = true;
            const email = document.getElementById('email');
            const password = document.getElementById('password');

            // Validate email
            if (!email.value || !email.value.includes('@')) {
                email.nextElementSibling.style.display = 'block';
                email.style.borderColor = 'rgba(220, 53, 69, 0.6)';
                valid = false;
            } else {
                email.nextElementSibling.style.display = 'none';
                email.style.borderColor = 'rgba(255, 255, 255, 0.2)';
            }

            // Validate password
            if (!password.value) {
                password.nextElementSibling.style.display = 'block';
                password.style.borderColor = 'rgba(220, 53, 69, 0.6)';
                valid = false;
            } else {
                password.nextElementSibling.style.display = 'none';
                password.style.borderColor = 'rgba(255, 255, 255, 0.2)';
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>