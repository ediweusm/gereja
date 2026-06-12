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
        @media print {
            @page { size: landscape; margin: 1cm; }
            body { background: white !important; color: black !important; margin: 0; padding: 0; }
            .print-btn { display: none !important; }
            .details-table { page-break-inside: auto; border-collapse: collapse; width: 100%; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
        }
        
        /* Menggunakan font Times New Roman */
        body { font-family: 'Times New Roman', Times, serif; padding: 2rem; max-width: 1100px; margin: auto; font-size: 9.5pt; color: #111827; }

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
        .print-btn:hover { background-color: #0369a1; }

        /* Title */
        .doc-title { text-align: center; margin: 20px 0; }
        .doc-title h3 { font-size: 14pt; font-weight: bold; margin: 0; text-transform: uppercase; letter-spacing: 2px; text-decoration: underline; }
        .doc-title p { margin: 5px 0 0 0; font-size: 10pt; font-weight: bold; color: #111827; }

        /* Info Grid */
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 9.5pt; }
        .info-table td { padding: 4px 5px; vertical-align: top; }
        .info-label { font-weight: bold; width: 180px; }
        .info-separator { width: 10px; text-align: center; }
        .info-value { font-weight: bold; }

        /* Table Section Header */
        .table-section-title { font-size: 9.5pt; font-weight: bold; margin: 15px 0 5px 0; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Details Table */
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 8.5pt; }
        .details-table th, .details-table td { border: 1px solid #333; padding: 5px 6px; vertical-align: middle; }
        .details-table th { background-color: #f3f4f6; font-weight: bold; text-align: center; text-transform: uppercase; }
        
        .text-center { text-align: center !important; }
        .nik-text { display: block; font-size: 7.5pt; color: #4b5563; margin-top: 2px; font-family: monospace; }

        /* Signatures */
        .footer-section { width: 100%; margin-top: 30px; page-break-inside: avoid; border-collapse: collapse; border: none; }
        .footer-section td { width: 33.33%; text-align: center; vertical-align: top; border: none !important; font-size: 9.5pt; padding: 0; }
        .sig-space { height: 70px; }
        .sig-name { font-weight: bold; text-decoration: underline; }
        .sig-title { font-size: 9pt; color: #4b5563; margin-top: 3px; font-weight: bold; }
    </style>
</head>
<body>

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

    <div style="text-align: right; margin-bottom: 10px;">
        <button class="print-btn" onclick="window.print()">Cetak PDF</button>
    </div>

    <div class="doc-title">
        <h3>KARTU KELUARGA JEMAAT</h3>
        <p>No. KK: {{ $family->family_number }}</p>
    </div>

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

    <div class="table-section-title">I. Data Demografi & Hubungan Keluarga</div>
    <table class="details-table">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 25%;">Nama Lengkap</th>
                <th style="width: 5%;">L/P</th>
                <th style="width: 17%;">Tempat / Tanggal Lahir</th>
                <th style="width: 12%;">Hubungan Keluarga</th>
                <th style="width: 11%;">Pendidikan</th>
                <th style="width: 11%;">Pekerjaan</th>
                <th style="width: 8%;">Status Nikah</th>
                <th style="width: 8%;">Status Jemaat</th>
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

    <div class="table-section-title">II. Data Sakramen & Orang Tua</div>
    <table class="details-table">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 20%;">Nama Lengkap</th>
                <th style="width: 8%;">Tanggal Baptis</th>
                <th style="width: 15%;">Gereja Tempat Baptis</th>
                <th style="width: 8%;">Tanggal Sidi</th>
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

    <table class="footer-section">
        <tr>
            <td>
                <div>Mengetahui,</div>
                <div class="sig-title">Kepala Keluarga</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ $headOfFamilyName }}</div>
            </td>
            <td>
                </td>
            <td>
                <div>Semarang, {{ \Illuminate\Support\Carbon::now()->isoFormat('D MMMM YYYY') }}</div>
                <div class="sig-title">Ketua Majelis Jemaat</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ $profile->ketua_majelis ?? '......................................................' }}</div>
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