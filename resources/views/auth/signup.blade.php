<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/styles_zeroing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <script type="text/javascript" src="{{ asset('js/script.js') }}" defer></script>
    <title>Sign Up</title>
</head>

<body>
    <div class="wrapper">
        <div class="content">
            <div class="breadcrumbs"><a href="{{route('index')}}">Home</a>/Sign Up</div>
            <h1>Sign Up</h1>
            <form action="{{route('register')}}" method="POST" id="form">
                @csrf
                <div class="input-field">
                    <label for="firstname-input" class="label">
                        <img src="img/person_24dp_000000_FILL1_wght400_GRAD0_opsz24.svg" alt="">
                    </label>
                    <input class="input-line" type="text" name="name" id="firstname-input" placeholder="Firstname"
                        value="{{old('name')}}">
                </div>
                <div class="input-field">
                    <label for="email-input" class="label">
                        <span>@</span>
                    </label>
                    <input class="input-line" type="email" name="email" id="email-input" placeholder="Email"
                        value="{{old('email')}}">
                </div>
                <div class="input-field">
                    <label for="password-input" class="label">
                        <img src="img/lock_24dp_000000_FILL1_wght400_GRAD0_opsz24.svg" alt="">
                    </label>
                    <input class="input-line" type="password" name="password" id="password-input"
                        placeholder="Password">
                </div>
                <div class="input-field">
                    <label for="repeat-password-input" class="label">
                        <img src="img/lock_24dp_000000_FILL1_wght400_GRAD0_opsz24.svg" alt="">
                    </label>
                    <input class="input-line" type="password" name="password_confirmation" id="repeat-password-input"
                        placeholder="Repeat password">
                </div>

                <button class="button" type="submit">Sign up</button>
                <p class="errors" id="error-message"></p>
                @if ($errors->any())
                    <ul class="px-4 py-2 ">
                        @foreach ($errors->all() as $error)
                            <li class="my-2 errors">{{$error}}</li>
                        @endforeach
                    </ul>
                @endif
            </form>
            <p class="login-link">Already have an account?<a href="{{route('login')}}"> LogIn</a></p>
        </div>
    </div>

</body>

</html>