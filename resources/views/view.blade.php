<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>View Excel document</title>
    <style>
        .table-container {
            max-width: 100%;
            overflow-x: auto;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        table {
            border-collapse: collapse;
            width: 100%;
            -pdf-keep-in-frame-mode: shrink;
            font-family: DejaVu Sans;
            color: #2d3748;
            background: white;
        }

        body {
            font-size: 12px;
        }

        caption {
            padding: 1rem;
            font-size: 1.4rem;
            font-weight: 600;
            color: #1a202c;
            text-align: left;
        }

        th {
            background-color: #4a5568;
            color: white;
            font-weight: 600;
            text-align: left;
            padding: 1rem;
            border-right: 1px solid #e2e8f0;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        tr:nth-of-type(even) {
            background-color: #f7fafc;
        }

        td:first-child {
            border-right: 1px solid #e2e8f0;
        }

        tr {
            transition: background-color 0.2s ease;
        }

        tr:hover {
            background-color: #ebf8ff;
        }
    </style>
</head>

<body>
    <div class="table-container">
        <table>
            <caption>{{$name}}</caption>
            <thead>
                <tr>
                    @foreach ($data[0] as $d)
                        <th>{{$d}}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @for ($i = 1; $i < count($data); $i++)
                    <tr>
                        @foreach ($data[$i] as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
</body>

</html>