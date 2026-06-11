# Walkthrough: Dashboard Stats Widgets (Jemaat & Keuangan)

Langkah-langkah berikut telah berhasil diselesaikan untuk membuat dashboard statistik (StatsOverview) guna menyajikan ringkasan data Jemaat dan Keuangan Gereja secara akurat.

## Perubahan yang Dilakukan

1. **[DemographicStatsWidget](file:///\\wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Widgets/DemographicStatsWidget.php)**
   - Menambahkan file widget baru untuk menampilkan demografi jemaat.
   - Menghitung **Total Jemaat** (aktif: `is_deceased = false`), **Jemaat Laki-Laki**, dan **Jemaat Perempuan** dari model `Member`.
   - Menghitung **Total Kepala Keluarga** dan **Keluarga Pra Sejahtera** (menggunakan scope `needsAssistance()`) dari model `Family`.
   - Menghitung **Jemaat Ulang Tahun Minggu Ini** menggunakan pencocokan tanggal dan bulan lahir terhadap 7 hari di minggu ini dengan Carbon.
   - Mengatur span kolom widget menjadi `full` dan sort order `$sort = 1`.

2. **[IncomeStatsWidget](file:///\\wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Widgets/IncomeStatsWidget.php)**
   - Menambahkan file widget baru untuk menampilkan statistik pendapatan gereja.
   - Menghitung **Persembahan Minggu Ini**, **Persembahan Bulan Ini**, dan **Persembahan Tahun Ini** dengan melakukan sum `credit - debit` pada akun-akun tipe `Revenue` yang difilter rentang tanggal transaksinya menggunakan Carbon.
   - Memformat nilai nominal ke dalam mata uang Rupiah (`Rp 1.234.567`).
   - Memberikan warna `success`, icon `'heroicon-o-arrow-trending-up'`, dan sort order `$sort = 2`.

3. **[ExpenseStatsWidget](file:///\\wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Widgets/ExpenseStatsWidget.php)**
   - Menambahkan file widget baru untuk menampilkan pengeluaran.
   - Membagi jenis akun pengeluaran (`Expense`) menjadi **Bantuan/Diakonia** (nama akun mengandung 'Bantuan', 'Diakonia', atau 'Diakona') dan **Biaya Operasional** (akun pengeluaran lainnya).
   - Menghitung total pengeluaran dengan sum `debit - credit` untuk filter Minggu Ini, Bulan Ini, dan Tahun Ini.
   - Mengatur layout grid widget menjadi `3` kolom, sehingga baris pertama (3 item) menampilkan Bantuan dan baris kedua (3 item) menampilkan Biaya Operasional.
   - Memberikan warna `danger` untuk Bantuan, `warning` untuk Biaya Operasional, icon `'heroicon-o-arrow-trending-down'`, dan sort order `$sort = 3`.

---

## Hasil Pengujian & Validasi

Sebuah test script internal telah dijalankan untuk melakukan instansiasi widget dan mengeksekusi fungsi queries-nya di atas database riil workspace. Seluruh queries berhasil dieksekusi tanpa error dengan hasil output sebagai berikut:

```text
=== TESTING DEMOGRAPHIC WIDGET ===
- Total Jemaat: 14
- Jemaat Laki-Laki: 8
- Jemaat Perempuan: 6
- Total Kepala Keluarga: 5
- Keluarga Pra Sejahtera: 1
- Ulang Tahun Minggu Ini: 0

=== TESTING INCOME WIDGET ===
- Persembahan Minggu Ini: Rp 0
- Persembahan Bulan Ini: Rp 3.125.000
- Persembahan Tahun Ini: Rp 3.125.000

=== TESTING EXPENSE WIDGET ===
- Bantuan Minggu Ini: Rp 0
- Bantuan Bulan Ini: Rp 0
- Bantuan Tahun Ini: Rp 0
- Biaya Operasional Minggu Ini: Rp 200.000
- Biaya Operasional Bulan Ini: Rp 4.150.000
- Biaya Operasional Tahun Ini: Rp 4.150.000
```
