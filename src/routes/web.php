<?php

use Agenciafmd\Payments\Http\Controllers\PaymentController;
use Agenciafmd\Payments\Models\Payment;

Route::get('payments', [PaymentController::class, 'index'])
    ->name('admix.payments.index')
    ->middleware('can:view,' . Payment::class);
Route::get('payments/trash', [PaymentController::class, 'index'])
    ->name('admix.payments.trash')
    ->middleware('can:restore,' . Payment::class);
Route::get('payments/create', [PaymentController::class, 'create'])
    ->name('admix.payments.create')
    ->middleware('can:create,' . Payment::class);
Route::post('payments', [PaymentController::class, 'store'])
    ->name('admix.payments.store')
    ->middleware('can:create,' . Payment::class);
Route::get('payments/{payment}/edit', [PaymentController::class, 'edit'])
    ->name('admix.payments.edit')
    ->middleware('can:update,' . Payment::class);
Route::put('payments/{payment}', [PaymentController::class, 'update'])
    ->name('admix.payments.update')
    ->middleware('can:update,' . Payment::class);
Route::delete('payments/destroy/{payment}', [PaymentController::class, 'destroy'])
    ->name('admix.payments.destroy')
    ->middleware('can:delete,' . Payment::class);
Route::post('payments/{id}/restore', [PaymentController::class, 'restore'])
    ->name('admix.payments.restore')
    ->middleware('can:restore,' . Payment::class);
Route::post('payments/batchDestroy', [PaymentController::class, 'batchDestroy'])
    ->name('admix.payments.batchDestroy')
    ->middleware('can:delete,' . Payment::class);
Route::post('payments/batchRestore', [PaymentController::class, 'batchRestore'])
    ->name('admix.payments.batchRestore')
    ->middleware('can:restore,' . Payment::class);
