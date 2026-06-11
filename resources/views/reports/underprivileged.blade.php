<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Keluarga Pra Sejahtera</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 12mm;
        }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #111827;
            font-size: 9pt;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        .container {
            border: 2px solid #1f2937;
            padding: 15px;
            box-sizing: border-box;
        }
        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #1f2937;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .kop-logo {
            width: 70px;
            vertical-align: middle;
            padding-right: 12px;
        }
        .kop-logo img {
            height: 50px;
            max-width: 70px;
            display: block;
        }
        .kop-text {
            text-align: center;
            vertical-align: middle;
        }
        .kop-text h1 {
            font-size: 11.5pt;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-text h2 {
            font-size: 13.5pt;
            font-weight: 800;
            margin: 2px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .kop-text p {
            font-size: 8pt;
            margin: 2px 0 0 0;
            color: #4b5563;
        }
        /* Title */
        .doc-title {
            text-align: center;
            margin: 15px 0;
        }
        .doc-title h3 {
            font-size: 13pt;
            font-weight: 800;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            text-decoration: underline;
        }
        .doc-title p {
            margin: 4px 0 0 0;
            font-size: 9pt;
            font-weight: 600;
            color: #4b5563;
        }
        /* Table */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9pt;
        }
        .report-table th {
            background-color: #f3f4f6;
            color: #111827;
            font-weight: 700;
            border: 1px solid #1f2937;
            padding: 6px 5px;
            text-align: center;
            text-transform: uppercase;
            font-size: 8pt;
        }
        .report-table td {
            border: 1px solid #1f2937;
            padding: 6px 6px;
            vertical-align: middle;
        }
        .group-header {
            background-color: #e5e7eb;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 8.5pt;
            color: #1f2937;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 7.5pt;
            border: 1px solid #d1d5db;
            background-color: #f3f4f6;
        }
        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .text-center {
            text-align: center;
        }
        /* Signatures */
        .signature-section {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature-section td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .sig-space {
            height: 50px;
        }
        .sig-name {
            font-weight: 700;
            text-decoration: underline;
        }
        .sig-title {
            font-size: 8pt;
            color: #4b5563;
            margin-top: 2px;
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
            .group-header {
                background-color: #d1d5db !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .badge {
                border: 1px solid #000000;
                background-color: #ffffff;
                color: #000000;
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
        <h3>DAFTAR KELUARGA PRA SEJAHTERA</h3>
        <p>Keluarga yang Membutuhkan Bantuan Sosial (Kondisi Rumah Darurat / Semi Permanen)</p>
    </div>

    <!-- Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">No</th>
                <th style="width: 15%;">No KK</th>
                <th style="width: 25%;">Nama Kepala Keluarga</th>
                <th style="width: 26%;">Alamat Lengkap</th>
                <th style="width: 10%;" class="text-center">Rayon</th>
                <th style="width: 10%;" class="text-center">Status Rumah</th>
                <th style="width: 10%;" class="text-center">Kondisi Rumah</th>
            </tr>
        </thead>
        <tbody>
            @php
                $groupedFamilies = $families->groupBy(fn($f) => $f->rayon->name ?? 'Tanpa Rayon');
                $globalIndex = 1;
            @endphp
            @forelse($groupedFamilies as $rayonName => $familyList)
                <tr class="group-header">
                    <td colspan="7">RAYON: {{ $rayonName }}</td>
                </tr>
                @foreach($familyList as $family)
                    @php
                        $head = $family->members->first(function ($member) {
                            return $member->familyPosition?->code === 'suami';
                        });
                        $headName = $head ? $head->fullName : '-';
                    @endphp
                    <tr>
                        <td class="text-center">{{ $globalIndex++ }}</td>
                        <td><strong>{{ $family->family_number }}</strong></td>
                        <td>{{ $headName }}</td>
                        <td>{{ $family->address }}</td>
                        <td class="text-center">{{ $family->rayon->name ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge">{{ $family->houseStatus?->label ?? '-' }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-warning">{{ $family->houseCategory?->label ?? '-' }}</span>
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 15px; color: #4b5563;">
                        Tidak ada data keluarga pra sejahtera yang tercatat.
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
                <div class="sig-title">Bendahara Diakonia</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ $profile->bendahara ?? 'Penatua Bendahara' }}</div>
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
