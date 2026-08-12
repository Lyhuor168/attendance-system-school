<?php

use App\Http\Controllers\AttendanceRecordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TimetableController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/attendance', [AttendanceRecordController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/{schoolClass}/record', [AttendanceRecordController::class, 'create'])
        ->middleware('can:record,schoolClass')->name('attendance.create');
    Route::post('/attendance/{schoolClass}', [AttendanceRecordController::class, 'store'])
        ->middleware('can:record,schoolClass')->name('attendance.store');
    Route::get('/attendance/{schoolClass}/{date}', [AttendanceRecordController::class, 'show'])
        ->middleware('can:record,schoolClass')->name('attendance.show');

    Route::middleware('admin')->group(function () {
        Route::resource('teachers', TeacherController::class)->except('show');
        Route::resource('subjects', SubjectController::class)->except('show');
        Route::resource('classes', SchoolClassController::class)->except('show')->parameters(['classes' => 'schoolClass']);
        Route::resource('students', StudentController::class)->except('show');
        Route::resource('timetables', TimetableController::class)->except('show');
    });
});

require __DIR__.'/auth.php';
