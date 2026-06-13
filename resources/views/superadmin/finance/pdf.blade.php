<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan & Stok Produk SahihStore</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            font-size: 11pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #F0A04B;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }
        .logo-text {
            font-size: 20pt;
            font-weight: bold;
            color: #B1C29E;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .title {
            font-size: 16pt;
            font-weight: bold;
            color: #333;
            margin-top: 5px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .meta-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .meta-label {
            font-weight: bold;
            width: 25%;
        }
        .section-title {
            font-size: 13pt;
            font-weight: bold;
            color: #F0A04B;
            border-bottom: 1px solid #eeeeee;
            padding-bottom: 5px;
            margin-top: 25px;
            margin-bottom: 15px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .data-table th {
            background-color: #B1C29E;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            border: 1px solid #B1C29E;
        }
        .data-table td {
            padding: 8px 10px;
            border: 1px solid #dddddd;
        }
        .data-table tr:nth-child(even) {
            background-color: #fcfcfc;
        }
        .total-row {
            font-weight: bold;
            background-color: #f5f5f5 !important;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8pt;
            font-weight: bold;
            color: #ffffff;
            background-color: #F0A04B;
            border-radius: 3px;
        }
        .footer {
            margin-top: 50px;
            width: 100%;
        }
        .signature-section {
            float: right;
            width: 200px;
            text-align: center;
        }
        .signature-space {
            height: 60px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <div class="logo-text">SAHIHBODYFEED</div>
                    <div class="title">LAPORAN MUTASI & STOK PRODUK</div>
                </td>
                <td style="text-align: right; vertical-align: bottom;">
                    <div style="font-size: 9pt; color: #777;">Tanggal Unduh: {{ $date }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Informasi Laporan</div>
    <table class="meta-table">
        <tr>
            <td class="meta-label">Bulan Laporan</td>
            <td>: {{ \Carbon\Carbon::now('Asia/Makassar')->translatedFormat('F Y') }}</td>
            <td class="meta-label">Status Transaksi</td>
            <td>: <span class="badge">Disetujui</span></td>
        </tr>
        <tr>
            <td class="meta-label">Periode Event Promo</td>
            <td>:
                @if($data['promo_start'] && $data['promo_end'])
                    {{ \Carbon\Carbon::parse($data['promo_start'])->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($data['promo_end'])->translatedFormat('d M Y') }}
                @else
                    Tidak Ada Promo / Event
                @endif
            </td>
            <td class="meta-label">Sistem Basis</td>
            <td>: In-Memory Accumulation (10 botol/transaksi)</td>
        </tr>
    </table>

    <div class="section-title">Parameter Perhitungan & Event</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Item / Tipe Transaksi</th>
                <th>Jumlah Transaksi</th>
                <th>Bonus / Event</th>
                <th>Transaksi dengan Bonus</th>
                <th>Total Produk Keluar</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Registrasi Agen Baru (New Agent)</strong></td>
                <td>{{ $data['new_agent_count'] }} kali</td>
                <td>+{{ $data['bonus_new_agent'] }} botol</td>
                <td>{{ $data['new_agent_bonus_cnt'] }} kali</td>
                <td>
                    @php
                        $regularNewAgent = $data['new_agent_count'] - $data['new_agent_bonus_cnt'];
                        $totalNewAgent = ($regularNewAgent * 10) + ($data['new_agent_bonus_cnt'] * (10 + $data['bonus_new_agent']));
                    @endphp
                    {{ $totalNewAgent }} botol
                </td>
            </tr>
            <tr>
                <td><strong>Repeat Order (RO)</strong></td>
                <td>{{ $data['ro_count'] }} kali</td>
                <td>+{{ $data['bonus_repeat_order'] }} botol</td>
                <td>{{ $data['ro_bonus_cnt'] }} kali</td>
                <td>
                    @php
                        $regularRO = $data['ro_count'] - $data['ro_bonus_cnt'];
                        $totalRO = ($regularRO * 10) + ($data['ro_bonus_cnt'] * (10 + $data['bonus_repeat_order']));
                    @endphp
                    {{ $totalRO }} botol
                </td>
            </tr>
            <tr class="total-row">
                <td colspan="4" style="text-align: right;">Total Mutasi Keluar:</td>
                <td>{{ $data['total_keluar'] }} botol</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Rekapitulasi Stok Akhir</div>
    <table class="data-table" style="width: 50%; float: left;">
        <tbody>
            <tr>
                <td style="width: 60%;">Stok Awal Bulan</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($data['stok_awal']) }}</td>
            </tr>
            <tr>
                <td>Total Produk Keluar</td>
                <td style="text-align: right; font-weight: bold; color: #c00;">-{{ number_format($data['total_keluar']) }}</td>
            </tr>
            <tr class="total-row" style="background-color: rgba(240,160,75,0.1) !important;">
                <td>Sisa Stok Akhir</td>
                <td style="text-align: right; font-weight: bold; color: #008000; font-size: 12pt;">
                    {{ number_format($data['sisa_stok']) }}
                </td>
            </tr>
        </tbody>
    </table>

    <div style="clear: both;"></div>

    <div class="footer">
        <div style="font-size: 9pt; color: #777; float: left; width: 60%; margin-top: 50px;">
            * Dokumen ini dibuat otomatis oleh Sistem Sahihbodyfeed Affiliate dan sah tanpa tanda tangan fisik.<br>
            * Stok keluar diakumulasikan dari transaksi berstatus Approved pada bulan berjalan.
        </div>
        <div class="signature-section">
            <p>SahihStore Owner,</p>
            <div class="signature-space"></div>
            <div class="signature-line">MANAGEMENT</div>
        </div>
    </div>

</body>
</html>
