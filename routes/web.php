<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/home');
});

Auth::routes();

Route::group([
    'middleware' => ['auth']
], function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    
    // Note Routes
    Route::group([
        'as'            => 'note.',
        'prefix'        => 'notes'
    ], function () {
        Route::get('/', [NoteController::class, 'overview'])->name('list');
        Route::get('/create', [NoteController::class, 'create'])->name('create');
        Route::get('/{note_id}', [NoteController::class, 'view'])->name('view');
        Route::post('/{note_id?}', [NoteController::class, 'store'])->name('store');
    });
});
