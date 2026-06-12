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
            <div id="financial-position-printable-area" class="bg-white dark:bg-gray-900 p-8 rounded-xl shadow-sm ring-1 ring-gray-950/5 text-gray-900 dark:text-gray-100">
                
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
                    <h3 class="text-lg font-bold uppercase tracking-wider m-0" style="font-size: 13pt; font-weight: bold; margin: 0; text-transform: uppercase; letter-spacing: 1px;">LAPORAN POSISI KEUANGAN (NERACA)</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400" style="margin-top: 5px; font-size: 9.5pt;">Per Tanggal: {{ $this->formatDateId($data['as_of_date']) }}</p>
                </div>

                {{-- Tabel Aset --}}
                <table class="data-table w-full text-sm text-left border-collapse mb-6" style="border: 1px solid #333;">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-bold">
                            <th colspan="3" class="py-2 px-3 text-center align-middle uppercase" style="border: 1px solid #333; font-size: 10.5pt; letter-spacing: 1px;">ASET</th>
                        </tr>
                        <tr class="bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-bold">
                            <th class="py-2 px-3 w-32" style="border: 1px solid #333; font-size: 9.5pt;">Kode</th>
                            <th class="py-2 px-3" style="border: 1px solid #333; font-size: 9.5pt;">Nama Akun</th>
                            <th class="py-2 px-3 text-right" style="border: 1px solid #333; font-size: 9.5pt;">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-800 dark:text-gray-200">
                        @forelse($data['assets'] as $asset)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="py-2 px-3" style="border: 1px solid #333;">{{ $asset['code'] }}</td>
                                <td class="py-2 px-3" style="border: 1px solid #333;">{{ $asset['name'] }}</td>
                                <td class="py-2 px-3 text-right" style="border: 1px solid #333;">{{ number_format($asset['balance'], 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center italic text-gray-500" style="border: 1px solid #333;">Tidak ada data aset.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="font-bold bg-gray-50 dark:bg-gray-800/20 text-gray-900 dark:text-gray-100">
                            <td colspan="2" class="py-3 px-3 text-right uppercase tracking-wider" style="border: 1px solid #333;">Total Aset</td>
                            <td class="py-3 px-3 text-right text-base" style="border: 1px solid #333;">{{ number_format($data['total_assets'], 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>

                {{-- Tabel Kewajiban --}}
                <table class="data-table w-full text-sm text-left border-collapse mb-6" style="border: 1px solid #333;">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-bold">
                            <th colspan="3" class="py-2 px-3 text-center align-middle uppercase" style="border: 1px solid #333; font-size: 10.5pt; letter-spacing: 1px;">KEWAJIBAN</th>
                        </tr>
                        <tr class="bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-bold">
                            <th class="py-2 px-3 w-32" style="border: 1px solid #333; font-size: 9.5pt;">Kode</th>
                            <th class="py-2 px-3" style="border: 1px solid #333; font-size: 9.5pt;">Nama Akun</th>
                            <th class="py-2 px-3 text-right" style="border: 1px solid #333; font-size: 9.5pt;">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-800 dark:text-gray-200">
                        @forelse($data['liabilities'] as $liability)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="py-2 px-3" style="border: 1px solid #333;">{{ $liability['code'] }}</td>
                                <td class="py-2 px-3" style="border: 1px solid #333;">{{ $liability['name'] }}</td>
                                <td class="py-2 px-3 text-right" style="border: 1px solid #333;">{{ number_format($liability['balance'], 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center italic text-gray-500" style="border: 1px solid #333;">Tidak ada data kewajiban.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="font-bold bg-gray-50 dark:bg-gray-800/20 text-gray-900 dark:text-gray-100">
                            <td colspan="2" class="py-2 px-3 text-right" style="border: 1px solid #333;">Subtotal Kewajiban</td>
                            <td class="py-2 px-3 text-right" style="border: 1px solid #333;">{{ number_format($data['total_liabilities'], 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>

                {{-- Tabel Aset Neto (Ekuitas) --}}
                <table class="data-table w-full text-sm text-left border-collapse mb-6" style="border: 1px solid #333;">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-bold">
                            <th colspan="3" class="py-2 px-3 text-center align-middle uppercase" style="border: 1px solid #333; font-size: 10.5pt; letter-spacing: 1px;">ASET NETO (EKUITAS)</th>
                        </tr>
                        <tr class="bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-bold">
                            <th class="py-2 px-3 w-32" style="border: 1px solid #333; font-size: 9.5pt;">Kode</th>
                            <th class="py-2 px-3" style="border: 1px solid #333; font-size: 9.5pt;">Nama Akun</th>
                            <th class="py-2 px-3 text-right" style="border: 1px solid #333; font-size: 9.5pt;">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-800 dark:text-gray-200">
                        @forelse($data['net_assets'] as $netAsset)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="py-2 px-3" style="border: 1px solid #333;">
                                    @if(isset($netAsset['is_virtual']) && $netAsset['is_virtual'])
                                        <span class="px-2 py-0.5 rounded text-xs border border-gray-400">AUTO</span>
                                    @else
                                        {{ $netAsset['code'] }}
                                    @endif
                                </td>
                                <td class="py-2 px-3 {{ isset($netAsset['is_virtual']) ? 'italic' : '' }}" style="border: 1px solid #333;">{{ $netAsset['name'] }}</td>
                                <td class="py-2 px-3 text-right" style="border: 1px solid #333;">{{ number_format($netAsset['balance'], 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center italic text-gray-500" style="border: 1px solid #333;">Tidak ada data aset neto.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="font-bold bg-gray-50 dark:bg-gray-800/20 text-gray-900 dark:text-gray-100">
                            <td colspan="2" class="py-2 px-3 text-right" style="border: 1px solid #333;">Subtotal Aset Neto</td>
                            <td class="py-2 px-3 text-right" style="border: 1px solid #333;">{{ number_format($data['total_net_assets'], 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>

                {{-- Grand Total Pasiva --}}
                <table class="data-table w-full text-sm text-left border-collapse mt-4" style="border: 1px solid #333;">
                    <tfoot>
                        <tr class="font-bold bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 {{ $data['is_balanced'] ? '' : 'text-red-600 dark:text-red-400' }}">
                            <td class="py-4 px-3 text-right uppercase tracking-wider text-base" style="border: 1px solid #333;">Total Kewajiban + Aset Neto</td>
                            <td class="py-4 px-3 text-right text-base w-48" style="border: 1px solid #333;">{{ number_format($data['total_liabilities_net_assets'], 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>

                {{-- Tanda Tangan --}}
                <table class="signature-section" style="width: 100%; margin-top: 40px; page-break-inside: avoid; border-collapse: collapse; border: none;">
                    <tr>
                        <td style="width: 50%; text-align: center; vertical-align: top; border: none !important; font-size: 9.5pt; padding: 0;">
                            <div>Mengetahui,</div>
                            <div style="font-size: 9pt; color: #4b5563; margin-top: 3px; font-weight: bold;">Ketua Majelis Jemaat</div>
                            <div style="height: 70px;"></div>
                            <div style="font-weight: bold; text-decoration: underline;">{{ $profile?->ketua_majelis ?? 'Pdt. Sion Oepura, S.Th' }}</div>
                        </td>
                        <td style="width: 50%; text-align: center; vertical-align: top; border: none !important; font-size: 9.5pt; padding: 0;">
                            <div>Semarang, {{ \Illuminate\Support\Carbon::now()->translatedFormat('d F Y') }}</div>
                            <div style="font-size: 9pt; color: #4b5563; margin-top: 3px; font-weight: bold;">Bendahara Jemaat</div>
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
    window.printFinancialPosition = function() {
        var el = document.getElementById('financial-position-printable-area');
        if (!el) {
            alert('Tidak ada data laporan untuk dicetak. Pilih tanggal terlebih dahulu.');
            return;
        }
        var css = [
            '@page { size: portrait; margin: 1.5cm; }',
            'body { font-family: "Times New Roman", Times, serif; margin: 0; padding: 0; color: #111827; background: #fff; font-size: 10pt; }',
            'table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }',
            'table.data-table th, table.data-table td { border: 1px solid #333 !important; padding: 6px 8px; vertical-align: middle; }',
            'table.data-table th { background-color: #f3f4f6 !important; font-weight: bold; text-align: left; font-size: 9.5pt; -webkit-print-color-adjust: exact; print-color-adjust: exact; }',
            'table.data-table th[colspan="3"] { text-align: center; text-transform: uppercase; font-size: 11pt; background-color: #e5e7eb !important; }',
            'table.data-table tfoot tr td { font-weight: bold; background-color: #f3f4f6 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }',
            '.text-right { text-align: right !important; }',
            '.text-center { text-align: center !important; }',
            '.kop-table td, .signature-section td { border: none !important; }',
            'h1, h2, h3, p { margin: 0; }'
        ].join('');
        
        var html = '<!DOCTYPE html><html lang="id"><head><title>Laporan Posisi Keuangan (Neraca)</title><style>' + css + '</style></head><body>' + el.innerHTML + '</body></html>';
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