# Walkthrough: Cetak Daftar Jemaat dengan Filter

Fitur "Cetak Daftar Jemaat" telah berhasil ditambahkan pada `MemberResource` lengkap dengan filter dinamis dan tampilan cetak monokrom.

## Perubahan yang Dilakukan

1. **Header Action di Indeks Member**
   - **[ListMembers.php](file:///\\wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/MemberResource/Pages/ListMembers.php)**: Menambahkan method `getHeaderActions()` yang mendaftarkan action `printMembers` (Label: "Cetak Daftar Jemaat", Icon: `heroicon-o-printer`, Color: `success`).
   - Menyediakan form modal dengan filter:
     - `membership_status_id`: Menggunakan relasi dynamic GMIT status yang aktif.
     - `gender`: Pilihan L (Laki-laki) atau P (Perempuan).
   - Diarahkan ke route `reports.members_list` dengan query parameters data filter.

2. **Controller Method**
   - **[PastoralReportController.php](file:///\\wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Http/Controllers/PastoralReportController.php)**: Menambahkan method `printMembersList(Request $request)` yang menyaring data `Member` berdasarkan parameter status keanggotaan dan jenis kelamin (bila diisi), mengurutkan jemaat secara `ASC` berdasarkan `first_name`, mengambil profil gereja `ChurchProfile::first()`, dan mengembalikan view `reports.members-list`.

3. **Routing**
   - **[web.php](file:///\\wsl.localhost/Ubuntu-24.04/home/ediwe/sig/routes/web.php)**: Mendaftarkan route GET `/admin/reports/members/print` yang memanggil `PastoralReportController@printMembersList` dengan nama rute `reports.members_list` di bawah middleware `auth`.

4. **Tampilan Cetak (Blade)**
   - **[members-list.blade.php](file:///\\wsl.localhost/Ubuntu-24.04/home/ediwe/sig/resources/views/reports/members-list.blade.php)**: Membuat berkas tampilan cetak monokrom portrait.
   - Kop Surat didesain menggunakan format tabel tanpa border (rata kiri) yang memuat Logo, `gmit_name`, `church_name`, dan `address` dari `$profile`.
   - Menyediakan tombol "Cetak PDF" dengan class `print-btn` dan event `onclick="window.print()"`.
   - Menampilkan tabel data jemaat (No, Nama Lengkap, L/P, Tempat Lahir, Tanggal Lahir, Usia, No. KK, SHDK, Status).
   - Menambahkan area tanda tangan dinamis (Ketua Majelis dan Sekretaris) di bawah tabel.
   - Menyertakan script auto-print `window.onload = function() { window.print(); }`.

5. **Pengujian & Validasi**
   - **[EventServiceTest.php](file:///\\wsl.localhost/Ubuntu-24.04/home/ediwe/sig/tests/Feature/EventServiceTest.php)**: Menambahkan test case untuk memastikan route `reports.members_list` memerlukan autentikasi dan berhasil diakses oleh user yang terautentikasi.

---

## Hasil Pengujian & Validasi

Unit/Feature test dijalankan menggunakan command `php artisan test`. Hasilnya:
- `test_print_members_requires_authentication` **PASSED**
- `test_authenticated_user_can_access_print_members` **PASSED**
