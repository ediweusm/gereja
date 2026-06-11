# Implementation Plan: Penyaluran Diakonia (Kas Bantuan)

Implement the `MemberAssistance` tracking feature, enabling recording of help/aid payments to church members (Diakonia). The system will automatically generate a double-entry cash out journal and a professional receipt.

## Proposed Changes

### 1. Database Model & Migration
#### [NEW] [create_member_assistances_table.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/database/migrations/2026_06_07_000001_create_member_assistances_table.php)
- Columns:
  - `id` (primary key)
  - `journal_id` (foreignId to `journals`, constrained, cascadeOnDelete)
  - `member_id` (foreignId to `members`, constrained, setNullOnDelete, nullable)
  - `amount` (decimal: 15, 2)
  - `timestamps`

#### [NEW] [MemberAssistance.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Models/MemberAssistance.php)
- Model properties:
  - `$guarded = ['id']`
  - Relation: `journal()` (BelongsTo `Journal`)
  - Relation: `member()` (BelongsTo `Member`)
  - Casts: `amount` to `decimal:2`

### 2. Filament Resource
#### [NEW] [MemberAssistanceResource.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/MemberAssistanceResource.php)
- **Navigation**:
  - Group: `'Manajemen Keuangan'`
  - Icon: `'heroicon-o-heart'`
  - Label / Plural Label: `'Penyaluran Diakonia'`
- **Form Schema**:
  - `transaction_date` (DatePicker, default: today, required, unmapped)
  - `member_id` (Select, relationship to `member` with search columns `first_name`, `middle_name`, `last_name`, label from record using `fullName`)
  - `expense_account_id` (Select, unmapped, label `'Beban Diakonia (Debit)'`, filtered query code starting with `412%` and has no children)
  - `cash_account_id` (Select, unmapped, label `'Sumber Dana Kas/Bank (Kredit)'`, filtered query transaksional active assets/cash accounts starting with `1%` or cash)
  - `amount` (TextInput, numeric, prefix `'Rp'`, required)
  - `description` (Textarea, unmapped, label `'Keterangan/Tujuan Bantuan'`, required)
- **Table Schema**:
  - Show: `member.fullName`, `amount` (currency formatted), `journal.transaction_number`, `created_at`.
  - Add print action in table row: `Cetak Nota` pointing to GET route `diakonia.receipt` (red/danger color).

#### [NEW] Pages under MemberAssistanceResource:
- `ListMemberAssistances.php`
- `CreateMemberAssistance.php`
- `EditMemberAssistance.php`

### 3. Form Record Creation Override
#### [MODIFY] [CreateMemberAssistance.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/MemberAssistanceResource/Pages/CreateMemberAssistance.php)
- Override `handleRecordCreation(array $data): Model`.
- Wrap in a `DB::transaction()` closure:
  1. Create a `Journal` entry:
     - `transaction_date` = `$data['transaction_date']`
     - `description` = `$data['description']`
     - `reference_number` = `'DIAKONIA-' . now()->format('YmdHis')`
  2. Create a DEBIT `JournalItem` for the diakonia expense account:
     - `account_id` = `$data['expense_account_id']`
     - `debit` = `$data['amount']`
     - `credit` = `0`
  3. Create a KREDIT `JournalItem` for the cash account:
     - `account_id` = `$data['cash_account_id']`
     - `debit` = `0`
     - `credit` = `$data['amount']`
  4. Create and return `MemberAssistance`:
     - `journal_id` = `$journal->id`
     - `member_id` = `$data['member_id']`
     - `amount` = `$data['amount']`

### 4. Print Controller & Route
#### [MODIFY] [JournalPrintController.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Http/Controllers/JournalPrintController.php)
- Add method `printDiakoniaReceipt(MemberAssistance $assistance)`.
- Eager load: `member`, `journal.items.account`.
- Fetch `ChurchProfile::first()`.
- Return view `reports.diakonia-receipt`.

#### [MODIFY] [web.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/routes/web.php)
- Register GET route `/admin/assistances/{assistance}/receipt` naming it `diakonia.receipt` pointing to `JournalPrintController@printDiakoniaReceipt` with `auth` middleware.

### 5. Receipt Template (Blade)
#### [NEW] [diakonia-receipt.blade.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/resources/views/reports/diakonia-receipt.blade.php)
- Classic A5 landscape cash out receipt layout.
- Letterhead (Kop) using dynamics from `ChurchProfile`.
- Title: "BUKTI PENYALURAN DIAKONIA".
- Fields:
  - Dibayarkan Kepada: [Nama Jemaat (Member Full Name)]
  - Sebesar: [Nominal formatted as Rupiah + Terbilang text]
  - Untuk Keperluan: [Keterangan Jurnal]
  - Sumber Dana: [Nama Akun Kas yang dikredit (JournalItem Credit name)]
  - Tanggal: [Transaction Date]
- Signatures:
  - Penerima Bantuan (Member name)
  - Mengetahui (Ketua Majelis/Diaken)
  - Bendahara yang menyerahkan

---

## Verification Plan

### Automated Tests
- Run `wsl php artisan migrate` to verify the table creation.

### Manual Verification
- Log in as admin, navigate to **Penyaluran Diakonia**.
- Click **Buat Penyaluran Diakonia** and fill in the form: select a member, expense account (e.g. Sumbangan Kesehatan), cash account, amount, and description.
- Click save. Confirm that:
  - The record is saved.
  - A corresponding Jurnal entry is generated with correct DEBIT/KREDIT double-entry balances in the `journals` list.
- Click **Cetak Nota** on the assistance table list. Confirm that:
  - The printable A5 landscape layout appears correctly.
  - Dynamic kop logo, text, member name, rupiah format, terbilang text, and signatures are fully visible.
