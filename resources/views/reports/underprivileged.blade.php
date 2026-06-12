<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Keluarga Pra Sejahtera</title>
    <style>
        @media print {
            @page { size: portrait; margin: 1cm; }
            body { background: white !important; color: black !important; margin: 0; padding: 0; }
            .print-btn { display: none !important; }
            .data-table { page-break-inside: auto; border-collapse: collapse; width: 100%; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
            .group-header td { background-color: #d1d5db !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
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
        .doc-title h3 { font-size: 13pt; font-weight: bold; margin: 0; text-transform: uppercase; letter-spacing: 1px; }
        .doc-title p { margin: 5px 0 0 0; font-size: 9pt; color: #4b5563; }

        /* Data Table */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .data-table, .data-table th, .data-table td { border: 1px solid #333; }
        .data-table th, .data-table td { padding: 6px 8px; text-align: left; vertical-align: middle; }
        .data-table th { background-color: #f3f4f6; font-weight: bold; text-transform: uppercase; font-size: 8.5pt; text-align: center; }
        
        .group-header td { background-color: #e5e7eb; font-weight: bold; text-transform: uppercase; font-size: 8.5pt; color: #1f2937; }
        .text-center { text-align: center !important; }

        /* Signatures */
        .signature-section { width: 100%; margin-top: 40px; page-break-inside: avoid; border-collapse: collapse; border: none; }
        .signature-section td { width: 50%; text-align: center; vertical-align: top; border: none !important; font-size: 9pt; padding: 0; }
        .sig-space { height: 70px; }
        .sig-name { font-weight: bold; text-decoration: underline; }
        .sig-title { font-size: 8.5pt; color: #4b5563; margin-top: 3px; font-weight: bold; }
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
        <h3>DAFTAR KELUARGA PRA SEJAHTERA</h3>
        <p>Keluarga yang Membutuhkan Bantuan Sosial (Kondisi Rumah Darurat / Semi Permanen)</p>
    </div>

    <table class="data-table">
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
                        <td class="text-center"><strong>{{ $family->family_number }}</strong></td>
                        <td>{{ $headName }}</td>
                        <td>{{ $family->address }}</td>
                        <td class="text-center">{{ $family->rayon->name ?? '-' }}</td>
                        <td class="text-center">{{ $family->houseStatus?->label ?? '-' }}</td>
                        <td class="text-center">{{ $family->houseCategory?->label ?? '-' }}</td>
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

    <table class="signature-section">
        <tr>
            <td>
                <div>Mengetahui,</div>
                <div class="sig-title">Ketua Majelis Jemaat</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ $profile->ketua_majelis ?? 'Pdt. Sion Oepura, S.Th' }}</div>
            </td>
            <td>
                <div>Semarang, {{ \Illuminate\Support\Carbon::now()->translatedFormat('d F Y') }}</div>
                <div class="sig-title">Bendahara Diakonia</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ $profile->bendahara ?? 'Penatua Bendahara' }}</div>
            </td>
        </tr>
    </table>

    <script>
        window.onload = function() {
            // Uncomment baris di bawah ini jika ingin memunculkan dialog print otomatis saat terbuka
            window.print();
        }
    </script>
</body>
</html>