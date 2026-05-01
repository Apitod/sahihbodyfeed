<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Komisi Pending</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #f1f5f9; }
        .summary-box { border: 1px solid #000; padding: 10px; width: 300px; float: right; }
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN PENCAIRAN KOMISI AGEN</h1>
        <p>PT. Sahihbodyfeed | Tanggal Cetak: {{ $date }} WITA</p>
    </div>

    @php $grandTotal = 0; @endphp

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%">Nama Agen</th>
                <th width="20%">Bank</th>
                <th width="20%">No. Rekening / A.n.</th>
                <th width="15%" class="text-right">Total Komisi</th>
                <th width="15%">Tanda Terima</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grouped as $agentId => $agentCommissions)
                @php
                    $agent = $agentCommissions->first()->recipient;
                    $totalAgent = $agentCommissions->sum('amount');
                    $grandTotal += $totalAgent;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <strong>{{ $agent->nama }}</strong><br>
                        <small style="color: #666;">Username: {{ $agent->user?->username ?? '-' }}</small>
                    </td>
                    <td>{{ $agent->bank_name ?? '-' }}</td>
                    <td>
                        {{ $agent->bank_account ?? '-' }}<br>
                        <small style="color: #666;">{{ $agent->bank_account_name ?? '-' }}</small>
                    </td>
                    <td class="text-right">Rp {{ number_format($totalAgent) }}</td>
                    <td></td> <!-- Empty column for physical signature/check -->
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <table style="margin: 0; border: none;">
            <tr>
                <td style="border: none;">Total Agen</td>
                <td style="border: none;" class="text-right">{{ count($grouped) }}</td>
            </tr>
            <tr>
                <td style="border: none;"><strong>Total Pencairan</strong></td>
                <td style="border: none;" class="text-right"><strong>Rp {{ number_format($grandTotal) }}</strong></td>
            </tr>
        </table>
    </div>

    <div style="clear: both; margin-top: 50px;">
        <table style="border: none; width: 100%;">
            <tr>
                <td style="border: none; text-align: center; width: 50%;">
                    Disetujui Oleh,<br><br><br><br>
                    (.....................................)<br>
                    Direktur Keuangan
                </td>
                <td style="border: none; text-align: center; width: 50%;">
                    Dibuat Oleh,<br><br><br><br>
                    (.....................................)<br>
                    Admin Keuangan
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
