<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penerimaan Jemaat - Periode {{ \Illuminate\Support\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Illuminate\Support\Carbon::parse($endDate)->translatedFormat('d F Y') }}</title>
    <style>
        @media print {
            @page { size: portrait; margin: 1cm; }
            body { background: white !important; color: black !important; margin: 0; padding: 0; font-size: 9pt; }
            .print-btn { display: none !important; }
            table { page-break-inside: auto; border-collapse: collapse; width: 100%; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
        }
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; padding: 2rem; max-width: 800px; margin: auto; color: #111827; font-size: 9.5pt; line-height: 1.4; }
        
        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #1f2937;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .kop-table td {
            border: none !important;
            padding: 4px;
        }
        .kop-logo {
            width: 70px;
            vertical-align: middle;
            padding-right: 12px;
        }
        .kop-logo img {
            height: 60px;
            max-width: 70px;
            display: block;
        }
        .kop-text {
            text-align: center;
            vertical-align: middle;
        }
        .kop-text h1 {
            font-size: 13pt;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-text h2 {
            font-size: 15pt;
            font-weight: 800;
            margin: 3px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .kop-text p {
            font-size: 8.5pt;
            margin: 3px 0 0 0;
            color: #4b5563;
        }

        /* Title */
        .doc-title {
            text-align: center;
            margin: 20px 0;
        }
        .doc-title h3 {
            font-size: 13pt;
            font-weight: 800;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .doc-title p {
            margin: 5px 0 0 0;
            font-size: 9pt;
            font-weight: 600;
            color: #4b5563;
        }

        .print-btn {
            display: inline-block;
            background-color: #0284c7;
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
        .print-btn:hover {
            background-color: #0369a1;
        }

        /* table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        table, th, td { border: 1px solid #333; } */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .data-table, .data-table th, .data-table td { border: 1px solid #333; }
        th, td { padding: 6px 8px; font-size: 9pt; text-align: left; }
        th { background-color: #f3f4f6; font-weight: 700; text-transform: uppercase; font-size: 8.5pt; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* Signatures */
        .signature-section {
            width: 100%;
            /* border-collapse: collapse; */
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signature-section td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            border: none !important;
            font-size: 9pt;
        }
        .sig-space {
            height: 70px;
        }
        .sig-name {
            font-weight: 700;
            text-decoration: underline;
        }
        .sig-title {
            font-size: 8.5pt;
            color: #4b5563;
            margin-top: 3px;
        }
    </style>
</head>
<body>

    <table class="kop-table">
        <tr>
            @if($profile->logo_path)
                <td class="kop-logo">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($profile->logo_path) }}" alt="Logo">
                </td>
            @endif
            <td class="kop-text">
                <h1>{{ $profile->gmit_name }}</h1>
                <h2>{{ $profile->church_name }}</h2>
                <p>{{ $profile->address }} | Telp: {{ $profile->phone }}</p>
            </td>
        </tr>
    </table>

    <div style="text-align: right; margin-bottom: 10px;">
        <button class="print-btn" onclick="window.print()">Cetak PDF</button>
    </div>

    <div class="doc-title">
        <h3>LAPORAN PENERIMAAN JEMAAT</h3>
        <p>Periode: {{ \Illuminate\Support\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Illuminate\Support\Carbon::parse($endDate)->translatedFormat('d F Y') }}</p>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 25%;">Nama Lengkap</th>
                <th style="width: 15%;" class="text-center">Tanggal Penerimaan</th>
                <th style="width: 15%;" class="text-center">Jumlah Uang</th>
                <th style="width: 25%;">Alamat / Rayon</th>
                <th style="width: 15%;" class="text-center">Keterangan / Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contributions as $contribution)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td><strong>{{ $contribution->member->fullName ?? '-' }}</strong></td>
                    <td class="text-center">{{ $contribution->created_at->translatedFormat('d M Y') }}</td>
                    <td class="text-right">Rp {{ number_format($contribution->amount, 0, ',', '.') }}</td>
                    <td>
                        {{ $contribution->member->family->address ?? '-' }} 
                        @if(isset($contribution->member->family->rayon))
                            <br><small style="color: #4b5563;">Rayon: {{ $contribution->member->family->rayon->name }}</small>
                        @endif
                    </td>
                    <td class="text-center">
                        {{ $contribution->contributionType->label ?? 'Kontribusi' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 15px; color: #4b5563;">
                        Tidak ada penerimaan jemaat pada periode tersebut.
                    </td>
                </tr>
            @endforelse
        </tbody>
        
        @if($contributions->isNotEmpty())
        <tfoot>
            <tr>
                <th colspan="3" class="text-right" style="padding-right: 15px; font-weight: 800;">TOTAL JUMLAH KESELURUHAN</th>
                <th class="text-right" style="font-weight: 800;">Rp {{ number_format($contributions->sum('amount'), 0, ',', '.') }}</th>
                <th colspan="2"></th>
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
                <div class="sig-name">{{ $profile->ketua_majelis ?? 'Pdt. Sion Oepura, S.Th' }}</div>
            </td>
            <td>
                <div>Kupang, {{ \Illuminate\Support\Carbon::now()->format('d F Y') }}</div>
                <div class="sig-title">Sekretaris Majelis Jemaat</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ $profile->sekretaris ?? 'Penatua Sekretaris' }}</div>
            </td>
        </tr>
    </table>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>