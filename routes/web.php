<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\HomeController;
Auth::routes(['verify' => true]);

// Todo Routes (يتطلب تسجيل الدخول)
Route::middleware(['auth'])->group(function () {
    Route::get('todos/trashed',[TodoController::class,'trashed'])->name('todos.trashed');


    Route::get('/',  [TodoController::class, 'index'])->name('welcome');
    // عرض المهام
    Route::get('/todos', [TodoController::class, 'index'])->name('todos.index');
    
    // إضافة مهمة جديدة
    Route::post('/todos', [TodoController::class, 'store'])->name('todos.store');
    
    // تحديث المهمة
    Route::put('/todos/{id}', [TodoController::class, 'update'])->name('todos.update');
    
    // حذف المهمة
    Route::delete('/todos/{id}', [TodoController::class, 'destroy'])->name('todos.destroy');
    
    // تغيير حالة إتمام المهمة
    Route::patch('/todos/{id}/complete', [TodoController::class, 'toggleComplete'])->name('todos.complete');
    
    // عرض مهمة معينة
    Route::get('/todos/{id}', [TodoController::class, 'show'])->name('todos.show');
    
    // Route::patch('/todos/{todo}/restore', [TodoController::class, 'restore'])->name('todos.restore');
    // Route::delete('/todos/{todo}/force-delete', [TodoController::class, 'forceDelete'])->name('todos.force-delete');


});


Route::PATCH('/todos/{id}/restore', [TodoController::class, 'restore'])->name('todos.restore');
Route::delete('/todos/{id}/force-delete', action: [TodoController::class, 'forceDelete'])->name('todos.forceDelete');
Route::get('/home', [HomeController::class, 'index'])->name('home');