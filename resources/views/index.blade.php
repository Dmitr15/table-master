<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Профессиональный конвертер файлов Excel - быстрая конвертация между форматами XLS, XLSX, CSV, ODS и HTML">
    <meta name="keywords" content="конвертер Excel, XLS в XLSX, конвертация файлов, Excel онлайн">
    <title>ExcelMaster - Профессиональный конвертер файлов Excel</title>
    <style>
        /* Reset и базовые стили */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary-color: #64748b;
            --accent-color: #f59e0b;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --background: #ffffff;
            --background-light: #f8fafc;
            --border: #e2e8f0;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            background: var(--background);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            justify-content: center;
        }

        /* Шапка */
        header {
            background: var(--background);
            box-shadow: var(--shadow);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: var(--primary-color);
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Герой секция */
        .hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 160px 0 100px;
            text-align: center;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .cta-button {
            display: inline-block;
            background: var(--accent-color);
            color: white;
            padding: 12px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        /* Особенности */
        .features {
            padding: 100px 0;
            background: var(--background-light);
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            font-weight: 700;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: var(--background);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: var(--shadow);
            text-align: center;
            transition: transform 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        /* Как это работает */
        .how-it-works {
            padding: 100px 0;
        }

        .steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .step {
            text-align: center;
            padding: 2rem;
        }

        .step-number {
            display: inline-block;
            width: 50px;
            height: 50px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin: 0 auto 1rem;
        }

        /* Подвал */
        footer {
            background: var(--text-dark);
            color: white;
            padding: 60px 0 30px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .footer-column h3 {
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.5rem;
        }

        .footer-links a {
            color: var(--text-light);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: white;
        }

        .copyright {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid var(--secondary-color);
            color: var(--text-light);
        }

        /* Адаптивность */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .mobile-menu-btn {
                display: block;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .section-title {
                font-size: 2rem;
            }
        }

        .btn {
            background: none;
            border: none;
            cursor: pointer;
            color: #1f2937;
            font-size: 1rem;
        }

        .btn:hover {
            color: #4f46e5;
        }
    </style>
</head>

<body>
    <!-- Шапка -->
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="{{ route('index') }}" class="logo">ExcelMaster</a>
                <ul class="nav-links">
                    <li><a href="{{ route('index') }}">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#benefits">Benefits</a></li>
                    <li><a href="#contact">Contact</a></li>
                    @guest
                        <a href="{{route('login')}}" class="text-gray-500 hover:text-primary transition duration-300">Log
                            in</a>
                        <a href="{{route('register')}}" class="text-primary font-medium border-b-2 border-primary pb-1">Sign
                            Up</a>
                    @endguest
                    @auth
                        <span>Hi there, {{Auth::user()->name}}</span>
                        <form action="{{route('logout')}}" method="post" style="margin:0;">
                            @csrf
                            <button class="btn" onclick="event.preventDefault(); this.closest('form').submit();">Log
                                Out</button>
                        </form>
                    @endauth
                </ul>
                <button class="mobile-menu-btn">☰</button>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <h1>Professional Excel File Conversion</h1>
            <p>Fast and accurate conversion between XLS, XLSX, CSV, ODS, and HTML formats</p>
            <a href="{{ route('dashboard') }}" class="cta-button">Upload</a>
        </div>
    </section>

    <section id="about" class="features">
        <div class="container">
            <h2 class="section-title">Our Capabilities</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3>Instant Conversion</h3>
                    <p>Real-time file conversion with all data and formatting preserved</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🛡️</div>
                    <h3>Data Security</h3>
                    <p>All files are processed locally and automatically deleted after conversion</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Full Format Support</h3>
                    <p>XLS, XLSX, CSV, ODS, HTML – complete compatibility with all Excel versions</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔧</div>
                    <h3>Style Preservation</h3>
                    <p>Automatic retention of fonts, colors, borders, and cell formatting</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🚀</div>
                    <h3>Memory Optimization</h3>
                    <p>Efficient handling of large files up to 2 GB without performance loss</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💾</div>
                    <h3>Batch Processing</h3>
                    <p>Convert multiple files simultaneously and automatically create archives</p>
                </div>
            </div>
        </div>
    </section>

    <section id="how-it-works" class="how-it-works">
        <div class="container">
            <h2 class="section-title">How It Works</h2>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Upload File</h3>
                    <p>Select an Excel file for conversion via our user-friendly upload interface</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Choose Format</h3>
                    <p>Select your desired output format: XLS, XLSX, CSV, ODS, or HTML</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Adjust Settings</h3>
                    <p>Optionally configure additional conversion parameters</p>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <h3>Get the converted file</h3>
                    <p>The finished file is automatically downloaded in the browser.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="benefits" class="features" style="background: var(--background);">
        <div class="container">
            <h2 class="section-title">Why Choose Us</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <h3>100% Data Accuracy</h3>
                    <p>We guarantee complete preservation of all data, formulas, and calculations</p>
                </div>
                <div class="feature-card">
                    <h3>100% FREE</h3>
                    <p>This service is absolutely free</p>
                </div>
                <div class="feature-card">
                    <h3>Easy to Use</h3>
                    <p>Intuitive interface with no software installation required</p>
                </div>
            </div>
        </div>
    </section>

    <footer id="contact">
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>ExcelMaster</h3>
                    <p>Professional Excel file conversion solution since 2024</p>
                </div>
                <div class="footer-column">
                    <h3>Navigation</h3>
                    <ul class="footer-links">
                        <li><a href="#features">Capabilities</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                        <li><a href="#benefits">Benefits</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contact</h3>
                    <ul class="footer-links">
                        <li>Email: info@excelmaster.com</li>
                        <li>Phone: +7 (999) 123-45-67</li>
                        <li>Support: 24/7</li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2024 ExcelMaster. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Плавная прокрутка для якорных ссылок
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Мобильное меню
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const navLinks = document.querySelector('.nav-links');

        mobileMenuBtn.addEventListener('click', () => {
            navLinks.style.display = navLinks.style.display === 'flex' ? 'none' : 'flex';
        });

        // Закрытие мобильного меню при клике на ссылку
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    navLinks.style.display = 'none';
                }
            });
        });

        // Фиксация шапки при прокрутке
        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(255, 255, 255, 0.95)';
                header.style.backdropFilter = 'blur(10px)';
            } else {
                header.style.background = 'var(--background)';
                header.style.backdropFilter = 'none';
            }
        });
    </script>
</body>

</html>