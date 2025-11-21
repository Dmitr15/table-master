<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register</title>
    <style>
        <style>* {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #dcdbed, #4c4a53);
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        /* Контейнер формы */
        .login-container {
            background: white;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 400px;
        }

        /* Заголовок */
        .login-container h1 {
            margin-bottom: 24px;
            font-weight: 700;
            font-size: 28px;
            color: #4f46e5;
            text-align: center;
        }

        /* Форма */
        form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        /* Поля ввода */
        input[type="email"],
        input[type="text"],
        input[type="password"] {
            padding: 14px 16px;
            font-size: 16px;
            border: 2px solid #ddd;
            border-radius: 8px;
            transition: border-color 0.3s ease;
        }

        input[type="email"]:focus,
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 8px rgba(79, 70, 229, 0.4);
        }

        /* Кнопка */
        button[type="submit"] {
            padding: 14px 16px;
            background-color: #4f46e5;
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #4338ca;
        }

        /* Ссылка */
        .login-footer {
            margin-top: 16px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }

        .login-footer a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 600;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        /* Адаптивность */
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>
    <form action="{{route('register')}}" method="POST">
        @csrf
        <h2>Log in to Your Account</h2>

        <label for="name">Name</label>
        <input type="text" name="name" placeholder="Name" required value="{{old('name')}}">

        <label for="email">Email</label>
        <input type="email" name="email" placeholder="Email" required value="{{old('email')}}">

        <label for="password">Password</label>
        <input type="password" name="password" placeholder="password" required>

        <label for="password_confirmation">Confirm password</label>
        <input type="password" name="password_confirmation" placeholder="Confirm password" required>


        {{-- <div>
            <input type="checkbox" name="subscribe" id="subscribe">
            <label for="subscribe"> Subscribe to our news</label>
        </div> --}}

        <button type="submit" class="btn mt-4">Sign up</button>

        <a href="{{route('login')}}">Already have an account?</a>
        @if ($errors->any())
            <ul class="px-4 py-2 bd-red-100">
                @foreach ($errors->all() as $error)
                    <li class="my-2 text-red-500">{{$error}}</li>
                @endforeach
            </ul>
        @endif
    </form>

</body>

</html>