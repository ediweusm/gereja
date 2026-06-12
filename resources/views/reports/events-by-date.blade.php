<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pelayanan Ibadah - Periode {{ \Illuminate\Support\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Illuminate\Support\Carbon::parse($endDate)->translatedFormat('d F Y') }}</title>
    <style>
        @media print {
            @page { size: portrait; margin: 1cm; }
            body { background: white !important; color: black !important; margin: 0; padding: 0; }
            .print-btn { display: none !important; }
            .event-card { page-break-inside: avoid; }
            .date-group-header { page-break-after: avoid; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        }
        
        /* Menggunakan font Times New Roman */
        body { font-family: 'Times New Roman', Times, serif; padding: 2rem; max-width: 900px; margin: auto; font-size: 9.5pt; color: #111827; }

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
        .doc-title { text-align: center; margin: 20px 0 30px 0; }
        .doc-title h3 { font-size: 13pt; font-weight: bold; margin: 0; text-transform: uppercase; letter-spacing: 1px; }

        /* Event Container */
        .event-card { margin-bottom: 25px; border: 1px solid #333; padding: 15px; }
        .event-header { margin-top: 0; margin-bottom: 10px; border-bottom: 1px solid #333; padding-bottom: 5px; font-size: 11pt; font-weight: bold; text-transform: uppercase; }

        /* Tables inside event card */
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; vertical-align: middle; }
        th { background-color: #f3f4f6; font-weight: bold; font-size: 8.5pt; }

        .empty-state { text-align: center; padding: 30px; border: 1px dashed #333; font-weight: bold; margin-top: 20px; }

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
        <h3>JADWAL PELAYANAN IBADAH</h3>
        <p style="margin-top: 5px; font-size: 9.5pt;">Periode: {{ \Illuminate\Support\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Illuminate\Support\Carbon::parse($endDate)->translatedFormat('d F Y') }}</p>
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
                <h3 style="text-transform: uppercase; font-weight: bold; margin-bottom: 15px; font-size: 11pt; border-bottom: 2px solid #333; padding-bottom: 4px;">
                    HARI {{ $event->event_date->translatedFormat('l, d F Y') }}
                </h3>
            </div>
        @endif

        <div class="event-card">
            <h4 class="event-header">{{ $event->name }}</h4>
            
            <table style="margin-top: 0; margin-bottom: 15px;">
                <tr>
                    <th style="width: 25%;">Tema</th>
                    <td>{{ $event->theme ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Waktu Mulai</th>
                    <td>{{ \Illuminate\Support\Carbon::parse($event->start_time)->format('H:i') }} WIB</td>
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

            <h5 style="margin: 15px 0 5px 0; font-size: 9.5pt; text-transform: uppercase; letter-spacing: 0.5px; font-weight: bold;">Petugas Pelayanan</h5>
            <table>
                <thead>
                    <tr>
                        <th style="width: 40%; text-align: center;">Peran Pelayanan</th>
                        <th style="width: 60%; text-align: center;">Nama Petugas</th>
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

    <table class="signature-section">
        <tr>
            <td>
                <div>Mengetahui,</div>
                <div class="sig-title">Ketua Majelis Jemaat</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ $profile->ketua_majelis ?? 'Pdt. Nama Ketua, S.Th' }}</div>
            </td>
            <td>
                <div>Semarang, {{ \Illuminate\Support\Carbon::now()->translatedFormat('d F Y') }}</div>
                <div class="sig-title">Sekretaris Majelis Jemaat</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ $profile->sekretaris ?? 'Penatua Sekretaris' }}</div>
            </td>
        </tr>
    </table>

    <script>
        window.onload = function() {
            // Uncomment baris di bawah ini jika ingin langsung memunculkan dialog print otomatis
            window.print();
        }
    </script>
</body>
</html>