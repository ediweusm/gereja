@php
    function terbilang($angka) {
        $angka = abs($angka);
        $baca = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $terbilang = "";
        if ($angka < 12) {
            $terbilang = " " . $baca[$angka];
        } else if ($angka < 20) {
            $terbilang = terbilang($angka - 10) . " belas";
        } else if ($angka < 100) {
            $terbilang = terbilang(floor($angka / 10)) . " puluh" . terbilang($angka % 10);
        } else if ($angka < 200) {
            $terbilang = " seratus" . terbilang($angka - 100);
        } else if ($angka < 1000) {
            $terbilang = terbilang(floor($angka / 100)) . " ratus" . terbilang($angka % 100);
        } else if ($angka < 2000) {
            $terbilang = " seribu" . terbilang($angka - 1000);
        } else if ($angka < 1000000) {
            $terbilang = terbilang(floor($angka / 1000)) . " ribu" . terbilang($angka % 1000);
        } else if ($angka < 1000000000) {
            $terbilang = terbilang(floor($angka / 1000000)) . " juta" . terbilang($angka % 1000000);
        } else if ($angka < 1000000000000) {
            $terbilang = terbilang(floor($angka / 1000000000)) . " milyar" . terbilang(fmod($angka, 1000000000));
        } else if ($angka < 1000000000000000) {
            $terbilang = terbilang(floor($angka / 1000000000000)) . " trilyun" . terbilang(fmod($angka, 1000000000000));
        }
        return $terbilang;
    }

    $totalNominal = (float) $assistance->amount;
    $terbilangStr = ucwords(trim(terbilang($totalNominal))) . " Rupiah";
    $partyName = $assistance->member ? $assistance->member->fullName : '-';
    
    // Find the cash source account (which is credited in the journal)
    $creditItem = $assistance->journal?->items->first(fn($item) => $item->credit > 0);
    $cashAccountName = $creditItem ? "{$creditItem->account->code} - {$creditItem->account->name}" : '-';
    
    $transactionDate = $assistance->journal?->transaction_date 
        ? $assistance->journal->transaction_date->format('d-m-Y') 
        : now()->format('d-m-Y');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Penyaluran Diakonia - {{ $assistance->journal?->transaction_number ?? 'REC-'.now()->format('YmdHis') }}</title>
    <style>
        @page {
            size: A5 landscape;
            margin: 8mm;
        }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #1f2937;
            font-size: 10pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        .receipt-border {
            border: 2px solid #374151;
            padding: 15px;
            border-radius: 6px;
            position: relative;
            background: #ffffff;
            height: 100%;
            box-sizing: border-box;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #374151;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .church-info {
            width: 55%;
            vertical-align: top;
        }
        .church-info h1 {
            font-size: 11pt;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .church-info h2 {
            font-size: 13pt;
            font-weight: 800;
            margin: 1px 0;
            text-transform: uppercase;
            color: #111827;
        }
        .church-info p {
            font-size: 8pt;
            margin: 3px 0 0 0;
            color: #4b5563;
            line-height: 1.3;
        }
        .logo-cell {
            width: 50px;
            vertical-align: middle;
            padding-right: 10px;
        }
        .logo-cell img {
            height: 40px;
            max-width: 50px;
            display: block;
        }
        .receipt-meta {
            width: 45%;
            text-align: right;
            vertical-align: top;
        }
        .receipt-title {
            font-size: 13pt;
            font-weight: 850;
            color: #dc2626; /* Crimson/red theme for cash out */
            margin: 0 0 4px 0;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .receipt-no {
            font-family: monospace;
            font-size: 9pt;
            font-weight: bold;
            color: #374151;
        }
        /* Details layout */
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 15px;
        }
        .content-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        .content-label {
            width: 130px;
            font-weight: 600;
            color: #4b5563;
        }
        .content-separator {
            width: 15px;
            color: #4b5563;
        }
        .content-value {
            font-weight: 700;
            color: #111827;
        }
        .terbilang-box {
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 8px 12px;
            font-style: italic;
            font-weight: 600;
            color: #374151;
            border-radius: 4px;
            display: inline-block;
            width: 95%;
            box-sizing: border-box;
        }
        /* Bottom row */
        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .amount-box {
            border: 2px solid #dc2626;
            background-color: #fef2f2;
            color: #dc2626;
            font-size: 15pt;
            font-weight: 850;
            padding: 6px 16px;
            border-radius: 4px;
            display: inline-block;
            letter-spacing: 0.5px;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }
        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-title {
            color: #4b5563;
            margin-bottom: 35px;
        }
        .signature-line {
            font-weight: 700;
            text-decoration: underline;
            color: #111827;
        }
        @media print {
            body {
                background-color: #fff;
            }
            .receipt-border {
                border: 2px solid #000;
            }
            .header-table {
                border-bottom: 2px solid #000;
            }
            .terbilang-box {
                border: 1px solid #000;
                background-color: #fff !important;
            }
            .amount-box {
                border: 2px solid #000;
                background-color: #fff !important;
                color: #000;
            }
        }
    </style>
</head>
<body>

<div class="receipt-border">
    <!-- Header -->
    <table class="header-table">
        <tr>
            @if($profile->logo_path)
                <td class="logo-cell">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($profile->logo_path) }}" alt="Logo">
                </td>
            @endif
            <td class="church-info">
                <h1>{{ $profile->gmit_name }}</h1>
                <h2>{{ $profile->church_name }}</h2>
                <p>{{ $profile->address }} | Telp: {{ $profile->phone }}</p>
            </td>
            <td class="receipt-meta">
                <div class="receipt-title">Bukti Penyaluran Diakonia</div>
                <div class="receipt-no">No: {{ $assistance->journal?->transaction_number ?? '-' }}</div>
            </td>
        </tr>
    </table>

    <!-- Main Content -->
    <table class="content-table">
        <tr>
            <td class="content-label">Dibayarkan Kepada</td>
            <td class="content-separator">:</td>
            <td class="content-value" style="font-size: 11pt;">{{ $partyName }}</td>
        </tr>
        <tr>
            <td class="content-label">Uang Sejumlah</td>
            <td class="content-separator">:</td>
            <td>
                <div class="terbilang-box">
                    # {{ $terbilangStr }} #
                </div>
            </td>
        </tr>
        <tr>
            <td class="content-label">Untuk Keperluan</td>
            <td class="content-separator">:</td>
            <td class="content-value">
                <span style="font-weight: 500; color: #111827;">{{ $assistance->journal?->description ?? '-' }}</span>
            </td>
        </tr>
        <tr>
            <td class="content-label">Sumber Dana</td>
            <td class="content-separator">:</td>
            <td class="content-value" style="color: #dc2626; font-size: 9pt;">
                {{ $cashAccountName }}
            </td>
        </tr>
    </table>

    <!-- Footer signatures -->
    <table class="bottom-table">
        <tr>
            <td style="vertical-align: middle; width: 40%;">
                <div class="amount-box">
                    Rp {{ number_format($totalNominal, 2, ',', '.') }}
                </div>
            </td>
            <td style="width: 60%;">
                <table class="signature-table">
                    <tr>
                        <td>
                            <div class="signature-title">Penerima Bantuan,</div>
                            <div class="signature-line" style="margin-top: 35px;">{{ $partyName }}</div>
                        </td>
                        <td>
                            <div class="signature-title">Mengetahui,</div>
                            <div class="signature-line" style="margin-top: 35px;">..................................</div>
                        </td>
                        <td>
                            <div>Tanggal: {{ $transactionDate }}</div>
                            <div class="signature-title">Bendahara,</div>
                            <div class="signature-line" style="margin-top: 35px;">..................................</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
