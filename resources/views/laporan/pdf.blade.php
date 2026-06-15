<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan - {{ $bulanLabel }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #16A34A;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .store-name {
            font-size: 20px;
            font-weight: bold;
            color: #15803D;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .title {
            font-size: 14px;
            font-weight: bold;
            color: #475569;
            margin-top: 5px;
        }
        .summary-container {
            text-align: center;
            margin-bottom: 25px;
        }
        .stat-box {
            display: inline-block;
            width: 29%;
            border: 1px solid #e2e8f0;
            padding: 10px;
            border-radius: 6px;
            margin: 0 8px;
            background-color: #f8fafc;
            text-align: left;
        }
        .stat-label {
            font-size: 9px;
            color: #64748B;
            text-transform: uppercase;
            font-weight: bold;
            display: block;
            margin-bottom: 4px;
        }
        .stat-value {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #1e293b;
            border-left: 3px solid #16A34A;
            padding-left: 8px;
            margin-top: 25px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            background-color: #f8fafc;
            padding: 7px 8px;
            text-align: left;
            border-bottom: 2px solid #e2e8f0;
            font-size: 10px;
            color: #475569;
            text-transform: uppercase;
        }
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 10px;
            color: #334155;
        }
        .total-row {
            font-weight: bold;
            background-color: #f8fafc;
            border-top: 1px solid #cbd5e1;
            border-bottom: 2px solid #cbd5e1;
        }
        .green {
            color: #16A34A;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            font-size: 9px;
            color: #64748B;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <div class="header">
        <div class="store-name">Toko Rukun Jaya</div>
        <div class="title">Laporan Keuangan — {{ $bulanLabel }}</div>
    </div>

    <!-- Summary boxes -->
    <div class="summary-container">
        <div class="stat-box">
            <span class="stat-label">Total Omset</span>
            <span class="stat-value">Rp {{ number_format($totalOmset, 0, ',', '.') }}</span>
        </div>
        <div class="stat-box">
            <span class="stat-label">Total Profit</span>
            <span class="stat-value green">Rp {{ number_format($totalProfit, 0, ',', '.') }}</span>
        </div>
        <div class="stat-box">
            <span class="stat-label">Total Transaksi</span>
            <span class="stat-value">{{ $totalTrx }}</span>
        </div>
    </div>

    <!-- Section "Rincian Harian" -->
    <div class="section-title">Rincian Harian</div>
    <table>
        <thead>
            <tr>
                <th style="width: 25%;">Tanggal</th>
                <th style="width: 15%;" class="text-center">Transaksi</th>
                <th style="width: 20%;">Omset</th>
                <th style="width: 20%;">HPP</th>
                <th style="width: 20%;">Profit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($harian as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y') }}</td>
                    <td class="text-center">{{ $row->jumlah_transaksi }}</td>
                    <td>Rp {{ number_format($row->omset, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($row->hpp, 0, ',', '.') }}</td>
                    <td class="green">Rp {{ number_format($row->profit, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="color: #64748b; padding: 20px 0;">
                        Belum ada transaksi bulan ini
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($harian->count() > 0)
            <tfoot>
                <tr class="total-row">
                    <td>TOTAL</td>
                    <td class="text-center">{{ $harian->sum('jumlah_transaksi') }}</td>
                    <td>Rp {{ number_format($totalOmset, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($harian->sum('hpp'), 0, ',', '.') }}</td>
                    <td class="green">Rp {{ number_format($totalProfit, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <!-- Section "Profit per Kategori" -->
    <div class="section-title">Profit per Kategori</div>
    <table style="width: 60%; margin-top: 10px;">
        <thead>
            <tr>
                <th style="width: 60%;">Kategori</th>
                <th style="width: 40%;" class="text-right">Profit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($profitKategori as $category)
                <tr>
                    <td>{{ $category->category_name }}</td>
                    <td class="text-right green">
                        Rp {{ number_format($category->total_profit ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center" style="color: #64748b; padding: 15px 0;">
                        Belum ada data profit per kategori
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <div>Dicetak pada {{ $printedAt }} oleh {{ $printedBy }}</div>
        <div style="margin-top: 4px; font-weight: bold; color: #475569;">Sistem Informasi Toko Rukun Jaya — Tim MBG</div>
    </div>

</body>
</html>
