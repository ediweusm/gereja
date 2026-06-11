# Implementation Plan: Cetak Bukti Transaksi (Print Journal Voucher)

Implement a print-friendly voucher system for Journal records. This allows users to print a transaction slip (Bukti Kas Masuk, Bukti Kas Keluar, or Bukti Memorial) directly from the Filament admin panel.

## Proposed Changes

### 1. Blade Template
#### [NEW] [journal-voucher.blade.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/resources/views/reports/journal-voucher.blade.php)
- Layout a clean, print-friendly HTML/CSS layout (using table grids, clean typography, proper spacing, borders, and page margins).
- Kop Dokumen:
  - Church Name: `GEREJA MASEHI INJILI DI TIMOR (GMIT) JEMAAT SION OEPURA`
- Title logic:
  - **BUKTI KAS MASUK** if there is any debit amount in accounts starting with `111` (Cash/Bank).
  - **BUKTI KAS KELUAR** if there is any credit amount in accounts starting with `111` (Cash/Bank).
  - **BUKTI MEMORIAL** otherwise.
- Transaction Header:
  - Nomor Jurnal, Tanggal Transaksi, Referensi, and Keterangan/Uraian.
- Transaction Items Table:
  - Account Code, Account Name, Debit (IDR format), Credit (IDR format).
- Summary:
  - Total Debit & Credit (perfectly balanced).
- Footer signature area:
  - "Dibuat Oleh: [pembuat]" (auth user / creator name).
  - "Diperiksa Oleh: [Bendahara]" (empty signature line for Bendahara).
  - "Disetujui Oleh: [Ketua Majelis]" (empty signature line for Ketua Majelis Jemaat).

### 2. Print Controller
#### [NEW] [JournalPrintController.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Http/Controllers/JournalPrintController.php)
- Fetch the `Journal` record with eager loaded relationships (`items.account`, `createdBy`).
- Implement a `print(Journal $journal)` method to render `reports.journal-voucher` view.

### 3. Web Routes
#### [MODIFY] [web.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/routes/web.php)
- Register `GET /admin/journals/{journal}/print` route named `journal.print` with the `auth` middleware.

### 4. Filament Actions
#### [MODIFY] [JournalResource.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/JournalResource.php)
- Add a table action in `$table->actions()`:
  - Label: `'Cetak Bukti'`
  - Icon: `'heroicon-o-printer'`
  - Color: `'success'`
  - URL: pointing to the print route, opening in a new tab.

#### [MODIFY] [EditJournal.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/JournalResource/Pages/EditJournal.php)
- Add a header page action in `getHeaderActions()`:
  - Same properties as the table action to allow printing directly from the edit/view page.

---

## Verification Plan

### Automated Tests
- Run `wsl php artisan route:list` to ensure the print route is registered.
- Build/compile check to ensure there are no syntax errors.

### Manual Verification
- Go to **Jurnal Umum** in the admin panel.
- Verify that a green printer icon labeled **Cetak Bukti** is shown on each row and on the Edit page.
- Click the action button on a journal entry. A new browser tab should open displaying the voucher.
- Verify the voucher type is correctly determined (Kas Masuk, Kas Keluar, or Memorial) based on cash/bank codes (`111...`).
- Verify that all data (numbers, dates, currency, description) matches the journal record perfectly.
