<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SurveyController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\ResponseController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Survey\TakingController;
use App\Http\Controllers\Survey\SyncController;
use App\Http\Controllers\Api\PsgcController;
use Illuminate\Support\Facades\Route;

// Root redirect
Route::get('/', fn() => redirect('/admin'));

// PSGC location API (public, cached)
Route::prefix('api/psgc')->group(function () {
    Route::get('provinces',            [PsgcController::class, 'provinces']);
    Route::get('cities/{provinceCode}',[PsgcController::class, 'cities']);
    Route::get('barangays/{cityCode}', [PsgcController::class, 'barangays']);
});

// Public survey
Route::get('/s/{token}',       [TakingController::class, 'show'])->name('survey.show');
Route::post('/s/{token}',      [TakingController::class, 'submit'])->name('survey.submit');
Route::get('/s/{token}/done',  [TakingController::class, 'complete'])->name('survey.complete');
Route::post('/s/{token}/sync', [SyncController::class, 'sync'])->name('survey.sync');  // offline sync (CSRF-exempt)

// Auth
Route::get('/login',  [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout',[LoginController::class, 'logout'])->name('logout');

// Admin / Researcher
Route::prefix('admin')->name('admin.')->middleware(['role'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Surveys
    Route::get('surveys',                    [SurveyController::class, 'index'])->name('surveys.index');
    Route::get('surveys/create',             [SurveyController::class, 'create'])->name('surveys.create');
    Route::post('surveys',                   [SurveyController::class, 'store'])->name('surveys.store');
    Route::get('surveys/{survey}',           [SurveyController::class, 'show'])->name('surveys.show');
    Route::get('surveys/{survey}/edit',      [SurveyController::class, 'edit'])->name('surveys.edit');
    Route::put('surveys/{survey}',           [SurveyController::class, 'update'])->name('surveys.update');
    Route::delete('surveys/{survey}',        [SurveyController::class, 'destroy'])->name('surveys.destroy');
    Route::get('surveys/{survey}/builder',   [SurveyController::class, 'builder'])->name('surveys.builder');
    Route::post('surveys/{survey}/activate', [SurveyController::class, 'activate'])->name('surveys.activate');
    Route::post('surveys/{survey}/close',    [SurveyController::class, 'close'])->name('surveys.close');
    Route::post('surveys/{survey}/duplicate',[SurveyController::class, 'duplicate'])->name('surveys.duplicate');

    // Questions (AJAX)
    Route::prefix('surveys/{survey}')->name('surveys.')->group(function () {
        Route::post('questions',                              [QuestionController::class, 'store'])->name('questions.store');
        Route::put('questions/{question}',                    [QuestionController::class, 'update'])->name('questions.update');
        Route::delete('questions/{question}',                 [QuestionController::class, 'destroy'])->name('questions.destroy');
        Route::post('questions/reorder',                      [QuestionController::class, 'reorder'])->name('questions.reorder');
        Route::post('sections',                               [SectionController::class, 'store'])->name('sections.store');
        Route::put('sections/{section}',                      [SectionController::class, 'update'])->name('sections.update');
        Route::delete('sections/{section}',                   [SectionController::class, 'destroy'])->name('sections.destroy');
        Route::post('questions/{question}/options',           [QuestionController::class, 'storeOption'])->name('questions.options.store');
        Route::put('questions/{question}/options/{option}',   [QuestionController::class, 'updateOption'])->name('questions.options.update');
        Route::delete('questions/{question}/options/{option}',[QuestionController::class, 'destroyOption'])->name('questions.options.destroy');
        Route::post('questions/{question}/grid-rows',         [QuestionController::class, 'storeGridRow'])->name('questions.gridrows.store');
        Route::delete('questions/{question}/grid-rows/{gridRow}', [QuestionController::class, 'destroyGridRow'])->name('questions.gridrows.destroy');

        Route::get('responses',                    [ResponseController::class, 'index'])->name('responses.index');
        Route::get('responses/{response}',         [ResponseController::class, 'show'])->name('responses.show');
        Route::get('responses/{response}/edit',    [ResponseController::class, 'edit'])->name('responses.edit');
        Route::put('responses/{response}',         [ResponseController::class, 'update'])->name('responses.update');
        Route::delete('responses/{response}',      [ResponseController::class, 'destroy'])->name('responses.destroy');

        Route::get('export',               [ExportController::class, 'index'])->name('export.index');
        Route::get('export/download',      [ExportController::class, 'download'])->name('export.download');

        Route::get('import',               [ImportController::class, 'index'])->name('import.index');
        Route::post('import',              [ImportController::class, 'store'])->name('import.store');

        Route::get('analytics',            [AnalyticsController::class, 'index'])->name('analytics.index');
    });

    // Users (admin only)
    Route::resource('users', UserController::class)->except(['show'])->middleware('role:admin');

    // Documentation
    Route::get('docs', fn() => view('admin.docs'))->name('docs');
});
