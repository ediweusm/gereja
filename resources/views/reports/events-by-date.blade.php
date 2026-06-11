<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pelayanan Ibadah - Periode {{ \Illuminate\Support\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Illuminate\Support\Carbon::parse($endDate)->translatedFormat('d F Y') }}</title>
    <style>
        @media print {
            @page { size: portrait; margin: 1cm; }
            body { background: white !important; color: black !important; }
            .print-btn { display: none !important; }
            table { page-break-inside: avoid; }
        }
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; padding: 2rem; max-width: 800px; margin: auto; color: #111827; }
        
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
            font-size: 14pt;
            font-weight: 800;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .doc-title p {
            margin: 5px 0 0 0;
            font-size: 9.5pt;
            font-weight: 600;
            color: #4b5563;
        }

        .print-btn {
            display: inline-block;
            background-color: #10b981;
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
            background-color: #059669;
        }

        /* Event Container */
        .event-card {
            margin-bottom: 30px;
            border: 2px solid #1f2937;
            padding: 15px;
            background-color: #fff;
            page-break-inside: avoid;
        }
        .event-header {
            margin-top: 0;
            margin-bottom: 10px;
            border-bottom: 2px solid #1f2937;
            padding-bottom: 5px;
            font-size: 12pt;
            font-weight: 700;
            text-transform: uppercase;
        }
        
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; }
        
        .empty-state {
            text-align: center;
            padding: 30px;
            border: 2px dashed #9ca3af;
            color: #4b5563;
            font-weight: 600;
            margin-top: 20px;
        }
    </style>
</head>
<body>

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

    <div style="text-align: right; margin-bottom: 10px;">
        <button class="print-btn" onclick="window.print()">Cetak PDF</button>
    </div>

    <!-- Document Title -->
    <div class="doc-title">
        <h3>JADWAL PELAYANAN IBADAH PERIODE {{ \Illuminate\Support\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Illuminate\Support\Carbon::parse($endDate)->translatedFormat('d F Y') }}</h3>
    </div>

    @php $currentDate = null; @endphp
    @forelse($events as $event)
        @php
            $eventDateString = $event->event_date->format('Y-m-d');
        @endphp
        @if($eventDateString !== $currentDate)
            @php
                $isFirstDate = ($currentDate === null);
                $currentDate = $eventDateString;
            @endphp
            <div class="date-group-header" style="{{ !$isFirstDate ? 'page-break-before: always; margin-top: 30px;' : 'margin-top: 20px;' }}">
                <h3 style="text-transform: uppercase; font-weight: bold; text-decoration: underline; margin-bottom: 15px; font-size: 13pt; border-bottom: 2px solid #1f2937; padding-bottom: 4px;">
                    HARI {{ $event->event_date->translatedFormat('l, d F Y') }}
                </h3>
            </div>
        @endif

        <div class="event-card">
            <h4 class="event-header">{{ $event->name }}</h4>
            
            <table class="detail-table" style="margin-top: 0; margin-bottom: 15px;">
                <tr>
                    <th style="width: 25%;">Tema</th>
                    <td>{{ $event->theme ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Waktu Mulai</th>
                    <td>{{ \Illuminate\Support\Carbon::parse($event->start_time)->format('H:i') }} WITA</td>
                </tr>
                <tr>
                    <th>Mode & Tempat</th>
                    <td>
                        {{ ucfirst($event->mode) }}
                        @if($event->location_notes)
                            ({{ $event->location_notes }})
                        @endif
                    </td>
                </tr>
                @if($event->event_type === 'Persekutuan Wilayah')
                    <tr>
                        <th>Rayon</th>
                        <td>{{ $event->rayon->name ?? '-' }}</td>
                    </tr>
                    @if($event->hostFamily)
                        <tr>
                            <th>Keluarga Penerima</th>
                            <td>
                                @php
                                    $head = $event->hostFamily->members->first(fn($m) => $m->familyPosition?->code === 'suami') ?? $event->hostFamily->members->first();
                                    $headName = $head ? $head->fullName : '-';
                                @endphp
                                No. KK: {{ $event->hostFamily->family_number }} (Keluarga {{ $headName }})
                            </td>
                        </tr>
                    @endif
                @endif
            </table>

            <h5 style="margin: 15px 0 5px 0; font-size: 10pt; text-transform: uppercase; letter-spacing: 0.5px;">Petugas Pelayanan</h5>
            <table>
                <thead>
                    <tr>
                        <th style="width: 40%;">Peran Pelayanan</th>
                        <th style="width: 60%;">Nama Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sortedAssignments = $event->assignments->sortBy(function($assignment) {
                            return $assignment->ministryRole->sort_order ?? 0;
                        });
                    @endphp
                    @forelse($sortedAssignments as $assignment)
                        <tr>
                            <td><strong>{{ $assignment->ministryRole->name }}</strong></td>
                            <td>
                                @if($assignment->member_id)
                                    {{ $assignment->member->fullName }} (Jemaat)
                                @else
                                    {{ $assignment->guest_name ?? '-' }} (Tamu)
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="text-align: center; color: #4b5563; padding: 12px;">Belum ada petugas yang dijadwalkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @empty
        <div class="empty-state">
            Tidak ada jadwal ibadah pada tanggal tersebut.
        </div>
    @endforelse

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
