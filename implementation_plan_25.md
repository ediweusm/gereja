# Rencana Implementasi: Cetak Daftar Jemaat dengan Filter

Dokumen ini menjelaskan rencana penambahan fitur cetak daftar jemaat berdasarkan filter status keanggotaan (`membership_status_id`) dan jenis kelamin (`gender`).

## Rencana Perubahan

### Langkah 1: Tambahkan Action di Halaman List
- **File:** `app/Filament/Resources/MemberResource/Pages/ListMembers.php`
- **Tindakan:** Tambahkan/perbarui method `getHeaderActions()` untuk mendaftarkan aksi `printMembers`.
- **Form Input:**
  - `membership_status_id`: Pilihan relasi ke status keanggotaan jemaat GMIT (aktif).
  - `gender`: Pilihan Laki-laki / Perempuan.
- **Aksi:** Melakukan redirect ke route `reports.members_list` dengan membawa query parameters.

### Langkah 2: Tambahkan Method di Controller
- **File:** `app/Http/Controllers/PastoralReportController.php`
- **Tindakan:** Tambahkan metode `printMembersList(Request $request)`:
  - Ambil parameter status dan gender dari request.
  - Query `Member` dengan relasi `['family', 'familyPosition', 'membershipStatus']`.
  - Gunakan `when` untuk menyaring berdasarkan status keanggotaan dan jenis kelamin jika bernilai tidak kosong.
  - Urutkan jemaat secara `ASC` berdasarkan nama depan (`first_name`).
  - Dapatkan profil gereja `ChurchProfile::first()`.
  - Kembalikan (return) view `reports.members-list`.

### Langkah 3: Daftarkan Rute (Route)
- **File:** `routes/web.php`
- **Tindakan:** Daftarkan rute GET `/admin/reports/members/print` yang memanggil `PastoralReportController@printMembersList`, beri nama rute `reports.members_list`, dan amankan dengan middleware `auth`.

### Langkah 4: Buat Template Cetak (Blade)
- **File:** `resources/views/reports/members-list.blade.php`
- **Tindakan:** Buat berkas tampilan cetak dengan gaya monokrom potret yang bersih:
  - Tampilkan Kop Surat rata kiri berisi Logo, GMIT Name, Church Name, dan Address dari `$profile`.
  - Tampilkan tombol print yang disembunyikan menggunakan `@media print`.
  - Tampilkan Tabel Jemaat (No, Nama Lengkap, L/P, Tempat Lahir, Tanggal Lahir, Usia, No. KK, SHDK, Status).
  - Tampilkan bagian Tanda Tangan dinamis (Ketua Majelis & Sekretaris) di bagian bawah.
  - Sertakan naskah otomatis `window.print()` pada saat pemuatan halaman.

---

## Verifikasi Plan

### Linter
- Jalankan `php -l` pada file yang diubah untuk memeriksa sintaks.

### Uji Manual & Integrasi
- Verifikasi tombol "Cetak Daftar Jemaat" muncul di halaman indeks Daftar Jemaat panel admin Filament.
- Coba cetak tanpa filter (semua), dan periksa apakah daftar jemaat terunduh/tercetak lengkap.
- Coba filter berdasarkan gender/status tertentu dan pastikan data terfilter secara akurat.
