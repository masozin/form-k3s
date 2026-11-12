<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FormBuilderController;
use App\Http\Controllers\FormPublicController;
use App\Http\Controllers\FormSubmissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SchoolController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/schools/{school}/edit', [SchoolController::class, 'edit'])->name('schools.edit');
    Route::put('/schools/{school}', [SchoolController::class, 'update'])->name('schools.update');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::resource('forms', FormBuilderController::class)->except(['show']);
        Route::put('/forms/{form}/structure', [FormBuilderController::class, 'updateStructure'])
            ->name('forms.structure.update');

        Route::get('/forms/{form}/submissions/export', [FormSubmissionController::class, 'export'])
            ->name('forms.submissions.export');
        Route::get('/forms/{form}/submissions/export-files', [FormSubmissionController::class, 'exportFiles'])
            ->name('forms.submissions.export-files');
        Route::get('/forms/{form}/submissions', [FormSubmissionController::class, 'index'])
            ->name('forms.submissions.index');
        Route::get('/forms/{form}/submissions/{submission}', [FormSubmissionController::class, 'show'])
            ->name('forms.submissions.show');
    });
});

// ---- Form Publik ----
Route::get('/form/{form:slug}', [FormPublicController::class, 'show'])->name('form.public.show');
Route::get('/form/{form:slug}/isi', [FormPublicController::class, 'create'])->name('form.public.create');
Route::post('/form/{form:slug}', [FormPublicController::class, 'store'])->name('form.public.store');

Route::scopeBindings()->group(function () {
    // User login
    Route::get('/form/{form:slug}/jawaban/{submission}', [FormPublicController::class, 'edit'])
        ->name('form.public.edit');
    Route::put('/form/{form:slug}/jawaban/{submission}', [FormPublicController::class, 'update'])
        ->name('form.public.update');
    Route::get('/form/{form:slug}/jawaban/{submission}/lihat', [FormPublicController::class, 'review'])
        ->middleware('auth')
        ->name('form.public.review');

    // Guest (signed URL)
    Route::middleware('signed')->group(function () {
        Route::get('/form/{form:slug}/jawaban/{submission}/tamu', [FormPublicController::class, 'editGuest'])
            ->name('form.public.edit.guest');
        Route::put('/form/{form:slug}/jawaban/{submission}/tamu', [FormPublicController::class, 'updateGuest'])
            ->name('form.public.update.guest');
        Route::get('/form/{form:slug}/jawaban/{submission}/tamu/lihat', [FormPublicController::class, 'reviewGuest'])
            ->name('form.public.review.guest');
    });
});

require __DIR__ . '/auth.php';
