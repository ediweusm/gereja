# Walkthrough: Penerjemahan Antarmuka Pengguna (UI) ke Bahasa Indonesia

Kami telah berhasil mengubah seluruh label, caption, nama kolom, grup navigasi, dan badge di dalam panel admin Filament menjadi berbahasa Indonesia.

---

## 1. Perubahan Struktur & Grouping Navigasi (Sidebar)

Navigasi di sidebar kini telah dikelompokkan dan dinamai ulang ke dalam Bahasa Indonesia:
1. **Akses & Keamanan**
   - **Pengguna** (`UserResource`): Mengelola data operator dan admin sistem.
   - **Peran** (`RoleResource` dari Filament Shield): Mengelola hak akses/role (Super Admin, Administrasi, Bendahara).
   - **Log Aktivitas** (`ActivityResource`): Audit trail untuk merekam aksi pengguna.
2. **Manajemen Keuangan**
   - **Akun** (`AccountResource`): Chart of Accounts (Daftar Akun Keuangan).
3. **Pengaturan & Master Data**
   - **Rayon** (`RayonResource`): Wilayah/sektor jemaat.
   - **Kamus Data** (`DataDictionaryResource`): Data referensi dinamis.

---

## 2. Detail Penerjemahan Resource

### A. Pengguna (Users)
- **Label Model**: `Pengguna` (Tunggal & Jamak).
- **Formulir**: `Nama Lengkap`, `Alamat Email`, `Kata Sandi`, `Peran / Hak Akses`, `Status Aktif`.
- **Tabel**: `Nama Lengkap`, `Alamat Email`, `Peran`, `Status Aktif`, `Tanggal Dibuat`.

### B. Akun (Accounts)
- **Label Model**: `Akun` (Tunggal & Jamak).
- **Formulir**: `Kode Akun`, `Nama Akun`, `Tipe Akun`, `Pembatasan Dana`, `Induk Akun`, `Status Aktif`.
- **Tipe Akun (Opsi & Badge)**:
  - `Asset` $\rightarrow$ **`Aset`**
  - `Liability` $\rightarrow$ **`Kewajiban`**
  - `Net Asset` $\rightarrow$ **`Aset Bersih`**
  - `Revenue` $\rightarrow$ **`Pendapatan`**
  - `Expense` $\rightarrow$ **`Beban`**
- **Tabel**: Kolom dan filter telah disesuaikan menggunakan pelabelan Bahasa Indonesia.

### C. Log Aktivitas (Activity Logs)
- **Label Model**: `Log Aktivitas` (Tunggal & Jamak).
- **Tabel**: `Nama Log`, `Deskripsi Tindakan`, `Model`, `Operator / Pengguna`, `Waktu Kejadian`.

### D. Peran (Roles - Filament Shield)
- Menggunakan terjemahan bawaan paket yang dipublikasikan di `lang/vendor/filament-shield/id/filament-shield.php`.
- Grup navigasi disesuaikan menjadi **`Akses & Keamanan`** agar sejajar dengan modul Pengguna dan Log Aktivitas.

---

## 3. Hasil Verifikasi Visual

Berikut adalah tangkapan layar (screenshots) hasil penerapan terjemahan Bahasa Indonesia pada antarmuka admin:

### Sidebar Navigation & Akses Keamanan
Grup navigasi **Akses & Keamanan** menampung menu Pengguna, Peran, dan Log Aktivitas secara rapi:

![Sidebar Navigation](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\sidebar_navigation_1780752454346.png)

### Daftar Akun Keuangan (Chart of Accounts)
Nama kolom tabel dan badge tipe akun (Aset, Kewajiban, Pendapatan, Beban) telah diterjemahkan sepenuhnya:

![Daftar Akun](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\accounts_page_1780752476040.png)

### Daftar Pengguna
Formulir dan tabel Pengguna menggunakan label Bahasa Indonesia seperti "Nama Lengkap" dan "Alamat Email":

![Daftar Pengguna](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\users_page_1780752495199.png)

### Rekaman Alur Verifikasi Otomatis (Video)
Rekaman WebP di bawah ini menampilkan navigasi lengkap pada panel admin setelah perubahan lokalisasi diterapkan:

![Rekaman Verifikasi UI](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\final_ui_check_1780752358040.webp)

---

## 4. Penerapan Auto-Slug Reaktif (Kamus Data)

Kami juga telah memodifikasi form schema pada `DataDictionaryResource` untuk mendukung fungsionalitas auto-slug yang reaktif:
- **Label**: Menambahkan parameter `->live(onBlur: true)` dan `afterStateUpdated` closure untuk menghasilkan slug secara otomatis ketika fokus keluar dari input Label.
- **Kode**: Bersifat *read-only*, *dehydrated* (agar tetap dikirim ke backend pada proses simpan), dan memiliki validasi *unique* yang mengabaikan rekaman saat ini pada mode edit.

### Tangkapan Layar Pengisian & Hasil Auto-Slug
Proses pengisian Label **Rumah Dinas Pastori** yang secara otomatis mengisi kolom Kode dengan **`rumah-dinas-pastori`**:

![Auto Populate Slug](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\datadict_slug_auto_populate_1780753778508.png)

### Rekaman Verifikasi Auto-Slug (Video)
Rekaman berikut menampilkan seluruh proses pengujian form reaktif auto-slug dari pembuatan hingga data berhasil disimpan ke database:

![Verifikasi Auto Slug](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\kamus_data_auto_slug_check_1780753715873.webp)

---

## 5. Modul Administrasi Jemaat (Kartu Keluarga & Mutasi Anggota)

Kami telah berhasil mengimplementasikan modul **Administrasi Jemaat** sesuai spesifikasi:

1. **`FamilyResource` (Kartu Keluarga)**:
   - Navigation: Di bawah grup **`Administrasi Jemaat`** dengan ikon `heroicon-o-home`.
   - Layout Form: Menggunakan struktur `Section` dan `Grid` untuk kebersihan antarmuka. Pilihan **Kategori Rumah** dan **Status Rumah** mengambil opsi secara dinamis dari database (`DataDictionary` yang aktif).
   - Tabel: Menampilkan nomor KK, **Nama Kepala Keluarga** (menggunakan eager-loaded relation dan full-text search), rayon, alamat, serta kolom khusus **`members_count`** (Jumlah Anggota).

2. **`MembersRelationManager` (Anggota Keluarga)**:
   - Ditautkan langsung di bawah halaman detail **Kartu Keluarga**.
   - Form menggunakan **Tabs** (Biodata, Kegerejaan, Sakramen, Kematian) untuk mengelola data yang sangat padat.
   - Visibilitas Kolom Kondisional:
     - Form sakramen baptis hanya muncul jika opsi **`Sudah Baptis`** dicentang.
     - Form sakramen pernikahan hanya muncul jika status pernikahan diatur sebagai **`Menikah Gereja`** atau **`Belum Menikah Gereja`** (civil).
     - Form tanggal kematian hanya muncul jika opsi **`Meninggal Dunia`** dicentang.

3. **`MemberMutationResource` (Mutasi Anggota)**:
   - Navigation: Di bawah grup **`Administrasi Jemaat`** dengan ikon `heroicon-o-arrows-right-left`.
   - Visibilitas Kolom Kondisional:
     - Input Rayon Lama/Baru muncul hanya jika jenis mutasi adalah **`Pindah Rayon`**.
     - Input Gereja Asal muncul hanya jika jenis mutasi adalah **`Atestasi Masuk`** atau **`Titipan`**.
     - Input Gereja Tujuan muncul hanya jika jenis mutasi adalah **`Atestasi Keluar`** atau **`Titipan`**.

### Tangkapan Layar Halaman & Relasi
#### Halaman Kartu Keluarga & Daftar Anggota
Daftar Kartu Keluarga menampilkan jumlah anggota secara real-time, dan halaman ubah menampilkan daftar anggota keluarga:

![Daftar Kartu Keluarga](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\families_list_1780756606055.png)

![Relation Manager Anggota](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\relation_manager_1780757621711.png)

#### Tab Sakramen Detail Anggota (Kondisional)
Tampilan data tab Sakramen saat status baptis dan sidi aktif:

![Tab Sakramen](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\sakramen_tab_1780757643571.png)

#### Daftar Mutasi Anggota
Tampilan daftar mutasi anggota dengan badge status yang berwarna-warni sesuai jenis mutasi:

![Daftar Mutasi](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\member_mutations_list_1780757658861.png)

### Rekaman Verifikasi Administrasi Jemaat (Video)
Rekaman WebP berikut menampilkan seluruh proses verifikasi data Kartu Keluarga, Anggota Keluarga, dan Mutasi Anggota di panel admin:

![Verifikasi Admin Jemaat](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\view_and_verify_admin_jemaat_1780756589189.webp)

---

## 6. Modul Manajemen Keuangan – Jurnal Umum (Double-Entry)

Kami telah mengimplementasikan modul **Jurnal Umum** lengkap dengan sistem **Double-Entry Bookkeeping** (Debit = Kredit) untuk Sprint 4 / Epic 4.

### Modifikasi [`Journal.php`](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Models/Journal.php)
1. **Auto-populate `created_by`** pada event `creating`: mengisi `created_by` dengan `auth()->id()` secara otomatis sehingga tidak perlu input manual oleh user.
2. **Auto-numbering `transaction_number`** (sudah ada): menghasilkan format `JRN-YYYYMM-XXXX` secara berurutan per bulan.
3. **Accessor `getTotalNominalAttribute()`**: menghitung total debit dari semua baris jurnal item untuk ditampilkan di tabel daftar jurnal.

### [`JournalResource.php`](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/JournalResource.php)

| Fitur | Detail |
|---|---|
| Grup Navigasi | `Manajemen Keuangan` |
| Ikon Navigasi | `heroicon-o-document-currency-dollar` |
| Label Navigasi | `Jurnal Umum` |

**Form** dibagi dua Section:
- **Header Jurnal**: tanggal transaksi (default hari ini), nomor bukti/nota, keterangan transaksi (wajib, full width).
- **Detail Transaksi / Double-Entry**: `Repeater` dengan `minItems(2)` dan 3 kolom (`Akun / Rekening`, `Debit`, `Kredit`).
  - Pilihan akun **hanya menampilkan akun transaksional** (leaf node / tidak memiliki child) yang aktif.
  - Format label akun: `{code} - {name}` (misal: `1-1001 - Kas Gereja`).
  - Akun yang sudah dipilih di baris lain **dinonaktifkan** (`disableOptionsWhenSelectedInSiblingRepeaterItems`).

**Validasi Balance (Debit = Kredit)**:
- **`rules()` pada Repeater**: Menggunakan rule kustom untuk memastikan jumlah total debit sama dengan jumlah total kredit. Jika tidak seimbang, validasi form akan gagal dan menampilkan pesan kesalahan di bawah repeater.

**Tabel**:
- Nomor Transaksi, Tanggal, Keterangan, dan **Total Transaksi** (diformat sebagai mata uang Rupiah Indonesia).
- **Filter rentang tanggal**: Dari Tanggal / Hingga Tanggal.

### Verifikasi Database
2 entri jurnal berhasil tersimpan dengan nomor otomatis:
- `JRN-202606-0001`
- `JRN-202606-0002`

---

## 7. Pengisian Data Sampel (Database Seeding)

Kami telah mengimplementasikan serangkaian seeder untuk mengisi database secara otomatis dengan data sampel yang sangat realistis untuk memudahkan proses pengujian sistem.

### Daftar Seeder Baru & Yang Dimodifikasi:
1. **[`DataDictionarySeeder.php`](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/database/seeders/DataDictionarySeeder.php)**: Menambahkan jenis persembahan/kontribusi jemaat (`contribution_type`) untuk mencakup:
   - Persembahan Persepuluhan (Tithing)
   - Persembahan Nazar (Pledge)
   - Persembahan Syukur (Thanksgiving)
   - Persembahan Pembangunan (Building Fund)
2. **[`UserSeeder.php`](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/database/seeders/UserSeeder.php)**: Membuat 3 akun pengguna bawaan beserta peran hak akses masing-masing (password: `password`):
   - **Super Admin**: `admin@sig.test` (peran: `super_admin`)
   - **Staf Administrasi**: `staff@sig.test` (peran: `administrasi`)
   - **Bendahara**: `bendahara@sig.test` (peran: `bendahara`)
3. **[`RayonSeeder.php`](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/database/seeders/RayonSeeder.php)**: Membuat 5 rayon wilayah pelayanan jemaat:
   - Rayon 1 (Matius), Rayon 2 (Markus), Rayon 3 (Lukas), Rayon 4 (Yohanes), Rayon 5 (Petrus)
4. **[`FamilyAndMemberSeeder.php`](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/database/seeders/FamilyAndMemberSeeder.php)**:
   - Membuat 10 Kartu Keluarga (KK) dengan detail alamat dan kontak yang realistis.
   - Mengisi 2-5 anggota keluarga per KK (total sekitar 30+ jiwa) yang memiliki status sakramen (baptis, sidi, nikah), jenis pekerjaan, tingkat pendidikan, dan penghasilan yang bervariasi.
   - Menambahkan 1 anggota jemaat berstatus meninggal dunia (`is_deceased = true`, lengkap dengan `death_date` di KK-2026-0004).
   - Menambahkan log mutasi anggota jemaat (Pindah Rayon dan Atestasi Masuk).
5. **[`JournalSeeder.php`](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/database/seeders/JournalSeeder.php)**:
   - Membuat 5 jurnal keuangan double-entry berimbang (Debit = Kredit) untuk transaksi: persembahan kolekte, pembayaran listrik & air, honorarium pendeta, bunga bank, dan tarian persepuluhan anggota.
   - Jurnal persepuluhan secara otomatis terhubung ke pencatatan kontribusi anggota jemaat.
6. **[`DatabaseSeeder.php`](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/database/seeders/DatabaseSeeder.php)**:
   - Mengintegrasikan seluruh seeder di atas.
   - Menambahkan proses pembersihan tabel jemaat dan keuangan secara aman (`FOREIGN_KEY_CHECKS=0`) untuk memastikan seeder bersifat *idempotent* (dapat dijalankan berulang-ulang tanpa terjadinya data duplikat).

---

## 8. Verifikasi Visual Data Hasil Seeder

Berikut adalah verifikasi tampilan data hasil seeder di panel admin Filament:

### A. Jurnal Umum (Journal Entries)
Kelima jurnal keuangan double-entry yang seimbang (Rutin Kolekte, Persepuluhan, Listrik, Honor Pendeta, Bunga Bank) berhasil di-seed:

![Daftar Jurnal Umum](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\jurnal_umum_list_1780769555917.png)

### B. Daftar Kartu Keluarga (KK)
Kesepuluh Kartu Keluarga (KK) beserta rayon dan jumlah anggota terhitung secara dinamis:

![Daftar Kartu Keluarga](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\kartu_keluarga_list_1780769574896.png)

### C. Daftar Anggota Keluarga
Anggota keluarga terpetakan di bawah masing-masing Kartu Keluarga secara otomatis dengan biodata lengkap:

![Daftar Anggota Keluarga](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\anggota_keluarga_list_1780769595734.png)


---

## 9. Fitur Cetak Bukti Transaksi (Journal Voucher)

Kami telah sukses mengimplementasikan fitur cetak bukti transaksi untuk setiap jurnal yang diinput.

### Detail Implementasi:
1. **Blade Template (`resources/views/reports/journal-voucher.blade.php`)**:
   - Layout ramah cetak (print-friendly) yang bersih dan profesional.
   - Penentuan judul bukti secara otomatis berdasarkan jenis transaksi keuangan:
     - **BUKTI KAS MASUK** (jika mendebit akun Kas/Bank yang diawali dengan `111`).
     - **BUKTI KAS KELUAR** (jika mengkredit akun Kas/Bank yang diawali dengan `111`).
     - **BUKTI MEMORIAL** (jika transaksi penyesuaian/non-kas lainnya).
   - Tanda tangan otorisasi di bagian bawah (Dibuat Oleh, Diperiksa Oleh, Disetujui Oleh).
2. **Controller & Route**:
   - Controller [`JournalPrintController.php`](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Http/Controllers/JournalPrintController.php) mengambil data relasi lengkap (`items.account`, `createdBy`) untuk dicetak secara instan.
   - Rute terproteksi middleware `auth` didaftarkan di [`web.php`](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/routes/web.php).
3. **Integrasi Filament**:
   - Ditambahkan tombol aksi **Cetak Bukti** di tabel daftar jurnal [`JournalResource.php`](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/JournalResource.php) dan header halaman Edit Jurnal [`EditJournal.php`](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/JournalResource/Pages/EditJournal.php).

### Hasil Cetak:

#### 1. Bukti Kas Masuk (Kas Masuk Voucher)
Contoh voucher transaksi penerimaan kolekte mingguan (`JRN-202606-0001`):

![Bukti Kas Masuk](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\journal_voucher_1_1780771498777.png)

#### 2. Bukti Kas Keluar (Kas Keluar Voucher)
Contoh voucher transaksi pembayaran honorarium pendeta via bank (`JRN-202606-0003`):

![Bukti Kas Keluar](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\journal_voucher_3_1780771512334.png)

---

## 10. Fitur Cetak Kwitansi (Layman-friendly Receipt A5 Landscape)

Kami telah sukses menambahkan fitur **Cetak Kwitansi** berdampingan dengan Cetak Bukti Jurnal sebelumnya. Fitur ini dirancang ramah cetak (print-friendly) pada ukuran kertas **A5 landscape** dan lebih dipahami oleh orang awam (layman-friendly).

### Karakteristik & Logika Khusus:
1. **Blade Template (`resources/views/reports/kwitansi.blade.php`)**:
   - Layout kwitansi klasik A5 landscape dengan kotak nominal, terbilang otomatis (teks bahasa Indonesia), serta ruang tanda tangan Penyerah & Penerima uang.
   - **Deteksi Jenis Kas**:
     - Jika Kas/Bank (`111`) di posisi **DEBIT**: Menampilkan judul **BUKTI PENERIMAAN KAS** dan label **Diterima Dari**.
     - Jika Kas/Bank (`111`) di posisi **KREDIT**: Menampilkan judul **BUKTI PENGELUARAN KAS** dan label **Dibayarkan Kepada**.
   - **Hirarki Visual Akun (Account Visual Hierarchy)**:
     - **Pada Penerimaan**: Akun lawan/kredit (misal: Persepuluhan, Kolekte) ditampilkan dalam font **tebal dan besar** agar fokus pada tujuan pembayaran. Akun Kas/debit (sumber penerimaan) ditampilkan kecil di bawahnya.
     - **Pada Pengeluaran**: Akun lawan/debit (misal: Honorarium Pendeta, Listrik) ditampilkan dalam font **tebal dan besar**. Akun Kas/kredit (sumber pengeluaran) ditampilkan kecil di bawahnya.
2. **Controller & Rute**:
   - Menambahkan metode `printKwitansi()` pada [`JournalPrintController.php`](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Http/Controllers/JournalPrintController.php) yang juga me-load data member jika transaksi berupa kontribusi jemaat.
   - Rute `/admin/journals/{journal}/kwitansi` didaftarkan dengan middleware `auth` di [`web.php`](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/routes/web.php).
3. **Integrasi Filament**:
   - Tombol **Cetak Kwitansi** (Ikon tiket berwarna kuning) ditambahkan di daftar tabel jurnal [`JournalResource.php`](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/JournalResource.php) dan halaman Edit Jurnal [`EditJournal.php`](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/JournalResource/Pages/EditJournal.php).

### Hasil Cetak Kwitansi:

#### A. Bukti Penerimaan Kas (Kwitansi Masuk)
Contoh penerimaan persepuluhan dari Yohanis Ndun (`JRN-202606-0002`).
- Terlihat nama pembayar terisi dinamis dari data Member.
- Akun Pendapatan Persepuluhan (`314001`) tampil menonjol (font besar), sedangkan akun Kas (`111120`) tampil kecil di bawahnya:

![Bukti Penerimaan Kas](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\kwitansi_journal_2_1780793635388.png)

#### B. Bukti Pengeluaran Kas (Kwitansi Keluar)
Contoh pengeluaran untuk Honorarium Pendeta (`JRN-202606-0003`).
- Akun Beban Honorarium (`421100`) tampil menonjol (font besar), sedangkan akun Bank Mandiri (`111210`) tampil kecil di bawahnya:

![Bukti Pengeluaran Kas](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\kwitansi_journal_3_1780793648440.png)

---

## 11. Fitur Cetak Kartu Keluarga Jemaat (Landscape A4 Layout)

Kami telah berhasil mengimplementasikan fitur **Cetak Kartu Keluarga** pada modul Administrasi Jemaat. Fitur ini dirancang ramah cetak (print-friendly) pada ukuran kertas **A4 landscape** dan mengambil data profil identitas gereja secara dinamis dari database.

### Karakteristik & Logika Khusus:
1. **Controller (`App\Http\Controllers\FamilyPrintController.php`)**:
   - Metode `print(Family $family)` dengan eager loading lengkap pada relasi `rayon`, `houseCategory`, `houseStatus`, dan `members` (dengan `familyPosition`, `maritalStatus`, `education`, `occupation`, `churchRole`, `membershipStatus`) untuk mencegah masalah *N+1 query*.
   - Memuat data `ChurchProfile::first()` untuk kop surat dan info kontak yang dinamis.
2. **Template Cetak (`resources/views/reports/kartu-keluarga.blade.php`)**:
   - Desain print-friendly landscape dengan pembatas ganda (double border) yang elegan.
   - Kop Surat berlogo dinamis menyesuaikan data identitas gereja yang diunggah.
   - Kolom Informasi Keluarga: Menampilkan Nomor KK, Nama Kepala Keluarga (Suami atau anggota pertama), Alamat, dan Rayon secara rapi.
   - Tabel Detail Anggota Keluarga: Mengurutkan anggota secara otomatis berdasarkan jabatan posisi keluarga (Suami paling atas, diikuti Istri, Anak, dll.), menampilkan NIK, jenis kelamin, tempat/tanggal lahir, serta seluruh label dari kamus data master.
   - Signature Area: Menyediakan tanda tangan untuk Kepala Keluarga dan Ketua Majelis Jemaat.
3. **Integrasi Panel Admin Filament**:
   - Ditambahkan tombol aksi **Cetak KK** (ikon printer hijau) pada baris tabel daftar KK (`FamilyResource.php`).
   - Ditambahkan tombol aksi **Cetak KK** pada bagian header atas halaman ubah data KK (`EditFamily.php`).

### Hasil Cetak & Verifikasi Visual:

#### A. Tampilan Cetak Layout Kartu Keluarga (Landscape A4 - Terbagi 2 Tabel)
Tampilan cetak Kartu Keluarga Jemaat terbagi secara otentik (menyerupai format Dukcapil) untuk kenyamanan membaca data yang padat:
- **Header**: Menampilkan Nama Kepala Keluarga, Alamat, Rayon Pelayanan, No. Telepon, Kategori Rumah, dan Status Kepemilikan Rumah.
- **Tabel I (Data Demografi & Hubungan Keluarga)**: Menampilkan NIK, Hubungan Keluarga, Pendidikan, Pekerjaan, Status Pernikahan, dan Keanggotaan.
- **Tabel II (Data Sakramen & Orang Tua)**: Menampilkan Tanggal & Gereja Baptis, Tanggal & Gereja Sidi (kolom dipersempit agar efisien), serta Nama Ayah & Ibu (kolom diperlebar).

![Tampilan Cetak Kartu Keluarga Terupdate](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\kk_print_updated_widths_1780798225060.png)

#### B. Tombol Aksi Cetak di Halaman Edit KK
Tombol "Cetak KK" berwarna hijau muncul secara dinamis di header halaman edit:

![Tombol Cetak Edit KK](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\edit_page_action_1780797751810.png)

#### C. Rekaman Alur Verifikasi Cetak KK (Video)
Rekaman WebP di bawah ini menampilkan jalannya navigasi dari daftar Kartu Keluarga, menguji cetak baris, serta menguji cetak dari dalam halaman ubah data:

![Rekaman Verifikasi Cetak KK](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\print_family_card_1780797706678.webp)

---

## 12. Modul Penerimaan Kontribusi Jemaat (Otomatisasi Jurnal & Nota Kwitansi)

Kami telah sukses mengimplementasikan fitur **Penerimaan Kontribusi Jemaat** yang merekam kontribusi warga (seperti Persepuluhan, Nazar, Pembangunan) dan secara otomatis mencatatnya ke dalam Jurnal Umum (Double-Entry) serta menerbitkan Nota Tanda Terima yang rapi.

### Fitur Utama:
1. **`MemberContributionResource` (Penerimaan Jemaat)**:
   - Navigation: Di bawah grup **`Manajemen Keuangan`** dengan ikon `heroicon-o-gift` dan label **`Penerimaan Jemaat`**.
   - Input Form: Menyediakan pencarian jemaat secara dinamis berdasarkan nama lengkap, serta filter akun kas/pendapatan yang hanya menampilkan akun transaksional yang aktif (tidak memiliki anak).
2. **Otomatisasi Jurnal & Double-Entry (`CreateMemberContribution.php` & `EditMemberContribution.php`)**:
   - Saat penerimaan baru disimpan, sistem membungkus prosesnya dalam `DB::transaction()` untuk:
     - Membuat entri `Journal` baru dengan penomoran otomatis `JRN-YYYYMM-XXXX`.
     - Membuat baris `JournalItem` untuk posisi DEBIT (akun kas/bank) dan KREDIT (akun pendapatan).
   - Pengeditan penerimaan juga disinkronkan secara real-time ke jurnal dan baris-baris jurnal item terkait.
3. **Cetak Nota Tanda Terima (`contribution-receipt.blade.php`)**:
   - Format Kwitansi A5 landscape yang bersih dan profesional.
   - Menampilkan logo dan nama gereja dinamis dari **Profil Gereja**.
   - Terbilang otomatis dalam format bahasa Indonesia (contoh: "Tujuh Ratus Lima Puluh Ribu Rupiah").
   - Deskripsi persembahan yang terintegrasi langsung dengan keterangan jurnal transaksi.

### Hasil Verifikasi & Tampilan Cetak:

#### A. Tanda Terima Persembahan (Kwitansi A5)
Contoh nota tanda terima persembahan persepuluhan dari Yohanis Ndun sebesar Rp 750.000,00 dengan teks terbilang otomatis dan kop surat dinamis:

![Tanda Terima Persembahan](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\contribution_receipt_1780800345627.png)

#### B. Rekaman Alur Verifikasi Kontribusi Jemaat (Video)
Rekaman WebP berikut menampilkan proses pengisian formulir penerimaan, validasi pembuatan jurnal otomatis, serta verifikasi tampilan nota cetak:

![Rekaman Verifikasi Kontribusi](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\print_contribution_receipt_1780800316524.webp)


---

## 13. Modul Penyaluran Diakonia (Otomatisasi Jurnal & Nota Pengeluaran)

Kami telah sukses mengimplementasikan fitur **Penyaluran Diakonia** yang merekam penyaluran bantuan kepada jemaat yang membutuhkan dan secara otomatis mencatatnya ke dalam Jurnal Umum (Double-Entry) serta menerbitkan Nota Pengeluaran Diakonia yang rapi.

### Fitur Utama:

1. **Model `MemberAssistance`** (`app/Models/MemberAssistance.php`):
   - Field: `journal_id` (FK), `member_id` (FK, nullable), `amount` (decimal:15,2)
   - Relasi: `journal()` (BelongsTo) dan `member()` (BelongsTo)
   - Migration dijalankan: ✅

2. **`MemberAssistanceResource` (Penyaluran Diakonia)**:
   - Navigation: Di bawah grup **`Manajemen Keuangan`** dengan ikon `heroicon-o-heart` dan label **`Penyaluran Diakonia`**.
   - Input Form: Tanggal transaksi, pilihan jemaat penerima (searchable by nama lengkap), nominal bantuan, akun beban diakonia (kode `412%`), sumber dana kas/bank, dan keterangan tujuan bantuan.
   - Tabel: Menampilkan nama penerima, jumlah bantuan (format Rupiah), nomor jurnal otomatis, dan tanggal input.

3. **Otomatisasi Jurnal & Double-Entry (`CreateMemberAssistance.php` & `EditMemberAssistance.php`)**:
   - Saat penyaluran baru disimpan, sistem membungkus prosesnya dalam `DB::transaction()` untuk:
     - Membuat entri `Journal` baru dengan referensi otomatis `DIAKONIA-YYYYMMDDHHmmss`.
     - Membuat baris `JournalItem` DEBIT (akun beban diakonia) dan KREDIT (akun kas/bank sumber dana).
   - Pengeditan penyaluran juga disinkronkan ke jurnal dan item jurnal terkait.

4. **Cetak Nota Pengeluaran Diakonia (`diakonia-receipt.blade.php`)**:
   - Format A5 landscape yang bersih dan profesional (tema merah untuk pengeluaran kas).
   - Menampilkan logo dan nama gereja dinamis dari **Profil Gereja**.
   - Terbilang otomatis dalam format bahasa Indonesia.
   - Mencantumkan nama penerima bantuan, tujuan bantuan, dan sumber dana akun.
   - Kolom tanda tangan untuk: Penerima Bantuan, Mengetahui (supervisor), dan Bendahara.

### Verifikasi Teknis (CLI):

| Komponen | Status |
|---|---|
| Migration `member_assistances` table | ✅ Ran (batch 3) |
| Route `GET admin/assistances/{assistance}/receipt` | ✅ Terdaftar (`diakonia.receipt`) |
| Route `GET admin/member-assistances` | ✅ Terdaftar |
| Route `GET admin/member-assistances/create` | ✅ Terdaftar |
| Syntax `MemberAssistanceResource.php` | ✅ No errors |
| Syntax `CreateMemberAssistance.php` | ✅ No errors |
| Syntax `EditMemberAssistance.php` | ✅ No errors |
| Syntax `JournalPrintController.php` | ✅ No errors |

---

## 14. Modul Laporan Penggembalaan dan Diakonia (Custom Pages dengan HasTable)

Kami telah sukses mengimplementasikan modul **Laporan Penggembalaan dan Diakonia** yang menyajikan visualisasi data terstruktur jemaat dan keluarga menggunakan Custom Pages di Filament v3 dengan `HasTable`.

### A. Laporan Ulang Tahun Jemaat (This Week)
Halaman kustom ini menyaring dan menampilkan jemaat yang berulang tahun pada minggu berjalan (Senin s.d. Minggu) secara dinamis:
1. **Navigasi**: Terletak di bawah grup **`Laporan Penggembalaan`** dengan ikon `heroicon-o-cake` dan label **`Ulang Tahun Minggu Ini`**.
2. **Logika Filter Tanggal & Cross-Month/Year**:
   - Sistem menghasilkan daftar 7 tanggal (hari dan bulan) untuk minggu berjalan.
   - Query memfilter anggota jemaat yang berulang tahun bertepatan dengan salah satu dari 7 tanggal tersebut tanpa melihat tahun lahirnya, memastikan logic aman sekalipun minggu ini melintasi bulan atau pergantian tahun (Desember - Januari).
   - Anggota jemaat yang meninggal (`is_deceased = true`) disaring secara otomatis dari laporan.
3. **Pengurutan Default**:
   - Diurutkan terdekat berdasarkan hari ulang tahun (Senin s.d. Minggu) menggunakan query `orderByRaw` dengan `CASE` statement.
4. **Kolom Tabel**:
   - **Nama Lengkap**: Diambil dari custom state logic dan didukung fitur pencarian (`searchable()`).
   - **Tanggal Ulang Tahun**: Menggunakan format `d F` (contoh: "05 Juni") dan dapat diurutkan (`sortable()`).
   - **Umur Tahun Ini**: Kolom kustom yang menghitung umur yang dicapai jemaat pada tahun berjalan (`Carbon::now()->year - birth_date->year`).
   - **Rayon**: Menampilkan nama rayon dari relasi `family.rayon`.
   - **No. Telepon**: Menampilkan nomor kontak jemaat (default: `-`).

#### Tangkapan Layar Halaman Laporan Ulang Tahun Jemaat:
![Laporan Ulang Tahun Jemaat](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\birthday_report_verification_1780816483270.png)

---

### B. Laporan Keluarga Pra Sejahtera
Halaman kustom ini menyajikan daftar keluarga pra sejahtera (membutuhkan bantuan sosial) lengkap dengan visualisasi pengelompokan (grouping) dinamis bawaan Filament v3:
1. **Navigasi**: Terletak di bawah grup **`Laporan Penggembalaan`** dengan ikon `heroicon-o-home-modern` dan label **`Keluarga Pra Sejahtera`**.
2. **Filter Query Bawaan**:
   - Menambahkan scope query `scopeNeedsAssistance` pada model `Family` untuk menyaring keluarga dengan kategori kondisi rumah `darurat` dan `semi-permanen`.
3. **Fitur Grouping (Filament v3)**:
   - Data otomatis dikelompokkan secara visual di antarmuka menggunakan method `->groups(['houseStatus.label', 'houseCategory.label'])`.
4. **Kolom Tabel**:
   - **No KK**: Nomor Kartu Keluarga jemaat.
   - **Nama Kepala Keluarga**: Custom column yang mencari secara dinamis anggota keluarga dengan posisi `suami` dari relasi members.
   - **Alamat**: Alamat tempat tinggal keluarga dengan text wrap diaktifkan.
   - **Rayon**: Nama wilayah rayon pelayanan.
   - **Status Rumah**: Tipe kepemilikan rumah (badge).
   - **Kondisi Rumah**: Kategori kondisi rumah (badge warning).
5. **Filter Pencarian Majelis**:
   - `rayon_id`: Menyaring berdasarkan wilayah pelayanan rayon.
   - `house_status_id`: Menyaring berdasarkan tipe status kepemilikan rumah.
   - `house_category_id`: Menyaring berdasarkan kondisi/kategori rumah.
   *(Filter dropdown Kamus Data secara cerdas disaring hanya untuk kategori relasi masing-masing)*

#### Tangkapan Layar Halaman Laporan Keluarga Pra Sejahtera:
![Keluarga Pra Sejahtera](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\underprivileged_family_initial_1780816503792.png)

#### Tangkapan Layar Hasil Pengelompokan Data (Grouping):
![Grouping Pra Sejahtera](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\underprivileged_family_grouped_1780816520527.png)

---

### C. Rekaman Alur Verifikasi Laporan Penggembalaan & Diakonia (Video)
Rekaman WebP di bawah ini menampilkan jalannya verifikasi pada panel admin untuk Laporan Ulang Tahun Jemaat (dengan simulasi data masuk minggu ini) serta interaksi filter dan pengelompokan dinamis pada Laporan Keluarga Pra Sejahtera:

![Rekaman Verifikasi Laporan](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\verify_reports_flow_1780816427578.webp)

---

## 15. Fitur Cetak Laporan Penggembalaan dan Diakonia (Print to PDF)

Kami telah sukses menambahkan fitur pencetakan laporan untuk **Laporan Ulang Tahun Jemaat** (format Portrait) dan **Laporan Keluarga Pra Sejahtera** (format Landscape) dengan Kop Surat Gereja dinamis dan pembatas border yang tegas untuk kebutuhan cetak fisik.

### Fitur & Struktur Implementasi:

1. **Tombol Header Actions**:
   - **Laporan Ulang Tahun**: Tombol berwarna hijau (`success`) berlabel **Cetak Laporan** (ikon printer) yang mengarah ke route `report.birthdays.print`.
   - **Keluarga Pra Sejahtera**: Tombol berwarna jingga (`warning`) berlabel **Cetak Laporan** (ikon printer) yang mengarah ke route `report.underprivileged.print`.

2. **Controller & Routing (`App\Http\Controllers\PastoralReportController.php`)**:
   - `printBirthdays()`: Mengambil daftar jemaat yang berulang tahun minggu ini (aman cross-month/year), mengurutkan dari Senin ke Minggu, memuat profil gereja (`ChurchProfile`), dan mengembalikan view `reports.birthdays`.
   - `printUnderprivilegedFamilies()`: Mengambil keluarga pra sejahtera dengan kondisi rumah `darurat` dan `semi-permanen` beserta relasi lengkap, memuat profil gereja, dan mengembalikan view `reports.underprivileged`.
   - Rute terdaftar di `routes/web.php` dan dilindungi oleh middleware `auth`.

3. **Template Tampilan Cetak (Blade & Print CSS)**:
   - **Ulang Tahun (`reports/birthdays.blade.php`)**: Layout portrait (A4) yang rapi, kop surat gereja berlogo dinamis, dan tabel bergaris tegas (No, Nama Jemaat, Tanggal Lahir, Usia, Rayon) dilengkapi area tanda tangan Majelis Jemaat.
   - **Keluarga Pra Sejahtera (`reports/underprivileged.blade.php`)**: Layout landscape (A4) untuk mengakomodasi data yang padat, kop surat gereja berlogo dinamis, pengelompokan baris data secara visual berdasarkan **Rayon**, dan tabel bergaris tegas (No, No KK, Nama Kepala Keluarga, Alamat, Rayon, Status Rumah, Kondisi Rumah) dilengkapi area tanda tangan.

### Tangkapan Layar & Verifikasi Hasil Cetak:

#### A. Tampilan Cetak Daftar Ulang Tahun Jemaat (Portrait A4)
Tampilan cetak dengan kop surat dinamis, border solid hitam yang rapi, dan tanda tangan Ketua serta Sekretaris Majelis Jemaat (dilengkapi nama hari seperti "Jumat, 05 Juni"):

![Cetak Ulang Tahun](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\birthday_report_print_1780819661106.png)

#### B. Tampilan Cetak Keluarga Pra Sejahtera (Landscape A4 - Grouped by Rayon)
Tampilan cetak horizontal dengan pengelompokan baris tabel per Rayon pelayanan secara visual, status rumah, dan kondisi rumah (badge):

![Cetak Pra Sejahtera](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\underprivileged_report_print_1780819417286.png)

#### C. Rekaman Alur Verifikasi Pencetakan (Video)
Rekaman WebP di bawah ini menampilkan jalannya verifikasi pembukaan laporan cetak langsung dari tombol di panel admin Filament dan otomatisasi pemuatan dialog printer browser:

![Rekaman Verifikasi Cetak Laporan](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\verify_day_name_in_reports_1780819626630.webp)

---

## 16. Kustomisasi Warna Latar Belakang Menu Sidebar

Kami telah berhasil menyesuaikan warna latar belakang menu sidebar di panel admin Filament agar senada namun memiliki perbedaan warna yang jelas dengan jendela utama (area konten), memberikan tampilan visual yang lebih dinamis dan premium.

### Metode Implementasi:
- Kami menggunakan fitur render hook `PanelsRenderHook::HEAD_END` pada berkas [AdminPanelProvider.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Providers/Filament/AdminPanelProvider.php) untuk menginjeksi inline CSS secara aman ke panel admin.
- Skema Warna yang Diterapkan:
  - **Light Mode (Mode Terang)**: Latar belakang sidebar diubah menjadi abu-abu kebiruan lembut **`#f1f5f9` (Slate-100)** dengan garis pembatas kanan **`#cbd5e1` (Slate-300)**. Ini membedakan sidebar dari jendela utama yang berwarna putih/light gray.
  - **Dark Mode (Mode Gelap)**: Latar belakang sidebar menggunakan warna **`#0f172a` (Slate-900)** dengan garis pembatas kanan **`#1e293b` (Slate-800)**.

### Tangkapan Layar & Verifikasi Hasil Kustomisasi:

#### A. Tampilan Sidebar Mode Terang (Light Mode)
Sidebar berwarna Slate-100 yang lembut, memberikan kontras yang nyaman dengan jendela utama:

![Sidebar Mode Terang](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\admin_sidebar_light_1780826640931.png)

#### B. Tampilan Sidebar Mode Gelap (Dark Mode)
Sidebar berwarna Slate-900 yang elegan, memberikan kontras yang sempurna dalam skema gelap:

![Sidebar Mode Gelap](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\admin_sidebar_dark_1780826658535.png)

#### C. Rekaman Alur Verifikasi Tampilan Sidebar (Video)
Rekaman WebP di bawah ini menampilkan pergantian tema (Terang ke Gelap) dan kecocokan warna sidebar yang dinamis di panel admin:

![Rekaman Verifikasi Sidebar](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\verify_sidebar_styling_1780826620960.webp)

---

## 17. Fitur Sidebar Collapsible (Toggle Minimize Sidebar)

Kami telah berhasil mengaktifkan fitur penciutan/minimasi menu sidebar secara interaktif (sidebar collapsible) pada panel admin Filament untuk layar desktop.

### Fitur & Metode Implementasi:
- Menambahkan method `->sidebarCollapsibleOnDesktop()` pada konfigurasi panel di berkas [AdminPanelProvider.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Providers/Filament/AdminPanelProvider.php).
- Tombol penciut/toggle menu muncul di sebelah kanan logo/judul pada bagian header sidebar.
- Ketika diciutkan (collapsed), sidebar menciut menjadi hanya menampilkan ikon menu (icon-only view), memberikan area kerja yang lebih luas bagi pengguna pada layar desktop.

### Tangkapan Layar & Verifikasi Hasil:

#### A. Tampilan Sidebar Terbuka Penuh (Expanded State)
Sidebar menampilkan label teks menu secara lengkap:

![Sidebar Expanded](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\sidebar_expanded_1780826931393.png)

#### B. Tampilan Sidebar Diciutkan (Collapsed State)
Sidebar menyembunyikan teks label dan hanya memunculkan ikon navigasi:

![Sidebar Collapsed](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\sidebar_collapsed_1780826919210.png)

#### C. Rekaman Alur Verifikasi Toggle Sidebar (Video)
Rekaman WebP di bawah ini menampilkan interaksi klik tombol collapse dan expand pada menu sidebar:

![Rekaman Verifikasi Collapse](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\verify_sidebar_collapse_1780826850554.webp)

---

## 18. Peningkatan Kontras Warna Latar Belakang Admin Panel

Kami telah meningkatkan kontras warna latar belakang (background) panel admin Filament untuk memberikan pembagian area yang sangat jelas, membuat komponen kartu (cards), widget, dan tabel "pop out" (menonjol) dengan sangat estetik dan profesional.

### Perubahan Desain CSS:
- Kami menyesuaikan aturan CSS yang diinjeksikan lewat render hook `PanelsRenderHook::HEAD_END` di [AdminPanelProvider.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Providers/Filament/AdminPanelProvider.php):
  - **Mode Terang (Light Mode)**: 
    - Latar belakang jendela utama (`body`) diubah menjadi abu-abu Slate-100 (**`#f1f5f9`**).
    - Latar belakang sidebar dan topbar diubah menjadi putih bersih (**`#ffffff`**).
    - Ini membuat widget kartu (berwarna putih) dan area navigasi terpisah secara tegas oleh latar belakang abu-abu yang lebih gelap.
  - **Mode Gelap (Dark Mode)**:
    - Latar belakang jendela utama (`body`) diubah menjadi hitam pekat (**`#09090b`** - Zinc-950).
    - Latar belakang sidebar dan topbar diubah menjadi Zinc-900 (**`#18181b`**).

### Tangkapan Layar & Verifikasi Hasil:

#### A. Kontras Tinggi di Mode Terang (Light Mode)
Jendela utama abu-abu Slate-100 berpadu dengan sidebar/topbar putih bersih, membuat tabel dan kartu sangat menonjol:

![Kontras Mode Terang](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\light_contrast_ok_1780827085646.png)

#### B. Kontras Tinggi di Mode Gelap (Dark Mode)
Latar belakang hitam pekat dengan sidebar/topbar Zinc-900 yang elegan:

![Kontras Mode Gelap](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\dark_contrast_ok_1780827101334.png)

#### C. Rekaman Alur Verifikasi Kontras (Video)
Rekaman WebP di bawah ini menampilkan pembuktian visual penajaman kontras panel admin:

![Rekaman Verifikasi Kontras](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\verify_contrast_ui_1780827041023.webp)

---

## 19. Penerapan Tema Warna-warni Soft Pastel (Sidebar-Only Blue/Navy Theme)

Kami telah menyempurnakan skema warna panel admin Filament sehingga warna pastel **hanya diterapkan pada menu sidebar (background panel)** dengan nilai warna heksadesimal spesifik:
* **Light Mode (Mode Terang)**: Menu sidebar diubah menjadi warna **Pastel Blue** (**`#739ec9`**), sedangkan topbar dan jendela utama tetap normal/default.
* **Dark Mode (Mode Gelap)**: Menu sidebar diubah menjadi warna **Pastel Dark Navy/Slate** (**`#313647`**), sedangkan topbar dan jendela utama tetap normal/default.

### Detail Palet Warna Pastel yang Diterapkan:
Aturan CSS yang diinjeksikan lewat render hook `PanelsRenderHook::HEAD_END` pada [AdminPanelProvider.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Providers/Filament/AdminPanelProvider.php) diubah menjadi:

* **Mode Terang (Light Mode)**:
  - **Sidebar**: Pastel Blue (**`#739ec9`**) dengan border **`#cbd5e1`**.
  - **Jendela Utama & Topbar**: Kembali ke warna normal/default.
* **Mode Gelap (Dark Mode)**:
  - **Sidebar**: Dark Navy (**`#313647`**) dengan border **`#2d264b`**.
  - **Jendela Utama & Topbar**: Kembali ke warna normal/default.

### Tangkapan Layar & Verifikasi Hasil:

#### A. Tampilan Sidebar Pastel Blue Mode Terang (Light Mode)
Sidebar bermotif pastel blue (#739ec9) yang lembut dengan seluruh teks, nama menu, logo, dan ikon berwarna hitam pekat agar kontras:

![Light Mode Pastel Blue Sidebar](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\light_mode_clean_1780828630077.png)

#### B. Tampilan Sidebar Dark Navy Mode Gelap (Dark Mode)
Sidebar bermotif dark navy (#313647) dengan teks dan ikon putih/abu-abu default bawaan Filament yang kontras:

![Dark Mode Dark Navy Sidebar](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\dark_mode_sidebar_elements_1780828642595.png)

#### C. Rekaman Alur Verifikasi Tema Pastel Sidebar-Only (Video)
Rekaman WebP di bawah ini menampilkan pembuktian visual bahwa warna hitam pekat diterapkan pada menu sidebar saat beralih ke Mode Terang, dan kembali ke normal saat beralih ke Mode Gelap:

![Rekaman Verifikasi Pastel Sidebar](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\verify_sidebar_text_icons_1780828598760.webp)

---

## 20. Redesain Laporan Posisi Keuangan (Neraca Standar Akuntansi)

Kami telah mendesain ulang halaman **Laporan Posisi Keuangan (Neraca)** menjadi format tabel akuntansi standar profesional yang clean, monokrom, dan print-friendly. Seluruh blok warna mencolok sebelumnya telah dihilangkan untuk mengutamakan keterbacaan tingkat tinggi yang formal.

### Perubahan Detail Desain Baru:
1. **Layout Tabel Akuntansi Standar**:
   - Tabel-tabel disajikan secara berurutan: **Aset**, **Kewajiban**, dan **Aset Neto (Ekuitas)**.
   - Header kolom menggunakan garis bawah tebal (`border-b-2 border-gray-800` / `border-gray-200`) yang klasik dan minimalis.
   - Menggunakan hover baris yang sangat tipis (`hover:bg-gray-50`) untuk melacak pembacaan data.
2. **Keterbacaan Monokrom & Print-Friendly**:
   - Menghilangkan warna background tebal seperti biru, oranye, dan hijau. Semua area kartu laporan menggunakan latar belakang putih bersih (`bg-white`) pada Light Mode dan abu-abu gelap (`bg-gray-900`) pada Dark Mode.
   - Judul dan subjudul berukuran proposional dengan warna monokrom kontras tinggi (`text-gray-900` / `text-gray-100`).
   - Garis batas tebal klasik (`border-t-4 border-gray-800`) dipasang pada footer Grand Total Kewajiban + Aset Neto untuk menonjolkan total neraca.

### Hasil Verifikasi & Tangkapan Layar:

#### A. Tampilan Neraca Monokrom dengan Tombol Cetak Mode Terang (Light Mode)
Desain clean dan formal dengan kop gereja (logo, nama, alamat) di bagian atas tengah, dan tombol "Cetak Laporan / PDF" yang diletakkan sejajar di bagian atas:

![Neraca Monokrom Light Mode](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\light_mode_dynamic_profile_1780852255109.png)

#### B. Tampilan Neraca Monokrom dengan Tombol Cetak Mode Gelap (Dark Mode)
Visualisasi monokrom mode gelap dengan kop gereja yang di-render secara dinamis dari database tanpa hardcoding:

![Neraca Monokrom Dark Mode](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\dark_mode_dynamic_profile_1780852271069.png)

#### C. Rekaman Alur Verifikasi Tombol Cetak Neraca (Video)
Rekaman WebP berikut menampilkan pembuktian visual penempatan judul kop gereja di tengah secara dinamis dan fungsionalitas cetak:

![Verifikasi Cetak Neraca](C:\Users\ASUS\.gemini\antigravity-ide\brain\e58020fb-3889-4faa-b9d9-90d4cd35b230\dynamic_profile_ok_1780852184950.webp)
