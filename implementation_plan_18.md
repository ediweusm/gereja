# Modul Laporan Penggembalaan dan Diakonia

Membuat 2 Custom Page di Filament v3 yang mengimplementasikan `HasTable` dan `InteractsWithTable` untuk menyajikan:
1. **Laporan Ulang Tahun Jemaat (This Week)**
2. **Laporan Keluarga Pra Sejahtera**

## User Review Required
- **Default Filter Pra Sejahtera**: Base query pada Laporan Keluarga Pra Sejahtera disaring hanya untuk kategori rumah ('darurat' dan 'semi-permanen'). Silakan konfirmasi jika Anda membutuhkan filter lain sebagai default.
- **Logika Hari Ulang Tahun**: Menggunakan pencocokan (hari, bulan) dari 7 tanggal dalam minggu berjalan (Senin-Minggu) untuk memastikan logic aman ketika melintasi bulan atau pergantian tahun.

## Proposed Changes

### 1. Custom Page: Laporan Ulang Tahun Jemaat (This Week)

#### [NEW] [BirthdayReport.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Pages/BirthdayReport.php)
Halaman kustom Filament yang mengimplementasikan `HasTable`.
- **Navigation Group**: 'Laporan Penggembalaan'
- **Navigation Icon**: 'heroicon-o-cake'
- **Title**: 'Ulang Tahun Minggu Ini'
- **Table Configuration**:
  - **Query**: Mengambil data `Member` dengan relasi `family.rayon`, di mana `is_deceased = false` dan `birth_date` cocok dengan hari dan bulan dari salah satu dari 7 hari minggu berjalan (Senin - Minggu).
  - **Sorting**: Menggunakan `orderByRaw` dengan `CASE` statement untuk mengurutkan terdekat berdasarkan urutan hari dari Senin ke Minggu.
  - **Columns**:
    - `full_name` (TextColumn, searchable)
    - `birth_date` (TextColumn, date: 'd F', sortable, label: 'Tanggal Ulang Tahun')
    - Umur tahun ini (custom TextColumn menghitung `Carbon::now()->year - birth_date->year`)
    - `family.rayon.name` (TextColumn, label: 'Rayon')
    - `phone` (TextColumn, default: '-')

#### [NEW] [birthday-report.blade.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/resources/views/filament/pages/birthday-report.blade.php)
Halaman blade dasar yang memuat table Filament:
```html
<x-filament-panels::page>
    {{ $this->table }}
</x-filament-panels::page>
```

---

### 2. Custom Page: Laporan Keluarga Pra Sejahtera

#### [MODIFY] [Family.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Models/Family.php)
Menambahkan local query scope `scopeNeedsAssistance(Builder $query)` untuk menyaring keluarga dengan kategori kondisi rumah `darurat` atau `semi-permanen`.

#### [NEW] [UnderprivilegedFamilyReport.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Pages/UnderprivilegedFamilyReport.php)
Halaman kustom Filament yang mengimplementasikan `HasTable`.
- **Navigation Group**: 'Laporan Penggembalaan'
- **Navigation Icon**: 'heroicon-o-home-modern'
- **Title**: 'Keluarga Pra Sejahtera'
- **Table Configuration**:
  - **Query**: Mengambil data `Family` dengan relasi `houseStatus`, `houseCategory`, `rayon`, dan `members.familyPosition`, yang disaring menggunakan scope `needsAssistance()`.
  - **Grouping**: Mengelompokkan berdasarkan `houseStatus.label` dan `houseCategory.label` menggunakan method `->groups()`.
  - **Columns**:
    - `family_number` (TextColumn, label: 'No KK')
    - Nama Kepala Keluarga (custom TextColumn mencari member dengan posisi `'suami'`)
    - `address` (TextColumn, wrap)
    - `rayon.name` (TextColumn)
    - `houseStatus.label` (TextColumn, badge)
    - `houseCategory.label` (TextColumn, badge, color: warning)
  - **Filters**: SelectFilter untuk `rayon_id`, `house_status_id`, dan `house_category_id`.

#### [NEW] [underprivileged-family-report.blade.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/resources/views/filament/pages/underprivileged-family-report.blade.php)
Halaman blade dasar yang memuat table Filament:
```html
<x-filament-panels::page>
    {{ $this->table }}
</x-filament-panels::page>
```

## Verification Plan

### Manual Verification
- Jalankan aplikasi secara lokal dan akses kedua menu laporan di bawah navigasi 'Laporan Penggembalaan'.
- Periksa kesesuaian data ulang tahun dengan rentang minggu ini.
- Periksa pengelompokan (grouping) dan filter pada laporan keluarga pra sejahtera.
- Pastikan tidak ada N+1 query.
