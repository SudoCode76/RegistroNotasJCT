<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\GradeEntryController;
use App\Http\Controllers\GradeReportController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/cursos', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/cursos/crear', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/cursos', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/cursos/{course}/editar', [CourseController::class, 'edit'])->whereNumber('course')->name('courses.edit');
    Route::put('/cursos/{course}', [CourseController::class, 'update'])->whereNumber('course')->name('courses.update');
    Route::delete('/cursos/{course}', [CourseController::class, 'destroy'])->whereNumber('course')->name('courses.destroy');
    Route::get('/cursos/{course}', [CourseController::class, 'show'])->whereNumber('course')->name('courses.show');

    Route::get('/estudiantes', [StudentController::class, 'index'])->name('students.index');
    Route::get('/estudiantes/crear', [StudentController::class, 'create'])->name('students.create');
    Route::post('/estudiantes', [StudentController::class, 'store'])->name('students.store');
    Route::get('/estudiantes/{student}/editar', [StudentController::class, 'edit'])->whereNumber('student')->name('students.edit');
    Route::put('/estudiantes/{student}', [StudentController::class, 'update'])->whereNumber('student')->name('students.update');
    Route::delete('/estudiantes/{student}', [StudentController::class, 'destroy'])->whereNumber('student')->name('students.destroy');

    Route::post('/inscripciones', [EnrollmentController::class, 'store'])->name('enrollments.store');
    Route::delete('/inscripciones/{enrollment}', [EnrollmentController::class, 'destroy'])
        ->whereNumber('enrollment')->name('enrollments.destroy');

    Route::get('/calificaciones', [GradeEntryController::class, 'index'])->name('grades.index');
    Route::post('/calificaciones', [GradeEntryController::class, 'store'])->name('grades.store');
    Route::get('/reprobados', [GradeReportController::class, 'index'])->name('reports.failed');
    Route::get('/centralizador', [GradeReportController::class, 'centralizer'])->name('reports.centralizer');
});
