# Implementation Plan: Cetak Kartu Keluarga Jemaat

Implement a printable family card feature in the Administrasi Jemaat module that shows family details and lists members in an elegant, landscape layout.

## Proposed Changes

### 1. Controller
#### [NEW] [FamilyPrintController.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Http/Controllers/FamilyPrintController.php)
- Method `print(Family $family)`
- Eager load relations to avoid N+1 query:
  - `rayon`
  - `houseCategory`
  - `houseStatus`
  - `members` with:
    - `familyPosition`
    - `maritalStatus`
    - `education`
    - `occupation`
    - `churchRole`
    - `membershipStatus`
- Query the dynamic `ChurchProfile::first()` config or fallback to defaults.
- Return view `reports.kartu-keluarga` with `$family` and `$profile`.

### 2. Route registration
#### [MODIFY] [web.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/routes/web.php)
- Add GET route `/admin/families/{family}/print` pointing to `FamilyPrintController@print` named `family.print` with `auth` middleware.

### 3. Print Template (Blade)
#### [NEW] [kartu-keluarga.blade.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/resources/views/reports/kartu-keluarga.blade.php)
- Landscape orientation, A4 paper size, clean double border.
- Header showing Church profile (Sinode, Church name, Address, Phone, Logo image).
- Title: "KARTU KELUARGA JEMAAT".
- Family info grid (2 columns):
  - Left column: Nomor KK, Nama Kepala Keluarga (Suami or first member).
  - Right column: Alamat, Rayon.
- Members table:
  - Columns: No, Nama Lengkap, L/P, Tempat/Tgl Lahir, Hubungan Keluarga, Pendidikan, Pekerjaan, Status Nikah, Status Keanggotaan.
- Footer signatures:
  - Left: Kepala Keluarga signature box.
  - Right: Ketua Majelis Jemaat signature box.

### 4. Filament Action Integration
#### [MODIFY] [FamilyResource.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/FamilyResource.php)
- In the table actions, add `Action::make('print')` with printer icon, success color, open in new tab.
#### [MODIFY] [EditFamily.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/FamilyResource/Pages/EditFamily.php)
- Add a header button/action in `getHeaderActions` for printing.

---

## Verification Plan

### Automated Tests
- None needed (custom UI route, will verify visually).

### Manual Verification
- Log in as admin, navigate to **Kartu Keluarga**.
- Verify that a "Cetak KK" action is visible for each family in the table actions.
- Click the print action and check the generated page layout, headers, family details, and member list.
- Navigate to the Edit page of a family and verify the "Cetak KK" header action works.
