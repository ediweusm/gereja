<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JournalPrintController;
use App\Http\Controllers\FamilyPrintController;
use App\Http\Controllers\PastoralReportController;

Route::get('/', function () {
    // 1. Ambil jadwal dalam rentang minggu ini (Senin s/d Minggu)
    $events = \App\Models\Event::whereBetween('event_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->orderBy('event_date', 'asc')
                ->orderBy('start_time', 'asc')
                ->get();

    $agendas = \App\Models\Agenda::whereNotIn('status', ['completed', 'canceled'])
                ->where('start_time', '>=', now())
                ->orderBy('start_time', 'asc')
                ->take(3)
                ->get();

    // 2. Ambil 3 berita terbaru yang berstatus 'published'
    $posts = \App\Models\Post::with('category') // Eager loading relasi kategori
                ->where('status', 'published')
                ->orderBy('published_at', 'desc')
                ->take(3)
                ->get();
    // 3. Ambil semua data Pendeta yang aktif (BARU)
    $pastors = \App\Models\Pastor::where('is_active', true)->get();

    // 4. Ambil gambar slider yang aktif
    $sliders = \App\Models\Slider::where('is_active', true)
                ->orderBy('order', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();

    // --- DATA STATISTIK BARU ---
    
    // Demografi Global
    $totalMembers = \App\Models\Member::where('is_deceased', false)->count();
    $maleMembers = \App\Models\Member::where('is_deceased', false)->where('gender', 'L')->count();
    $femaleMembers = \App\Models\Member::where('is_deceased', false)->where('gender', 'P')->count();

    // Demografi Per Rayon 
    // (Asumsi model Rayon memiliki relasi 'members'. Jika melalui keluarga, sesuaikan relasinya)
    $rayons = \App\Models\Rayon::withCount([
        'members' => fn($q) => $q->where('is_deceased', false),
        'members as male_count' => fn($q) => $q->where('is_deceased', false)->where('gender', 'L'),
        'members as female_count' => fn($q) => $q->where('is_deceased', false)->where('gender', 'P'),
    ])->get();
    
    $totalRayons = $rayons->count();

    // Total Diakonia / Bantuan (Tahun Ini)
    $bantuanYear = \App\Models\JournalItem::query()
        ->join('journals', 'journal_items.journal_id', '=', 'journals.id')
        ->join('accounts', 'journal_items.account_id', '=', 'accounts.id')
        ->where('accounts.type', 'Expense')
        ->whereBetween('journals.transaction_date', [
            now()->startOfYear()->toDateString(),
            now()->endOfYear()->toDateString()
        ])
        ->where(function ($q) {
            $q->where('accounts.name', 'like', '%Bantuan%')
              ->orWhere('accounts.name', 'like', '%Diakonia%')
              ->orWhere('accounts.name', 'like', '%Diakona%');
        })
        ->selectRaw('SUM(journal_items.debit) - SUM(journal_items.credit) as balance')
        ->value('balance') ?? 0.0;
            
    // Kirim semua variabel ke view welcome
    return view('welcome', compact('events', 'agendas', 'posts', 'pastors', 'sliders','totalMembers', 'maleMembers', 'femaleMembers', 
        'rayons', 'totalRayons', 'bantuanYear'
    ));
});

// Rute untuk Indeks Berita (Daftar semua artikel)
Route::get('/berita', function () {
    // Kita gunakan paginate(9) agar pas dengan desain grid 3 kolom
    $posts = \App\Models\Post::with('category')
                ->where('status', 'published')
                ->orderBy('published_at', 'desc')
                ->paginate(9);

    return view('posts.index', compact('posts'));
})->name('posts.index');

// Rute untuk membaca detail berita berdasarkan slug
Route::get('/berita/{post:slug}', function (\App\Models\Post $post) {
    // Pastikan hanya berita yang di-publish yang bisa diakses
    if ($post->status !== 'published') {
        abort(404);
    }
    
    return view('posts.show', compact('post'));
})->name('posts.show');

// Rute untuk Indeks Agenda
Route::get('/agenda', function () {
    // Ambil agenda yang akan datang, urutkan dari yang paling dekat
    $agendas = \App\Models\Agenda::whereNotIn('status', ['completed', 'canceled'])
                ->where('start_time', '>=', now())
                ->orderBy('start_time', 'asc')
                ->paginate(10);

    return view('agendas.index', compact('agendas'));
})->name('agendas.index');

// Rute untuk Indeks Galeri
Route::get('/galeri', function () {
    // Tampilkan album terbaru lebih dulu
    $galleries = \App\Models\Gallery::orderBy('created_at', 'desc')->paginate(9);

    return view('galleries.index', compact('galleries'));
})->name('galleries.index');

// Rute untuk Arsip Khotbah
Route::get('/khotbah', function () {
    $sermons = \App\Models\Sermon::orderBy('sermon_date', 'desc')->paginate(10);
    return view('sermons.index', compact('sermons'));
})->name('sermons.index');


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

Route::get('/admin/reports/journals/print', [JournalPrintController::class, 'printJournalRange'])
    ->name('reports.journal_range')
    ->middleware(['auth']);

Route::get('/reports/assistances',[PastoralReportController::class, 'printAssistancesByRange'])->name('reports.assistances_by_range');

Route::get('/login', function () { return redirect('/admin/login'); })->name('login');
