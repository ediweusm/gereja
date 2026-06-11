# Create Database Migrations for `sig` Application

Create 10 Laravel migration files for the core modules of the church geographic information system (`sig`):
1. Master Data & Settings (`rayons`, `data_dictionaries`)
2. Congregation Administration (`families`, `members`, `member_mutations`)
3. Financial / Accounting (`accounts`, `journals`, `journal_items`, `member_contributions`, `account_monthly_balances`)

## Proposed Changes

We will create new migration files in [database/migrations](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/database/migrations).

### Creation Order & Schema Details

To prevent foreign key constraints failures, we must create the tables in the following order:

1. **`rayons`**:
   - `id`: primary key
   - `name`: string (100)
   - `description`: text, nullable
   - `timestamps`

2. **`data_dictionaries`**:
   - `id`: primary key
   - `category`: string (50)
   - `label`: string (100)
   - `code`: string (50), nullable
   - `sort_order`: integer, default 0
   - `is_active`: boolean, default true
   - `timestamps`
   - Index: `(category, is_active)`

3. **`families`**:
   - `id`: primary key
   - `family_number`: string (50), unique
   - `rayon_id`: foreign key (nullable, references `rayons.id`, onDelete set null)
   - `address`: text
   - `phone`: string (20), nullable
   - `house_category_id`: foreign key (nullable, references `data_dictionaries.id`, onDelete set null)
   - `house_status_id`: foreign key (nullable, references `data_dictionaries.id`, onDelete set null)
   - `notes`: text, nullable
   - `timestamps`
   - Index: `(family_number)`

4. **`members`**:
   - `id`: primary key
   - `family_id`: foreign key (references `families.id`, onDelete cascade)
   - `first_name`: string (100)
   - `middle_name`: string (100), nullable
   - `last_name`: string (100), nullable
   - `nik`: string (20), unique, nullable
   - `phone`: string (20), nullable
   - `birth_place`: string (100), nullable
   - `birth_date`: date, nullable
   - `gender`: enum ('L', 'P')
   - `family_position_id`: foreign key (nullable, references `data_dictionaries.id`, onDelete set null)
   - `marital_status_id`: foreign key (nullable, references `data_dictionaries.id`, onDelete set null)
   - `father_name`: string (100), nullable
   - `mother_name`: string (100), nullable
   - `education_id`: foreign key (nullable, references `data_dictionaries.id`, onDelete set null)
   - `occupation_id`: foreign key (nullable, references `data_dictionaries.id`, onDelete set null)
   - `income`: decimal (15, 2), default 0.00
   - `church_role_id`: foreign key (nullable, references `data_dictionaries.id`, onDelete set null)
   - `membership_status_id`: foreign key (nullable, references `data_dictionaries.id`, onDelete set null)
   - Sacrament Baptis: `status_baptis` (boolean, default false), `baptism_date` (date, nullable), `baptism_church` (string 150, nullable), `baptism_pastor` (string 100, nullable), witnesses...
   - Sacrament Sidi: `sidi_date` (date, nullable), `sidi_church` (string 150, nullable), `sidi_pastor` (string 100, nullable)
   - Sacrament Marriage: `marriage_date` (date, nullable), `marriage_church` (string 150, nullable), `marriage_pastor` (string 100, nullable), witnesses...
   - Death Data: `is_deceased` (boolean, default false), `death_date` (date, nullable)
   - `timestamps`
   - Index: `(first_name, last_name)`

5. **`member_mutations`**:
   - `id`: primary key
   - `member_id`: foreign key (references `members.id`, onDelete cascade)
   - `mutation_type`: enum ('Atestasi Masuk', 'Atestasi Keluar', 'Pindah Rayon', 'Titipan', 'Lainnya')
   - `mutation_date`: date
   - `origin_church`: string (150), nullable
   - `destination_church`: string (150), nullable
   - `old_rayon_id`: foreign key (nullable, references `rayons.id`, onDelete set null)
   - `new_rayon_id`: foreign key (nullable, references `rayons.id`, onDelete set null)
   - `reason`: text, nullable
   - `document_number`: string (100), nullable
   - `timestamps`

6. **`accounts`**:
   - `id`: primary key
   - `code`: string (20), unique
   - `name`: string (150)
   - `type`: enum ('Asset', 'Liability', 'Net Asset', 'Revenue', 'Expense')
   - `restriction_type`: enum ('Tidak Terikat', 'Terikat Temporer', 'Terikat Permanen'), default 'Tidak Terikat'
   - `parent_id`: foreign key (nullable, references `accounts.id`, onDelete set null)
   - `is_active`: boolean, default true
   - `timestamps`
   - Index: `(code)`

7. **`journals`**:
   - `id`: primary key
   - `transaction_number`: string (50), unique
   - `transaction_date`: date
   - `description`: text
   - `reference_number`: string (100), nullable
   - `created_by`: foreign key (references `users.id`, onDelete restrict)
   - `is_locked`: boolean, default false
   - `timestamps`
   - Index: `(transaction_date)`, `(transaction_number)`

8. **`journal_items`**:
   - `id`: primary key
   - `journal_id`: foreign key (references `journals.id`, onDelete cascade)
   - `account_id`: foreign key (references `accounts.id`, onDelete restrict)
   - `debit`: decimal (15, 2), default 0.00
   - `credit`: decimal (15, 2), default 0.00
   - `timestamps`
   - Index: `(journal_id, account_id)`

9. **`member_contributions`**:
   - `id`: primary key
   - `journal_id`: foreign key (references `journals.id`, onDelete cascade)
   - `member_id`: foreign key (nullable, references `members.id`, onDelete set null)
   - `contribution_type_id`: foreign key (references `data_dictionaries.id`, onDelete restrict)
   - `amount`: decimal (15, 2)
   - `timestamps`

10. **`account_monthly_balances`**:
    - `id`: primary key
    - `account_id`: foreign key (references `accounts.id`, onDelete cascade)
    - `period_year`: year
    - `period_month`: tinyInteger
    - `beginning_balance`: decimal (15, 2), default 0.00
    - `debit_mutation`: decimal (15, 2), default 0.00
    - `credit_mutation`: decimal (15, 2), default 0.00
    - `ending_balance`: decimal (15, 2), default 0.00
    - `timestamps`
    - Unique Index: `(account_id, period_year, period_month)`

## Verification Plan

### Automated Tests
- Run `php artisan migrate:fresh` to drop and recreate all tables including the newly added tables.
- Check generated schemas using `mysql -u root -proot -e "use sig; show tables;"` and verify the columns.
