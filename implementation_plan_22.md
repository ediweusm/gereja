# Rencana Implementasi: Dashboard Stats Widgets (Jemaat & Keuangan)

Dokumen ini menjelaskan rencana pembuatan tiga buah widget Dashboard Filament (`StatsOverview`) untuk menyajikan informasi ringkasan Jemaat dan Keuangan Gereja secara berurutan.

## Asumsi & Temuan Kode Sumber

Berdasarkan analisis file model dan migrasi:
1. **Member (Jemaat) Aktif:** Model `Member` memiliki kolom `is_deceased` (boolean). Status "aktif" didefinisikan sebagai jemaat yang masih hidup (`is_deceased = false`), sesuai dengan pola filter pada halaman `BirthdayReport.php`.
2. **Keluarga Pra Sejahtera:** Model `Family` memiliki scope `needsAssistance()` yang menyaring data `families` dengan kategori rumah/kondisi rumah (`house_category_id`) bernilai `darurat` atau `semi-permanen`. Kita akan menggunakan scope ini untuk menghitung keluarga pra-sejahtera.
3. **Ulang Tahun Minggu Ini:** Menggunakan logika penentuan hari dalam minggu aktif saat ini (dari Senin hingga Minggu menggunakan Carbon) dan mencocokkan bulan (`whereMonth`) dan tanggal (`whereDay`) lahir jemaat.
4. **Akun Pendapatan (Revenue) & Pengeluaran (Expense):**
   - Transaksi terhubung melalui `journal_items` (debit, credit) ke `journals` (tanggal transaksi: `transaction_date`) dan `accounts` (tipe: `type`, nama: `name`).
   - Pendapatan (Revenue) dihitung sebagai `Sum(credit) - Sum(debit)`.
   - Pengeluaran (Expense) dihitung sebagai `Sum(debit) - Sum(credit)`.
   - Akun **Bantuan** diidentifikasi berdasarkan nama akun yang mengandung kata `'Bantuan'`, `'Diakonia'`, atau `'Diakona'` (untuk mencakup `'Diakona Lainnya'` di seeder).
   - Akun **Biaya Operasional** diidentifikasi dari seluruh akun `Expense` lainnya yang tidak mengandung kata-kata di atas.

---

## Rencana Perubahan

### Langkah 1: DemographicStatsWidget
- **Command:** `php artisan make:filament-widget DemographicStatsWidget --stats-overview`
- **File Baru:** `app/Filament/Widgets/DemographicStatsWidget.php`
- **Isi Stats:**
  1. **Total Jemaat:** `Member::where('is_deceased', false)->count()`
  2. **Jemaat Laki-Laki:** `Member::where('is_deceased', false)->where('gender', 'L')->count()`
  3. **Jemaat Perempuan:** `Member::where('is_deceased', false)->where('gender', 'P')->count()`
  4. **Total Kepala Keluarga:** `Family::count()`
  5. **Keluarga Pra Sejahtera:** `Family::needsAssistance()->count()`
  6. **Ulang Tahun Minggu Ini:** Count Member yang `birth_date` jatuh pada hari-hari di minggu berjalan.
- **Konfigurasi Tambahan:**
  - `protected int | string | array $columnSpan = 'full';`
  - `protected static ?int $sort = 1;`
  - Icon yang relevan pada masing-masing stat.

### Langkah 2: IncomeStatsWidget
- **Command:** `php artisan make:filament-widget IncomeStatsWidget --stats-overview`
- **File Baru:** `app/Filament/Widgets/IncomeStatsWidget.php`
- **Isi Stats (Filter `accounts.type = 'Revenue'`):**
  1. **Persembahan Minggu Ini:** Filter `journals.transaction_date` minggu ini.
  2. **Persembahan Bulan Ini:** Filter `journals.transaction_date` bulan ini.
  3. **Persembahan Tahun Ini:** Filter `journals.transaction_date` tahun ini.
- **Konfigurasi Tambahan:**
  - `protected static ?int $sort = 2;`
  - Format Rupiah (`Rp ` + `number_format`).
  - Warna `success` dan icon `'heroicon-o-arrow-trending-up'`.

### Langkah 3: ExpenseStatsWidget
- **Command:** `php artisan make:filament-widget ExpenseStatsWidget --stats-overview`
- **File Baru:** `app/Filament/Widgets/ExpenseStatsWidget.php`
- **Isi Stats (Filter `accounts.type = 'Expense'`):**
  - **Bantuan (Minggu/Bulan/Tahun ini):** Filter nama akun mengandung `'Bantuan'`, `'Diakonia'`, atau `'Diakona'`.
  - **Operasional (Minggu/Bulan/Tahun ini):** Filter nama akun tidak mengandung kata-kata tersebut.
- **Konfigurasi Tambahan:**
  - `protected static ?int $sort = 3;`
  - `protected function getColumns(): int { return 3; }` (mengatur grid columns menjadi 3 agar Bantuan di baris pertama dan Biaya Operasional di baris kedua).
  - Format Rupiah, warna `danger` (untuk bantuan) dan `warning` (untuk operasional), serta icon `'heroicon-o-arrow-trending-down'`.

---

## Verifikasi Plan

### Otomatis / Unit Test / Linter
- Jalankan pemeriksaan sintaksis php menggunakan linting atau mengecek status via Filament.
- Pastikan tidak ada error kompilasi/run-time ketika mengakses dashboard admin.

### Verifikasi Manual
- Buka dashboard utama panel Filament.
- Periksa tampilan ketiga widget (Demographic, Income, Expense) yang harus muncul berurutan dari atas ke bawah.
- Periksa keselarasan data (nilai-nilai rupiah terformat dengan baik, warna stat sesuai, dan penempatan baris grid pada ExpenseStatsWidget rapi).
