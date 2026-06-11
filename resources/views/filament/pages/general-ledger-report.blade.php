<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header Form Filter dan Tombol Cetak --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 print-hidden">
            <div class="flex-1 w-full">
                {{ $this->form }}
            </div>
            
            {{-- Menggunakan komponen native Filament agar warna otomatis beradaptasi --}}
            <x-filament::button
                color="success"
                icon="heroicon-o-printer"
                onclick="printGeneralLedger()"
                class="shrink-0"
            >
                Cetak Laporan / PDF
            </x-filament::button>
        </div>
        @php
            $stats = $this->reportStats;
            $profile = \App\Models\ChurchProfile::first();
        @endphp

        {{-- 2. Tampilkan Ringkasan dan Tabel HANYA jika akun sudah dipilih --}}
        @if(!empty($stats))
            
            {{-- PERBAIKAN 3: Elemen Hidden (Sembunyi) untuk Kop Surat saat di-print --}}
            <div id="print-header-kop" class="hidden">
                <div style="text-align:center; border-bottom:2px solid #000; padding-bottom:12px; margin-bottom:20px;">
                    @if($profile?->logo_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($profile->logo_path) }}" style="display:block; margin:0 auto 8px; max-height:60px;" alt="Logo">
                    @endif
                    <div style="font-size:10px; font-weight:bold; text-transform:uppercase;">{{ $profile?->gmit_name }}</div>
                    <div style="font-size:14px; font-weight:900; text-transform:uppercase; margin:4px 0;">{{ $profile?->church_name }}</div>
                    <div style="font-size:10px; color:#444;">{{ $profile?->address }}@if($profile?->phone) | Telp: {{ $profile->phone }}@endif</div>
                    
                    <h2 style="font-size:16px; font-weight:bold; text-transform:uppercase; margin:15px 0 4px 0;">Buku Besar (General Ledger)</h2>
                    <p style="font-size:11px; color:#555; margin:0;">Periode: {{ Carbon\Carbon::parse($this->start_date)->format('d/m/Y') }} s/d {{ Carbon\Carbon::parse($this->end_date)->format('d/m/Y') }}</p>
                </div>
            </div>

            {{-- PERBAIKAN 2: Tombol cetak ganda berwarna biru sudah dihapus di sini --}}

            {{-- Tabel Ringkasan Mutasi --}}
            <div id="general-ledger-printable-area" class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-950/5 overflow-hidden print-summary-table mt-4">
                <table class="w-full text-sm text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <th colspan="4" class="px-4 py-3 text-base font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider">
                                Ringkasan Akun: {{ $stats['account_name'] }}
                                <span class="text-xs font-normal text-gray-500 ml-2 capitalize">({{ $stats['account_type'] }})</span>
                            </th>
                        </tr>
                        <tr class="border-b border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400">
                            <th class="px-4 py-2 font-semibold text-right w-1/4">Saldo Awal</th>
                            <th class="px-4 py-2 font-semibold text-right w-1/4">Total Debit</th>
                            <th class="px-4 py-2 font-semibold text-right w-1/4">Total Kredit</th>
                            <th class="px-4 py-2 font-semibold text-right w-1/4 border-l border-gray-200 dark:border-gray-700">Saldo Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-800 dark:text-gray-200">
                        <tr>
                            <td class="px-4 py-4 text-right text-base">{{ $this->formatRupiah($stats['beginning_balance']) }}</td>
                            <td class="px-4 py-4 text-right text-base text-green-600 dark:text-green-400">{{ $this->formatRupiah($stats['total_debit']) }}</td>
                            <td class="px-4 py-4 text-right text-base text-red-600 dark:text-red-400">{{ $this->formatRupiah($stats['total_credit']) }}</td>
                            <td class="px-4 py-4 text-right text-base font-bold border-l border-gray-200 dark:border-gray-700">{{ $this->formatRupiah($stats['ending_balance']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- 3. Tabel Detail Transaksi Bawaan Filament --}}
            <div class="print-filament-table mt-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2 print-hidden">Detail Transaksi</h3>
                {{ $this->table }}
            </div>
            
        @endif
    </div>

    @script
    <script>
    window.printGeneralLedger = function() {
        var summaryEl = document.getElementById('general-ledger-printable-area');
        var kopEl = document.getElementById('print-header-kop');
        
        if (!summaryEl) {
            alert('Tidak ada data laporan untuk dicetak. Pilih akun dan periode terlebih dahulu.');
            return;
        }
        
        var filamentTableEl = document.querySelector('.print-filament-table .fi-ta-table');
        var css = [
            'body{font-family:Arial,sans-serif;margin:1cm;color:#000;background:#fff;}',
            'h3{font-size:14px;font-weight:bold;margin:0 0 8px 0;border-bottom:2px solid #374151;padding-bottom:6px;text-transform:uppercase;}',
            'table{width:100%;border-collapse:collapse;font-size:10px;margin-bottom:20px;}',
            'th,td{border:1px solid #d1d5db;padding:4px 7px;vertical-align:middle;}',
            'thead th{background:#f3f4f6;font-weight:bold;text-transform:uppercase;font-size:10px;}',
            'tbody tr:nth-child(even){background:#f9fafb;}',
            '.text-right{text-align:right;} .text-center{text-align:center;}',
            '.font-bold{font-weight:bold;} .font-mono{font-family:monospace;}',
            'tfoot td,tfoot th{border-top:2px solid #1f2937;font-weight:bold;background:#f3f4f6;}'
        ].join('');
        
        // Gabungkan Kop Surat + Ringkasan + Tabel Transaksi
        var body = (kopEl ? kopEl.innerHTML : '') + '<h3>Ringkasan Mutasi</h3>' + summaryEl.innerHTML;
        
        if (filamentTableEl) {
            body += '<h3 style="margin-top:20px;">Rincian Transaksi</h3>' + filamentTableEl.outerHTML;
        }
        
        var html = '<!DOCTYPE html><html><head><title>Buku Besar (General Ledger)</title><style>' + css + '</style></head><body>' + body + '</body></html>';
        var blob = new Blob([html], {type: 'text/html'});
        var url = URL.createObjectURL(blob);
        var win = window.open(url, '_blank', 'width=1200,height=850');
        
        win.addEventListener('load', function() {
            setTimeout(function() { win.print(); }, 300);
        });
    };
    </script>
    @endscript
</x-filament-panels::page>