<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Operasional Pengiriman BBM - PT Indah Kuala Kapuas</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #1f2937;
            margin: 0;
            padding: 30px;
            font-size: 13px;
        }
        .toolbar {
            position: fixed;
            top: 16px;
            right: 16px;
            display: flex;
            gap: 8px;
        }
        .toolbar button, .toolbar a {
            background: #0d6efd;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
        }
        .toolbar a.secondary {
            background: #6c757d;
        }
        .kop {
            text-align: center;
            border-bottom: 3px solid #1f2937;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .kop h1 {
            font-size: 18px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop p {
            margin: 2px 0 0;
            font-size: 12px;
            color: #4b5563;
        }
        .judul {
            text-align: center;
            margin-bottom: 4px;
        }
        .judul h2 {
            font-size: 15px;
            text-decoration: underline;
            margin: 0 0 2px;
        }
        .judul p {
            margin: 0;
            font-size: 12px;
            color: #4b5563;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }
        th, td {
            border: 1px solid #333;
            padding: 6px 8px;
            font-size: 12px;
            vertical-align: top;
        }
        th {
            background: #e5e7eb;
            text-align: center;
        }
        td.center {
            text-align: center;
        }
        .ttd {
            margin-top: 50px;
            display: flex;
            justify-content: flex-end;
        }
        .ttd-box {
            text-align: center;
            width: 220px;
        }
        .ttd-box .spasi {
            height: 70px;
        }
        .footer-cetak {
            margin-top: 30px;
            font-size: 11px;
            color: #6b7280;
            text-align: right;
        }

        @media print {
            .toolbar {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <div class="toolbar">
        <button onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
        <a href="{{ route('admin.laporan.index') }}" class="secondary">Kembali</a>
    </div>

    <div class="kop">
        <h1>PT Indah Kuala Kapuas</h1>
        <p>Distribusi &amp; Transportasi Bahan Bakar Minyak (BBM)</p>
    </div>

    <div class="judul">
        <h2>Laporan Operasional Pengiriman BBM</h2>
        <p>
            Periode:
            @if($dari || $sampai)
                {{ $dari ? \Carbon\Carbon::parse($dari)->locale('id')->translatedFormat('d F Y') : '...' }}
                s/d
                {{ $sampai ? \Carbon\Carbon::parse($sampai)->locale('id')->translatedFormat('d F Y') : '...' }}
            @else
                Seluruh Data
            @endif
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Mitra Penerima</th>
                <th width="70">Volume (L)</th>
                <th>Driver Pelaksana</th>
                <th>Armada</th>
                <th width="90">Tanggal Selesai</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporans as $index => $lap)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $lap->pengiriman->pesanan->mitra->nama_perusahaan }}</td>
                    <td class="center">{{ number_format($lap->pengiriman->pesanan->jumlah_bbm) }}</td>
                    <td>{{ $lap->pengiriman->driver->user->name }}</td>
                    <td>{{ $lap->pengiriman->armada->no_polisi }} ({{ $lap->pengiriman->armada->jenis }})</td>
                    <td class="center">{{ \Carbon\Carbon::parse($lap->created_at)->format('d-m-Y') }}</td>
                    <td>{{ $lap->keterangan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center">Tidak ada data laporan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="ttd">
        <div class="ttd-box">
            <p>Pontianak, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</p>
            <p>Mengetahui,</p>
            <div class="spasi"></div>
            <p><strong>___________________________</strong><br>Admin PT Indah Kuala Kapuas</p>
        </div>
    </div>

    <div class="footer-cetak">
        Dicetak otomatis oleh sistem pada {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y, H:i') }} WIB
    </div>

</body>
</html>