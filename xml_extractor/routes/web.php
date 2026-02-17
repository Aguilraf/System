<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\UploadBatch;
use App\Livewire\BatchList;
use App\Livewire\DataSelector;

Route::get('/', UploadBatch::class);
Route::get('/batches', BatchList::class);
Route::get('/batch/{batch}', DataSelector::class);
