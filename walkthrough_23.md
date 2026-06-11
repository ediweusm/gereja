# Walkthrough: MemberResource (Read-Only)

Langkah-langkah berikut telah berhasil diselesaikan untuk membuat dan mengonfigurasi `MemberResource` sebagai halaman "Daftar Seluruh Jemaat" yang bersifat **Strictly Read-Only** (Hanya Baca).

## Perubahan yang Dilakukan

1. **[MemberResource.php](file:///\\wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/MemberResource.php)**
   - Menambahkan properti navigasi (`$navigationIcon`, `$navigationGroup`, `$navigationLabel`, `$recordTitleAttribute`) untuk menempatkannya pada menu "Administrasi Jemaat".
   - Menambahkan kebijakan otorisasi `canCreate()`, `canEdit()`, dan `canDelete()` yang semuanya mengembalikan nilai `false` untuk menonaktifkan tombol dan rute mutasi.
   - Mengatur struktur tabel (`table()`):
     - Kolom `full_name` menggunakan `getStateUsing()` agar memuat gabungan nama dengan benar, dicari pada nama depan/tengah/belakang, dan diurutkan berdasarkan nama depan.
     - Kolom `gender` berupa badge berwarna biru/hijau (`info`/`success`).
     - Kolom `birth_date` yang menghitung usia saat ini dalam tahun (`age . ' thn'`).
     - Kolom relasi `family.family_number` (No. KK), `familyPosition.label` (SHDK), dan `membershipStatus.label` (Status).
     - Filter pencarian untuk Jenis Kelamin (`gender`) dan Status Jemaat (`membership_status_id`).
     - Hanya mengaktifkan `ViewAction::make()`, serta mengosongkan bulk actions (tidak ada `DeleteBulkAction`).
   - Mengatur form detail dalam format `Tabs` (`form()`):
     - **Tab 'Data Pribadi'**: first_name, middle_name, last_name, nik, birth_place, birth_date, gender, phone, is_deceased (Toggle), death_date (hanya muncul jika is_deceased bernilai true).
     - **Tab 'Keluarga & Pekerjaan'**: family_id (Select relationship), family_position_id, father_name, mother_name, marital_status_id, education_id, occupation_id, income.
     - **Tab 'Data Gerejawi'**: membership_status_id, church_role_id, status_baptis (Toggle).
     - **Tab 'Sakramen & Pernikahan'**:
       - Fieldset Baptis: baptism_date, baptism_church, baptism_pastor (hanya muncul jika status_baptis bernilai true).
       - Fieldset Sidi: sidi_date, sidi_church, sidi_pastor.
       - Fieldset Pernikahan: marriage_date, marriage_church, marriage_pastor.

2. **[ListMembers.php](file:///\\wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/MemberResource/Pages/ListMembers.php)**
   - Menghapus `CreateAction` dari menu header halaman indeks untuk menghilangkan tombol tambah jemaat.

3. **[ViewMember.php](file:///\\wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/MemberResource/Pages/ViewMember.php)**
   - Menghapus `EditAction` dari menu header halaman view jemaat untuk memastikan data tidak dapat diubah dari detail.

4. **Pembersihan File & Rute**
   - Menghapus halaman rute `CreateMember.php` dan `EditMember.php` yang tidak digunakan.

---

## Asumsi Nama Kolom

Berdasarkan analisis migrasi dan model:
- Tabel `data_dictionaries` **tidak memiliki** kolom bernama `name`, melainkan **`label`** (misalnya `familyPosition.label` dan `membershipStatus.label`). Oleh karena itu, kolom tabel dan filter relasi menggunakan `label` (mengasumsikan instruksi aslinya menggunakan `name` sebagai analogi label).

---

## Hasil Pengujian & Validasi

Linter PHP telah memvalidasi berkas tanpa kesalahan sintaksis. Script uji internal juga dijalankan dengan hasil sukses, memvalidasi bahwa `MemberResource` dapat dimuat oleh Framework Laravel/Filament, properti otorisasi read-only aktif, rute terkonfigurasi, form memiliki layout tab tunggal, dan tabel memiliki 6 kolom, 2 filter, serta 1 aksi baca (View).
