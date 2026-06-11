# Implementation Plan: Administrasi Jemaat Module

Implement `FamilyResource`, `MembersRelationManager`, and `MemberMutationResource` for the Congregation Administration module, using Indonesian UI terms.

## Proposed Changes

### 1. FamilyResource (Kartu Keluarga)
- **Model**: `App\Models\Family`
- **Navigation Group**: `'Administrasi Jemaat'`
- **Navigation Icon**: `'heroicon-o-home'`
- **Form Schema**:
  - Grid layout with the following fields:
    - `family_number`: TextInput, required, unique:ignoreRecord, label `'Nomor KK Gereja'`.
    - `rayon_id`: Select, relationship to `Rayon` (label `'name'`), searchable, preload, label `'Rayon'`.
    - `address`: Textarea, required, label `'Alamat'`.
    - `phone`: TextInput, label `'Telepon/HP'`.
    - `house_category_id`: Select, options retrieved from active `DataDictionary` with category `'house_category'`, label `'Kategori Rumah'`.
    - `house_status_id`: Select, options retrieved from active `DataDictionary` with category `'house_status'`, label `'Status Rumah'`.
    - `notes`: Textarea, label `'Catatan'`.
- **Table Schema**:
  - `family_number`: TextColumn, searchable, sortable, label `'Nomor KK'`.
  - `rayon.name`: TextColumn, searchable, sortable, label `'Rayon'`.
  - `address`: TextColumn, searchable, limit 50, label `'Alamat'`.
  - `members_count`: TextColumn counts `members`, label `'Jumlah Anggota'`.

### 2. MembersRelationManager (Anggota Keluarga)
- **Generates**: `App\Filament\Resources\FamilyResource\RelationManagers\MembersRelationManager`
- **Title**: `'Anggota Keluarga'`
- **Tabs Layout**:
  - **Tab 'Biodata'**:
    - `first_name`: TextInput, required, label `'Nama Depan'`.
    - `middle_name`: TextInput, label `'Nama Tengah'`.
    - `last_name`: TextInput, label `'Nama Belakang'`.
    - `nik`: TextInput, label `'NIK'`, unique:ignoreRecord.
    - `gender`: Select (`'L' => 'Laki-laki', 'P' => 'Perempuan'`), label `'Jenis Kelamin'`.
    - `birth_place`: TextInput, label `'Tempat Lahir'`.
    - `birth_date`: DatePicker, label `'Tanggal Lahir'`.
    - `family_position_id`: Select, options from active `DataDictionary` category `'family_position'`, label `'Hubungan Keluarga'`, required.
    - `marital_status_id`: Select, options from active `DataDictionary` category `'marital_status'`, label `'Status Pernikahan'`, live().
    - `education_id`: Select, options from active `DataDictionary` category `'education'`, label `'Pendidikan'`.
    - `occupation_id`: Select, options from active `DataDictionary` category `'occupation'`, label `'Pekerjaan'`.
    - `income`: TextInput, numeric, label `'Penghasilan'`.
  - **Tab 'Kegerejaan'**:
    - `church_role_id`: Select, options from active `DataDictionary` category `'church_role'`, label `'Jabatan Struktural'`.
    - `membership_status_id`: Select, options from active `DataDictionary` category `'membership_status'`, label `'Status Keanggotaan'`.
  - **Tab 'Sakramen'**:
    - `status_baptis`: Toggle, label `'Sudah Baptis'`, live().
    - `baptism_date`: DatePicker, label `'Tanggal Baptis'`, visible if `status_baptis` is true.
    - `baptism_church`: TextInput, label `'Gereja Baptis'`, visible if `status_baptis` is true.
    - `baptism_pastor`: TextInput, label `'Pendeta Baptis'`, visible if `status_baptis` is true.
    - `sidi_date`: DatePicker, label `'Tanggal Sidi'`.
    - `sidi_church`: TextInput, label `'Gereja Sidi'`.
    - `marriage_date`: DatePicker, label `'Tanggal Pernikahan'`, visible if `marital_status_id` resolves to code `'married'` or `'married-civil'`.
    - `marriage_church`: TextInput, label `'Gereja Pernikahan'`, visible if `marital_status_id` resolves to code `'married'` or `'married-civil'`.
  - **Tab 'Kematian'**:
    - `is_deceased`: Toggle, label `'Meninggal Dunia'`, live().
    - `death_date`: DatePicker, label `'Tanggal Kematian'`, visible if `is_deceased` is true.
- **Table Schema**:
  - `full_name`: TextColumn showing member's full name, label `'Nama Lengkap'`.
  - `familyPosition.label`: TextColumn, label `'Hubungan Keluarga'`.
  - `gender`: TextColumn, label `'Jenis Kelamin'`, formatStateUsing.
  - `birth_date`: TextColumn, date, label `'Tanggal Lahir'`.
  - `membershipStatus.label`: TextColumn, label `'Status Anggota'`.

### 3. MemberMutationResource
- **Model**: `App\Models\MemberMutation`
- **Navigation Group**: `'Administrasi Jemaat'`
- **Navigation Icon**: `'heroicon-o-arrows-right-left'`
- **Form Schema**:
  - `member_id`: Select, relationship to `Member`, searchable, preload, displaying full name, label `'Anggota Jemaat'`.
  - `mutation_type`: Select, options: `'Atestasi Masuk'`, `'Atestasi Keluar'`, `'Pindah Rayon'`, `'Titipan'`, `'Lainnya'`, label `'Jenis Mutasi'`.
  - `mutation_date`: DatePicker, label `'Tanggal Mutasi'`, required.
  - `origin_church`: TextInput, label `'Gereja Asal'`.
  - `destination_church`: TextInput, label `'Gereja Tujuan'`.
  - `reason`: Textarea, label `'Alasan'`.
- **Table Schema**:
  - Member name: TextColumn displaying full name, label `'Nama Anggota'`.
  - `mutation_type`: TextColumn, badge, label `'Jenis Mutasi'`.
  - `mutation_date`: TextColumn, date, label `'Tanggal Mutasi'`.

---

## Verification Plan

### Automated Tests
- Run resource creation commands.
- Run database seeding to ensure dictionaries exist.
- Verify compiling status by checking routes.

### Manual Verification
- Access the admin panel.
- Verify creation and validation of a Family card (`FamilyResource`).
- Verify adding family members through the relation manager under a Family card, checking tab switching and visibility logics (Baptism, Marriage, Death).
- Verify recording a member mutation under `MemberMutationResource`.
