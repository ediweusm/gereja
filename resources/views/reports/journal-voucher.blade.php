@php
    $profile = \App\Models\ChurchProfile::first() ?? new \App\Models\ChurchProfile([
        'gmit_name' => 'Majelis Sinode GMIT',
        'church_name' => 'Jemaat Sion Oepura',
        'address' => 'Jl. H.R. Koroh, Oepura, Kec. Maulafa, Kota Kupang, Nusa Tenggara Timur',
        'phone' => '081123456789'
    ]);

    $hasCashDebit = false;
    $hasCashCredit = false;
    foreach ($journal->items as $item) {
        if (str_starts_with($item->account->code, '111')) {
            if ($item->debit > 0) {
                $hasCashDebit = true;
            }
            if ($item->credit > 0) {
                $hasCashCredit = true;
            }
        }
    }

    if ($hasCashCredit) {
        $voucherType = 'BUKTI KAS KELUAR';
        $themeColor = '#dc2626'; // Red
    } elseif ($hasCashDebit) {
        $voucherType = 'BUKTI KAS MASUK';
        $themeColor = '#16a34a'; // Green
    } else {
        $voucherType = 'BUKTI MEMORIAL';
        $themeColor = '#2563eb'; // Blue
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher Jurnal - {{ $journal->transaction_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #1f2937;
            font-size: 11pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px double #1f2937;
            padding-bottom: 8px;
        }
        .header h1 {
            font-size: 14pt;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header h2 {
            font-size: 16pt;
            font-weight: 800;
            margin: 2px 0 0 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            font-size: 9pt;
            margin: 4px 0 0 0;
            color: #4b5563;
        }
        .voucher-title {
            text-align: center;
            margin: 15px 0 25px 0;
        }
        .voucher-title h3 {
            font-size: 14pt;
            font-weight: 800;
            margin: 0;
            padding: 6px 20px;
            display: inline-block;
            border: 2px solid {{ $themeColor }};
            color: {{ $themeColor }};
            letter-spacing: 2px;
            border-radius: 4px;
            background-color: {{ $themeColor }}08;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-row {
            display: table-row;
        }
        .info-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .info-col-inner {
            display: table;
            width: 100%;
            font-size: 10pt;
        }
        .info-field {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 35%;
            font-weight: 600;
            color: #4b5563;
            padding: 4px 0;
        }
        .info-value {
            display: table-cell;
            width: 65%;
            font-weight: 700;
            color: #1f2937;
            padding: 4px 0;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10pt;
        }
        .details-table th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: 700;
            border-top: 1px solid #d1d5db;
            border-bottom: 1px solid #d1d5db;
            padding: 10px;
            text-align: left;
        }
        .details-table td {
            border-bottom: 1px solid #e5e7eb;
            padding: 10px;
        }
        .details-table .number-col {
            text-align: right;
            font-family: monospace;
            font-size: 11pt;
            font-weight: 600;
        }
        .details-table .total-row td {
            font-weight: 800;
            border-top: 2px solid #374151;
            border-bottom: 2px solid #374151;
            background-color: #f9fafb;
            font-size: 10pt;
        }
        .description-box {
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
            padding: 12px 15px;
            border-radius: 4px;
            margin-bottom: 30px;
            font-size: 10pt;
        }
        .description-box strong {
            display: block;
            margin-bottom: 4px;
            color: #4b5563;
        }
        .description-content {
            font-style: italic;
            color: #1f2937;
        }
        .signature-section {
            display: table;
            width: 100%;
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .signature-row {
            display: table-row;
        }
        .signature-col {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            font-size: 10pt;
        }
        .signature-space {
            height: 75px;
        }
        .signature-name {
            font-weight: 700;
            text-decoration: underline;
        }
        .signature-title {
            color: #4b5563;
            font-size: 9pt;
            margin-top: 2px;
        }
        @media print {
            body {
                background-color: #fff;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                @if($profile->logo_path)
                    <td style="width: 70px; padding: 0; text-align: left; vertical-align: middle;">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($profile->logo_path) }}" alt="Logo" style="height: 60px; max-width: 70px; display: block;">
                    </td>
                @endif
                <td style="text-align: center; padding: 0; vertical-align: middle;">
                    <h1>{{ $profile->gmit_name }}</h1>
                    <h2>{{ $profile->church_name }}</h2>
                    <p>{{ $profile->address }} | Telp: {{ $profile->phone }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="voucher-title">
        <h3>{{ $voucherType }}</h3>
    </div>

    <div class="info-grid">
        <div class="info-row">
            <div class="info-col">
                <div class="info-col-inner">
                    <div class="info-field">
                        <div class="info-label">No. Transaksi</div>
                        <div class="info-value">: {{ $journal->transaction_number }}</div>
                    </div>
                    <div class="info-field">
                        <div class="info-label">Tanggal</div>
                        <div class="info-value">: {{ $journal->transaction_date ? $journal->transaction_date->format('d-m-Y') : '-' }}</div>
                    </div>
                </div>
            </div>
            <div class="info-col">
                <div class="info-col-inner">
                    <div class="info-field">
                        <div class="info-label">No. Referensi</div>
                        <div class="info-value">: {{ $journal->reference_number ?? '-' }}</div>
                    </div>
                    <div class="info-field">
                        <div class="info-label">Dibuat Oleh</div>
                        <div class="info-value">: {{ $journal->createdBy->name ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <table class="details-table">
        <thead>
            <tr>
                <th style="width: 20%;">Kode Akun</th>
                <th style="width: 44%;">Nama Akun / Rekening</th>
                <th style="width: 18%; text-align: right;">Debit (Rp)</th>
                <th style="width: 18%; text-align: right;">Kredit (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalDebit = 0;
                $totalCredit = 0;
            @endphp
            @foreach($journal->items as $item)
                @php
                    $totalDebit += (float) $item->debit;
                    $totalCredit += (float) $item->credit;
                @endphp
                <tr>
                    <td>{{ $item->account->code }}</td>
                    <td>{{ $item->account->name }}</td>
                    <td class="number-col">{{ $item->debit > 0 ? number_format($item->debit, 2, ',', '.') : '-' }}</td>
                    <td class="number-col">{{ $item->credit > 0 ? number_format($item->credit, 2, ',', '.') : '-' }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2">TOTAL</td>
                <td class="number-col">{{ number_format($totalDebit, 2, ',', '.') }}</td>
                <td class="number-col">{{ number_format($totalCredit, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="description-box">
        <strong>Uraian / Keterangan Transaksi:</strong>
        <div class="description-content">
            {{ $journal->description }}
        </div>
    </div>

    <div class="signature-section">
        <div class="signature-row">
            <div class="signature-col">
                <div>Disetujui Oleh,</div>
                <div class="signature-space"></div>
                <div class="signature-name">................................................</div>
                <div class="signature-title">Ketua Majelis Jemaat</div>
            </div>
            <div class="signature-col">
                <div>Diperiksa Oleh,</div>
                <div class="signature-space"></div>
                <div class="signature-name">................................................</div>
                <div class="signature-title">Bendahara</div>
            </div>
            <div class="signature-col">
                <div>Dibuat Oleh,</div>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $journal->createdBy->name ?? 'Staff Administrasi' }}</div>
                <div class="signature-title">Operator / Staf</div>
            </div>
        </div>
    </div>

    <script>
        // Auto print window when loaded, if requested
        window.addEventListener('DOMContentLoaded', () => {
            // Optional: window.print();
        });
    </script>
</body>
</html>
