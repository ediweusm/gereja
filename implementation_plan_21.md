# Implementation Plan - Refactoring Cetak Jadwal Pelayanan (Rentang Tanggal)

Rencana implementasi untuk mengubah fitur "Cetak Jadwal Pelayanan" dari per-tanggal tunggal menjadi berbasis rentang tanggal (start_date dan end_date).

## User Review Required

> [!IMPORTANT]
> - Action di `ListEvents.php` akan diubah nama dari `printByDate` menjadi `printByRange`.
> - Route akan diubah dari `/admin/events/print-by-date` ke `/admin/events/print-by-range`.
> - Tampilan Blade akan melakukan pengelompokan (grouping) otomatis berdasarkan tanggal kegiatan (`event_date`) dengan page-break agar rapi saat dicetak.

## Proposed Changes

---

### Filament Resources

#### [MODIFY] [ListEvents.php](file:///\\wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/EventResource/Pages/ListEvents.php)
- Ganti action `printByDate` menjadi `printByRange`.
- Ubah label menjadi `"Cetak Jadwal per Periode"`.
- Gunakan dua `DatePicker` di dalam form: `start_date` dan `end_date`.
- Di dalam `action()`, alihkan ke route `events.print_by_range` dengan menyertakan parameter `start_date` dan `end_date`.

---

### Http Controllers & Routes

#### [MODIFY] [WartaPrintController.php](file:///\\wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Http/Controllers/WartaPrintController.php)
- Ubah method `printByDate` menjadi `printByRange`.
- Ambil `$startDate` dan `$endDate` dari request query.
- Query data `Event` dengan filter `whereBetween('event_date', [$startDate, $endDate])`.
- Urutkan (orderBy) berdasarkan `event_date` ASC, kemudian `start_time` ASC.
- Kirim `$startDate`, `$endDate`, `$events`, dan `$profile` ke view.

#### [MODIFY] [web.php](file:///\\wsl.localhost/Ubuntu-24.04/home/ediwe/sig/routes/web.php)
- Ubah definisi route `/admin/events/print-by-date` menjadi `/admin/events/print-by-range`.
- Ubah route name dari `events.print_by_date` menjadi `events.print_by_range`.

---

### Blade Templates

#### [MODIFY] [events-by-date.blade.php](file:///\\wsl.localhost/Ubuntu-24.04/home/ediwe/sig/resources/views/reports/events-by-date.blade.php)
- Ubah judul kop/laporan: `"JADWAL PELAYANAN IBADAH PERIODE {{ $startDate }} s/d {{ $endDate }}"`.
- Tambahkan logika grouping berdasarkan `$event->event_date`.
- Setiap pergantian tanggal, cetak header tanggal baru (contoh: "HARI MINGGU, 21 JUNI 2026") dengan style tebal dan garis bawah.
- Tambahkan `page-break-before: always;` pada header tanggal baru (selain tanggal pertama) agar terpotong rapi per hari pada saat cetak fisik/PDF.

---

### Tests

#### [MODIFY] [EventServiceTest.php](file:///\\wsl.localhost/Ubuntu-24.04/home/ediwe/sig/tests/Feature/EventServiceTest.php)
- Perbarui test case untuk menguji route `events.print_by_range` dengan parameter `start_date` dan `end_date`.

---

## Verification Plan

### Automated Tests
- Jalankan test `wsl php artisan test --filter=EventServiceTest` untuk memverifikasi fungsionalitas route dan autentikasi.

### Manual Verification
- Masuk ke dashboard Filament, buka modul **Jadwal Ibadah**.
- Klik tombol header action **"Cetak Jadwal per Periode"**.
- Pilih rentang tanggal (misalnya `2026-06-01` s/d `2026-06-30`).
- Klik cetak dan verifikasi tampilan PDF/halaman print.
