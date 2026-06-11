# Implementation Plan: Seeding Sample Data (Rayon, Family, Member, Journal, etc.)

This plan describes how we will populate the database with realistic sample data for all major models (Users, Rayons, Families, Members, Mutations, Contributions, Journals, and Journal Items) so the application is immediately ready for testing and reporting.

## Proposed Changes

### 1. Dictionary Seeder
#### [MODIFY] [DataDictionarySeeder.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/database/seeders/DataDictionarySeeder.php)
- Add a new category `'contribution_type'` containing common contribution types:
  - `'Persembahan Persepuluhan'` (Tithing)
  - `'Persembahan Nazar'` (Pledge)
  - `'Persembahan Syukur'` (Thanksgiving)
  - `'Persembahan Pembangunan'` (Building Fund)

### 2. User Seeder
#### [NEW] [UserSeeder.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/database/seeders/UserSeeder.php)
- Create three default users with standard credentials and assign their Spatie roles:
  1. **Super Admin**: `admin@sig.test` (role: `super_admin`)
  2. **Staf Administrasi**: `staff@sig.test` (role: `administrasi`)
  3. **Bendahara**: `bendahara@sig.test` (role: `bendahara`)
- Password for all users: `password`

### 3. Rayon Seeder
#### [NEW] [RayonSeeder.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/database/seeders/RayonSeeder.php)
- Seed 5 standard church rayons (sectors):
  - `'Rayon 1 (Matius)'`
  - `'Rayon 2 (Markus)'`
  - `'Rayon 3 (Lukas)'`
  - `'Rayon 4 (Yohanes)'`
  - `'Rayon 5 (Petrus)'`

### 4. Family and Member Seeder
#### [NEW] [FamilyAndMemberSeeder.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/database/seeders/FamilyAndMemberSeeder.php)
- Create 10 realistic family cards (Kartu Keluarga - KK) across the 5 rayons.
- For each family, seed 2 to 5 members (husband, wife, children, sometimes relatives/parents) with realistic data:
  - NIK, phone number, birth place, and birth dates.
  - Correct family positions, education levels, occupations, marital statuses, and income ranges from the `data_dictionaries` table.
  - Varied sacraments: some baptized (with pastor, church, witness info), some sidi-confirmed, and husbands/wives with church marriage dates.
  - Seed at least 1 deceased member (e.g. `is_deceased = true`, `death_date` filled).
  - Seed a few mutations in `member_mutations` (e.g. `'Pindah Rayon'`, `'Atestasi Masuk'`) to populate the mutations log.

### 5. Journal Seeder
#### [NEW] [JournalSeeder.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/database/seeders/JournalSeeder.php)
- Seed at least 5 realistic financial journal entries representing common church operations:
  1. **Rutin Weekly Offering**: Debit Cash, Credit Weekly Offering Revenue.
  2. **Tithing/Persepuluhan (Member Contribution)**: Debit Cash, Credit Tithing Revenue. (Link this with `MemberContribution` to test member contributions).
  3. **Pastor Honorarium Payment**: Debit Pastor Honorarium Expense, Credit Bank Mandiri.
  4. **Electricity & Water Bill Payment**: Debit Utilities Expense, Credit Kas Kecil.
  5. **Bank Mandiri Interest**: Debit Bank Mandiri, Credit Bank Interest Revenue.
- Ensure all entries:
  - Are perfectly balanced (Debit sum = Credit sum).
  - Use transactional leaf accounts.
  - Set realistic dates, descriptions, and reference numbers.

### 6. Main Database Seeder
#### [MODIFY] [DatabaseSeeder.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/database/seeders/DatabaseSeeder.php)
- Call all seeders in order:
  1. `RoleSeeder`
  2. `AccountSeeder`
  3. `DataDictionarySeeder`
  4. `UserSeeder`
  5. `RayonSeeder`
  6. `FamilyAndMemberSeeder`
  7. `JournalSeeder`
- Ensure data is cleared/cleaned gracefully to prevent foreign key errors. (Using truncate/delete on seed targets).

---

## Verification Plan

### Automated Tests
- Run `wsl php artisan db:seed` to verify that seeding runs successfully without errors.
- Run `wsl php artisan migrate:fresh --seed` to ensure the entire database can be rebuilt and seeded from scratch cleanly.

### Manual Verification
- Access Filament dashboard to verify:
  - Users are present and assigned correct roles.
  - Rayon count matches the 5 seeded rayons.
  - Families and members list displays all seeded records with realistic data (Indonesian labels).
  - Journal entries and transaction items show up perfectly balanced in Jurnal Umum.
  - Member Mutations list displays the sample mutations.
