# Implementation Plan: Profil Identitas Gereja (Church Identity Profile Setting)

Implement a White-labeling settings system to manage the church identity dynamically (GMIT Name, Church Name, Address, Phone, Logo). Integrate this settings data into the printed voucher/receipt headers instead of using hardcoded values.

## Proposed Changes

### 1. Database Model, Migration, and Seeder
#### [NEW] [create_church_profiles_table.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/database/migrations/2026_06_07_000000_create_church_profiles_table.php)
- Column definition:
  - `id` (primary key)
  - `gmit_name` (string, e.g., "Majelis Sinode GMIT")
  - `church_name` (string, e.g., "Jemaat Sion Oepura")
  - `address` (text)
  - `phone` (string)
  - `logo_path` (string, nullable)
  - `timestamps`

#### [NEW] [ChurchProfile.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Models/ChurchProfile.php)
- Define `ChurchProfile` model with `$guarded = ['id']` and trait `HasFactory`.

#### [NEW] [ChurchProfileSeeder.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/database/seeders/ChurchProfileSeeder.php)
- Seed 1 default row:
  - `gmit_name` = `"Majelis Sinode GMIT"`
  - `church_name` = `"Jemaat Sion Oepura"`
  - `address` = `"Jl. H.R. Koroh, Oepura, Kec. Maulafa, Kota Kupang, Nusa Tenggara Timur"`
  - `phone` = `"081123456789"`
  - `logo_path` = `null`

#### [MODIFY] [DatabaseSeeder.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/database/seeders/DatabaseSeeder.php)
- Call `ChurchProfileSeeder::class` in the seeders array. Add cleanup logic for `church_profiles` table.

### 2. Filament Settings Page
#### [NEW] [ManageChurchProfile.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Pages/ManageChurchProfile.php)
- **Path**: `app/Filament/Pages/ManageChurchProfile.php`
- **Class**: `class ManageChurchProfile extends Page implements Forms\Contracts\HasForms` (uses `Forms\Concerns\InteractsWithForms`)
- **Navigation**:
  - Navigation Group: `'Pengaturan & Master Data'`
  - Navigation Icon: `'heroicon-o-building-office-2'`
  - Navigation Label & Title: `'Profil Gereja'`
- **Access Control**:
  - Restrict access to Super Admin role: override `canAccess()` method returning `auth()->user()->hasRole('super_admin')`.
- **Form Schema**:
  - `gmit_name` (TextInput, required)
  - `church_name` (TextInput, required)
  - `address` (Textarea, required)
  - `phone` (TextInput, required)
  - `logo_path` (FileUpload, image, directory: `'logos'`)
- **Life-Cycle**:
  - `mount()`: Load data from `ChurchProfile::first()`. Fill form `$this->form->fill($profile?->toArray() ?? [])`.
  - `save()`: Save form state using `updateOrCreate(['id' => 1], $this->form->getState())` and show notification.

#### [NEW] [manage-church-profile.blade.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/resources/views/filament/pages/manage-church-profile.blade.php)
- Standard Filament custom page view to render the settings form.

### 3. Print Templates Integration
#### [MODIFY] [journal-voucher.blade.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/resources/views/reports/journal-voucher.blade.php)
- Load `$profile = \App\Models\ChurchProfile::first() ?? new \App\Models\ChurchProfile(['gmit_name' => 'Majelis Sinode GMIT', 'church_name' => 'Jemaat Sion Oepura', 'address' => 'Jl. H.R. Koroh, Oepura, Kec. Maulafa, Kota Kupang, Nusa Tenggara Timur', 'phone' => '081123456789'])`.
- Replace hardcoded header strings with `$profile->gmit_name`, `$profile->church_name`, `$profile->address`, and `$profile->phone`.
- Display Logo image using `Storage::url($profile->logo_path)` if `$profile->logo_path` is not null.

#### [MODIFY] [kwitansi.blade.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/resources/views/reports/kwitansi.blade.php)
- Apply the same dynamic profile integration in the header.

---

## Verification Plan

### Automated Tests
- Run `wsl php artisan migrate` to create the table.
- Run `wsl php artisan db:seed` to verify seeder works correctly.
- Verify compilation and route lists.

### Manual Verification
- Log in as Super Admin. Navigate to **Profil Gereja** in the Sidebar.
- Verify that the settings form loads with seeded default data.
- Upload a test logo, modify the address, and click save. Verify that settings are updated and saved correctly.
- Log in as a non-Super Admin (e.g. `staff@sig.test`). Verify that the **Profil Gereja** navigation menu is hidden and accessing `/admin/manage-church-profile` directly is blocked.
- Go to Jurnal Umum, click **Cetak Bukti** and **Cetak Kwitansi**. Verify that the updated address, church name, and uploaded logo are displayed correctly in the print view.
