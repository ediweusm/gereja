# Implement Sprint 1: Access Management & Security System

Implement User CRUD (US 1.1) and Roles & Permissions management (US 1.2) using Filament v3, Spatie Permission, and Filament Shield.

## Proposed Changes

We will modify/create the following files in the project:

### 1. Database & Seeding
- **[NEW] Migration**: Create a migration to add `is_active` (boolean, default: true) to the `users` table.
- **[NEW] [RoleSeeder.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/database/seeders/RoleSeeder.php)**: Create a seeder to register the 3 main roles (`super_admin`, `administrasi`, `bendahara`) if they don't exist.
- **[MODIFY] [DatabaseSeeder.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/database/seeders/DatabaseSeeder.php)**: Call `RoleSeeder` during DB seed.

### 2. User Authentication & Authorization Model
- **[MODIFY] [User.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Models/User.php)**:
  - Implement `FilamentUser`.
  - Import `HasRoles`.
  - Define `canAccessPanel(Panel $panel): bool` to restrict access if `is_active` is false.
  - Define `is_active` boolean cast.

### 3. Filament Resource
- **[NEW] [UserResource.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/UserResource.php)**: Create the Filament resource for managing users, containing the specified Form and Table schema (with unique email checks, dehydrated password hashing, roles selection, and direct toggles for `is_active`).

---

## Verification Plan

### Automated Tests
- Run migrations (`php artisan migrate`) to add the `is_active` column.
- Run seeders (`php artisan db:seed`) to create the base roles.
- Run manual tinker commands to verify the role seeder and user active check.

### Manual Verification
- Run the local dev server.
- Log in to the admin panel using the browser agent.
- Navigate to the Users resource at `http://localhost:8000/admin/users`.
- Perform CRUD operations on users, assign roles, and test toggle behavior for `is_active`.
