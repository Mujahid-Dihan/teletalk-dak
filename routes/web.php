<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DakController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Exports\AuditLogExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// Group all Dak-related routes inside the 'auth' middleware so only logged-in users can access them
Route::middleware(['auth', 'verified'])->group(function () {
    
    // The main Dashboard
    Route::get('/dashboard', [DakController::class, 'index'])->name('dashboard');
    
    // Creating a new file
    Route::post('/dak/store', [DakController::class, 'store'])->name('dak.store');
    
    // The AJAX endpoint for the barcode scanner
    Route::get('/dak/search', [DakController::class, 'search'])->name('dak.search');
    
    // Archiving the file
    Route::patch('/dak/{id}/archive', [DakController::class, 'archive'])->name('dak.archive');
    
    Route::post('/dak/{id}/forward', [DakController::class, 'forward'])->name('dak.forward');

    // Uploading Scanned PDF
    Route::post('/dak/{id}/upload-pdf', [DakController::class, 'uploadPdf'])->name('dak.upload_pdf');

    // Admin Routes
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::patch('/admin/users/{user}/approve', [UserController::class, 'approveUser'])->name('admin.users.approve');
    
    // Export Audit Log
    Route::get('/admin/export-audit', function () {
        return Excel::download(new AuditLogExport, 'teletalk_audit_log_'.now()->format('Y-m-d').'.xlsx');
    })->name('admin.export.audit');

    // PDF Reports
    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
