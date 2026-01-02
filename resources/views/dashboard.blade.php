<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/styles_zeroing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <script type="text/javascript" src="{{ asset('js/dashboard_script.js') }}" defer></script>
    <title>Dashboard</title>
</head>

<body>
    <div class="wrapper">
        <div class="content">
            <header>
                <div class="container">
                    <h1><a href="#">Table Master</a></h1>
                    <nav>
                        <ul>
                            <li><a href="{{ route('index') }}">Home</a></li>
                            <li><a href="#">About</a></li>
                            <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            @auth
                                <span class="greeting">Hi there, {{Auth::user()->name}}</span>
                                <form action="{{route('logout')}}" method="post" style="margin:0;">
                                    @csrf
                                    <button class="btn" onclick="event.preventDefault(); this.closest('form').submit();">Log
                                        Out</button>
                                </form>
                            @endauth
                        </ul>
                    </nav>
                </div>
            </header>

            <div class="breadcrumbs">
                <p><a class="breadcrumb-link" href="{{route('index')}}">Home</a>/Dashboard</p>
            </div>

            <div class="container main">
                <main class="posts">
                    @foreach ($files as $file)
                        <article id="file-{{$file->id}}">
                            <h2>{{$file->original_name}}</h2>
                            <div class="article__row">
                                <div class="time_posted">
                                    <p class="meta">Loaded on {{$file->created_at}}</p>
                                    <!-- Контейнер для статуса конвертации -->
                                    <div class="conversion-info" id="conversion-info-{{$file->id}}"></div>
                                </div>
                                <div class="row__btn">
                                    <div class="delete">
                                        <form action="{{route('files.destroy', $file->id)}}" method="post"
                                            class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn"
                                                onclick="return confirm('Are you sure you want to delete this file?')">Delete</button>
                                        </form>
                                    </div>
                                    <div class="download">
                                        <form action="{{route('download', $file->id)}}" method="post">
                                            @csrf
                                            <button type="submit" class="action-btn">Download</button>
                                        </form>
                                    </div>

                                    @if (pathinfo($file->path, PATHINFO_EXTENSION) === 'xls')
                                        <div class="xlsToXlsx">
                                            <form class="conversion-form" data-file-id="{{$file->id}}"
                                                data-conversion-type="xlsToXlsx">
                                                @csrf
                                                <button type="submit" class="action-btn convert-btn">Convert to xlsx</button>
                                            </form>
                                        </div>
                                    @else
                                        <div class="xlsxToXls">
                                            <form class="conversion-form" data-file-id="{{$file->id}}"
                                                data-conversion-type="xlsxToXls">
                                                @csrf
                                                <button type="submit" class="action-btn convert-btn">Convert to xls</button>
                                            </form>
                                        </div>
                                    @endif
                                    <div class="excelToOds">
                                        <form class="conversion-form" data-file-id="{{$file->id}}"
                                            data-conversion-type="excelToOds">
                                            @csrf
                                            <button type="submit" class="action-btn convert-btn">Convert to ods</button>
                                        </form>
                                    </div>
                                    <div class="excelToCsv">
                                        <form class="conversion-form" data-file-id="{{$file->id}}"
                                            data-conversion-type="excelToCsv">
                                            @csrf
                                            <button type="submit" class="action-btn convert-btn">Convert to csv</button>
                                        </form>
                                    </div>
                                    <div class="excelToHtml">
                                        <form class="conversion-form" data-file-id="{{$file->id}}"
                                            data-conversion-type="excelToHtml">
                                            @csrf
                                            <button type="submit" class="action-btn convert-btn">Convert to html</button>
                                        </form>
                                    </div>
                                    <div class="split">
                                        <form class="conversion-form" data-file-id="{{$file->id}}"
                                            data-conversion-type="split">
                                            @csrf
                                            <button type="submit" class="action-btn convert-btn">Split</button>
                                        </form>
                                    </div>
                                    <div class="merge">
                                        <form class="conversion-form" data-file-id="{{$file->id}}"
                                            data-conversion-type="merge" data-use-formdata="true">
                                            @csrf
                                            <input type="file" name="merge_file" id="merge_file_{{ $file->id }}"
                                                accept=".xls,.xlsx" required>
                                            <label for="merge_file_{{ $file->id }}">Choose file for merging</label>
                                            @error('merge_file_' . $file->id)
                                                <p class="error">{{ $message }}</p>
                                            @enderror
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @error('file_' . $file->id)
                                <div class="error-container">
                                    <p class="error">{{$message}}</p>
                                </div>
                            @enderror
                            @error('delete_file_' . $file->id)
                                <div class="error-container">
                                    <p class="error">{{$message}}</p>
                                </div>
                            @enderror
                            @error('download_file_' . $file->id)
                                <div class="error-container">
                                    <p class="error">{{$message}}</p>
                                </div>
                            @enderror
                        </article>
                    @endforeach
                </main>
            </div>
            <div class="download-form">
                <form action="{{route('files.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="upload">
                        <label for="xls_file">Choose file</label>
                        <input type="file" name="xls_file" id="xls_file">
                        <p class="file-name" id="file-name">No file selected</p>
                        @error('xls_file')
                            <p class="error">{{$message}}</p>
                        @enderror
                    </div>
                    <button type="submit">Upload</button>
                </form>
            </div>
        </div>
        <footer>
            <div class="footer-container">
                <div class="footer-content">
                    <div class="footer-column">
                        <h3>Table Master</h3>
                        <p>Professional Excel file conversion solution since 2024</p>
                    </div>
                    <div class="footer-column">
                        <h3>Quick Links</h3>
                        <ul class="footer-links">
                            <li><a href="{{ route('index') }}">Home</a></li>
                            <li><a href="#about">About</a></li>
                            <li><a href="#how-it-works">How It Works</a></li>
                            <li><a href="#benefits">Benefits</a></li>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h3>Contact Info</h3>
                        <ul class="footer-links">
                            <li><a href="mailto:info@tablemaster.com">📧 info@tablemaster.com</a></li>
                            <li><a href="tel:+79991234567">📱 +7 (999) 123-45-67</a></li>
                            <li>🕐 Support: 24/7</li>
                        </ul>
                    </div>
                </div>
                <div class="copyright">
                    <p>&copy; 2025 Table Master. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>