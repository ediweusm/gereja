# Rencana Implementasi: Fitur Cetak Laporan (Print to PDF)

Rencana ini bertujuan untuk menambahkan tombol "Cetak Laporan" di halaman Filament Laporan Ulang Tahu Jemaat dan Laporan Keluarga Pra Sejahtera, menghubungkannya ke Controller khusus, dan merender tampilan PDF/cetak HTML yang ramah cetak (print-friendly) lengkap dengan Kop Surat Gereja dinamis.

## User Review Required
- **Layout Cetak**:
  - Laporan Ulang Tahu Jemaat akan menggunakan tata letak **Portrait** (A4).
  - Laporan Keluarga Pra Sejahtera akan menggunakan tata letak **Landscape** (A4) untuk mengakomodasi kolom yang padat.
- **Pengelompokan (Group By) Pra Sejahtera**: Di halaman cetak keluarga pra sejahtera, baris akan dikelompokkan secara visual berdasarkan **Rayon** pelayanan.

## Proposed Changes

### 1. Filament Custom Pages

#### [MODIFY] [BirthdayReport.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Pages/BirthdayReport.php)
Menambahkan method `getHeaderActions()` untuk memuat tombol cetak:
```php
protected function getHeaderActions(): array
{
    return [
        \Filament\Actions\Action::make('print')
            ->label('Cetak Laporan')
            ->icon('heroicon-o-printer')
            ->color('success')
            ->url(fn () => route('report.birthdays.print'))
            ->openUrlInNewTab(),
    ];
}
```

#### [MODIFY] [UnderprivilegedFamilyReport.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Pages/UnderprivilegedFamilyReport.php)
Menambahkan method `getHeaderActions()` untuk memuat tombol cetak:
```php
protected function getHeaderActions(): array
{
    return [
        \Filament\Actions\Action::make('print')
            ->label('Cetak Laporan')
            ->icon('heroicon-o-printer')
            ->color('warning')
            ->url(fn () => route('report.underprivileged.print'))
            ->openUrlInNewTab(),
    ];
}
```

---

### 2. Backend & Routing

#### [NEW] [PastoralReportController.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Http/Controllers/PastoralReportController.php)
Controller baru yang bertugas mengolah data dan merender tampilan siap cetak.
- `printBirthdays()`:
  - Mengambil daftar hari dalam minggu ini (Senin - Minggu).
  - Mengambil data `Member` dengan relasi `family.rayon`, mengecualikan jemaat meninggal (`is_deceased = false`), dan mencocokkan bulan dan hari ulang tahun.
  - Mengurutkan menggunakan `orderByRaw` sesuai urutan hari minggu ini.
  - Memuat `ChurchProfile::first()`.
  - Merender view `reports.birthdays`.
- `printUnderprivilegedFamilies()`:
  - Mengambil data `Family` dengan relasi `houseStatus`, `houseCategory`, `rayon`, dan `members.familyPosition`.
  - Memfilter data keluarga pra sejahtera (`needsAssistance()`).
  - Memuat `ChurchProfile::first()`.
  - Merender view `reports.underprivileged`.

#### [MODIFY] [web.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/routes/web.php)
Mendaftarkan route cetak di dalam middleware `auth`:
- `GET /admin/reports/birthdays/print` -> `PastoralReportController@printBirthdays` (name: `report.birthdays.print`)
- `GET /admin/reports/underprivileged/print` -> `PastoralReportController@printUnderprivilegedFamilies` (name: `report.underprivileged.print`)

---

### 3. Frontend / Blade Templates

#### [NEW] [birthdays.blade.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/resources/views/reports/birthdays.blade.php)
- Layout Portrait dengan double border frame yang rapi.
- Kop Surat berlogo dinamis dari `$profile`.
- Tabel dengan kolom: No, Nama Jemaat, Tanggal Lahir, Usia (Tahun Ini), Rayon.

#### [NEW] [underprivileged.blade.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/resources/views/reports/underprivileged.blade.php)
- Layout Landscape dengan double border frame yang rapi.
- Kop Surat berlogo dinamis dari `$profile`.
- Tabel dengan kolom: No, No KK, Nama Kepala Keluarga, Alamat, Rayon, Status Rumah, Kategori/Kondisi Rumah.
- Baris data dikelompokkan secara visual berdasarkan **Rayon** pelayanan.

## Verification Plan

### Manual Verification
- Klik tombol "Cetak Laporan" di pojok kanan atas masing-masing halaman.
- Pastikan tab baru terbuka dengan layout cetak yang rapi, border tabel tegas, dan tidak ada elemen berantakan.
- Verifikasi kesesuaian data yang ditampilkan di halaman cetak dengan data di tabel Filament.
- Periksa keselarasan kop surat gereja dan logo.
