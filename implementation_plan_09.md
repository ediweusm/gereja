# Implementation Plan: JournalResource & Double-Entry Validation

Implement `JournalResource` (Jurnal Umum) to record transactions with a double-entry system (Debit = Credit).

## Proposed Changes

### 1. Journal Model
#### [MODIFY] [Journal.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Models/Journal.php)
- Auto-populate `created_by` in `booted()` under the `creating` callback if not already set (fallback to `1` if CLI/seeder).
- Add `total_nominal` accessor (`getTotalNominalAttribute()`) to compute the sum of debits of associated items.

### 2. Journal Resource
#### [NEW] [JournalResource.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/JournalResource.php)
- **Navigation Group**: `'Manajemen Keuangan'`
- **Navigation Icon**: `'heroicon-o-document-currency-dollar'`
- **Navigation Label**: `'Jurnal Umum'`
- **Model Labels**: `getModelLabel()` $\rightarrow$ `'Jurnal'`, `getPluralModelLabel()` $\rightarrow$ `'Jurnal'`
- **Form Schema**:
  - Divided into two Sections:
    - **Section 1: Header Jurnal**
      - `transaction_date` (DatePicker, required, default: today, label: `'Tanggal Transaksi'`)
      - `reference_number` (TextInput, nullable, label: `'Nomor Bukti/Nota'`)
      - `description` (Textarea, required, label: `'Keterangan Transaksi'`, full width)
    - **Section 2: Detail Transaksi / Double-Entry**
      - `items` (Repeater, relationship `'items'`, `minItems(2)`, `columns(3)`)
        - `account_id` (Select, relationship to active transactional accounts, searchable, preload, label: `'Akun / Rekening'`)
        - `debit` (TextInput, numeric, default 0, required, label: `'Debit'`)
        - `credit` (TextInput, numeric, default 0, required, label: `'Kredit'`)
- **Balance Validation**:
  - Add a custom validation rule to the `items` repeater ensuring the sum of all `debit` values equals the sum of all `credit` values. Throw a `ValidationException` (or validation fail message) if they are not balanced.
- **Table Schema**:
  - `transaction_number` (TextColumn, searchable, sortable, label: `'Nomor Transaksi'`)
  - `transaction_date` (TextColumn, date, sortable, label: `'Tanggal Transaksi'`)
  - `description` (TextColumn, limit 50, label: `'Keterangan'`)
  - `total_nominal` (TextColumn, state accessor, money formatted, label: `'Total Transaksi'`)
  - Filter: Date range filter (`from` / `until`) for `transaction_date`.

---

## Verification Plan

### Automated Tests
- Save changes, verify compiler compatibility by listing routes.

### Manual Verification
- Access the admin panel.
- Go to **Jurnal Umum**.
- Attempt to create an unbalanced journal entry (e.g. Debit = 10,000, Credit = 5,000). Check if saving is prevented with a clear error message.
- Save a balanced journal entry (e.g. Debit = 10,000, Credit = 10,000) and verify that it compiles and saves successfully, generating the transaction number in the form `JRN-YYYYMM-XXXX`.
- Verify the total nominal is displayed on the index page with Indonesian Rupiah format.
