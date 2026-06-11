<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Jemaat Ulang Tahun Minggu Ini</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm;
        }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #111827;
            font-size: 9.5pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        .container {
            border: 2px solid #1f2937;
            padding: 20px;
            box-sizing: border-box;
        }
        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #1f2937;
            padding-bottom: 8px;
            margin-bottom: 20px;
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
            font-size: 14pt;
            font-weight: 800;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            text-decoration: underline;
        }
        .doc-title p {
            margin: 5px 0 0 0;
            font-size: 9.5pt;
            font-weight: 600;
            color: #4b5563;
        }
        /* Table */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 9.5pt;
        }
        .report-table th {
            background-color: #f3f4f6;
            color: #111827;
            font-weight: 700;
            border: 1px solid #1f2937;
            padding: 8px 6px;
            text-align: center;
            text-transform: uppercase;
            font-size: 8.5pt;
        }
        .report-table td {
            border: 1px solid #1f2937;
            padding: 8px 8px;
            vertical-align: middle;
        }
        .report-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        /* Signatures */
        .signature-section {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signature-section td {
            width: 50%;
            text-align: center;
            vertical-align: top;
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
        @media print {
            body {
                background-color: #ffffff;
            }
            .container {
                border: 2px solid #000000;
            }
            .kop-table {
                border-bottom: 3px double #000000;
            }
            .report-table th, .report-table td {
                border: 1px solid #000000;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Kop Surat -->
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

    <!-- Document Title -->
    <div class="doc-title">
        <h3>DAFTAR JEMAAT ULANG TAHUN MINGGU INI</h3>
        <p>Periode Minggu Ini (Senin s.d. Minggu)</p>
    </div>

    <!-- Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 45%;">Nama Lengkap Jemaat</th>
                <th style="width: 20%;" class="text-center">Tanggal Lahir / Hari Ultah</th>
                <th style="width: 10%;" class="text-center">Usia</th>
                <th style="width: 20%;" class="text-center">Rayon</th>
            </tr>
        </thead>
        <tbody>
            @forelse($members as $member)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td><strong>{{ $member->fullName }}</strong></td>
                    <td class="text-center">
                        @php
                            $formattedDate = '-';
                            if ($member->birth_date) {
                                $start = \Illuminate\Support\Carbon::now()->startOfWeek();
                                for ($i = 0; $i < 7; $i++) {
                                    $date = $start->copy()->addDays($i);
                                    if ($date->month === $member->birth_date->month && $date->day === $member->birth_date->day) {
                                        $formattedDate = $date->translatedFormat('l, d F');
                                        break;
                                    }
                                }
                                if ($formattedDate === '-') {
                                    $formattedDate = $member->birth_date->translatedFormat('l, d F');
                                }
                            }
                        @endphp
                        {{ $formattedDate }}
                    </td>
                    <td class="text-center">
                        {{ $member->birth_date ? (\Illuminate\Support\Carbon::now()->year - $member->birth_date->year) : '-' }} Tahun
                    </td>
                    <td class="text-center">{{ $member->family->rayon->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 15px; color: #4b5563;">
                        Tidak ada jemaat yang berulang tahun minggu ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Section -->
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
</div>

<script>
    // Auto print when page loads
    window.onload = function() {
        window.print();
    }
</script>
</body>
</html>
