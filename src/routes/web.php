<?php

use Agenciafmd\Pages\Http\Controllers\PageController;
use Agenciafmd\Pages\Models\Page;

Route::get('pages', [PageController::class, 'index'])
    ->name('admix.pages.index')
    ->middleware('can:view,' . Page::class);
Route::get('pages/trash', [PageController::class, 'index'])
    ->name('admix.pages.trash')
    ->middleware('can:restore,' . Page::class);
Route::get('pages/create', [PageController::class, 'create'])
    ->name('admix.pages.create')
    ->middleware('can:create,' . Page::class);
Route::post('pages', [PageController::class, 'store'])
    ->name('admix.pages.store')
    ->middleware('can:create,' . Page::class);
Route::get('pages/{page}/edit', [PageController::class, 'edit'])
    ->name('admix.pages.edit')
    ->middleware('can:update,' . Page::class);
Route::put('pages/{page}', [PageController::class, 'update'])
    ->name('admix.pages.update')
    ->middleware('can:update,' . Page::class);
Route::delete('pages/destroy/{page}', [PageController::class, 'destroy'])
    ->name('admix.pages.destroy')
    ->middleware('can:delete,' . Page::class);
Route::post('pages/{id}/restore', [PageController::class, 'restore'])
    ->name('admix.pages.restore')
    ->middleware('can:restore,' . Page::class);
Route::post('pages/batchDestroy', [PageController::class, 'batchDestroy'])
    ->name('admix.pages.batchDestroy')
    ->middleware('can:delete,' . Page::class);
Route::post('pages/batchRestore', [PageController::class, 'batchRestore'])
    ->name('admix.pages.batchRestore')
    ->middleware('can:restore,' . Page::class);
