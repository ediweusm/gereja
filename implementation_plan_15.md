# Implementation Plan: Penerimaan Kontribusi Jemaat (Perperpuluhan/Syukur)

Implement a user-friendly form for recording church member contributions. This action automatically creates a double-entry journal voucher, and provides a dynamic receipt printing page.

## Proposed Changes

### 1. Filament Resource
#### [NEW] [MemberContributionResource.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/MemberContributionResource.php)
- **Path**: `app/Filament/Resources/MemberContributionResource.php`
- **Navigation**:
  - Group: `'Manajemen Keuangan'`
  - Icon: `'heroicon-o-gift'`
  - Label / Plural Label: `'Penerimaan Jemaat'`
- **Form Schema**:
  - `transaction_date` (DatePicker, default: today, required, `mapped(false)`)
  - `member_id` (Select, relationship to `member` with search columns `first_name`, `middle_name`, `last_name`, label from record using `fullName`)
  - `contribution_type_id` (Select, relationship to `contributionType` filtered where category is `contribution_type` and active)
  - `amount` (TextInput, numeric, prefix `'Rp'`, required)
  - `cash_account_id` (Select, unmapped, label `'Masuk ke Kas/Bank (Debit)'`, filtered for transaksional active accounts)
  - `revenue_account_id` (Select, unmapped, label `'Akun Pendapatan (Kredit)'`, filtered for transaksional active accounts)
  - `description` (Textarea, unmapped, label `'Keterangan untuk Jurnal'`, required)
- **Table Schema**:
  - Show: `member.fullName`, `contributionType.label`, `amount` (currency formatted), `journal.transaction_number`, `created_at`.
  - Add print action in table row: `Cetak Nota` pointing to GET route `contribution.receipt`.

#### [NEW] Pages under MemberContributionResource:
- `ListMemberContributions.php`
- `CreateMemberContribution.php`
- `EditMemberContribution.php`

### 2. Form Record Creation Override
#### [MODIFY] [CreateMemberContribution.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/MemberContributionResource/Pages/CreateMemberContribution.php)
- Override `handleRecordCreation(array $data): Model`.
- Wrap in a `DB::transaction()` closure:
  1. Create a `Journal` entry:
     - `transaction_date` = `$data['transaction_date']`
     - `description` = `$data['description']`
     - `reference_number` = `'KONTRIBUSI-' . now()->format('YmdHis')`
  2. Create a DEBIT `JournalItem` for the cash account:
     - `account_id` = `$data['cash_account_id']`
     - `debit` = `$data['amount']`
     - `credit` = `0`
  3. Create a KREDIT `JournalItem` for the revenue account:
     - `account_id` = `$data['revenue_account_id']`
     - `debit` = `0`
     - `credit` = `$data['amount']`
  4. Create and return `MemberContribution`:
     - `journal_id` = `$journal->id`
     - `member_id` = `$data['member_id']`
     - `contribution_type_id` = `$data['contribution_type_id']`
     - `amount` = `$data['amount']`

### 3. Print Controller & Route
#### [MODIFY] [JournalPrintController.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Http/Controllers/JournalPrintController.php)
- Add method `printContributionReceipt(MemberContribution $contribution)`.
- Eager load: `member`, `contributionType`, `journal`.
- Fetch `ChurchProfile::first()`.
- Return view `reports.contribution-receipt`.

#### [MODIFY] [web.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/routes/web.php)
- Register GET route `/admin/contributions/{contribution}/receipt` naming it `contribution.receipt` pointing to `JournalPrintController@printContributionReceipt` with `auth` middleware.

### 4. Receipt Template (Blade)
#### [NEW] [contribution-receipt.blade.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/resources/views/reports/contribution-receipt.blade.php)
- Classic A5 landscape receipt layout.
- Letterhead (Kop) using dynamics from `ChurchProfile`.
- Title: "TANDA TERIMA PERSEMBAHAN".
- Fields:
  - Telah terima dari: [Nama Jemaat]
  - Sebesar: [Nominal formatted as Rupiah + Terbilang]
  - Untuk pembayaran: [Jenis Kontribusi (e.g. Persepuluhan)] - [Keterangan Jurnal]
  - Tanggal: [Transaction Date]
- Signature box for "Bendahara Jemaat".

---

## Verification Plan

### Automated Tests
- None needed (will verify visually).

### Manual Verification
- Log in as admin, navigate to **Penerimaan Jemaat**.
- Click **Buat Penerimaan Jemaat** and fill in the form: select a member, contribution type (e.g., Persepuluhan), amount, debit cash account, credit revenue account, and transaction date/description.
- Click save. Confirm that:
  - The record is saved.
  - A corresponding Jurnal entry is generated with correct DEBIT/KREDIT double-entry balances in the `journals` list.
- Click **Cetak Nota** on the contribution table list. Confirm that:
  - The printable A5 landscape layout appears correctly.
  - Dynamic kop logo, text, member name, rupiah format, terbilang text, and descriptions are fully visible.
