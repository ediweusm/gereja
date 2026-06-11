# Rencana Implementasi: Cetak Laporan Mutasi Jemaat Berdasarkan Rentang Tanggal

Dokumen ini menjelaskan rencana penambahan fitur cetak laporan mutasi jemaat berdasarkan filter tanggal mulai (`start_date`) dan selesai (`end_date`).

## Rencana Perubahan

### Langkah 1: Tambahkan Action di Halaman List
- **File:** `app/Filament/Resources/MemberMutationResource/Pages/ListMemberMutations.php`
- **Tindakan:** Tambahkan aksi `printMutationsByRange` di dalam `getHeaderActions()`.
- **Form Input:** `start_date` dan `end_date` (keduanya `DatePicker` dan `required`).
- **Aksi:** Melakukan redirect ke route `reports.mutations_by_range` dengan menyertakan query parameters dari data form.

### Langkah 2: Tambahkan Method di Controller
- **File:** `app/Http/Controllers/PastoralReportController.php`
- **Tindakan:** Tambahkan metode `printMutationsByRange(Request $request)`:
  - Ambil filter tanggal dari query string.
  - Ambil data `MemberMutation` dengan relasi `['member', 'oldRayon', 'newRayon']`, difilter menggunakan `whereBetween('mutation_date', [$startDate, $endDate])` dan diurutkan secara `ASC` berdasarkan tanggal mutasi.
  - Ambil data profil gereja `ChurchProfile::first()`.
  - Return view `reports.mutations-by-range`.

### Langkah 3: Daftarkan Rute (Route)
- **File:** `routes/web.php`
- **Tindakan:** Daftarkan rute GET `admin/reports/mutations/print` yang memanggil `PastoralReportController@printMutationsByRange`, beri nama `reports.mutations_by_range`, dan amankan dengan middleware `auth`.

### Langkah 4: Buat Template Cetak (Blade)
- **File:** `resources/views/reports/mutations-by-range.blade.php`
- **Tindakan:** Buat template cetak monokrom khusus landscape:
  - Tampilkan Kop Surat dari data profil gereja.
  - Tampilkan Tombol Cetak (yang hilang saat dicetak menggunakan CSS `@media print`).
  - Tampilkan Tabel Mutasi (No, Tanggal, Nama Jemaat, Jenis Mutasi, Keterangan, Alasan).
  - Terapkan logika Blade pada kolom Keterangan untuk memilah tampilan berdasarkan jenis mutasi (Atestasi Masuk/Titipan -> `origin_church`, Atestasi Keluar -> `destination_church`, Pindah Rayon -> `oldRayon -> newRayon`).
  - Tampilkan area Tanda Tangan pengurus gereja.
  - Tambahkan script JavaScript `window.onload = function() { window.print(); }`.

---

## Verifikasi Plan

### Linter & PHP Compiler
- Lakukan linting `php -l` pada semua berkas php yang diubah (`ListMemberMutations.php`, `PastoralReportController.php`, `web.php`).

### Uji Manual & Integrasi
- Verifikasi tombol "Cetak Laporan Mutasi" muncul di halaman indeks mutasi di dashboard Filament.
- Masukkan rentang tanggal, klik kirim, dan pastikan sistem membuka tab/halaman baru untuk cetak laporan.
- Pastikan halaman cetak otomatis memicu dialog cetak browser (`window.print()`) dan memiliki tata letak landscape monokrom yang rapi dengan Kop Surat, data mutasi yang terfilter secara benar, dan tanda tangan pengurus di bagian bawah.
