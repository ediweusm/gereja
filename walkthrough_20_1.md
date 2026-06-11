# Walkthrough - Sprint 6: Modul Manajemen Ibadah & Pelayanan

Modul Manajemen Ibadah & Pelayanan (Sprint 6) telah selesai diimplementasikan. Berikut adalah rangkuman dari perubahan yang dilakukan dan hasil verifikasi.

## Perubahan yang Dilakukan

### 1. Database Migrations
Tiga migrasi database telah berhasil diverifikasi dan dijalankan:
- **`create_ministry_roles_table`**: Menyimpan peran pelayanan (seperti Pendeta, Singer, Musik, dll.) beserta kolom `sort_order` untuk pengurutan dinamis.
- **`create_events_table`**: Menyimpan jadwal ibadah beserta detail kegiatan (nama, tema, tanggal, waktu, tipe ibadah, mode pelayanan (onsite/online/hybrid), serta relasi ke Rayon dan Keluarga Penerima/Host Family).
- **`create_event_assignments_table`**: Tabel pivot/penghubung yang memetakan kegiatan (`events`) ke peran pelayanan (`ministry_roles`) dan petugas (`members` internal atau nama tamu luar `guest_name`).

### 2. Eloquent Models
Menambahkan tiga model baru dengan `$guarded = ['id']` dan relasi Eloquent yang tepat:
- **`MinistryRole`**
  - Relasi `assignments()` -> `hasMany(EventAssignment::class)`
- **`Event`**
  - Relasi `rayon()` -> `belongsTo(Rayon::class)`
  - Relasi `hostFamily()` -> `belongsTo(Family::class, 'host_family_id')`
  - Relasi `assignments()` -> `hasMany(EventAssignment::class)`
  - Casts: `event_date` di-cast ke tipe `date`.
- **`EventAssignment`**
  - Relasi `event()` -> `belongsTo(Event::class)`
  - Relasi `ministryRole()` -> `belongsTo(MinistryRole::class)`
  - Relasi `member()` -> `belongsTo(Member::class)`

### 3. Filament Resources
- **`MinistryRoleResource`**
  - Ditempatkan dalam grup **Pengaturan & Master Data** dengan ikon `heroicon-o-users`.
  - Form: Input teks untuk `name` dan input angka untuk `sort_order`.
  - Table: Menampilkan `name` dan `sort_order` dengan kemampuan sorting.
- **`EventResource`**
  - Ditempatkan dalam grup **Manajemen Pelayanan** dengan ikon `heroicon-o-calendar-days`.
  - Form:
    - **Section 1 (Detail Ibadah)**: Input detail ibadah dengan form reaktif. Pilihan `rayon_id` dan `host_family_id` (dengan label dinamis: nomor KK beserta Kepala Keluarga) hanya ditampilkan ketika jenis kegiatan yang dipilih adalah **Persekutuan Wilayah** (`event_type === 'Persekutuan Wilayah'`).
    - **Section 2 (Jadwal Petugas)**: Menggunakan `Repeater` yang terintegrasi dengan relasi `assignments`. Form di dalam repeater ditata dalam 3 kolom untuk memilih Peran Pelayanan, Petugas Jemaat Internal (menampilkan nama lengkap), dan nama Petugas Tamu Eksternal.
  - Table: Menampilkan `event_date` (terformat), `start_time`, `name` (searchable), `theme`, dan `mode` (badge dengan warna dinamis sesuai jenis mode).

## Hasil Pengujian (Verification Results)

Pengujian unit/fitur otomatis telah dibuat di `tests/Feature/EventServiceTest.php` untuk memastikan integritas relasi database dan model.

### Output Eksekusi Test:
```bash
wsl php artisan test --filter=EventServiceTest

   PASS  Tests\Feature\EventServiceTest
  ✓ can create ministry role                                             9.93s  
  ✓ can create event with assignments                                    0.11s  

  Tests:    2 passed (8 assertions)
  Duration: 10.09s
```
Semua relasi berhasil diuji secara otomatis dan terbukti bekerja sesuai spesifikasi teknis Laravel dan Filament.
