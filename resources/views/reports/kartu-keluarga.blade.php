@php
    // Sort members by family position's sort order (Suami first, then Istri, Anak, etc.)
    $members = $family->members->sortBy(fn($m) => $m->familyPosition?->sort_order ?? 99);
    
    // Find the head of family
    $headOfFamily = $members->first(fn($m) => $m->familyPosition?->code === 'suami') ?? $members->first();
    $headOfFamilyName = $headOfFamily ? $headOfFamily->fullName : '-';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Keluarga Jemaat - {{ $family->family_number }}</title>
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
            border-radius: 6px;
            box-sizing: border-box;
            min-height: 100%;
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
            height: 55px;
            max-width: 70px;
            display: block;
        }
        .kop-text {
            text-align: center;
            vertical-align: middle;
        }
        .kop-text h1 {
            font-size: 12pt;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-text h2 {
            font-size: 14pt;
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
            margin: 10px 0;
        }
        .doc-title h3 {
            font-size: 14pt;
            font-weight: 800;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-decoration: underline;
        }
        .doc-title p {
            margin: 2px 0 0 0;
            font-size: 9.5pt;
            font-weight: 600;
            color: #374151;
        }
        /* Info Grid */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 3px 5px;
            vertical-align: top;
        }
        .info-label {
            font-weight: 600;
            width: 180px;
            color: #374151;
            font-size: 8.5pt;
        }
        .info-separator {
            width: 10px;
            text-align: center;
            font-size: 8.5pt;
        }
        .info-value {
            font-weight: 700;
            font-size: 8.5pt;
        }
        /* Table Section Header */
        .table-section-title {
            font-size: 8.5pt;
            font-weight: 800;
            color: #1f2937;
            margin: 10px 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        /* Details Table */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8pt;
        }
        .details-table th {
            background-color: #f3f4f6;
            color: #1f2937;
            font-weight: 700;
            border: 1px solid #1f2937;
            padding: 5px 4px;
            text-align: center;
            text-transform: uppercase;
            font-size: 7.5pt;
        }
        .details-table td {
            border: 1px solid #1f2937;
            padding: 5px 4px;
            vertical-align: middle;
        }
        .details-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-center {
            text-align: center;
        }
        .nik-text {
            display: block;
            font-size: 7pt;
            color: #4b5563;
            margin-top: 1px;
            font-family: monospace;
        }
        /* Footer / Signatures */
        .footer-section {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .footer-section td {
            vertical-align: top;
            width: 33.33%;
        }
        .sig-container {
            text-align: center;
            font-size: 8.5pt;
        }
        .sig-space {
            height: 55px;
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
                background-color: #fff;
            }
            .container {
                border: 2px solid #000;
            }
            .kop-table {
                border-bottom: 3px double #000;
            }
            .details-table th, .details-table td {
                border: 1px solid #000;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Kop Surat / Letterhead -->
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
        <h3>KARTU KELUARGA JEMAAT</h3>
        <p>No. KK: {{ $family->family_number }}</p>
    </div>

    <!-- Family Info Header -->
    <table class="info-table">
        <tr>
            <td class="info-label">Nama Kepala Keluarga</td>
            <td class="info-separator">:</td>
            <td class="info-value">{{ $headOfFamilyName }}</td>
            
            <td class="info-label" style="padding-left: 40px;">Rayon Pelayanan</td>
            <td class="info-separator">:</td>
            <td class="info-value">{{ $family->rayon->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Alamat Tinggal</td>
            <td class="info-separator">:</td>
            <td class="info-value">{{ $family->address }}</td>
            
            <td class="info-label" style="padding-left: 40px;">Status Kepemilikan Rumah</td>
            <td class="info-separator">:</td>
            <td class="info-value">{{ $family->houseStatus?->label ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">No. Telepon / HP</td>
            <td class="info-separator">:</td>
            <td class="info-value">{{ $family->phone ?? '-' }}</td>
            
            <td class="info-label" style="padding-left: 40px;">Kondisi / Kategori Rumah</td>
            <td class="info-separator">:</td>
            <td class="info-value">{{ $family->houseCategory?->label ?? '-' }}</td>
        </tr>
    </table>

    <!-- Tabel I: DATA DEMOGRAFI & HUBUNGAN KELUARGA -->
    <div class="table-section-title">I. Data Demografi & Hubungan Keluarga</div>
    <table class="details-table">
        <thead>
            <tr>
                <th style="width: 3%;" class="text-center">No</th>
                <th style="width: 25%;">Nama Lengkap</th>
                <th style="width: 5%;" class="text-center">L/P</th>
                <th style="width: 17%;">Tempat / Tanggal Lahir</th>
                <th style="width: 12%;" class="text-center">Hubungan Keluarga</th>
                <th style="width: 11%;">Pendidikan</th>
                <th style="width: 11%;">Pekerjaan</th>
                <th style="width: 8%;" class="text-center">Status Nikah</th>
                <th style="width: 8%;" class="text-center">Status Jemaat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($members as $member)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <strong>{{ $member->fullName }}</strong>
                        @if($member->nik)
                            <span class="nik-text">NIK: {{ $member->nik }}</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $member->gender }}</td>
                    <td>
                        {{ $member->birth_place ?? '-' }}, 
                        {{ $member->birth_date ? $member->birth_date->format('d-m-Y') : '-' }}
                    </td>
                    <td class="text-center">{{ $member->familyPosition?->label ?? '-' }}</td>
                    <td>{{ $member->education?->label ?? '-' }}</td>
                    <td>{{ $member->occupation?->label ?? '-' }}</td>
                    <td class="text-center">{{ $member->maritalStatus?->label ?? '-' }}</td>
                    <td class="text-center">{{ $member->membershipStatus?->label ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 10px; font-style: italic; color: #6b7280;">
                        Tidak ada data anggota keluarga.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tabel II: DATA SAKRAMEN & ORANG TUA -->
    <div class="table-section-title">II. Data Sakramen & Orang Tua</div>
    <table class="details-table">
        <thead>
            <tr>
                <th style="width: 3%;" class="text-center">No</th>
                <th style="width: 20%;">Nama Lengkap</th>
                <th style="width: 8%;" class="text-center">Tanggal Baptis</th>
                <th style="width: 15%;">Gereja Tempat Baptis</th>
                <th style="width: 8%;" class="text-center">Tanggal Sidi</th>
                <th style="width: 15%;">Gereja Tempat Sidi</th>
                <th style="width: 15%;">Nama Ayah</th>
                <th style="width: 16%;">Nama Ibu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($members as $member)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <strong>{{ $member->fullName }}</strong>
                    </td>
                    <td class="text-center">
                        {{ $member->baptism_date ? $member->baptism_date->format('d-m-Y') : '-' }}
                    </td>
                    <td>{{ $member->baptism_church ?? '-' }}</td>
                    <td class="text-center">
                        {{ $member->sidi_date ? $member->sidi_date->format('d-m-Y') : '-' }}
                    </td>
                    <td>{{ $member->sidi_church ?? '-' }}</td>
                    <td>{{ $member->father_name ?? '-' }}</td>
                    <td>{{ $member->mother_name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 10px; font-style: italic; color: #6b7280;">
                        Tidak ada data anggota keluarga.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer / Signatures -->
    <table class="footer-section">
        <tr>
            <td>
                <div class="sig-container">
                    <div>Mengetahui,</div>
                    <div class="sig-title">Kepala Keluarga</div>
                    <div class="sig-space"></div>
                    <div class="sig-name">{{ $headOfFamilyName }}</div>
                </div>
            </td>
            <td>
                <!-- Spacer col -->
            </td>
            <td>
                <div class="sig-container">
                    <div>Ditetapkan di: {{ str_replace('Jemaat ', '', $profile->church_name) }}</div>
                    <div>Pada Tanggal: {{ \Illuminate\Support\Carbon::now()->isoFormat('D MMMM YYYY') }}</div>
                    <div class="sig-title">Ketua Majelis Jemaat</div>
                    <div class="sig-space"></div>
                    <div class="sig-name">......................................................</div>
                    <div class="sig-title">Pendeta / Pelayan Jemaat</div>
                </div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
