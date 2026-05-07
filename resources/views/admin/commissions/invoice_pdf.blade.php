<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Komisi #{{ $commission->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #1a1a2e;
            background: #fff;
            padding: 30px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #4f46e5;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .brand h1 {
            font-size: 22px;
            font-weight: 700;
            color: #4f46e5;
            letter-spacing: -0.5px;
        }
        .brand p { font-size: 11px; color: #6b7280; margin-top: 2px; }
        .invoice-meta { text-align: right; }
        .invoice-meta .invoice-title {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
        }
        .invoice-meta p { font-size: 11px; color: #6b7280; margin-top: 3px; }
        .badge-status {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            margin-top: 6px;
        }
        .badge-paid { background: #d1fae5; color: #065f46; }
        .badge-pending { background: #fef3c7; color: #92400e; }

        /* Recipient Card */
        .section-title {
            font-size: 11px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .recipient-card {
            background: #f8faff;
            border: 1px solid #e0e7ff;
            border-left: 4px solid #4f46e5;
            border-radius: 6px;
            padding: 14px 16px;
            margin-bottom: 20px;
        }
        .recipient-card .name {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
        }
        .recipient-card .detail {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
        }
        .recipient-card .bank-row {
            display: flex;
            gap: 24px;
            margin-top: 8px;
        }
        .recipient-card .bank-item label {
            font-size: 10px;
            color: #9ca3af;
            text-transform: uppercase;
            display: block;
        }
        .recipient-card .bank-item span {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
        }

        /* Amount Box */
        .amount-box {
            background: #4f46e5;
            border-radius: 8px;
            padding: 18px 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        .amount-box .label {
            font-size: 11px;
            color: #c7d2fe;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .amount-box .amount {
            font-size: 26px;
            font-weight: 700;
            color: #fff;
            margin-top: 4px;
        }

        /* Detail Table */
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .detail-table th {
            background: #f3f4f6;
            font-size: 10px;
            text-transform: uppercase;
            color: #6b7280;
            padding: 8px 10px;
            text-align: left;
        }
        .detail-table td {
            padding: 9px 10px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 12px;
            color: #374151;
        }
        .detail-table td.value {
            text-align: right;
            font-weight: 600;
            color: #111827;
        }

        /* Footer */
        .footer {
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
            margin-top: 10px;
            font-size: 10px;
            color: #9ca3af;
            text-align: center;
        }
        .footer strong { color: #6b7280; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="brand">
            <h1>Sahihbodyfeed</h1>
            <p>Platform Afiliasi Multi-Level</p>
        </div>
        <div class="invoice-meta">
            <div class="invoice-title">INVOICE KOMISI</div>
            <p>No: KOM-{{ str_pad($commission->id, 6, '0', STR_PAD_LEFT) }}</p>
            <p>Tanggal: {{ $date }}</p>
            @if($commission->status->value === 'paid')
                <span class="badge-status badge-paid">✓ LUNAS</span>
            @else
                <span class="badge-status badge-pending">⏳ PENDING</span>
            @endif
        </div>
    </div>

    <!-- Nominal Pembayaran -->
    <div class="amount-box">
        <div class="label">Total Pembayaran Komisi</div>
        <div class="amount">Rp {{ number_format((float)$commission->amount, 0, ',', '.') }}</div>
    </div>

    <!-- Data Penerima -->
    <p class="section-title">Data Penerima</p>
    <div class="recipient-card">
        <div class="name">{{ $commission->recipient->nama }}</div>
        <div class="detail">
            NIK: {{ $commission->recipient->nik ?? '—' }}
            &nbsp;|&nbsp;
            No. Telp: {{ $commission->recipient->no_telp ?? '—' }}
        </div>
        <div class="bank-row" style="margin-top: 10px;">
            <div class="bank-item">
                <label>Nama Bank</label>
                <span>{{ $commission->recipient->bank_name ?? '—' }}</span>
            </div>
            <div class="bank-item">
                <label>No. Rekening</label>
                <span>{{ $commission->recipient->bank_account ?? '—' }}</span>
            </div>
            <div class="bank-item">
                <label>Atas Nama</label>
                <span>{{ $commission->recipient->bank_account_name ?? $commission->recipient->nama }}</span>
            </div>
        </div>
    </div>

    <!-- Detail Komisi -->
    <p class="section-title">Rincian Komisi</p>
    <table class="detail-table">
        <thead>
            <tr>
                <th>Keterangan</th>
                <th style="text-align:right;">Nilai</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>ID Komisi</td>
                <td class="value">#{{ $commission->id }}</td>
            </tr>
            <tr>
                <td>Jenis Komisi</td>
                <td class="value">{{ $commission->type->label() }}</td>
            </tr>
            <tr>
                <td>Level Generasi</td>
                <td class="value">Gen-{{ $commission->generation_level }}</td>
            </tr>
            <tr>
                <td>ID Transaksi Sumber</td>
                <td class="value">#{{ $commission->transaction_id }}</td>
            </tr>
            <tr>
                <td>Tanggal Dibuat</td>
                <td class="value">{{ $commission->created_at->timezone('Asia/Makassar')->format('d M Y H:i') }} WITA</td>
            </tr>
            @if($commission->paid_at)
            <tr>
                <td>Tanggal Dibayar</td>
                <td class="value">{{ $commission->paid_at->timezone('Asia/Makassar')->format('d M Y H:i') }} WITA</td>
            </tr>
            @endif
            @if($commission->payment_notes)
            <tr>
                <td>Catatan Pembayaran</td>
                <td class="value">{{ $commission->payment_notes }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini diterbitkan otomatis oleh sistem <strong>Sahihbodyfeed</strong>.</p>
        <p>Dicetak pada {{ $date }} WITA &nbsp;|&nbsp; Invoice KOM-{{ str_pad($commission->id, 6, '0', STR_PAD_LEFT) }}</p>
    </div>
</body>
</html>
