<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Hệ thống Quản lý Học tập hiện đại và chuyên nghiệp - Learning Management System">

    <title>Hệ thống Quản lý Học tập - LMS</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            background: #ffffff;
            overflow-x: hidden;
        }

        /* Navigation */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(229, 231, 235, 0.8);
            padding: 1rem 0;
            transition: all 0.3s ease;
        }

        .nav-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-link {
            color: #4b5563;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            padding: 0.5rem 0;
        }

        .nav-link:hover {
            color: #667eea;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.625rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
            box-shadow: 0 4px 6px -1px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: white;
            color: #667eea;
            padding: 0.625rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
            border: 2px solid #667eea;
        }

        .btn-secondary:hover {
            background: #667eea;
            color: white;
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 8rem 2rem 4rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.3;
        }

        .hero-content {
            max-width: 1200px;
            width: 100%;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            animation: fadeInUp 0.8s ease;
        }

        .hero-subtitle {
            font-size: 1.5rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2.5rem;
            font-weight: 400;
            animation: fadeInUp 0.8s ease 0.2s both;
        }

        .hero-description {
            font-size: 1.125rem;
            color: rgba(255, 255, 255, 0.8);
            max-width: 700px;
            margin: 0 auto 3rem;
            line-height: 1.8;
            animation: fadeInUp 0.8s ease 0.4s both;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 0.8s ease 0.6s both;
        }

        .btn-hero {
            padding: 1rem 2.5rem;
            font-size: 1.125rem;
            border-radius: 0.75rem;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-hero-primary {
            background: white;
            color: #667eea;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }

        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.4);
        }

        .btn-hero-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .btn-hero-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
        }

        /* Features Section */
        .features {
            padding: 6rem 2rem;
            background: #f9fafb;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .section-subtitle {
            font-size: 1.25rem;
            color: #6b7280;
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .feature-card {
            background: white;
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .feature-icon {
            width: 3.5rem;
            height: 3.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .feature-icon svg {
            width: 2rem;
            height: 2rem;
            color: white;
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .feature-description {
            color: #6b7280;
            line-height: 1.7;
        }

        /* Stats Section */
        .stats {
            padding: 4rem 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 3rem;
            text-align: center;
        }

        .stat-item {
            color: white;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1.125rem;
            opacity: 0.9;
        }

        /* Footer */
        .footer {
            background: #1f2937;
            color: #9ca3af;
            padding: 3rem 2rem 2rem;
        }

        .footer-content {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            color: white;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .footer-section p {
            line-height: 1.8;
        }

        .footer-bottom {
            max-width: 1280px;
            margin: 0 auto;
            padding-top: 2rem;
            border-top: 1px solid #374151;
            text-align: center;
            font-size: 0.875rem;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                gap: 1rem;
            }

            .nav-link {
                font-size: 0.875rem;
            }

            .btn-primary, .btn-secondary {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.25rem;
            }

            .hero-description {
                font-size: 1rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn-hero {
                width: 100%;
                max-width: 300px;
            }
        }

        @media (max-width: 640px) {
            .nav-container {
                padding: 0 1rem;
            }

            .nav-links {
                gap: 0.5rem;
            }

            .hero {
                padding: 6rem 1rem 3rem;
            }

            .features {
                padding: 4rem 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">LMS Platform</div>
            <div class="nav-links">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/home') }}" class="nav-link">Trang chủ</a>
                    @else
                        <a href="{{ route('login') }}" class="nav-link">Đăng nhập</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary">Đăng ký</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">Hệ thống Quản lý Học tập</h1>
            <p class="hero-subtitle">Learning Management System</p>
            <p class="hero-description">
                Nền tảng học tập trực tuyến hiện đại, giúp bạn quản lý khóa học, theo dõi tiến độ học tập
                và tạo trải nghiệm học tập tốt nhất cho học viên của mình.
            </p>
            <div class="hero-buttons">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/home') }}" class="btn-hero btn-hero-primary">Vào hệ thống</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-hero btn-hero-primary">Bắt đầu ngay</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-hero btn-hero-secondary">Tạo tài khoản</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Tính năng nổi bật</h2>
                <p class="section-subtitle">
                    Hệ thống được trang bị đầy đủ các công cụ cần thiết để quản lý và phát triển chương trình học tập hiệu quả
                </p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">Quản lý Khóa học</h3>
                    <p class="feature-description">
                        Tạo và quản lý các khóa học một cách dễ dàng. Hệ thống cho phép bạn tổ chức nội dung học tập,
                        phân loại khóa học theo chủ đề, và quản lý học viên đăng ký tham gia.
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">Quản lý Bài học</h3>
                    <p class="feature-description">
                        Tổ chức nội dung học tập thành các bài học chi tiết. Mỗi bài học có thể chứa video, tài liệu,
                        bài tập và các tài nguyên học tập khác để hỗ trợ học viên tốt nhất.
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">Theo dõi Tiến độ</h3>
                    <p class="feature-description">
                        Hệ thống tự động theo dõi tiến độ học tập của từng học viên. Bạn có thể xem báo cáo chi tiết về
                        số bài học đã hoàn thành, thời gian học tập và kết quả đạt được.
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">Quản lý Người dùng</h3>
                    <p class="feature-description">
                        Hệ thống phân quyền linh hoạt cho phép quản lý vai trò và quyền truy cập của người dùng.
                        Hỗ trợ nhiều cấp độ người dùng như quản trị viên, giảng viên, và học viên với các quyền hạn phù hợp.
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">Bảo mật Cao</h3>
                    <p class="feature-description">
                        Hệ thống được xây dựng với các tiêu chuẩn bảo mật cao, đảm bảo dữ liệu và thông tin của người dùng
                        được bảo vệ an toàn và tuân thủ các quy định về bảo mật thông tin.
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">Responsive Design</h3>
                    <p class="feature-description">
                        Giao diện được tối ưu hóa cho mọi thiết bị, từ máy tính để bàn đến điện thoại di động.
                        Học viên có thể học mọi lúc, mọi nơi với trải nghiệm tốt nhất.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Bảo mật</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Hỗ trợ</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">∞</div>
                    <div class="stat-label">Khóa học</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Responsive</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Về hệ thống</h3>
                <p>
                    Hệ thống Quản lý Học tập (LMS) là nền tảng học tập trực tuyến hiện đại,
                    giúp tổ chức và quản lý các chương trình đào tạo một cách hiệu quả.
                </p>
            </div>
            <div class="footer-section">
                <h3>Liên hệ</h3>
                <p>
                    Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua email
                    hoặc hệ thống hỗ trợ trực tuyến.
                </p>
            </div>
            <div class="footer-section">
                <h3>Công nghệ</h3>
                <p>
                    Powered by Laravel v{{ Illuminate\Foundation\Application::VERSION }}
                    (PHP v{{ PHP_VERSION }})
                </p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Hệ thống Quản lý Học tập. Tất cả quyền được bảo lưu.</p>
        </div>
    </footer>
</body>
</html>
