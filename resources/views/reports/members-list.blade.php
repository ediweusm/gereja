<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Jemaat</title>
    <style>
        @media print {
            @page { size: portrait; margin: 1cm; }
            body { background: white !important; color: black !important; margin: 0; padding: 0; }
            .print-btn { display: none !important; }
            .data-table { page-break-inside: auto; border-collapse: collapse; width: 100%; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
        }
        body { font-family: Times New Roman; padding: 2rem; max-width: 900px; margin: auto; font-size: 9.5pt; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .data-table, .data-table th, .data-table td { border: 1px solid #333; }
        .data-table th, .data-table td { padding: 6px 8px; text-align: left; }
        .data-table th { background-color: #f3f4f6; font-weight: bold; text-transform: uppercase; font-size: 8.5pt;}
        .text-center { text-align: center; }
    </style>
</head>
<body>

<!-- Kop Surat -->
<table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 20px; border-bottom: 3px double #333; padding-bottom: 10px;">
    <tr>
        @if($profile->logo_path)
            <td style="width: 80px; border: none; padding: 0 15px 0 0; vertical-align: middle;">
                <img src="{{ \Illuminate\Support\Facades\Storage::url($profile->logo_path) }}" alt="Logo" style="height: 65px; max-width: 80px; display: block;">
            </td>
        @endif
        <td style="border: none; padding: 0; vertical-align: middle; text-align: center;">
            <h1 style="margin: 0; font-size: 13pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">{{ $profile->gmit_name }}</h1>
            <h2 style="margin: 3px 0; font-size: 15pt; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">{{ $profile->church_name }}</h2>
            <p style="margin: 3px 0 0 0; font-size: 8.5pt; color: #4b5563;">{{ $profile->address }} | Telp: {{ $profile->phone }}</p>
        </td>
    </tr>
</table>

<!-- Tombol Cetak -->
<button class="print-btn" onclick="window.print()" style="margin-bottom: 20px; padding: 8px 16px; background-color: #10b981; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Cetak PDF</button>

<!-- Judul Dokumen -->
<h3 class="text-center" style="font-size: 13pt; font-weight: bold; margin: 15px 0; text-transform: uppercase; letter-spacing: 1px;">DAFTAR JEMAAT</h3>

<!-- Tabel Jemaat -->
<table class="data-table">
    <thead>
        <tr>
            <th class="text-center" style="width: 5%;">No</th>
            <th>Nama Lengkap</th>
            <th class="text-center" style="width: 3%;">L/P</th>
            <th>Tempat Lahir</th>
            <th class="text-center" style="width: 15%;">Tanggal Lahir</th>
            <th class="text-center" style="width: 8%;">Usia</th>
            <th>No. KK</th>
        </tr>
    </thead>
    <tbody>
        @forelse($members as $member)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $member->full_name }}</td>
                <td class="text-center">{{ $member->gender }}</td>
                <td>{{ $member->birth_place ?? '-' }}</td>
                <td class="text-center">{{ $member->birth_date ? $member->birth_date->format('d-m-Y') : '-' }}</td>
                <td class="text-center">
                    {{ $member->birth_date ? \Carbon\Carbon::parse($member->birth_date)->age . ' thn' : '-' }}
                </td>
                <td>{{ $member->family->family_number ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 15px; color: #4b5563;">
                    Tidak ada data jemaat yang sesuai filter.
                </td>
            </tr>
        @endforelse
    </tbody>
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
            <div style="font-size: 8.5pt; color: #4b5563; margin-top: 3px; font-weight: bold;">Sekretaris Majelis Jemaat</div>
            <div style="height: 70px;"></div>
            <div style="font-weight: bold; text-decoration: underline;">{{ $profile->sekretaris ?? 'Penatua Sekretaris' }}</div>
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
