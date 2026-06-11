# Implementation Plan - Sprint 6: Modul Manajemen Ibadah & Pelayanan

Rencana implementasi untuk membangun arsitektur penjadwalan ibadah dan warta jemaat di aplikasi Gereja dengan Laravel 11 dan Filament v3.

## User Review Required

> [!IMPORTANT]
> - Relasi `rayon` dan `hostFamily` di model `Event` akan diset menggunakan `nullOnDelete` jika data terkait dihapus (berdasarkan rancangan database).
> - Di `EventAssignment`, jika `member_id` kosong, data `guest_name` digunakan untuk mewakili petugas eksternal.

## Proposed Changes

---

### Database Migration

#### [MODIFY] [2026_06_07_124905_create_ministry_roles_table.php](file:///\\wsl.localhost/Ubuntu-24.04/home/ediwe/sig/database/migrations/2026_06_07_124905_create_ministry_roles_table.php)
Verifikasi dan jalankan migrasi tabel `ministry_roles`:
- `id`
- `name` (string)
- `sort_order` (integer, default: 0)
- `timestamps()`

#### [MODIFY] [2026_06_07_124926_create_events_table.php](file:///\\wsl.localhost/Ubuntu-24.04/home/ediwe/sig/database/migrations/2026_06_07_124926_create_events_table.php)
Verifikasi dan jalankan migrasi tabel `events`:
- `id`
- `name` (string)
- `theme` (string, nullable)
- `event_date` (date)
- `start_time` (time)
- `event_type` (string)
- `mode` (string, default: 'onsite')
- `rayon_id` (foreignId, nullable, constrained, setNullOnDelete)
- `host_family_id` (foreignId ke `families`, nullable, constrained, setNullOnDelete)
- `location_notes` (string, nullable)
- `timestamps()`

#### [MODIFY] [2026_06_07_124941_create_event_assignments_table.php](file:///\\wsl.localhost/Ubuntu-24.04/home/ediwe/sig/database/migrations/2026_06_07_124941_create_event_assignments_table.php)
Verifikasi dan jalankan migrasi tabel `event_assignments`:
- `id`
- `event_id` (foreignId, constrained, cascadeOnDelete)
- `ministry_role_id` (foreignId, constrained)
- `member_id` (foreignId, nullable, constrained, setNullOnDelete)
- `guest_name` (string, nullable)
- `timestamps()`

---

### Eloquent Models

#### [NEW] [MinistryRole.php](file:///\\wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Models/MinistryRole.php)
- Guarded `['id']`
- Properti `fillable`/`guarded`
- Timestamps otomatis

#### [NEW] [Event.php](file:///\\wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Models/Event.php)
- Guarded `['id']`
- Relasi `rayon()` -> `belongsTo(Rayon::class)`
- Relasi `hostFamily()` -> `belongsTo(Family::class, 'host_family_id')`
- Relasi `assignments()` -> `hasMany(EventAssignment::class)`

#### [NEW] [EventAssignment.php](file:///\\wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Models/EventAssignment.php)
- Guarded `['id']`
- Relasi `ministryRole()` -> `belongsTo(MinistryRole::class)`
- Relasi `member()` -> `belongsTo(Member::class)`
- Relasi `event()` -> `belongsTo(Event::class)`

---

### Filament Resources

#### [NEW] [MinistryRoleResource.php](file:///\\wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/MinistryRoleResource.php)
- Navigation Group: `'Pengaturan & Master Data'`
- Navigation Icon: `'heroicon-o-users'`
- Form:
  - `TextInput` `name` (required)
  - `TextInput` `sort_order` (numeric, default: 0)
- Table:
  - `name` (searchable)
  - `sort_order` (sortable)

#### [NEW] [EventResource.php](file:///\\wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/EventResource.php)
- Navigation Group: `'Manajemen Pelayanan'`
- Navigation Icon: `'heroicon-o-calendar-days'`
- Form Layout:
  - **Section 1: Detail Ibadah**
    - Grid 2: `name` (required), `theme` (nullable)
    - Grid 3: `event_date` (DatePicker, required), `start_time` (TimePicker, required), `event_type` (Select: `'Ibadah Raya'`, `'Persekutuan Wilayah'`, `'Persekutuan Kategorial'`, required)
    - Grid 2: `mode` (Select: `'onsite'`, `'online'`, `'hybrid'`, default: `'onsite'`, required), `location_notes` (Textarea, nullable)
    - Grid 2: `rayon_id` (Select, relationship `rayon.name`, searchable, preload, nullable), `host_family_id` (Select, relationship `hostFamily.family_number`, searchable, preload, nullable)
  - **Section 2: Jadwal Petugas**
    - `Repeater` `assignments` (relationship: `assignments`)
      - Grid 3 columns:
        - `ministry_role_id` (Select, relationship `ministryRole.name`, searchable, preload, required)
        - `member_id` (Select, relationship `member.first_name`, custom label/accessor `full_name`, searchable, nullable, helper: `"Pilih jika petugas adalah jemaat internal"`)
        - `guest_name` (TextInput, nullable, helper: `"Isi jika petugas adalah tamu/eksternal"`)
- Table Schema:
  - `event_date` (TextColumn, date, sortable)
  - `start_time` (TextColumn, time)
  - `name` (TextColumn, searchable)
  - `theme` (TextColumn, limit: 30)
  - `mode` (TextColumn, badge)

---

## Verification Plan

### Automated Tests
- Menjalankan `php artisan db:seed` atau unit test jika ada untuk memastikan skema migrasi valid.
- Menjalankan linting/analisis statis jika ada.

### Manual Verification
- Menjalankan migrasi: `php artisan migrate`.
- Membuka halaman dashboard Filament untuk memverifikasi resource baru tampil.
- Menguji pembuatan `MinistryRole` dan mengurutkannya.
- Menguji pengisian form `Event` beserta repeater petugas (`EventAssignment`).
- Memastikan pilihan relasi jemaat menampilkan nama lengkap jemaat (menggunakan custom label accessor `fullName` / `full_name`).
