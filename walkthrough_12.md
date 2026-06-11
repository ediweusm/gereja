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
   - Tabel: Menampilkan nomor KK, rayon, alamat, serta kolom khusus **`members_count`** (Jumlah Anggota).

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

