<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/styles_zeroing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <script type="text/javascript" src="{{ asset('js/script.js') }}" defer></script>
    <title>Login</title>
</head>

<body>
    <div class="wrapper">
        <div class="content">
            <div class="breadcrumbs"><a href="{{route('index')}}">Home</a>/Sign Up</div>
            <h1>LogIn</h1>
            <form action="{{route('login')}}" method="POST">
                @csrf
                <div class="input-field">
                    <label for="email-input" class="label">
                        <span>@</span>
                    </label>
                    <input class="input-line" type="email" name="email" id="email-input" placeholder="Email" value="{{old('email')}}">
                </div>
                <div class="input-field">
                    <label for="password-input" class="label">
                        <img src="img/lock_24dp_000000_FILL1_wght400_GRAD0_opsz24.svg" alt="">
                    </label>
                    <input class="input-line" type="password" name="password" id="password-input"
                        placeholder="Password">
                </div>
                <button class="button" type="submit">LogIn</button>
                <p class="errors" id="error-message"></p>
                @if ($errors->any())
                    <ul class="px-4 py-2 bd-red-100">
                        @foreach ($errors->all() as $error)
                            <li class="my-2 text-red-500">{{$error}}</li>
                        @endforeach
                    </ul>
                @endif
            </form>
            <p class="login-link">Don`t have an Account?<a href="{{route('register')}}"> Sign Up!</a></p>
        </div>
    </div>
</body>

</html>