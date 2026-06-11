<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Mutasi Jemaat - Periode {{ \Illuminate\Support\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Illuminate\Support\Carbon::parse($endDate)->translatedFormat('d F Y') }}</title>
    <style>
        @media print {
            @page { size: landscape; margin: 1cm; }
            body { background: white !important; color: black !important; margin: 0; padding: 0; }
            .print-btn { display: none !important; }
            .data-table { page-break-inside: auto; border-collapse: collapse; width: 100%; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
        }
        body { font-family: sans-serif; padding: 2rem; max-width: 1000px; margin: auto; font-size: 10pt; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .data-table, .data-table th, .data-table td { border: 1px solid #333; }
        .data-table th, .data-table td { padding: 6px 8px; text-align: left; }
        .data-table th { background-color: #f3f4f6; font-weight: bold; text-transform: uppercase; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

    <table style="width: 100%; border-collapse: collapse; border-bottom: 3px double #333; padding-bottom: 8px; margin-bottom: 20px;">
        <tr>
            @if($profile->logo_path)
                <td style="width: 70px; vertical-align: middle; padding-right: 12px; border: none !important;">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($profile->logo_path) }}" alt="Logo" style="height: 60px; max-width: 70px; display: block;">
                </td>
            @endif
            <td style="text-align: center; vertical-align: middle; border: none !important;">
                <h1 style="font-size: 13pt; font-weight: 700; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">{{ $profile->gmit_name }}</h1>
                <h2 style="font-size: 15pt; font-weight: 800; margin: 3px 0; text-transform: uppercase; letter-spacing: 1px;">{{ $profile->church_name }}</h2>
                <p style="font-size: 8.5pt; margin: 3px 0 0 0; color: #4b5563;">{{ $profile->address }} | Telp: {{ $profile->phone }}</p>
            </td>
        </tr>
    </table>

    <div style="text-align: right; margin-bottom: 10px;">
        <button class="print-btn" onclick="window.print()" style="display: inline-block; background-color: #0284c7; color: white; padding: 8px 16px; font-weight: bold; border: none; border-radius: 4px; cursor: pointer; font-size: 10pt; text-transform: uppercase;">Cetak PDF</button>
    </div>

    <div style="text-align: center; margin: 20px 0;">
        <h3 style="font-size: 13pt; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 1.5px;">LAPORAN MUTASI JEMAAT (MASUK & KELUAR)</h3>
        <p style="margin: 5px 0 0 0; font-size: 9pt; font-weight: 600; color: #4b5563;">Periode: {{ \Illuminate\Support\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Illuminate\Support\Carbon::parse($endDate)->translatedFormat('d F Y') }}</p>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;" class="text-center">No</th>
                <th style="width: 10%;" class="text-center">Tanggal</th>
                <th style="width: 25%;">Nama Jemaat</th>
                <th style="width: 15%;">Jenis Mutasi</th>
                <th style="width: 25%;">Keterangan (Asal/Tujuan)</th>
                <th style="width: 22%;">Alasan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mutations as $mutation)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ $mutation->mutation_date?->translatedFormat('d M Y') }}</td>
                    <td><strong>{{ $mutation->member->fullName ?? '-' }}</strong></td>
                    <td>{{ $mutation->mutation_type }}</td>
                    <td>
                        @if($mutation->mutation_type === 'Atestasi Masuk' || $mutation->mutation_type === 'Titipan')
                            Asal: {{ $mutation->origin_church ?? '-' }}
                        @elseif($mutation->mutation_type === 'Atestasi Keluar')
                            Tujuan: {{ $mutation->destination_church ?? '-' }}
                        @elseif($mutation->mutation_type === 'Pindah Rayon')
                            Rayon: {{ $mutation->oldRayon?->name ?? '-' }} &rarr; {{ $mutation->newRayon?->name ?? '-' }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $mutation->reason ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 15px; color: #4b5563;">
                        Tidak ada data mutasi jemaat pada periode tersebut.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table style="width: 100%; margin-top: 40px; page-break-inside: avoid; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; text-align: center; vertical-align: top; border: none !important; font-size: 10pt;">
                <div>Mengetahui,</div>
                <div style="font-size: 9.5pt; color: #4b5563; margin-top: 3px;">Ketua Majelis Jemaat</div>
                <div style="height: 70px;"></div>
                <div style="font-weight: 700; text-decoration: underline;">{{ $profile->ketua_majelis ?? 'Pdt. Sion Oepura, S.Th' }}</div>
            </td>
            <td style="width: 50%; text-align: center; vertical-align: top; border: none !important; font-size: 10pt;">
                <div>Kupang, {{ \Illuminate\Support\Carbon::now()->translatedFormat('d F Y') }}</div>
                <div style="font-size: 9.5pt; color: #4b5563; margin-top: 3px;">Sekretaris Majelis Jemaat</div>
                <div style="height: 70px;"></div>
                <div style="font-weight: 700; text-decoration: underline;">{{ $profile->sekretaris ?? 'Penatua Sekretaris' }}</div>
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
