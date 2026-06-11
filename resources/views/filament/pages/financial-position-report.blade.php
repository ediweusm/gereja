<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header Form Filter dan Tombol Cetak --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div class="flex-1 w-full">
                {{ $this->form }}
            </div>
            
            <button onclick="printFinancialPosition()"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm font-bold text-gray-900 dark:text-gray-200 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shrink-0">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.056 48.056 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
                </svg>
                Cetak Laporan / PDF
            </button>
        </div>

        @php
            $data = $this->reportData;
            $profile = \App\Models\ChurchProfile::first();
        @endphp

        @if(!empty($data))
            <div id="financial-position-printable-area" class="bg-white dark:bg-gray-900 p-8 rounded-xl shadow-sm ring-1 ring-gray-950/5">
                
                <div class="text-center mb-8 border-b-2 border-gray-800 dark:border-gray-750 pb-6 relative print-header-container">
                    {{-- Logo --}}
                    @if($profile?->logo_path)
                        <div class="mb-3">
                            <img class="h-16 w-auto mx-auto" src="{{ \Illuminate\Support\Facades\Storage::url($profile->logo_path) }}" alt="Logo">
                        </div>
                    @endif
                    <div class="space-y-0.5 print-kop">
                        <div class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 print-gmit">{{ $profile?->gmit_name }}</div>
                        
                        {{-- Nama Gereja dengan class khusus print-church --}}
                        <div class="text-lg font-extrabold uppercase text-gray-900 dark:text-gray-100 print-church">{{ $profile?->church_name }}</div>
                        
                        <div class="text-xs text-gray-500 dark:text-gray-400 print-address">{{ $profile?->address }}@if($profile?->phone) | Telp: {{ $profile->phone }}@endif</div>
                    </div>
                    
                    <div class="relative mt-6">
                        <h2 class="text-2xl font-black uppercase tracking-wider text-gray-900 dark:text-gray-100 print-title">Laporan Posisi Keuangan (Neraca)</h2>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 font-semibold mt-1 print-subtitle">Per Tanggal: {{ $this->formatDateId($data['as_of_date']) }}</p>
                </div>

                {{-- Tabel Aset --}}
                <div class="mb-8">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead>
                            <tr class="border-b-2 border-gray-800 dark:border-gray-200">
                                <th colspan="3" class="py-2 text-lg font-bold uppercase text-gray-900 dark:text-gray-100">Aset</th>
                            </tr>
                            <tr class="border-b border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-400">
                                <th class="py-2 w-32 font-semibold">Kode</th>
                                <th class="py-2 font-semibold">Nama Akun</th>
                                <th class="py-2 text-right font-semibold">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800 dark:text-gray-200">
                            @forelse($data['assets'] as $asset)
                                <tr class="border-b border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="py-2">{{ $asset['code'] }}</td>
                                    <td class="py-2">{{ $asset['name'] }}</td>
                                    <td class="py-2 text-right">{{ number_format($asset['balance'], 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 text-center italic text-gray-500">Tidak ada data aset.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="font-bold border-t-2 border-gray-800 dark:border-gray-200 text-gray-900 dark:text-gray-100">
                                <td colspan="2" class="py-3 text-right uppercase tracking-wider">Total Aset</td>
                                <td class="py-3 text-right text-base">{{ number_format($data['total_assets'], 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Tabel Kewajiban --}}
                <div class="mb-8">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead>
                            <tr class="border-b-2 border-gray-800 dark:border-gray-200">
                                <th colspan="3" class="py-2 text-lg font-bold uppercase text-gray-900 dark:text-gray-100">Kewajiban</th>
                            </tr>
                            <tr class="border-b border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-400">
                                <th class="py-2 w-32 font-semibold">Kode</th>
                                <th class="py-2 font-semibold">Nama Akun</th>
                                <th class="py-2 text-right font-semibold">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800 dark:text-gray-200">
                            @forelse($data['liabilities'] as $liability)
                                <tr class="border-b border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="py-2">{{ $liability['code'] }}</td>
                                    <td class="py-2">{{ $liability['name'] }}</td>
                                    <td class="py-2 text-right">{{ number_format($liability['balance'], 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 text-center italic text-gray-500">Tidak ada data kewajiban.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="font-bold border-t border-gray-400 dark:border-gray-600 text-gray-800 dark:text-gray-300">
                                <td colspan="2" class="py-2 text-right">Subtotal Kewajiban</td>
                                <td class="py-2 text-right">{{ number_format($data['total_liabilities'], 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Tabel Aset Neto (Ekuitas) --}}
                <div class="mb-8">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead>
                            <tr class="border-b-2 border-gray-800 dark:border-gray-200">
                                <th colspan="3" class="py-2 text-lg font-bold uppercase text-gray-900 dark:text-gray-100">Aset Neto (Ekuitas)</th>
                            </tr>
                            <tr class="border-b border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-400">
                                <th class="py-2 w-32 font-semibold">Kode</th>
                                <th class="py-2 font-semibold">Nama Akun</th>
                                <th class="py-2 text-right font-semibold">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800 dark:text-gray-200">
                            @forelse($data['net_assets'] as $netAsset)
                                <tr class="border-b border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="py-2">
                                        @if(isset($netAsset['is_virtual']) && $netAsset['is_virtual'])
                                            <span class="px-2 py-0.5 rounded text-xs border border-gray-400">AUTO</span>
                                        @else
                                            {{ $netAsset['code'] }}
                                        @endif
                                    </td>
                                    <td class="py-2 {{ isset($netAsset['is_virtual']) ? 'italic' : '' }}">{{ $netAsset['name'] }}</td>
                                    <td class="py-2 text-right">{{ number_format($netAsset['balance'], 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 text-center italic text-gray-500">Tidak ada data aset neto.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="font-bold border-t border-gray-400 dark:border-gray-600 text-gray-800 dark:text-gray-300">
                                <td colspan="2" class="py-2 text-right">Subtotal Aset Neto</td>
                                <td class="py-2 text-right">{{ number_format($data['total_net_assets'], 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Grand Total Pasiva --}}
                <table class="w-full text-sm text-left border-collapse mt-4">
                    <tfoot>
                        <tr class="font-bold border-t-4 border-gray-800 dark:border-gray-200 text-gray-900 dark:text-gray-100 {{ $data['is_balanced'] ? '' : 'text-red-600 dark:text-red-400' }}">
                            <td class="py-4 text-right uppercase tracking-wider text-lg">Total Kewajiban + Aset Neto</td>
                            <td class="py-4 text-right text-lg w-48">{{ number_format($data['total_liabilities_net_assets'], 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>


            </div>
        @endif
    </div>

    @script
    <script>
    window.printFinancialPosition = function() {
        var el = document.getElementById('financial-position-printable-area');
        if (!el) {
            alert('Tidak ada data laporan untuk dicetak. Pilih tanggal terlebih dahulu.');
            return;
        }
        var css = [
            'body{font-family:Arial,sans-serif;margin:1.5cm;color:#000;background:#fff;}',
            'img{display:block;margin:0 auto 8px;max-height:60px;}',
            
            /* CSS KHUSUS KOP SURAT SAAT DICETAK */
            '.print-header-container{text-align:center; border-bottom:2px solid #000; padding-bottom:12px; margin-bottom:20px;}',
            '.print-kop{text-align:center; margin-bottom: 12px;}',
            '.print-gmit{font-size:10px; font-weight:bold; text-transform:uppercase;}',
            '.print-church{font-size:14px; font-weight:900; text-transform:uppercase; margin:4px 0;}', /* 14px membuat ukurannya lebih kecil, namun tetap tebal */
            '.print-address{font-size:10px; color:#444;}',
            '.print-title{font-size:16px;font-weight:bold;text-transform:uppercase;text-align:center;margin:0 0 4px 0;}',
            '.print-subtitle{text-align:center;font-size:11px;margin:0 0 4px 0;color:#555;}',
            
            /* CSS TABEL STANDAR */
            'table{width:100%;border-collapse:collapse;font-size:11px;margin-bottom:24px;}',
            'th,td{padding:6px 8px;}',
            'thead tr:first-child th{border-bottom:2px solid #1f2937;font-weight:bold;font-size:13px;text-transform:uppercase;}',
            'thead tr:last-child th{border-bottom:1px solid #6b7280;font-weight:600;color:#4b5563;}',
            'tbody tr{border-bottom:1px solid #e5e7eb;}',
            'tfoot tr{border-top:2px solid #1f2937;font-weight:bold;}',
            '.text-right{text-align:right;}'
        ].join('');
        var html = '<!DOCTYPE html><html><head><title>Laporan Posisi Keuangan</title><style>' + css + '</style></head><body>' + el.innerHTML + '</body></html>';
        var blob = new Blob([html], {type: 'text/html'});
        var url = URL.createObjectURL(blob);
        var win = window.open(url, '_blank', 'width=900,height=800');
        win.addEventListener('load', function() {
            setTimeout(function() { win.print(); }, 300);
        });
    };
    </script>
    @endscript
</x-filament-panels::page>

