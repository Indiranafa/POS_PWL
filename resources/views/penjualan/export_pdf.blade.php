<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            margin: 6px 20px;
            line-height: 1.5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 4px;
            border: 1px solid black;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-center {
            text-align: center;
        }

        .header-table td {
            border: none;
        }

        .logo {
            width: 80px;
            height: auto;
        }

        h3 {
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td width="15%" class="text-center">
                <img src="{{ public_path('logo_polinema.png') }}" class="logo">
            </td>
            <td width="85%" class="text-center">
                <div><strong>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</strong></div>
                <div><strong>POLITEKNIK NEGERI MALANG</strong></div>
                <div>Jl. Soekarno-Hatta No. 9 Malang 65141</div>
                <div>Telepon (0341) 404424 Pes. 101-105, Fax. (0341) 404420</div>
                <div>Laman: www.polinema.ac.id</div>
            </td>
        </tr>
    </table>

    <h3 class="text-center">LAPORAN DATA PENJUALAN</h3>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Nama Pembeli</th>
                <th>Kode Penjualan</th>
                <th>User yang Melayani</th>
                <th>Tanggal Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penjualan as $s)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $s->pembeli }}</td>
                <td>{{ $s->penjualan_kode }}</td>
                <td>{{ $s->user ? $s->user->username : 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($s->penjualan_tanggal)->format('d-m-Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
