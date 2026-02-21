<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\UploadBatch;
use App\Livewire\BatchList;
use App\Livewire\DataSelector;
use App\Livewire\FinanceDashboard;
use App\Livewire\ExpenseManager;
use App\Livewire\IncomeManager;

// XML Module
Route::get('/', UploadBatch::class);
Route::get('/batches', BatchList::class);
Route::get('/batch/{batch}', DataSelector::class);

// Finance Module
Route::get('/finance', FinanceDashboard::class);
Route::get('/expenses', ExpenseManager::class);
Route::get('/incomes', IncomeManager::class);
