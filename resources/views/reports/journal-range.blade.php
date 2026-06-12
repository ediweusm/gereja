<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Jurnal Umum</title>
    <style>
        @media print {
            @page { size: landscape; margin: 1cm; }
            body { background: white !important; color: black !important; margin: 0; padding: 0; }
            .print-btn { display: none !important; }
            .data-table { page-break-inside: auto; border-collapse: collapse; width: 100%; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
        }
        body { font-family: 'Times New Roman', Times, serif; padding: 2rem; max-width: 900px; margin: auto; font-size: 9.5pt; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .data-table, .data-table th, .data-table td { border: 1px solid #333; }
        .data-table th, .data-table td { padding: 6px 8px; text-align: left; vertical-align: top; }
        .data-table th { background-color: #f3f4f6; font-weight: bold; text-transform: uppercase; font-size: 8.5pt; text-align: center; }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>

<!-- Kop Surat -->
<table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 20px; border-bottom: 1px double #333; padding-bottom: 20px;">
    <tr>
        @if($profile->logo_path)
            <td style="width: 80px; border: none; padding: 0 0 5px 0; vertical-align: middle;">
                <img src="{{ \Illuminate\Support\Facades\Storage::url($profile->logo_path) }}" alt="Logo" style="height: 65px; max-width: 80px; display: block;">
            </td>
        @endif
        <td style="border: none; padding: 0 0 5px 0; vertical-align: middle; text-align: left;">
            <h1 style="margin: 0; font-size: 10pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">{{ $profile->gmit_name }}</h1>
            <h2 style="margin: 2px 0 2px; font-size: 14pt; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">{{ $profile->church_name }}</h2>
            <p style="margin: 2px 0 2px; font-size: 8.5pt; color: #4b5563;">{{ $profile->address }} | Telp: {{ $profile->phone }}</p>
        </td>
    </tr>
</table>

<!-- Tombol Cetak -->
<button class="print-btn" onclick="window.print()" style="margin-bottom: 20px; padding: 8px 16px; background-color: #10b981; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Cetak PDF</button>

<!-- Judul Dokumen -->
<div class="text-center" style="margin: 15px 0;">
    <h3 style="font-size: 13pt; font-weight: bold; margin: 0; text-transform: uppercase; letter-spacing: 1px;">LAPORAN JURNAL UMUM</h3>
    <p style="margin: 5px 0 0 0; font-size: 10pt; font-weight: bold; color: #4b5563;">
        Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s.d. {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
    </p>
</div>

<!-- Tabel Laporan Jurnal -->
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 9%;">Tanggal</th>
            <th style="width: 13%;">No.Bukti</th>            
            <th style="width: 18%;">Akun</th>
            <th style="width: 15%;">Debit (Rp)</th>
            <th style="width: 15%;">Kredit (Rp)</th>
            <th style="width: 30%;">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @php
            $grandTotalDebit = 0;
            $grandTotalCredit = 0;
        @endphp
        @forelse($journals as $journal)
            @foreach($journal->items as $index => $item)
                @php
                    $grandTotalDebit += $item->debit;
                    $grandTotalCredit += $item->credit;
                @endphp
                <tr>
                    @if($index === 0)
                        <td class="text-center" rowspan="{{ $journal->items->count() }}">
                            {{ $journal->transaction_date ? $journal->transaction_date->format('d-m-Y') : '-' }}
                        </td>
                        <td rowspan="{{ $journal->items->count() }}">
                            {{ $journal->transaction_number }}
                        </td>                        
                    @endif
                    <td>
                        {{ $item->account->code }} - {{ $item->account->name }}
                    </td>
                    <td class="text-right">
                        {{ $item->debit > 0 ? 'Rp ' . number_format($item->debit, 2, ',', '.') : '-' }}
                    </td>
                    <td class="text-right">
                        {{ $item->credit > 0 ? 'Rp ' . number_format($item->credit, 2, ',', '.') : '-' }}
                    </td>
                    @if($index === 0)                        
                        <td rowspan="{{ $journal->items->count() }}">
                            {{ $journal->description ?? '-' }}
                        </td>
                    @endif
                </tr>
            @endforeach
        @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 15px; color: #4b5563;">
                    Tidak ada transaksi jurnal pada periode ini.
                </td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" class="text-right font-bold">TOTAL KESELURUHAN</th>
            <th class="text-right font-bold" style="font-size: 10pt;">Rp {{ number_format($grandTotalDebit, 2, ',', '.') }}</th>
            <th class="text-right font-bold" style="font-size: 10pt;">Rp {{ number_format($grandTotalCredit, 2, ',', '.') }}</th>
        </tr>
    </tfoot>
</table>

<!-- Tanda Tangan -->
<table style="width: 100%; margin-top: 40px; border-collapse: collapse; border: none; page-break-inside: avoid;">
    <tr>
        <td style="width: 50%; border: none; text-align: center; vertical-align: top; padding: 0;">
            <div>Mengetahui,</div>
            <div style="font-size: 8.5pt; color: #4b5563; margin-top: 3px; font-weight: bold;">Ketua Majelis Jemaat</div>
            <div style="height: 70px;"></div>
            <div style="font-weight: bold; text-decoration: underline;">{{ $profile->ketua_majelis ?? 'Pdt. Sion Oepura, S.Th' }}</div>
        </td>
        <td style="width: 50%; border: none; text-align: center; vertical-align: top; padding: 0;">
            <div>Kupang, {{ \Illuminate\Support\Carbon::now()->translatedFormat('d F Y') }}</div>
            <div style="font-size: 8.5pt; color: #4b5563; margin-top: 3px; font-weight: bold;">Bendahara</div>
            <div style="height: 70px;"></div>
            <div style="font-weight: bold; text-decoration: underline;">{{ $profile->bendahara ?? 'Bertha S. Djahi' }}</div>
        </td>
    </tr>
</table>

<script>
    window.onload = function() {
        window.print();
    }
</script>
</body>
</html>
