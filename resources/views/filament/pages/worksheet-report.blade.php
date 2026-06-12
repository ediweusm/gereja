<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header Form Filter dan Tombol Cetak --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 print-hidden">
            <div class="flex-1 w-full">
                {{ $this->form }}
            </div>
            
            <x-filament::button
                color="success"
                icon="heroicon-o-printer"
                onclick="printWorksheet()"
                class="shrink-0"
            >
                Cetak Laporan / PDF
            </x-filament::button>
        </div>

        @php
            $data = $this->reportData;
            $fmt = fn($val) => ($val == 0) ? '-' : number_format($val, 2, ',', '.');
            
            // Panggil profil gereja untuk kop surat
            $profile = \App\Models\ChurchProfile::first();
        @endphp

        @if(!empty($data))
            <div id="worksheet-printable-area" class="bg-white dark:bg-gray-900 p-8 rounded-xl shadow-sm ring-1 ring-gray-950/5 text-gray-900 dark:text-gray-100">
                
                {{-- Kop Surat Seragam --}}
                <table class="kop-table w-full border-collapse border-none mb-5 pb-5" style="border-bottom: 1px double #333;">
                    <tr>
                        @if($profile?->logo_path)
                            <td class="w-[80px] border-none p-0 pb-1 align-middle" style="border: none; padding: 0 0 5px 0;">
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($profile->logo_path) }}" alt="Logo" class="h-[65px] max-w-[80px] block" style="height: 65px; max-width: 80px; display: block;">
                            </td>
                        @endif
                        <td class="border-none p-0 pb-1 align-middle text-left" style="border: none; padding: 0 0 5px 0;">
                            <h1 class="m-0 text-sm font-bold uppercase tracking-wider" style="margin: 0; font-size: 10pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">{{ $profile?->gmit_name }}</h1>
                            <h2 class="my-1 text-lg font-extrabold uppercase tracking-widest" style="margin: 2px 0 2px; font-size: 14pt; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">{{ $profile?->church_name }}</h2>
                            <p class="m-0 text-xs text-gray-600 dark:text-gray-400" style="margin: 2px 0 2px; font-size: 8.5pt; color: #4b5563;">{{ $profile?->address }}@if($profile?->phone) | Telp: {{ $profile->phone }}@endif</p>
                        </td>
                    </tr>
                </table>

                {{-- Judul Dokumen --}}
                <div class="text-center mb-6 mt-4">
                    <h3 class="text-lg font-bold uppercase tracking-wider m-0" style="font-size: 13pt; font-weight: bold; margin: 0; text-transform: uppercase; letter-spacing: 1px;">NERACA LAJUR (WORKSHEET)</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400" style="margin-top: 5px; font-size: 9.5pt;">Periode: {{ Carbon\Carbon::parse($this->start_date)->format('d/m/Y') }} s/d {{ Carbon\Carbon::parse($this->end_date)->format('d/m/Y') }}</p>
                </div>

                {{-- Tabel Neraca Lajur --}}
                <div class="overflow-x-auto">
                    <table class="data-table w-full text-sm text-left border-collapse min-w-[1000px]" style="border: 1px solid #333;">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-bold uppercase">
                                <th rowspan="2" class="py-2 px-3 text-center align-middle" style="border: 1px solid #333; font-size: 8.5pt;">Kode</th>
                                <th rowspan="2" class="py-2 px-3 text-center align-middle" style="border: 1px solid #333; font-size: 8.5pt;">Nama Akun</th>
                                <th colspan="2" class="py-1 px-3 text-center" style="border: 1px solid #333; font-size: 8.5pt;">Neraca Saldo</th>
                                <th colspan="2" class="py-1 px-3 text-center" style="border: 1px solid #333; font-size: 8.5pt;">Laba Rugi</th>
                                <th colspan="2" class="py-1 px-3 text-center" style="border: 1px solid #333; font-size: 8.5pt;">Neraca</th>
                            </tr>
                            <tr class="bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-bold text-center">
                                <th class="py-1 px-2 w-[110px]" style="border: 1px solid #333; font-size: 8.5pt;">Debit</th>
                                <th class="py-1 px-2 w-[110px]" style="border: 1px solid #333; font-size: 8.5pt;">Kredit</th>
                                <th class="py-1 px-2 w-[110px]" style="border: 1px solid #333; font-size: 8.5pt;">Debit</th>
                                <th class="py-1 px-2 w-[110px]" style="border: 1px solid #333; font-size: 8.5pt;">Kredit</th>
                                <th class="py-1 px-2 w-[110px]" style="border: 1px solid #333; font-size: 8.5pt;">Debit</th>
                                <th class="py-1 px-2 w-[110px]" style="border: 1px solid #333; font-size: 8.5pt;">Kredit</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800 dark:text-gray-200">
                            @forelse($data['rows'] as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="py-1.5 px-3 print-font-normal" style="border: 1px solid #333;">{{ $row['code'] }}</td>
                                    <td class="py-1.5 px-3 font-medium print-font-normal" style="border: 1px solid #333;">{{ $row['name'] }}</td>
                                    <td class="py-1.5 px-2 text-right print-font-normal" style="border: 1px solid #333;">{{ $fmt($row['tb_debit']) }}</td>
                                    <td class="py-1.5 px-2 text-right print-font-normal" style="border: 1px solid #333;">{{ $fmt($row['tb_credit']) }}</td>
                                    <td class="py-1.5 px-2 text-right text-amber-700 dark:text-amber-400 print-font-normal" style="border: 1px solid #333;">{{ $fmt($row['pl_debit']) }}</td>
                                    <td class="py-1.5 px-2 text-right text-amber-700 dark:text-amber-400 print-font-normal" style="border: 1px solid #333;">{{ $fmt($row['pl_credit']) }}</td>
                                    <td class="py-1.5 px-2 text-right text-emerald-700 dark:text-emerald-400 print-font-normal" style="border: 1px solid #333;">{{ $fmt($row['bs_debit']) }}</td>
                                    <td class="py-1.5 px-2 text-right text-emerald-700 dark:text-emerald-400 print-font-normal" style="border: 1px solid #333;">{{ $fmt($row['bs_credit']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-8 text-center italic text-gray-500" style="border: 1px solid #333;">Tidak ada transaksi dalam periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            @php
                                $tb_debit_total = $data['totals']['tb_debit'];
                                $tb_credit_total = $data['totals']['tb_credit'];
                                $pl_debit_total = $data['totals']['pl_debit'];
                                $pl_credit_total = $data['totals']['pl_credit'];
                                $bs_debit_total = $data['totals']['bs_debit'];
                                $bs_credit_total = $data['totals']['bs_credit'];
                                $sd = $data['surplus_deficit'];
                            @endphp
                            <tr class="font-bold bg-gray-50 dark:bg-gray-800/20 text-gray-900 dark:text-gray-100">
                                <td colspan="2" class="py-2 px-3 text-right uppercase tracking-wider" style="border: 1px solid #333;">Jumlah</td>
                                <td class="py-2 px-2 text-right print-font-normal" style="border: 1px solid #333;">{{ $fmt($tb_debit_total) }}</td>
                                <td class="py-2 px-2 text-right print-font-normal" style="border: 1px solid #333;">{{ $fmt($tb_credit_total) }}</td>
                                <td class="py-2 px-2 text-right text-amber-700 dark:text-amber-400 print-font-normal" style="border: 1px solid #333;">{{ $fmt($pl_debit_total) }}</td>
                                <td class="py-2 px-2 text-right text-amber-700 dark:text-amber-400 print-font-normal" style="border: 1px solid #333;">{{ $fmt($pl_credit_total) }}</td>
                                <td class="py-2 px-2 text-right text-emerald-700 dark:text-emerald-400 print-font-normal" style="border: 1px solid #333;">{{ $fmt($bs_debit_total) }}</td>
                                <td class="py-2 px-2 text-right text-emerald-700 dark:text-emerald-400 print-font-normal" style="border: 1px solid #333;">{{ $fmt($bs_credit_total) }}</td>
                            </tr>

                            <tr class="italic text-gray-800 dark:text-gray-200">
                                <td colspan="2" class="py-2 px-3 text-right font-semibold" style="border: 1px solid #333;">
                                    {{ $sd >= 0 ? 'Surplus Berjalan' : 'Defisit Berjalan' }}
                                </td>
                                <td class="py-2 px-2 text-right" style="border: 1px solid #333;">-</td>
                                <td class="py-2 px-2 text-right" style="border: 1px solid #333;">-</td>
                                <td class="py-2 px-2 text-right font-bold text-amber-700 dark:text-amber-400 print-font-normal" style="border: 1px solid #333;">
                                    {{ $sd >= 0 ? $fmt($sd) : '-' }}
                                </td>
                                <td class="py-2 px-2 text-right font-bold text-amber-700 dark:text-amber-400 print-font-normal" style="border: 1px solid #333;">
                                    {{ $sd < 0 ? $fmt(abs($sd)) : '-' }}
                                </td>
                                <td class="py-2 px-2 text-right font-bold text-emerald-700 dark:text-emerald-400 print-font-normal" style="border: 1px solid #333;">
                                    {{ $sd < 0 ? $fmt(abs($sd)) : '-' }}
                                </td>
                                <td class="py-2 px-2 text-right font-bold text-emerald-700 dark:text-emerald-400 print-font-normal" style="border: 1px solid #333;">
                                    {{ $sd >= 0 ? $fmt($sd) : '-' }}
                                </td>
                            </tr>

                            <tr class="font-extrabold bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                <td colspan="2" class="py-2.5 px-3 text-right uppercase tracking-wider" style="border: 1px solid #333;">Total Keseluruhan</td>
                                <td class="py-2.5 px-2 text-right print-font-normal" style="border: 1px solid #333;">{{ $fmt($tb_debit_total) }}</td>
                                <td class="py-2.5 px-2 text-right print-font-normal" style="border: 1px solid #333;">{{ $fmt($tb_credit_total) }}</td>
                                <td class="py-2.5 px-2 text-right text-amber-900 dark:text-amber-200 print-font-normal" style="border: 1px solid #333;">
                                    {{ $sd >= 0 ? $fmt($pl_debit_total + $sd) : $fmt($pl_debit_total) }}
                                </td>
                                <td class="py-2.5 px-2 text-right text-amber-900 dark:text-amber-200 print-font-normal" style="border: 1px solid #333;">
                                    {{ $sd < 0 ? $fmt($pl_credit_total + abs($sd)) : $fmt($pl_credit_total) }}
                                </td>
                                <td class="py-2.5 px-2 text-right text-emerald-900 dark:text-emerald-200 print-font-normal" style="border: 1px solid #333;">
                                    {{ $sd < 0 ? $fmt($bs_debit_total + abs($sd)) : $fmt($bs_debit_total) }}
                                </td>
                                <td class="py-2.5 px-2 text-right text-emerald-900 dark:text-emerald-200 print-font-normal" style="border: 1px solid #333;">
                                    {{ $sd >= 0 ? $fmt($bs_credit_total + $sd) : $fmt($bs_credit_total) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Tanda Tangan --}}
                <table class="signature-section" style="width: 100%; margin-top: 40px; page-break-inside: avoid; border-collapse: collapse; border: none;">
                    <tr>
                        <td style="width: 50%; text-align: center; vertical-align: top; border: none !important; font-size: 9pt; padding: 0;">
                            <div>Mengetahui,</div>
                            <div style="font-size: 8.5pt; color: #4b5563; margin-top: 3px; font-weight: bold;">Ketua Majelis Jemaat</div>
                            <div style="height: 70px;"></div>
                            <div style="font-weight: bold; text-decoration: underline;">{{ $profile?->ketua_majelis ?? 'Pdt. Sion Oepura, S.Th' }}</div>
                        </td>
                        <td style="width: 50%; text-align: center; vertical-align: top; border: none !important; font-size: 9pt; padding: 0;">
                            <div>Semarang, {{ \Illuminate\Support\Carbon::now()->translatedFormat('d F Y') }}</div>
                            <div style="font-size: 8.5pt; color: #4b5563; margin-top: 3px; font-weight: bold;">Bendahara Jemaat</div>
                            <div style="height: 70px;"></div>
                            <div style="font-weight: bold; text-decoration: underline;">{{ $profile?->bendahara ?? 'Penatua Bendahara' }}</div>
                        </td>
                    </tr>
                </table>

            </div>
        @endif
    </div>

    @script
    <script>
    window.printWorksheet = function() {
        var el = document.getElementById('worksheet-printable-area');
        if (!el) {
            alert('Tidak ada data laporan untuk dicetak. Pilih periode terlebih dahulu.');
            return;
        }
        var css = [
            '@page { size: landscape; margin: 1cm; }',
            'body { font-family: "Times New Roman", Times, serif; margin: 0; padding: 0; color: #111827; background: #fff; font-size: 9.5pt; }',
            'table { width: 100%; border-collapse: collapse; }',
            'table.data-table th, table.data-table td { border: 1px solid #333 !important; padding: 6px 8px; vertical-align: middle; }',
            'table.data-table th { background-color: #f3f4f6 !important; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 8.5pt; -webkit-print-color-adjust: exact; print-color-adjust: exact; }',
            'table.data-table tfoot tr { background-color: #f3f4f6 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }',
            '.print-font-normal { font-family: "Times New Roman", Times, serif !important; }',
            '.text-right { text-align: right !important; }',
            '.text-center { text-align: center !important; }',
            '.kop-table td, .signature-section td { border: none !important; }',
            '.text-emerald-700, .text-amber-700, .text-emerald-900, .text-amber-900, .dark\\:text-emerald-400, .dark\\:text-amber-400, .text-gray-600, .dark\\:text-gray-400, .text-gray-500 { color: #111827 !important; }',
            'h1, h2, h3, p { margin: 0; }'
        ].join('');
        
        var html = '<!DOCTYPE html><html lang="id"><head><title>Neraca Lajur (Worksheet)</title><style>' + css + '</style></head><body>' + el.innerHTML + '</body></html>';
        var blob = new Blob([html], {type: 'text/html'});
        var url = URL.createObjectURL(blob);
        var win = window.open(url, '_blank');
        
        win.addEventListener('load', function() {
            setTimeout(function() { win.print(); }, 300);
        });
    };
    </script>
    @endscript
</x-filament-panels::page>