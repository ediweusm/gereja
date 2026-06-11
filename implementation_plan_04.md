# Eloquent Models & Relations Setup for `sig`

Create and update Eloquent Models for the Congregation Management & Church Accounting System (SAK ETAP) in [app/Models](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Models).

## General Requirements
1. **Guarded Attributes**: Use `protected $guarded = ['id'];` on all models.
2. **Factories**: Implement the `Illuminate\Database\Eloquent\Factories\HasFactory` trait.
3. **Relation Return Types**: Explicitly define return types on all relations (e.g., `BelongsTo`, `HasMany`).

---

## Proposed Changes

We will create/modify the following models:

### 1. Master Data Models

#### [NEW] [Rayon.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Models/Rayon.php)
- **Relationships**:
  - `families()`: `HasMany` to `Family`.

#### [NEW] [DataDictionary.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Models/DataDictionary.php)
- **Scopes**:
  - `scopeActive($query)`: filter by `is_active = true`.
  - `scopeCategory($query, $category)`: filter by `category = $category`.
- **Casts**:
  - `is_active`: `boolean`.

---

### 2. Congregation Module Models

#### [NEW] [Family.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Models/Family.php)
- **Relationships**:
  - `rayon()`: `BelongsTo` to `Rayon`.
  - `houseCategory()`: `BelongsTo` to `DataDictionary`.
  - `houseStatus()`: `BelongsTo` to `DataDictionary`.
  - `members()`: `HasMany` to `Member`.

#### [NEW] [Member.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Models/Member.php)
- **Relationships**:
  - `family()`: `BelongsTo` to `Family`.
  - `familyPosition()`: `BelongsTo` to `DataDictionary`.
  - `maritalStatus()`: `BelongsTo` to `DataDictionary`.
  - `education()`: `BelongsTo` to `DataDictionary`.
  - `occupation()`: `BelongsTo` to `DataDictionary`.
  - `churchRole()`: `BelongsTo` to `DataDictionary`.
  - `membershipStatus()`: `BelongsTo` to `DataDictionary`.
  - `mutations()`: `HasMany` to `MemberMutation`.
  - `contributions()`: `HasMany` to `MemberContribution`.
- **Casts**:
  - `birth_date`, `baptism_date`, `sidi_date`, `marriage_date`, `death_date`: `date`.
  - `status_baptis`, `is_deceased`: `boolean`.
  - `income`: `decimal:2`.
- **Accessors**:
  - `fullName()` (returns formatted full name by concatenating `first_name`, `middle_name`, and `last_name`).

#### [NEW] [MemberMutation.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Models/MemberMutation.php)
- **Relationships**:
  - `member()`: `BelongsTo` to `Member`.
  - `oldRayon()`: `BelongsTo` to `Rayon`.
  - `newRayon()`: `BelongsTo` to `Rayon`.
- **Casts**:
  - `mutation_date`: `date`.

---

### 3. Financial Module Models

#### [MODIFY] [Account.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Models/Account.php)
- **Relationships**:
  - `parent()`: `BelongsTo` to `Account` (self-referencing).
  - `children()`: `HasMany` to `Account` (self-referencing).
  - `journalItems()`: `HasMany` to `JournalItem`.
- **Scopes**:
  - `scopeActive($query)`: filter by `is_active = true`.
- **Casts**:
  - `is_active`: `boolean`.
- **Accessors**:
  - `fullName()` (returns formatted string e.g., `"code - name"`).

#### [NEW] [Journal.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Models/Journal.php)
- **Relationships**:
  - `createdBy()`: `BelongsTo` to `User`.
  - `items()`: `HasMany` to `JournalItem`.
  - `contributions()`: `HasMany` to `MemberContribution`.
- **Casts**:
  - `transaction_date`: `date`.
  - `is_locked`: `boolean`.
- **Auto-Generate (booted/creating event)**:
  - Logic to generate `transaction_number` as `JRN-YYYYMM-XXXX`, where `XXXX` is a 4-digit sequence number starting from `0001` per month.

#### [NEW] [JournalItem.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Models/JournalItem.php)
- **Relationships**:
  - `journal()`: `BelongsTo` to `Journal`.
  - `account()`: `BelongsTo` to `Account`.
- **Casts**:
  - `debit`, `credit`: `decimal:2`.

#### [NEW] [MemberContribution.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Models/MemberContribution.php)
- **Relationships**:
  - `journal()`: `BelongsTo` to `Journal`.
  - `member()`: `BelongsTo` to `Member`.
  - `contributionType()`: `BelongsTo` to `DataDictionary`.
- **Casts**:
  - `amount`: `decimal:2`.

#### [NEW] [AccountMonthlyBalance.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Models/AccountMonthlyBalance.php)
- **Relationships**:
  - `account()`: `BelongsTo` to `Account`.
- **Casts**:
  - `beginning_balance`, `debit_mutation`, `credit_mutation`, `ending_balance`: `decimal:2`.

---

## Verification Plan

### Automated Tests
- We will write a test script in [DatabaseSeeder.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/database/seeders/DatabaseSeeder.php) or run a tinker command to verify that all relations can be loaded without exceptions.
- Verify `Journal` creating observer generates the correct format (`JRN-YYYYMM-0001`).
- Run a clean migration and database seeding.
