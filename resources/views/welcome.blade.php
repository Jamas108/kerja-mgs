<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Management System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --light-color: #f8f9fa;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f4f7fa;
        }

        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background-color: var(--light-color);
        }

        .hero-content {
            padding: 4rem 0;
        }

        .navbar {
            background-color: white;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                Performance Management System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item">
                            <a href="{{ url('/home') }}" class="nav-link">Dashboard</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="nav-link">Log in</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a href="{{ route('register') }}" class="nav-link">Register</a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center hero-content">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Effortless Employee Performance Management</h1>
                    <p class="lead mb-4">Streamline your employee evaluation process with our comprehensive performance management system. Set goals, track progress, and provide feedback all in one place.</p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        @auth
                            <a href="{{ url('/home') }}" class="btn btn-primary btn-lg px-4">Go to Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4">Get Started</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg px-4">Register</a>
                            @endif
                        @endauth
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="https://source.unsplash.com/random/600x400/?team,office" alt="Team" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Key Features</h2>
                <p class="lead text-muted">Everything you need to manage employee performance effectively</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 p-4 text-center">
                        <div class="feature-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h4>Job Desk Management</h4>
                        <p class="text-muted">Create and assign tasks to employees based on their divisions and track their progress in real-time.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 p-4 text-center">
                        <div class="feature-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h4>Performance Evaluation</h4>
                        <p class="text-muted">Evaluate employee performance with a multi-level review process involving division heads and directors.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 p-4 text-center">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Analytics & Reports</h4>
                        <p class="text-muted">Generate comprehensive reports to gain insights into employee performance and identify areas for improvement.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-4 bg-dark text-white">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2025 Performance Management System. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">Designed with <i class="fas fa-heart text-danger"></i> for better performance management</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
