<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JournalPrintController;
use App\Http\Controllers\FamilyPrintController;
use App\Http\Controllers\PastoralReportController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/admin');
    }

    return redirect('/admin/login');
});

Route::get('/admin/journals/{journal}/print', [JournalPrintController::class, 'print'])
    ->name('journal.print')
    ->middleware(['auth']);

Route::get('/admin/journals/{journal}/kwitansi', [JournalPrintController::class, 'printKwitansi'])
    ->name('journal.kwitansi')
    ->middleware(['auth']);

Route::get('/admin/families/{family}/print', [FamilyPrintController::class, 'print'])
    ->name('family.print')
    ->middleware(['auth']);

Route::get('/admin/contributions/{contribution}/receipt', [JournalPrintController::class, 'printContributionReceipt'])
    ->name('contribution.receipt')
    ->middleware(['auth']);

Route::get('/admin/assistances/{assistance}/receipt', [JournalPrintController::class, 'printDiakoniaReceipt'])
    ->name('diakonia.receipt')
    ->middleware(['auth']);

Route::get('/admin/reports/birthdays/print', [PastoralReportController::class, 'printBirthdays'])
    ->name('report.birthdays.print')
    ->middleware(['auth']);

Route::get('/admin/reports/underprivileged/print', [PastoralReportController::class, 'printUnderprivilegedFamilies'])
    ->name('report.underprivileged.print')
    ->middleware(['auth']);


Route::get('/admin/events/print-by-range', [\App\Http\Controllers\WartaPrintController::class, 'printByRange'])->name('events.print_by_range')->middleware(['auth']);

Route::get('/admin/reports/admissions/print', [PastoralReportController::class, 'printAdmissionsByRange'])
    ->name('reports.admissions_by_range')
    ->middleware(['auth']);

Route::get('/admin/reports/mutations/print', [PastoralReportController::class, 'printMutationsByRange'])
    ->name('reports.mutations_by_range')
    ->middleware(['auth']);

Route::get('/admin/reports/members/print', [PastoralReportController::class, 'printMembersList'])
    ->name('reports.members_list')
    ->middleware(['auth']);

Route::get('/login', function () { return redirect('/admin/login'); })->name('login');
