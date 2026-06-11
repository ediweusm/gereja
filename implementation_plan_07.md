# Implement Filament Resources for Master Data & Chart of Accounts

Create three Filament Resources: `RayonResource`, `DataDictionaryResource`, and `AccountResource` in the [app/Filament/Resources](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources) folder.

## Proposed Changes

We will create the resources using the Filament generator and customize their schemas.

### 1. RayonResource
- **Navigation Group**: `'Pengaturan & Master Data'`
- **Navigation Icon**: `'heroicon-o-map'`
- **Form Schema**:
  - `name`: TextInput, required, max 100.
  - `description`: Textarea, nullable, columnSpanFull.
- **Table Schema**:
  - `name`: TextColumn, searchable, sortable.
  - `description`: TextColumn, limit 50.
  - `families_count`: TextColumn displaying count of families (`counts('families')`).

### 2. DataDictionaryResource (Kamus Data)
- **Navigation Group**: `'Pengaturan & Master Data'`
- **Navigation Icon**: `'heroicon-o-book-open'`
- **Form Schema**:
  - `category`: Select, required, standard options.
  - `label`: TextInput, required, max 100.
  - `code`: TextInput, nullable, max 50.
  - `sort_order`: TextInput, numeric, default 0.
  - `is_active`: Toggle, default true.
- **Table Schema**:
  - `category`: TextColumn, badge, searchable, sortable.
  - `label`: TextColumn, searchable.
  - `code`: TextColumn, searchable.
  - `sort_order`: TextColumn, sortable.
  - `is_active`: ToggleColumn.
- **Filters**:
  - SelectFilter for `category`.

### 3. AccountResource (Chart of Accounts)
- **Navigation Group**: `'Manajemen Keuangan'`
- **Navigation Icon**: `'heroicon-o-calculator'`
- **Form Schema**:
  - `code`: TextInput, required, unique:ignoreRecord.
  - `name`: TextInput, required.
  - `type`: Select, required, options: Asset, Liability, Net Asset, Revenue, Expense.
  - `restriction_type`: Select, required, options: Tidak Terikat, Terikat Temporer, Terikat Permanen.
  - `parent_id`: Select, relationship parent.name, searchable, preload.
  - `is_active`: Toggle, default true.
- **Table Schema**:
  - `code`: TextColumn, searchable, sortable.
  - `name`: TextColumn, searchable.
  - `type`: TextColumn, badge, sortable.
  - `restriction_type`: TextColumn, badge.
  - `parent.name`: TextColumn.
  - `is_active`: ToggleColumn.
- **Filters**:
  - SelectFilters for `type` and `restriction_type`.
- **Actions**:
  - DeleteAction: intercept and halt with a user-friendly danger notification if the account has any journal items.

---

## Verification Plan

### Automated Tests
- Run `php artisan make:filament-resource` for the three models.
- Customize the forms and tables and check compilation.

### Manual Verification
- Access the admin dashboard.
- Verify that the three resources appear in the sidebar under their respective groups.
- Perform CRUD operations for Rayons, Kamus Data, and Chart of Accounts.
- Verify that deleting an account that has journal items is successfully prevented with a notification.
