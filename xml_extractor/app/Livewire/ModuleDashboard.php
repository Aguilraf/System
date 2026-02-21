<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Batch;
use Livewire\Component;

class ModuleDashboard extends Component
{
    public function render()
    {
        // Quick stats for the cards
        $xmlBatchCount = Batch::count();
        $expenseCount = Expense::count();
        $incomeCount = Income::count();
        $totalExpenses = Expense::sum('total');
        $totalIncomes = Income::sum('total');

        return view('livewire.module-dashboard', [
            'xmlBatchCount' => $xmlBatchCount,
            'expenseCount' => $expenseCount,
            'incomeCount' => $incomeCount,
            'totalExpenses' => (float) $totalExpenses,
            'totalIncomes' => (float) $totalIncomes,
        ])->layout('layouts.app');
    }
}
