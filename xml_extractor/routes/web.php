<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\ModuleDashboard;
use App\Livewire\UploadBatch;
use App\Livewire\BatchList;
use App\Livewire\DataSelector;
use App\Livewire\FinanceDashboard;
use App\Livewire\ExpenseManager;
use App\Livewire\IncomeManager;

// Main Dashboard
Route::get('/', ModuleDashboard::class);

// XML Module
Route::get('/xml', UploadBatch::class);
Route::get('/xml/batches', BatchList::class);
Route::get('/xml/batch/{batch}', DataSelector::class);

// Finance Module
Route::get('/finance', FinanceDashboard::class);
Route::get('/finance/expenses', ExpenseManager::class);
Route::get('/finance/incomes', IncomeManager::class);
