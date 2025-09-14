<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            line-height: 1.6;
        }

        .wrapper {
            min-height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .content {
            flex: 1 1 auto;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        a:hover {
            color: #3f4949;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 0 1rem;
            justify-content: center;
        }

        header {
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1rem;
        }

        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            font-size: 1.5rem;
            font-weight: bold;
            color: #171718;
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        nav li {
            margin: 0;
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

        .main {
            display: flex;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        @media (min-width: 768px) {
            .main {
                grid-template-columns: 2fr 1fr;
            }
        }

        article {
            background: #fff;
            padding: 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            margin-bottom: 14px
        }

        article h2 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        article p.meta {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 1rem;
        }

        footer {
            background: #fff;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.05);
            margin-top: 2rem;
            padding: 1rem;
            text-align: center;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .article__row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .row__btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .row__btn a,
        .row__btn button {
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            color: #080808;
            transition: background 0.2s ease;
            background: none;
            font-family: inherit;
        }

        .view a:hover,
        .view button:hover {
            background-color: #648ce4;
        }

        .delete a:hover,
        .delete button:hover {
            background-color: #d47474;
        }

        .download a:hover,
        .download button:hover {
            background-color: #6fb19c;
        }

        /* Стили для форм */
        .delete-form {
            display: inline;
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="content">
            <header>
                <div class="container">
                    <h1><a href="#">Table Master</a></h1>
                    <nav>
                        <ul>
                            <li><a href="#">Home</a></li>
                            <li><a href="#">About</a></li>
                            <li><a href="#">Blog</a></li>
                            @auth
                                <span>Hi there, {{Auth::user()->name}}</span>
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

            <!-- Main Content -->
            <div class="container main">
                <main class="posts">
                    @foreach ($files as $file)
                        <article>
                            <h2>{{$file->original_name}}</h2>
                            <div class="article__row">
                                <div class="time_posted">
                                    <p class="meta">Loaded on {{$file->created_at}}</p>
                                </div>
                                <div class="row__btn">
                                    <div class="view">
                                        <a href="{{route('files.show', $file->id)}}">View</a>
                                    </div>
                                    <div class="delete">
                                        <form action="{{route('files.destroy', $file->id)}}" method="post">
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
                                </div>
                            </div>
                        </article>
                    @endforeach

                </main>

            </div>
        </div>

        <footer>
            © 2025 Table Master. All rights reserved.
        </footer>
    </div>

</body>

</html>