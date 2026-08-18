<?php

use App\Http\Controllers\AttendanceRecordController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ClassTeacherController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PaymentController;
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

Route::get('/language/{locale}', [LocaleController::class, 'update'])->name('language.switch');

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

    Route::get('/leave-requests', [LeaveRequestController::class, 'index'])->name('leave-requests.index');
    Route::get('/leave-requests/create', [LeaveRequestController::class, 'create'])->name('leave-requests.create');
    Route::post('/leave-requests', [LeaveRequestController::class, 'store'])->name('leave-requests.store');
    Route::post('/leave-requests/{leaveRequest}/respond', [LeaveRequestController::class, 'respond'])->name('leave-requests.respond');
    Route::delete('/leave-requests/{leaveRequest}', [LeaveRequestController::class, 'destroy'])->name('leave-requests.destroy');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{student}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{student}', [ChatController::class, 'store'])->name('chat.store');

    Route::middleware('admin')->group(function () {
        Route::resource('teachers', TeacherController::class)->except('show');
        Route::resource('subjects', SubjectController::class)->except('show');
        Route::resource('classes', SchoolClassController::class)->except('show')->parameters(['classes' => 'schoolClass']);
        Route::resource('students', StudentController::class)->except('show');
        Route::resource('timetables', TimetableController::class)->except('show');

        Route::get('/classes/{schoolClass}/teachers', [ClassTeacherController::class, 'index'])->name('class-teachers.index');
        Route::post('/classes/{schoolClass}/teachers', [ClassTeacherController::class, 'store'])->name('class-teachers.store');
        Route::delete('/class-teachers/{classTeacher}', [ClassTeacherController::class, 'destroy'])->name('class-teachers.destroy');

        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    });
});

require __DIR__.'/auth.php';
