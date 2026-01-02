<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="" type="image/x-icon">
    <meta name="description"
        content="Профессиональный конвертер файлов Excel - быстрая конвертация между форматами XLS, XLSX, CSV, ODS и HTML">
    <meta name="keywords" content="конвертер Excel, XLS в XLSX, конвертация файлов, Excel онлайн">
    <link rel="stylesheet" href="{{ asset('css/styles_zeroing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <script type="text/javascript" src="{{ asset('js/index.js') }}" defer></script>
    <title>ExcelMaster - Профессиональный конвертер файлов Excel</title>
</head>

<body>
    <!-- Шапка -->
    <header id="header">
        <div class="container">
            <nav class="navbar">
                <a href="{{ route('index') }}" class="logo">ExcelMaster</a>
                <ul class="nav-links">
                    <li><a href="{{ route('index') }}">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#how-it-works">How It Works</a></li>
                    <li><a href="#benefits">Benefits</a></li>
                    <li><a href="#contact">Contact</a></li>
                    @guest
                        <div class="auth-buttons">
                            <a href="{{route('login')}}" class="login-btn">Log in</a>
                            <a href="{{route('register')}}" class="signup-btn">Sign Up</a>
                        </div>
                    @endguest
                    @auth
                        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <div class="auth-buttons">
                            <span class="user-greeting">👋 Hi, {{Auth::user()->name}}</span>
                            <form action="{{route('logout')}}" method="post" style="margin:0;">
                                @csrf
                                <button type="submit" class="logout-btn">Log Out</button>
                            </form>
                        </div>
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
            <a href="{{ route('dashboard') }}" class="cta-button">Start Converting →</a>
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
                    <h3>Get Converted File</h3>
                    <p>The finished file is automatically downloaded in the browser</p>
                </div>
            </div>
        </div>
    </section>

    <section id="benefits" class="features" style="background: var(--background);">
        <div class="container">
            <h2 class="section-title">Why Choose Us</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">✅</div>
                    <h3>100% Data Accuracy</h3>
                    <p>We guarantee complete preservation of all data, formulas, and calculations</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🎁</div>
                    <h3>100% FREE</h3>
                    <p>This service is absolutely free with no hidden costs</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🎯</div>
                    <h3>Easy to Use</h3>
                    <p>Intuitive interface with no software installation required</p>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="contact-form">
        <div class="container">
            <div class="contact-container">
                <form action="https://api.web3forms.com/submit" method="POST" class="contact-left">
                    <h2 class="contact-left-title">Contact Us</h2>
                    <input type="hidden" name="access_key" value="YOUR_ACCESS_KEY_HERE">
                    <input type="text" name="name" placeholder="Your Name" class="contact-inputs" required>
                    <input type="email" name="email" placeholder="Your Email" class="contact-inputs" required>
                    <textarea name="message" placeholder="Your message" class="contact-inputs" required></textarea>
                    <button type="submit">Send Message →</button>
                </form>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>ExcelMaster</h3>
                    <p>Professional Excel file conversion solution since 2024. Fast, secure, and free.</p>
                </div>
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="#about">Capabilities</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                        <li><a href="#benefits">Benefits</a></li>
                        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contact Info</h3>
                    <ul class="footer-links">
                        <li><a href="mailto:info@excelmaster.com">📧 info@excelmaster.com</a></li>
                        <li><a href="tel:+79991234567">📱 +7 (999) 123-45-67</a></li>
                        <li>🕐 Support: 24/7</li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2024 ExcelMaster. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>

</html>