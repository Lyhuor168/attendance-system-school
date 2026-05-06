<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\PermissionController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])
        ->name('admin.dashboard');
    Route::get('/admin/users', [AdminController::class, 'users'])
        ->name('admin.users');
    Route::get('/admin/classes', [AdminController::class, 'classes'])
        ->name('admin.classes');
    Route::post('/admin/classes', [AdminController::class, 'storeClass'])
        ->name('admin.classes.store');
    Route::put('/admin/classes/{id}', [AdminController::class, 'updateClass'])
        ->name('admin.classes.update');
    Route::delete('/admin/classes/{id}', [AdminController::class, 'destroyClass'])
        ->name('admin.classes.destroy');
    Route::get('/admin/report', [AdminController::class, 'report'])
        ->name('admin.report');
});


Route::middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('/teacher/dashboard', [TeacherController::class, 'index'])
        ->name('teacher.dashboard');
    Route::post('/teacher/attendance', [TeacherController::class, 'store'])
        ->name('teacher.attendance.store');
    Route::get('/teacher/edit', [TeacherController::class, 'edit'])
        ->name('teacher.edit');
    Route::put('/teacher/attendance/{id}', [TeacherController::class, 'update'])
        ->name('teacher.attendance.update');
    Route::get('/teacher/report', [TeacherController::class, 'report'])
        ->name('teacher.report');
    Route::get('/teacher/students/{class_id}', [TeacherController::class, 'getStudents'])
        ->name('teacher.students');
});


Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/student/dashboard', [StudentController::class, 'index'])
        ->name('student.dashboard');
});


// Student Management
Route::get('/admin/students', [AdminController::class, 'students'])
    ->name('admin.students');
Route::post('/admin/students', [AdminController::class, 'storeStudent'])
    ->name('admin.students.store');
Route::put('/admin/students/{id}', [AdminController::class, 'updateStudent'])
    ->name('admin.students.update');
Route::delete('/admin/students/{id}', [AdminController::class, 'destroyStudent'])
    ->name('admin.students.destroy');
    Route::get('/student/permissions', [PermissionController::class, 'index'])
    ->name('student.permissions');
Route::post('/student/permissions', [PermissionController::class, 'store'])
    ->name('student.permissions.store');

    Route::get('/teacher/permissions', [PermissionController::class, 'teacherIndex'])
    ->name('teacher.permissions');
Route::put('/teacher/permissions/{id}', [PermissionController::class, 'update'])
    ->name('teacher.permissions.update');

    Route::get('/student/profile', [StudentController::class, 'profile'])
    ->name('student.profile');
Route::put('/student/profile', [StudentController::class, 'updateProfile'])
    ->name('student.profile.update');