<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            // AKTIVA
            '100000' => 'AKTIVA',
            '110000' => 'AKTIVA LANCAR',
            '111000' => 'KAS DAN SETARA KAS',
            '111100' => 'KAS',
            '111110' => 'Kas Kecil',
            '111120' => 'Brankas',
            '111200' => 'BANK',
            '111210' => 'Bank Mandiri Rek. No.',
            '111220' => 'Bank BCA Rek. No.',
            '120000' => 'AKTIVA TETAP',
            '121000' => 'BANGUNAN',
            '121100' => 'AKUMULASI PENYUSUTAN BANGUNAN',
            '122000' => 'PERALATAN',
            '122100' => 'AKUMULASI PENYUSUTAN PERALATAN',
            '123000' => 'TANAH',
            
            // KEWAJIBAN & AKTIVA BERSIH
            '200000' => 'KEWAJIBAN DAN AKTIVA BERSIH',
            '210000' => 'KEWAJIBAN',
            '211000' => 'Simpanan Jemaat',
            '290000' => 'AKTIVA BERSIH',
            
            // PENDAPATAN
            '300000' => 'PENDAPATAN',
            '310000' => 'PENDAPATAN RUTIN',
            '311000' => 'PERSEMBAHAN KOLEKTE',
            '311100' => 'KOLEKTE IBADAH RUTIN',
            '311101' => 'Tangguk 1 (Rutin)',
            '311102' => 'Kolekte Hari - Hari Raya Gerejawi',
            '311103' => 'Kolekte Ibadat Syukur Keluarga',
            '311104' => 'Kolekte Ibadat Rumah Tangga',
            '311105' => 'Kolekte Kebaktian Persiapan & Perjamuan Kudus',
            '311106' => 'Kolekte Kebaktian Penggembalaan & Baptisan Kudus',
            '311107' => 'Kolekte Kebaktian Penggembalaan & Peneguhan Sidi',
            '311108' => 'Kolekte Kebaktian Penggemb & Pernikahan Kudus',
            '311200' => 'KOLEKTE KATEGORIAL',
            '311201' => 'Kolekte Ibadat Kaum Bapak',
            '311202' => 'Kolekte Ibadat Kaum Perempuan',
            '311203' => 'Kolekte Ibadat Pemuda',
            '311204' => 'Kolekte Ibadat PAR',
            '311205' => 'Kolekte Ibadat PD/PI',
            '311206' => 'Kolekte Pra Katekisasi & Katekisasi Sidi',
            '311207' => 'Kolekte TK, SD, SMP,SMA PT, Pemuda, Pelajar, Mhs',
            '311208' => 'Kolekte UPP Lansia / Badan Diakonat',
            '311209' => 'Kolekte Ibadat Ibadat MJH/Sekretariat JSO',
            '311210' => 'Kolekte Ibadat Pasutri',
            '311300' => 'KOLEKTE LAIN LAIN',
            '311301' => 'Kolekte Kebaktian Tahun Baru 1 Januari 2025',
            '311302' => 'Kolekte HUT JSO tanggal, 14 Desember 2025',
            '311303' => 'Kolekte Natal Rayon dan Kategorial Fungsional',
            '311304' => 'Kolekte (Amplop Palungan) Natal',
            '311305' => 'Kolekte Kotak HUT Warga JSO menurut bulan lahir',
            '311306' => 'Kolekte Tanggal 31 Desember 2025',
            '311307' => 'Kolekte (Amplop Palungan) Tanggal 31 Desember 2025',
            '311308' => 'Kolekte Persid. MJ, Keg. MJH, BPPJ, UPPMJ & Lainnya',
            '311309' => 'Uang pinangan',
            '311310' => 'Sisa belanja UPP, Panitia - Panitia, dll.',
            '312000' => 'NATURA',
            '312100' => 'Lelang Natura (yang telah diuangkan)',
            '313000' => 'NAZAR',
            '313001' => 'Persembahan Nazar Kebaktian Minggu',
            '313002' => 'Persembahan Nazar Hari Raya Gerejawi',
            '313003' => 'Persembahan Nazar Syukur Keluarga',
            '313004' => 'Persembahan Nazar Ibadat Rumah Tangga',
            '313005' => 'Persembahan Nazar Peneguhan Nikah',
            '313006' => 'Persembahan Nazar dalam Kebaktian PMK.',
            '313007' => 'Persembahan Nazar Ibadat UPP. Kaum Bapak',
            '313008' => 'Persembahan Nazar Ibadat kamper',
            '313009' => 'Persembahan Nazar Ibadat UPP. Pemuda',
            '313010' => 'Persembahan Nazar Ibadat UPP. PAR',
            '313011' => 'Persembahan Nazar PD/PI',
            '313012' => 'Persembahan Nazar Khusus dari Jemaat',
            '313013' => 'Persembahan Nazar Baptisan Kudus',
            '313014' => 'Persembahan Nazar Peneguhan Sidi',
            '314000' => 'PERPULUHAN',
            '314001' => 'Persembahan Perpuluhan Kebaktian Minggu',
            '314002' => 'Persembahan Perpuluhan Hari Raya Gerejawi',
            '314003' => 'Persembahan Perp. dalam Kebaktian PMK.',
            '314004' => 'Persembahan Prp. khusus dr Warga JSO',
            '315000' => 'PERSEMBAHAN SYUKUR JEMAAT',
            '315001' => 'Kebaktian Hari Minggu',
            '315002' => 'Kebaktian Hari Raya Gerejawi',
            '315003' => 'Spontanitas Syukur dari Keluarga JSO',
            '315004' => 'Spontanitas Syukur dari Keluarga dari luar JSO',
            '316000' => 'USAHA DANA BPPJ',
            '316100' => 'YAYASAN PENDIDIKAN KRISTEN SION OEPURA',
            '316101' => 'Uang Pendaftaran TK.Sion Oepura TA. 2025-2026',
            '316102' => 'SPP dari Anak TK. Sion Oepura. TA. 2025-2026',
            '316103' => 'Kolekte Ibadat Pengajar & Anak.',
            '317000' => 'DANA CADANGAN',
            '317001' => 'Dana Cadangan dari Saldo Akhir Tahun',
            '318000' => 'LAIN-LAIN',
            '318001' => 'Surat - surat Gerejawi',
            '318002' => 'Bunga Bank',
            '318003' => 'Sewa Gedung',
            '320000' => 'PENDAPATAN PEMBANGUNAN',
            '321000' => 'DALAM JEMAAT',
            '321100' => 'PERSEMBAHAN JEMAAT',
            '321101' => 'Tanggungan Pembangunan tiap KK Warga JSO (Kartu Pe',
            '321102' => 'Persembahan Khusus Pembangunan Warga JSO',
            '321103' => 'Persembahan kantong ke 2/Peti Pembangunan',
            '321104' => 'Persembahan Etnis dan Lelang',
            '321105' => 'Persembahan Kasih Sidi Baru',
            '321106' => 'Persembangan Khusun Celengan Pembangunan',
            '321200' => 'USAHA DANA BPPJ & UPPMJ',
            '321201' => 'Kaum Bapak',
            '321202' => 'Kaum Perempuan',
            '321203' => 'Pemuda',
            '321300' => 'USAHA DANA PANITIA PEMBANGUNAN',
            '321301' => 'Kupon Berhadiah',
            '321302' => 'Proposal dll',
            '322000' => 'LUAR JEMAAT',
            '322001' => 'Donatur dalam bentuk Uang',
            '322002' => 'Donatur dalam bentuk Barang',
            '323000' => 'SUMBANGAN/PINJAMAN LAINNYA',
            '323100' => 'SUMBANGAN',
            '323101' => 'Propinsi/PEMKOT KUPANG',
            '323102' => 'Lembaga Sosial',
            '323103' => 'MS GMIT + MK KOTA KUPANG',
            '323200' => 'PINJAMAN',
            '323201' => 'Pemerintah',
            '323202' => 'Swasta (Bank TLM)',
            '323203' => 'Perseorangan',
            '323300' => 'LAIN LAIN',
            
            // BIAYA
            '400000' => 'BIAYA',
            '410000' => 'PROGRAM GEREJA',
            '411000' => 'PENGEMBANGAN KOMPETENSI JEMAAT',
            '411100' => 'Organisasi Pemuda',
            '411200' => 'Organisasi PAR',
            '411300' => 'Kegiatan Pengembangan Manajemen Gereja',
            '411400' => 'Kegiatan Hubungan Masyarakat',
            '412000' => 'DIAKONIA',
            '412100' => 'Sumbangan Perekonomian',
            '412200' => 'Sumbangan Kesehatan',
            '412300' => 'Sumbangan Kematian',
            '412400' => 'Sumbangan Pendidikan',
            '412500' => 'Diakona Lainnya',
            '413000' => 'SUBSIDI PROGRAM',
            '413100' => 'Operasional Rayon',
            '413200' => 'Subsidi Yayasan',
            '413300' => 'Subsidi Lainnya',
            '414000' => 'PENGEMBANGAN SARANA DAN PRASARANA',
            '414100' => 'Pengadaan dan Pemeliharaan Invn Gereja',
            '414200' => 'Pengadaan dan perawatan mebelair',
            '414300' => 'Pengembangan sarana perpustakaan',
            '414400' => 'Buku Perpustakaan / Majalah / Tabloid',
            '420000' => 'NON PROGRAM GEREJA',
            '421000' => 'BELANJA PEGAWAI',
            '421100' => 'Honorarium Pendeta',
            '421200' => 'Honorarium Pengurus Gereja',
            '421300' => 'Gaji Karyawan',
            '421400' => 'Gaji Koster',
            '421500' => 'Gaji Tenaga Pengamanan dan Driver',
            '421600' => 'Honorarium Lain-lain',
            '430000' => 'BELANJA KANTOR/SEKRETARIAT',
            '430100' => 'Komputer, LCD dsb',
            '430200' => 'Belanja ATK',
            '430300' => 'Perbaikan Peralatan/Gedung/Taman dll',
            '430400' => 'Biaya Listrik',
            '430500' => 'Biaya Telepon',
            '430600' => 'Biaya Air PAM',
            '430700' => 'Biaya Air Minum',
            '430800' => 'Biaya Operasional Lainnya',
        ];

        // Menyimpan id dari akun yang sudah di-insert untuk memetakan parent_id
        $insertedAccounts = [];

        foreach ($accounts as $code => $name) {
            // Menentukan Tipe Akun (Asset, Liability, dll)
            $type = 'Asset'; // Default 1xxxxx
            if (str_starts_with($code, '2')) {
                $type = str_starts_with($code, '21') ? 'Liability' : 'Net Asset';
            } elseif (str_starts_with($code, '3')) {
                $type = 'Revenue';
            } elseif (str_starts_with($code, '4')) {
                $type = 'Expense';
            }

            // Menentukan Pembatasan Dana (Terikat Temporer untuk Pendapatan Pembangunan 32xxxx)
            $restrictionType = 'Tidak Terikat';
            if (str_starts_with($code, '32')) {
                $restrictionType = 'Terikat Temporer';
            }

            // Memanggil helper internal untuk mencari Parent Code
            $parentCode = $this->resolveParentCode($code);
            $parentId = $parentCode && isset($insertedAccounts[$parentCode]) 
                            ? $insertedAccounts[$parentCode] 
                            : null;

            // Memasukkan data ke database menggunakan updateOrCreate agar aman dijalankan berulang
            $account = Account::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'type' => $type,
                    'restriction_type' => $restrictionType,
                    'parent_id' => $parentId,
                    'is_active' => true,
                ]
            );

            // Simpan ID untuk keperluan referensi child di iterasi berikutnya
            $insertedAccounts[$code] = $account->id;
        }
    }

    /**
     * Helper untuk menentukan kode induk (parent) berdasarkan pola trailing zero (0).
     */
    private function resolveParentCode(string $code): ?string
    {
        // Jika kode berakhiran 5 buah nol (Level 1 / Root), tidak punya parent
        if (substr($code, 1, 5) === '00000') return null; 
        
        // Level 2 (Berakhiran 4 buah nol) -> Parent adalah digit ke-1 + 5 buah nol
        if (substr($code, 2, 4) === '0000') return substr($code, 0, 1) . '00000';
        
        // Level 3 (Berakhiran 3 buah nol) -> Parent adalah digit 1-2 + 4 buah nol
        if (substr($code, 3, 3) === '000') return substr($code, 0, 2) . '0000';
        
        // Level 4 (Berakhiran 2 buah nol) -> Parent adalah digit 1-3 + 3 buah nol
        if (substr($code, 4, 2) === '00') return substr($code, 0, 3) . '000';
        
        // Level 5 & 6 (Transaksi spesifik) -> Parent adalah digit 1-4 + 2 buah nol
        return substr($code, 0, 4) . '00';
    }
}