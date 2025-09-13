<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Table Master</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f9f9f9;
            margin: 0;
            padding: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        form {
            background: #fff;
            padding: 1.5rem 2rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 400px;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        input[type="file"] {
            display: block;
            margin-bottom: 1rem;
        }

        .file-name {
            font-size: 0.9rem;
            color: #555;
            margin-bottom: 1rem;
        }

        .error {
            color: #c00;
            font-size: 0.9rem;
            margin-top: -0.5rem;
            margin-bottom: 1rem;
        }

        button {
            display: inline-block;
            padding: 0.6rem 1.2rem;
            background: #007acc;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        button:hover {
            background: #005f99;
        }
    </style>
</head>

<body>
    <form action="{{route('files.store')}}" method="POST" enctype="multipart/form-data">
        @csrf

        <div>
            <label for="xls_file">Choose file</label>
            <input type="file" name="xls_file" id="xls_file">
            <p class="file-name" id="file-name">No file selected</p>
            @error('body')
                <p class="error">{{$message}}</p>
            @enderror
        </div>

        <button type="submit">Upload</button>
    </form>

    <script>
        const fileInput = document.getElementById('xls_file');
        const fileName = document.getElementById('file-name');

        fileInput.addEventListener('change', () => {
            fileName.textContent = fileInput.files.length > 0
                ? fileInput.files[0].name
                : 'No file selected';
        });
    </script>
</body>

</html>