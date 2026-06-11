# Implementation Plan: Cetak Kwitansi (Print layperson-friendly Receipt)

Implement an A5 landscape receipt (Kwitansi) system for Journal records, designed to be layperson-friendly with clear cash direction indicators and a visual account hierarchy.

## Proposed Changes

### 1. Blade Template
#### [NEW] [kwitansi.blade.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/resources/views/reports/kwitansi.blade.php)
- Layout a clean A5 Landscape receipt structure with CSS:
  ```css
  @page {
      size: A5 landscape;
      margin: 10mm;
  }
  ```
- **Transaction Type Identification**:
  - Check if any item's account code starts with `111` (Cash/Bank) at DEBIT position:
    - YES: Title is `"BUKTI PENERIMAAN KAS"`, labels use `"Diterima Dari"`, theme is green.
    - NO: Title is `"BUKTI PENGELUARAN KAS"`, labels use `"Dibayarkan Kepada"`, theme is red/amber.
- **Visual Account Hierarchy**:
  - **For Penerimaan (Receipt)**:
    - Iterates over items. CREDIT items (representing the revenue accounts / what the money is for) are displayed with a **large, bold font** (Account Code, Name, Amount).
    - DEBIT items (representing Cash/Bank accounts / where the money goes) are displayed with a **smaller, lighter font** below.
  - **For Pengeluaran (Disbursement)**:
    - Iterates over items. DEBIT items (representing expense accounts / what was paid for) are displayed with a **large, bold font** (Account Code, Name, Amount).
    - CREDIT items (representing Cash/Bank accounts / where the money came from) are displayed with a **smaller, lighter font** below.
- **Terbilang conversion**:
  - Add an inline PHP helper `terbilang($amount)` to display the total amount in Indonesian words (e.g., `"Seratus Ribu Rupiah"`).
- **Footer signature area**:
  - Columns for "Penerima" (Receiver) and "Penyerah Uang" (Giver) at the bottom.

### 2. Print Controller
#### [MODIFY] [JournalPrintController.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Http/Controllers/JournalPrintController.php)
- Add `printKwitansi(Journal $journal)` method to render the `reports.kwitansi` view with eager loaded relations.

### 3. Web Routes
#### [MODIFY] [web.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/routes/web.php)
- Register `GET /admin/journals/{journal}/kwitansi` route named `journal.kwitansi` with the `auth` middleware.

### 4. Filament Actions
#### [MODIFY] [JournalResource.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/JournalResource.php)
- Add a table action in `$table->actions()`:
  - Label: `'Cetak Kwitansi'`
  - Icon: `'heroicon-o-ticket'`
  - Color: `'warning'`
  - URL pointing to `journal.kwitansi`.

#### [MODIFY] [EditJournal.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/JournalResource/Pages/EditJournal.php)
- Add a header page action in `getHeaderActions()` same as above.

---

## Verification Plan

### Automated Tests
- Verify compilation and route lists.

### Manual Verification
- Access Jurnal Umum in the Filament admin panel.
- Verify that a yellow ticket icon labeled **Cetak Kwitansi** is shown on each row and on the Edit page.
- Click **Cetak Kwitansi** on a receipt transaction (e.g., JRN-202606-0001). Verify:
  - Title: "BUKTI PENERIMAAN KAS"
  - Large font: Revenue account (`311101 - Tangguk 1 (Rutin)`)
  - Small font: Cash account (`111120 - Brankas`)
  - Terbilang matches amount.
- Click **Cetak Kwitansi** on an expense transaction (e.g., JRN-202606-0003). Verify:
  - Title: "BUKTI PENGELUARAN KAS"
  - Large font: Expense account (`421100 - Honorarium Pendeta`)
  - Small font: Bank account (`111210 - Bank Mandiri Rek. No.`)
