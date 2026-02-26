<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\FileManagerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/projects/{id}/logs', [ProjectController::class, 'getLogs'])->name('projects.logs');

    // Route untuk Notifikasi (Tambahkan ini)
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    // FILE MANAGER
    Route::prefix('management/file-manager')->name('filemanager.')->group(function () {
        Route::get('/', [FileManagerController::class, 'index'])->name('index');
        Route::get('/edit/{path}', [FileManagerController::class, 'edit'])
        ->name('edit')
        ->where('path', '.*');
        Route::post('/save', [FileManagerController::class, 'save'])->name('save');
    });
});

require __DIR__.'/auth.php';

// User Routes
Route::middleware(['auth','userMiddleware'])->group(function(){

    Route::get('dashboard',[UserController::class,'index'])->name('dashboard');

    // Tambahkan Route untuk Upload Project di sini
    // Untuk memproses pengiriman file (POST /store)
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    // Untuk menghapus project sendiri (Fungsional ID 05)
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

});

// Admin Routes
Route::middleware(['auth','adminMiddleware'])->group(function(){

    Route::get('/admin/dashboard',[AdminController::class,'index'])->name('admin.dashboard');
    
    // Daftar Antrean (Pending)
    Route::get('/admin/projects/pending', [AdminController::class, 'projectsIndex'])->name('admin.projects.index');

    // Project Aktif (Active) 
    Route::get('/admin/projects/active', [AdminController::class, 'activeProjects'])->name('admin.projects.active');

    // Log Aktivitas - INI JUGA DIBUTUHKAN NAVIGASI
    Route::get('/admin/logs', [AdminController::class, 'activityLogs'])->name('admin.logs.index');

    // Daftar User
    Route::get('/admin/users', [AdminController::class, 'userIndex'])->name('admin.users.index');

    // Rute untuk memproses persetujuan & penolakan
    Route::post('/admin/projects/{project}/approve', [ProjectController::class, 'approve'])->name('admin.projects.approve');
    Route::post('/admin/projects/{project}/reject', [ProjectController::class, 'reject'])->name('admin.projects.reject');

    // Aktif dan Non aktif proyek
    Route::post('/admin/projects/{id}/toggle', [ProjectController::class, 'toggleStatus'])->name('admin.projects.toggle');

    // Tombol log error nginx per domain
    Route::get('/admin/projects/{id}/error-log', [ProjectController::class, 'getProjectErrorLog'])->name('projects.error-log');

    // Gunakan rute khusus admin untuk hapus project
    Route::delete('/projects/{id}/force-delete', [AdminController::class, 'destroyProject'])->name('admin.projects.destroy');
    
    // tammbah dan hapus user diadmin
    Route::get('/users', [AdminController::class, 'userIndex'])->name('admin.users.index');
    Route::post('/users', [AdminController::class, 'userStore'])->name('admin.users.store');
    Route::delete('/users/{id}', [AdminController::class, 'userDestroy'])->name('admin.users.destroy');

    // TERMINAL
    Route::get('/admin/terminal', [AdminController::class, 'terminalIndex'])->name('admin.terminal');
    Route::post('/admin/terminal/execute', [AdminController::class, 'terminalExecute'])->name('admin.terminal.execute');

    // BACKUP MANAGER
    Route::get('/admin/backups', [AdminController::class, 'backupIndex'])->name('admin.backups.index');
    Route::get('/admin/backups/download-file/{author}/{filename}', [AdminController::class, 'downloadBackupFile'])->name('admin.backups.downloadFile');
    Route::get('/admin/backups/download-folder/{author}', [AdminController::class, 'downloadBackupFolder'])->name('admin.backups.downloadFolder');
    Route::delete('/admin/backups/file/{author}/{filename}', [AdminController::class, 'destroyBackupFile'])->name('admin.backups.destroyFile');
    Route::delete('/admin/backups/folder/{author}', [AdminController::class, 'destroyBackupFolder'])->name('admin.backups.destroyFolder');
});