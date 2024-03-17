<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\ProcessController;
Route::get('/process', [ProcessController::class, 'index'])->name('process.index');

Route::get('/process/create', [ProcessController::class, 'create'])->name('process.create');
Route::post('/process', [ProcessController::class, 'store'])->name('process.store');


//Route::get('/process/{id}/edit', [ProcessController::class, 'edit'])->name('process.edit');
//
//Route::put('/process/{id}', [ProcessController::class, 'update'])->name('process.update');


/*Route::get('/processes/{id}/edit', [ProcessController::class, 'edit']);
Route::put('/processes/{id}', [ProcessController::class, 'update']);*/


// Edit process form route
Route::get('/processes/{id}/edit', [ProcessController::class, 'edit'])->name('processes.edit');

// Update process route
Route::put('/processes/{id}', [ProcessController::class, 'update'])->name('processes.update');

Route::delete('/processes/{id}', [ProcessController::class, 'destroy']);









