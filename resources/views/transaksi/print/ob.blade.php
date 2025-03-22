<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CETAK OB</title>
    <style>
        #header>thead>tr>th {
            background-color: #CCCCCC;
        }
    </style>
</head>

<body>
    <table style="border-collapse:collapse;width:100%" id="header" border="1">
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>DINKESKB</td>
                <td>{{ $item->rekening_awal }}</td>
                <td>{{ $item->nm_rekening_tujuan }}</td>
                <td>{{ $item->rekening_tujuan }}</td>
                <td>{{ $item->nilai }}</td>
                <td>{{ $item->ket_tpp }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br>
</body>
</html>
