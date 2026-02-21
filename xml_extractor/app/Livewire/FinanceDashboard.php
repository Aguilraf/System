<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\Income;
use Livewire\Component;

class FinanceDashboard extends Component
{
    public $period = 'month'; // month, year, all
    public $selectedMonth;
    public $selectedYear;

    public function mount()
    {
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;
    }

    public function getFilteredExpensesProperty()
    {
        $query = Expense::query();

        if ($this->period === 'month') {
            $query->whereMonth('fecha', $this->selectedMonth)
                  ->whereYear('fecha', $this->selectedYear);
        } elseif ($this->period === 'year') {
            $query->whereYear('fecha', $this->selectedYear);
        }

        return $query->latest('fecha')->get();
    }

    public function getFilteredIncomesProperty()
    {
        $query = Income::query();

        if ($this->period === 'month') {
            $query->whereMonth('fecha', $this->selectedMonth)
                  ->whereYear('fecha', $this->selectedYear);
        } elseif ($this->period === 'year') {
            $query->whereYear('fecha', $this->selectedYear);
        }

        return $query->latest('fecha')->get();
    }

    public function getTotalExpensesProperty()
    {
        return $this->filteredExpenses->sum('total');
    }

    public function getTotalIncomesProperty()
    {
        return $this->filteredIncomes->sum('total');
    }

    public function getBalanceProperty()
    {
        return $this->totalIncomes - $this->totalExpenses;
    }

    public function getMonthlyDataProperty()
    {
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthExpenses = Expense::whereMonth('fecha', $i)
                ->whereYear('fecha', $this->selectedYear)
                ->sum('total');
            $monthIncomes = Income::whereMonth('fecha', $i)
                ->whereYear('fecha', $this->selectedYear)
                ->sum('total');
            $months[] = [
                'month' => $i,
                'expenses' => (float) $monthExpenses,
                'incomes' => (float) $monthIncomes,
            ];
        }
        return $months;
    }

    public function render()
    {
        return view('livewire.finance-dashboard', [
            'expenses' => $this->filteredExpenses,
            'incomes' => $this->filteredIncomes,
            'totalExpenses' => $this->totalExpenses,
            'totalIncomes' => $this->totalIncomes,
            'balance' => $this->balance,
            'monthlyData' => $this->monthlyData,
        ])->layout('layouts.app');
    }
}
