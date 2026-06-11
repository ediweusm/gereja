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
        $isReceipt = false;
        $title = "BUKTI PENGELUARAN KAS";
        $partyLabel = "Dibayarkan Kepada";
        $themeColor = '#d97706'; // Amber/Orange
    } else {
        $isReceipt = true;
        $title = "BUKTI PENERIMAAN KAS";
        $partyLabel = "Diterima Dari";
        $themeColor = '#059669'; // Green
    }

    $totalNominal = (float) $journal->total_nominal;
    $terbilangStr = ucwords(trim(terbilang($totalNominal))) . " Rupiah";

    // Cek apakah ada relasi kontribusi anggota
    $contribution = $journal->contributions->first() ?? null;
    $member = $contribution ? $contribution->member : null;
    if ($member) {
        $partyName = collect([$member->first_name, $member->middle_name, $member->last_name])->filter()->implode(' ');
    } else {
        $partyName = '.......................................................................';
    }

    // Klasifikasi item berdasarkan visual hierarchy
    $prominentItems = [];
    $secondaryItems = [];
    foreach ($journal->items as $item) {
        $isCashAccount = str_starts_with($item->account->code, '111');
        if ($isReceipt) {
            // Penerimaan: yang KREDIT (pendapatan) menonjol, yang DEBIT (kas) sekunder
            if ($item->credit > 0) {
                $prominentItems[] = $item;
            } else {
                $secondaryItems[] = $item;
            }
        } else {
            // Pengeluaran: yang DEBIT (biaya) menonjol, yang KREDIT (kas) sekunder
            if ($item->debit > 0) {
                $prominentItems[] = $item;
            } else {
                $secondaryItems[] = $item;
            }
        }
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi - {{ $journal->transaction_number }}</title>
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
            margin-bottom: 15px;
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
            margin: 0;
            color: #4b5563;
        }
        .title-info {
            width: 45%;
            text-align: right;
            vertical-align: top;
        }
        .title-info h3 {
            font-size: 12pt;
            font-weight: 800;
            margin: 0;
            color: {{ $themeColor }};
            letter-spacing: 1px;
        }
        .title-info .doc-number {
            font-size: 10pt;
            font-weight: 700;
            font-family: monospace;
            margin-top: 4px;
        }
        .body-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .body-table td {
            padding: 6px 4px;
            vertical-align: top;
        }
        .label-cell {
            width: 20%;
            font-weight: 600;
            color: #4b5563;
        }
        .value-cell {
            width: 80%;
            font-weight: 700;
        }
        .dots-separator {
            color: #9ca3af;
            font-weight: normal;
        }
        .spelled-out-box {
            border: 1px dashed #9ca3af;
            background-color: #f9fafb;
            padding: 6px 10px;
            font-style: italic;
            border-radius: 4px;
            color: #111827;
            font-weight: 600;
            display: inline-block;
            width: 95%;
        }
        
        /* Hierarchy Styling */
        .hierarchy-box {
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 12px;
            background-color: #fafafa;
        }
        .hierarchy-title {
            font-size: 7.5pt;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }
        .prominent-item {
            font-size: 10.5pt;
            font-weight: 700;
            color: #111827;
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .prominent-item:last-child {
            border-bottom: none;
        }
        .secondary-item {
            font-size: 8pt;
            font-weight: 500;
            color: #6b7280;
            margin-top: 4px;
            padding-left: 10px;
            display: flex;
            justify-content: space-between;
        }
        
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .amount-box-cell {
            width: 40%;
            vertical-align: middle;
        }
        .amount-box {
            border: 2px solid {{ $themeColor }};
            background-color: {{ $themeColor }}0c;
            color: {{ $themeColor }};
            font-size: 14pt;
            font-weight: 800;
            padding: 8px 15px;
            display: inline-block;
            border-radius: 4px;
            font-family: monospace;
            letter-spacing: 0.5px;
        }
        .signatures-cell {
            width: 60%;
            text-align: right;
            vertical-align: top;
        }
        .signature-container {
            display: inline-table;
            width: 100%;
        }
        .signature-col {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .sig-space {
            height: 45px;
        }
        .sig-line {
            font-weight: 700;
            text-decoration: underline;
        }
        .sig-title {
            font-size: 7.5pt;
            color: #6b7280;
            margin-top: 2px;
        }
    </style>
</head>
<body>

<div class="receipt-border">
    <table class="header-table">
        <tr>
            @if($profile->logo_path)
                <td style="width: 65px; padding: 0 10px 0 0; vertical-align: middle;">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($profile->logo_path) }}" alt="Logo" style="height: 50px; max-width: 65px; display: block;">
                </td>
            @endif
            <td class="church-info" style="vertical-align: middle;">
                <h1>{{ $profile->gmit_name }}</h1>
                <h2>{{ $profile->church_name }}</h2>
                <p>{{ $profile->address }} | Telp: {{ $profile->phone }}</p>
            </td>
            <td class="title-info" style="vertical-align: middle;">
                <h3>{{ $title }}</h3>
                <div class="doc-number">No. {{ $journal->transaction_number }}</div>
            </td>
        </tr>
    </table>

    <table class="body-table">
        <tr>
            <td class="label-cell">{{ $partyLabel }}</td>
            <td class="value-cell"><span class="dots-separator">:</span> {{ $partyName }}</td>
        </tr>
        <tr>
            <td class="label-cell">Uang Sejumlah</td>
            <td class="value-cell">
                <span class="dots-separator">:</span>
                <div class="spelled-out-box">
                    *** {{ $terbilangStr }} ***
                </div>
            </td>
        </tr>
        <tr>
            <td class="label-cell">Untuk Pembayaran</td>
            <td class="value-cell"><span class="dots-separator">:</span> {{ $journal->description }}</td>
        </tr>
    </table>

    <div class="hierarchy-box">
        <div class="hierarchy-title">Detail Akun & Rekening (Visual Hierarchy)</div>
        
        <!-- Pihak Akun Lawan / Transaksi Utama (Menonjol / Font Besar) -->
        @foreach($prominentItems as $item)
            <div class="prominent-item">
                <span>{{ $item->account->code }} - {{ $item->account->name }}</span>
                <span>Rp {{ number_format($isReceipt ? $item->credit : $item->debit, 2, ',', '.') }}</span>
            </div>
        @endforeach

        <!-- Sumber/Tujuan Kas (Font Kecil di Bawahnya) -->
        @foreach($secondaryItems as $item)
            <div class="secondary-item">
                <span>[Sumber Kas/Bank] &nbsp;&raquo;&nbsp; {{ $item->account->code }} - {{ $item->account->name }}</span>
                <span>Rp {{ number_format($isReceipt ? $item->debit : $item->credit, 2, ',', '.') }}</span>
            </div>
        @endforeach
    </div>

    <table class="footer-table">
        <tr>
            <td class="amount-box-cell">
                <div class="amount-box">
                    Rp {{ number_format($totalNominal, 2, ',', '.') }}
                </div>
            </td>
            <td class="signatures-cell">
                <div class="signature-container">
                    <div class="signature-col">
                        <div style="font-size: 8.5pt; color: #4b5563; font-weight: 600;">Penyerah Uang,</div>
                        <div class="sig-space"></div>
                        <div class="sig-line">................................................</div>
                        <div class="sig-title">Gereja / Jemaat</div>
                    </div>
                    <div class="signature-col">
                        <div style="font-size: 8.5pt; color: #4b5563; font-weight: 600;">Penerima Uang,</div>
                        <div class="sig-space"></div>
                        <div class="sig-line">{{ $journal->createdBy->name ?? 'Bendahara/Staf' }}</div>
                        <div class="sig-title">Operator Keuangan</div>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
