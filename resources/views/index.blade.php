<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Table Master</title>
</head>
<body>
    <form action="{{route('files.store')}}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div>
        <label for="xls_file">Choose file</label>
        <input type="file" name="xls_file" id="xls_file">
    </div>

    <button type="submit">Upload</button>
    </form>
</body>
</html>
