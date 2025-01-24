<?php

use Agenciafmd\Pages\Livewire\Pages;
use Illuminate\Support\Facades\Route;

Route::get('/pages', Pages\Page\Index::class)
    ->name('admix.pages.index');
Route::get('/pages/trash', Pages\Page\Index::class)
    ->name('admix.pages.trash');
Route::get('/pages/create', Pages\Page\Component::class)
    ->name('admix.pages.create');
Route::get('/pages/{page}/edit', Pages\Page\Component::class)
    ->name('admix.pages.edit');
