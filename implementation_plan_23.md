# Rencana Implementasi: MemberResource Read-Only

Dokumen ini menjelaskan rencana pembuatan dan konfigurasi `MemberResource` menggunakan model `App\Models\Member` sebagai halaman "Daftar Seluruh Jemaat" yang bersifat **Strictly Read-Only** (Hanya Baca).

## Asumsi & Kebijakan Akses (Read-Only)

Untuk memastikan bahwa resource ini tidak dapat dimodifikasi oleh pengguna:
1. Menambahkan metode otorisasi berikut ke dalam kelas `MemberResource`:
   - `canCreate(): bool { return false; }`
   - `canEdit(Model $record): bool { return false; }`
   - `canDelete(Model $record): bool { return false; }`
2. Pada konfigurasi Actions tabel, hanya menyertakan `ViewAction::make()`. Menghapus `EditAction` dan `DeleteAction`.
3. Pada Bulk Actions tabel, menghapus `DeleteBulkAction`.
4. Pada metode `getPages()`, hanya menyisakan rute `'index'` (untuk List) dan `'view'` (untuk View). Menghapus rute `'create'` dan `'edit'`.

---

## Rencana Perubahan

### Langkah 1: Generate Resource
- **Command:** `php artisan make:filament-resource Member --view`
- Ini akan menghasilkan file:
  - `app/Filament/Resources/MemberResource.php`
  - `app/Filament/Resources/MemberResource/Pages/ListMembers.php`
  - `app/Filament/Resources/MemberResource/Pages/ViewMember.php`
  - (Halaman `CreateMember.php` dan `EditMember.php` tidak akan dibuat karena menggunakan bendera `--view` / we will clean up getPages).

### Langkah 2: Konfigurasi Properti & Otorisasi
Buka `MemberResource.php` dan atur properti navigasi:
- `protected static ?string $navigationIcon = 'heroicon-o-users';`
- `protected static ?string $navigationGroup = 'Administrasi Jemaat';`
- `protected static ?string $navigationLabel = 'Daftar Seluruh Jemaat';`
- `protected static ?string $recordTitleAttribute = 'first_name';`

Tambahkan method otorisasi:
```php
public static function canCreate(): bool
{
    return false;
}

public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
{
    return false;
}

public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
{
    return false;
}
```

### Langkah 3: Konfigurasi Table
Di dalam method `table(Table $table)`:
- **Columns:**
  - `full_name`: Pencarian menggunakan nama depan, tengah, belakang. Diurutkan berdasarkan nama depan. Dicetak tebal.
  - `gender`: L/P badge. Warna `info` untuk L, `success` untuk P.
  - `birth_date`: Usia terformat (misal: '25 thn' atau '-'). Bisa diurutkan.
  - `family.family_number`: Nomor Kartu Keluarga. Bisa dicari & diurutkan.
  - `familyPosition.name`: SHDK (Status Hubungan Dalam Keluarga). Bisa dicari.
  - `membershipStatus.name`: Status Keanggotaan dengan badge.
- **Filters:**
  - `gender` select filter.
  - `membership_status_id` select filter menggunakan relasi.
- **Actions:**
  - Hanya `ViewAction::make()`.
- **Bulk Actions:**
  - Kosongkan (Hapus `DeleteBulkAction`).

### Langkah 4: Konfigurasi Form (Tabs Layout untuk View Mode)
Gunakan `Tabs::make('Data Jemaat')->tabs([...])` dengan 4 Tab:
1. **Data Pribadi:** `first_name`, `middle_name`, `last_name`, `nik`, `birth_place`, `birth_date`, `gender`, `phone`, `is_deceased` (Toggle), `death_date`.
2. **Keluarga & Pekerjaan:** `family_id` (Select relasi), `family_position_id` (Select relasi), `father_name`, `mother_name`, `marital_status_id` (Select relasi), `education_id` (Select relasi), `occupation_id` (Select relasi), `income`.
3. **Data Gerejawi:** `membership_status_id` (Select relasi), `church_role_id` (Select relasi), `status_baptis` (Toggle).
4. **Sakramen & Pernikahan:**
   - Fieldset Baptis: `baptism_date`, `baptism_church`, `baptism_pastor`
   - Fieldset Sidi: `sidi_date`, `sidi_church`, `sidi_pastor`
   - Fieldset Pernikahan: `marriage_date`, `marriage_church`, `marriage_pastor`

### Langkah 5: Bersihkan Pages
Dalam method `getPages()`, hapus baris create dan edit. Hanya sisakan:
```php
'index' => Pages\ListMembers::route('/'),
'view' => Pages\ViewMember::route('/{record}'),
```

---

## Verifikasi Plan

### Linter & PHP Compiler
- Jalankan `php -l` untuk memeriksa sintaks kelas yang dimodifikasi.
- Pastikan modul / kelas yang diimpor (`Tabs`, `Tab`, `Fieldset`, `TextColumn`, `SelectFilter`, dll) terdefinisi dengan benar.

### Verifikasi Manual
- Buka panel Filament dan akses navigasi "Daftar Seluruh Jemaat".
- Periksa tombol "Create" tidak muncul di halaman indeks.
- Periksa kolom tabel tampil sesuai spesifikasi (badge gender, usia terhitung, nomor KK, SHDK, Status).
- Buka detail jemaat menggunakan tombol "View" (mata) untuk memastikan form tampil dalam format tabs dan semua input bersifat read-only (disabled) tanpa tombol Edit/Save.
