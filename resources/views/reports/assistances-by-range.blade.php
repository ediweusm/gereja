<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penyaluran Diakonia - Periode {{ \Illuminate\Support\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Illuminate\Support\Carbon::parse($endDate)->translatedFormat('d F Y') }}</title>
    <style>
        @media print {
            @page { size: portrait; margin: 1cm; }
            body { background: white !important; color: black !important; margin: 0; padding: 0; }
            .print-btn { display: none !important; }
            .data-table { page-break-inside: auto; border-collapse: collapse; width: 100%; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
        }
        
        /* Menggunakan font Times New Roman */
        body { font-family: 'Times New Roman', Times, serif; padding: 2rem; max-width: 900px; margin: auto; font-size: 10pt; color: #111827; }

        .print-btn {
            display: inline-block;
            background-color: #090494ff; /* Warna merah senada dengan tema tombol Filament */
            color: white;
            padding: 8px 16px;
            font-weight: bold;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 20px;
            font-size: 10pt;
            text-transform: uppercase;
        }
        .print-btn:hover { background-color: #dc2626; }

        /* Title */
        .doc-title { text-align: center; margin: 20px 0 30px 0; }
        .doc-title h3 { font-size: 13pt; font-weight: bold; margin: 0; text-transform: uppercase; letter-spacing: 1px; }

        /* Data Table */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .data-table, .data-table th, .data-table td { border: 1px solid #333; }
        .data-table th, .data-table td { padding: 6px 8px; text-align: left; vertical-align: middle; }
        .data-table th { background-color: #f3f4f6; font-weight: bold; text-transform: uppercase; font-size: 9pt; text-align: center; }
        .data-table tfoot th { background-color: #f3f4f6; font-weight: bold; }
        
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }

        /* Signatures */
        .signature-section { width: 100%; margin-top: 40px; page-break-inside: avoid; border-collapse: collapse; border: none; }
        .signature-section td { width: 50%; text-align: center; vertical-align: top; border: none !important; font-size: 9.5pt; padding: 0; }
        .sig-space { height: 70px; }
        .sig-name { font-weight: bold; text-decoration: underline; }
        .sig-title { font-size: 9pt; color: #4b5563; margin-top: 3px; font-weight: bold; }
    </style>
</head>
<body>

    <table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 20px; border-bottom: 1px double #333; padding-bottom: 20px;">
        <tr>
            @if($profile?->logo_path)
                <td style="width: 80px; border: none; padding: 0 0 5px 0; vertical-align: middle;">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($profile->logo_path) }}" alt="Logo" style="height: 65px; max-width: 80px; display: block;">
                </td>
            @endif
            <td style="border: none; padding: 0 0 5px 0; vertical-align: middle; text-align: left;">
                <h1 style="margin: 0; font-size: 10pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">{{ $profile?->gmit_name }}</h1>
                <h2 style="margin: 2px 0 2px; font-size: 14pt; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">{{ $profile?->church_name }}</h2>
                <p style="margin: 2px 0 2px; font-size: 8.5pt; color: #4b5563;">{{ $profile?->address }} @if($profile?->phone)| Telp: {{ $profile->phone }}@endif</p>
            </td>
        </tr>
    </table>

    <div style="text-align: right; margin-bottom: 10px;">
        <button class="print-btn" onclick="window.print()">Cetak PDF</button>
    </div>

    <div class="doc-title">
        <h3>LAPORAN PENYALURAN DIAKONIA</h3>
        <p style="margin-top: 5px; font-size: 9.5pt;">Periode: {{ \Illuminate\Support\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Illuminate\Support\Carbon::parse($endDate)->translatedFormat('d F Y') }}</p>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 15%;" class="text-center">Tanggal</th>
                <th style="width: 35%;">Nama Penerima (Jemaat)</th>
                <th style="width: 25%;">Keterangan Bantuan</th>
                <th style="width: 20%;" class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assistances as $assistance)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ \Illuminate\Support\Carbon::parse($assistance->transaction_date ?? $assistance->created_at)->translatedFormat('d/m/Y') }}</td>
                    <td><strong>{{ $assistance->member->fullName ?? '-' }}</strong></td>
                    
                    {{-- Mengambil deskripsi dari jurnal jika ada, atau bisa juga menambahkan kolom description langsung di tabel MemberAssistance --}}
                    <td>{{ $assistance->journal->description ?? 'Penyaluran Bantuan Diakonia' }}</td>
                    
                    <td class="text-right">{{ number_format($assistance->amount, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 15px; color: #4b5563; font-style: italic;">
                        Tidak ada transaksi penyaluran diakonia pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        
        @if($assistances->isNotEmpty())
        <tfoot>
            <tr>
                <th colspan="4" class="text-right" style="padding-right: 15px; font-size: 10.5pt; letter-spacing: 0.5px;">TOTAL KESELURUHAN</th>
                <th class="text-right" style="font-size: 10.5pt;">{{ number_format($assistances->sum('amount'), 2, ',', '.') }}</th>
            </tr>
        </tfoot>
        @endif
    </table>

    <table class="signature-section">
        <tr>
            <td>
                <div>Mengetahui,</div>
                <div class="sig-title">Ketua Majelis Jemaat</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ $profile?->ketua_majelis ?? 'Pdt. Sion Oepura, S.Th' }}</div>
            </td>
            <td>
                <div>Semarang, {{ \Illuminate\Support\Carbon::now()->translatedFormat('d F Y') }}</div>
                <div class="sig-title">Bendahara Diakonia</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ $profile?->bendahara ?? 'Penatua Bendahara' }}</div>
            </td>
        </tr>
    </table>

    <script>
        window.onload = function() {
            // Uncomment baris di bawah ini jika ingin langsung memunculkan dialog print otomatis
            // window.print();
        }
    </script>
</body>
</html>