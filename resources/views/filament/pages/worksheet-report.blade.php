<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header Form Filter dan Tombol Cetak --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 print-hidden">
            <div class="flex-1 w-full">
                {{ $this->form }}
            </div>
            
            {{-- Menggunakan komponen native Filament agar responsif terhadap Light/Dark mode --}}
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
            <div id="worksheet-printable-area" class="bg-white dark:bg-gray-900 p-8 rounded-xl shadow-sm ring-1 ring-gray-950/5">
                
                {{-- Judul Kop dengan styling print khusus --}}
                <div class="text-center mb-8 border-b-2 border-gray-800 dark:border-gray-750 pb-6 relative print-header-container">
                    {{-- Logo --}}
                    @if($profile?->logo_path)
                        <div class="mb-3">
                            <img class="h-16 w-auto mx-auto" src="{{ \Illuminate\Support\Facades\Storage::url($profile->logo_path) }}" alt="Logo">
                        </div>
                    @endif
                    <div class="space-y-0.5 print-kop">
                        <div class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 print-gmit">{{ $profile?->gmit_name }}</div>
                        <div class="text-lg font-extrabold uppercase text-gray-900 dark:text-gray-100 print-church">{{ $profile?->church_name }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 print-address">{{ $profile?->address }}@if($profile?->phone) | Telp: {{ $profile->phone }}@endif</div>
                    </div>
                    
                    <div class="relative mt-6">
                        <h2 class="text-2xl font-black uppercase tracking-wider text-gray-900 dark:text-gray-100 print-title">Neraca Lajur (Worksheet)</h2>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 font-semibold mt-1 print-subtitle">Periode: {{ Carbon\Carbon::parse($this->start_date)->format('d/m/Y') }} s/d {{ Carbon\Carbon::parse($this->end_date)->format('d/m/Y') }}</p>
                </div>

                {{-- Tabel Neraca Lajur --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left border-collapse border border-gray-300 dark:border-gray-700 min-w-[1000px]">
                        <thead>
                            {{-- Baris Header Utama --}}
                            <tr class="border-b-2 border-gray-800 dark:border-gray-200 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-bold uppercase">
                                <th rowspan="2" class="py-3 px-4 border-r border-gray-300 dark:border-gray-700 text-center align-middle w-[100px]">Kode</th>
                                <th rowspan="2" class="py-3 px-4 border-r border-gray-300 dark:border-gray-700 text-center align-middle">Nama Akun</th>
                                <th colspan="2" class="py-2 px-4 border-r border-gray-300 dark:border-gray-700 text-center">Neraca Saldo</th>
                                <th colspan="2" class="py-2 px-4 border-r border-gray-300 dark:border-gray-700 text-center">Laba Rugi</th>
                                <th colspan="2" class="py-2 px-4 text-center">Neraca</th>
                            </tr>
                            {{-- Baris Sub Header (Debit / Kredit) --}}
                            <tr class="border-b-2 border-gray-800 dark:border-gray-200 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-bold text-center">
                                <th class="py-2 px-3 border-r border-gray-300 dark:border-gray-700 w-[120px]">Debit</th>
                                <th class="py-2 px-3 border-r border-gray-300 dark:border-gray-700 w-[120px]">Kredit</th>
                                <th class="py-2 px-3 border-r border-gray-300 dark:border-gray-700 w-[120px]">Debit</th>
                                <th class="py-2 px-3 border-r border-gray-300 dark:border-gray-700 w-[120px]">Kredit</th>
                                <th class="py-2 px-3 border-r border-gray-300 dark:border-gray-700 w-[120px]">Debit</th>
                                <th class="py-2 px-3 w-[120px]">Kredit</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800 dark:text-gray-200">
                            @forelse($data['rows'] as $row)
                                <tr class="border-b border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="py-2 px-4 border-r border-gray-200 dark:border-gray-800 font-mono">{{ $row['code'] }}</td>
                                    <td class="py-2 px-4 border-r border-gray-200 dark:border-gray-800 font-medium">{{ $row['name'] }}</td>
                                    <td class="py-2 px-3 border-r border-gray-200 dark:border-gray-800 text-right font-mono">{{ $fmt($row['tb_debit']) }}</td>
                                    <td class="py-2 px-3 border-r border-gray-200 dark:border-gray-800 text-right font-mono">{{ $fmt($row['tb_credit']) }}</td>
                                    <td class="py-2 px-3 border-r border-gray-200 dark:border-gray-800 text-right font-mono text-amber-700 dark:text-amber-400">{{ $fmt($row['pl_debit']) }}</td>
                                    <td class="py-2 px-3 border-r border-gray-200 dark:border-gray-800 text-right font-mono text-amber-700 dark:text-amber-400">{{ $fmt($row['pl_credit']) }}</td>
                                    <td class="py-2 px-3 border-r border-gray-200 dark:border-gray-800 text-right font-mono text-emerald-700 dark:text-emerald-400">{{ $fmt($row['bs_debit']) }}</td>
                                    <td class="py-2 px-3 text-right font-mono text-emerald-700 dark:text-emerald-400">{{ $fmt($row['bs_credit']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-8 text-center italic text-gray-500">Tidak ada transaksi dalam periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            {{-- Baris Jumlah --}}
                            @php
                                $tb_debit_total = $data['totals']['tb_debit'];
                                $tb_credit_total = $data['totals']['tb_credit'];
                                $pl_debit_total = $data['totals']['pl_debit'];
                                $pl_credit_total = $data['totals']['pl_credit'];
                                $bs_debit_total = $data['totals']['bs_debit'];
                                $bs_credit_total = $data['totals']['bs_credit'];
                                $sd = $data['surplus_deficit'];
                            @endphp
                            <tr class="font-bold border-t-2 border-gray-800 dark:border-gray-200 bg-gray-50 dark:bg-gray-800/20 text-gray-900 dark:text-gray-100">
                                <td colspan="2" class="py-2 px-4 border-r border-gray-300 dark:border-gray-700 text-right uppercase tracking-wider">Jumlah</td>
                                <td class="py-2 px-3 border-r border-gray-300 dark:border-gray-700 text-right font-mono">{{ $fmt($tb_debit_total) }}</td>
                                <td class="py-2 px-3 border-r border-gray-300 dark:border-gray-700 text-right font-mono">{{ $fmt($tb_credit_total) }}</td>
                                <td class="py-2 px-3 border-r border-gray-300 dark:border-gray-700 text-right font-mono text-amber-700 dark:text-amber-400">{{ $fmt($pl_debit_total) }}</td>
                                <td class="py-2 px-3 border-r border-gray-300 dark:border-gray-700 text-right font-mono text-amber-700 dark:text-amber-400">{{ $fmt($pl_credit_total) }}</td>
                                <td class="py-2 px-3 border-r border-gray-300 dark:border-gray-700 text-right font-mono text-emerald-700 dark:text-emerald-400">{{ $fmt($bs_debit_total) }}</td>
                                <td class="py-2 px-3 text-right font-mono text-emerald-700 dark:text-emerald-400">{{ $fmt($bs_credit_total) }}</td>
                            </tr>

                            {{-- Baris Surplus / Defisit --}}
                            <tr class="italic text-gray-800 dark:text-gray-200 border-t border-gray-300 dark:border-gray-700">
                                <td colspan="2" class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-800 text-right font-semibold">
                                    {{ $sd >= 0 ? 'Surplus Berjalan' : 'Defisit Berjalan' }}
                                </td>
                                {{-- Trial balance: empty --}}
                                <td class="py-2.5 px-3 border-r border-gray-200 dark:border-gray-800 text-right">-</td>
                                <td class="py-2.5 px-3 border-r border-gray-200 dark:border-gray-800 text-right">-</td>
                                
                                {{-- Laba rugi balancing --}}
                                <td class="py-2.5 px-3 border-r border-gray-200 dark:border-gray-800 text-right font-mono font-bold text-amber-700 dark:text-amber-400">
                                    {{ $sd >= 0 ? $fmt($sd) : '-' }}
                                </td>
                                <td class="py-2.5 px-3 border-r border-gray-200 dark:border-gray-800 text-right font-mono font-bold text-amber-700 dark:text-amber-400">
                                    {{ $sd < 0 ? $fmt(abs($sd)) : '-' }}
                                </td>

                                {{-- Neraca balancing --}}
                                <td class="py-2.5 px-3 border-r border-gray-200 dark:border-gray-800 text-right font-mono font-bold text-emerald-700 dark:text-emerald-400">
                                    {{ $sd < 0 ? $fmt(abs($sd)) : '-' }}
                                </td>
                                <td class="py-2.5 px-3 text-right font-mono font-bold text-emerald-700 dark:text-emerald-400">
                                    {{ $sd >= 0 ? $fmt($sd) : '-' }}
                                </td>
                            </tr>

                            {{-- Baris Total Keseluruhan --}}
                            <tr class="font-extrabold border-t-2 border-b-4 border-double border-gray-800 dark:border-gray-250 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                <td colspan="2" class="py-3 px-4 border-r border-gray-300 dark:border-gray-700 text-right uppercase tracking-wider">Total Keseluruhan</td>
                                <td class="py-3 px-3 border-r border-gray-300 dark:border-gray-700 text-right font-mono">{{ $fmt($tb_debit_total) }}</td>
                                <td class="py-3 px-3 border-r border-gray-300 dark:border-gray-700 text-right font-mono">{{ $fmt($tb_credit_total) }}</td>
                                
                                {{-- Laba rugi balanced totals --}}
                                <td class="py-3 px-3 border-r border-gray-300 dark:border-gray-700 text-right font-mono text-amber-900 dark:text-amber-200">
                                    {{ $sd >= 0 ? $fmt($pl_debit_total + $sd) : $fmt($pl_debit_total) }}
                                </td>
                                <td class="py-3 px-3 border-r border-gray-300 dark:border-gray-700 text-right font-mono text-amber-900 dark:text-amber-200">
                                    {{ $sd < 0 ? $fmt($pl_credit_total + abs($sd)) : $fmt($pl_credit_total) }}
                                </td>

                                {{-- Neraca balanced totals --}}
                                <td class="py-3 px-3 border-r border-gray-300 dark:border-gray-700 text-right font-mono text-emerald-900 dark:text-emerald-200">
                                    {{ $sd < 0 ? $fmt($bs_debit_total + abs($sd)) : $fmt($bs_debit_total) }}
                                </td>
                                <td class="py-3 px-3 text-right font-mono text-emerald-900 dark:text-emerald-200">
                                    {{ $sd >= 0 ? $fmt($bs_credit_total + $sd) : $fmt($bs_credit_total) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

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
            'body{font-family:Arial,sans-serif;margin:1cm;color:#000;background:#fff;}',
            
            /* CSS KHUSUS KOP SURAT SAAT DICETAK */
            '.print-header-container{text-align:center; border-bottom:2px solid #000; padding-bottom:12px; margin-bottom:20px;}',
            '.print-kop{text-align:center; margin-bottom: 12px;}',
            '.print-gmit{font-size:10px; font-weight:bold; text-transform:uppercase;}',
            '.print-church{font-size:14px; font-weight:900; text-transform:uppercase; margin:4px 0;}',
            '.print-address{font-size:10px; color:#444;}',
            '.print-title{font-size:16px;font-weight:bold;text-transform:uppercase;text-align:center;margin:0 0 4px 0;}',
            '.print-subtitle{text-align:center;font-size:11px;margin:0 0 16px 0;color:#555;}',
            'img{display:block;margin:0 auto 8px;max-height:60px;}',
            
            /* CSS TABEL STANDAR */
            'table{width:100%;border-collapse:collapse;font-size:10px;}',
            'th,td{border:1px solid #333;padding:4px 6px;}',
            'thead th{background:#f3f4f6;font-weight:bold;text-align:center;}',
            'tbody td{vertical-align:middle;}',
            'td.text-right,th.text-right{text-align:right;}',
            'tfoot td,tfoot th{background:#f3f4f6;font-weight:bold;}'
        ].join('');
        var html = '<!DOCTYPE html><html><head><title>Neraca Lajur (Worksheet)</title><style>' + css + '</style></head><body>' + el.innerHTML + '</body></html>';
        var blob = new Blob([html], {type: 'text/html'});
        var url = URL.createObjectURL(blob);
        var win = window.open(url, '_blank', 'width=1200,height=800');
        win.addEventListener('load', function() {
            setTimeout(function() { win.print(); }, 300);
        });
    };
    </script>
    @endscript
</x-filament-panels::page>