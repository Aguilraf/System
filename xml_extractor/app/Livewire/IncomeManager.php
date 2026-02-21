<?php

namespace App\Livewire;

use App\Models\Income;
use Livewire\Component;
use Livewire\WithPagination;

class IncomeManager extends Component
{
    use WithPagination;

    // Form visibility
    public $showForm = false;
    public $editingId = null;

    // Income fields
    public $fecha;
    public $cantidad = 0;
    public $descripcion = '';
    public $total = 0;
    public $notas = '';

    // Confirmation modal
    public $showDeleteModal = false;
    public $deletingId = null;

    // Search
    public $search = '';

    protected $rules = [
        'fecha' => 'required|date',
        'cantidad' => 'required|numeric|min:0',
        'descripcion' => 'required|string|max:255',
        'total' => 'required|numeric|min:0',
    ];

    protected $messages = [
        'fecha.required' => 'La fecha es requerida.',
        'cantidad.required' => 'La cantidad es requerida.',
        'descripcion.required' => 'La descripción es requerida.',
        'total.required' => 'El total es requerido.',
    ];

    public function mount()
    {
        $this->fecha = now()->format('Y-m-d');
    }

    public function openForm()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function closeForm()
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->fecha = now()->format('Y-m-d');
        $this->cantidad = 0;
        $this->descripcion = '';
        $this->total = 0;
        $this->notas = '';
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'fecha' => $this->fecha,
            'cantidad' => $this->cantidad,
            'descripcion' => $this->descripcion,
            'total' => $this->total,
            'notas' => $this->notas ?: null,
        ];

        if ($this->editingId) {
            Income::findOrFail($this->editingId)->update($data);
            session()->flash('message', 'Ingreso actualizado correctamente.');
        } else {
            Income::create($data);
            session()->flash('message', 'Ingreso registrado correctamente.');
        }

        $this->closeForm();
    }

    public function edit($id)
    {
        $income = Income::findOrFail($id);

        $this->editingId = $income->id;
        $this->fecha = $income->fecha->format('Y-m-d');
        $this->cantidad = (float) $income->cantidad;
        $this->descripcion = $income->descripcion;
        $this->total = (float) $income->total;
        $this->notas = $income->notas ?? '';

        $this->showForm = true;
    }

    public function confirmDelete($id)
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->deletingId) {
            Income::findOrFail($this->deletingId)->delete();
            $this->showDeleteModal = false;
            $this->deletingId = null;
            session()->flash('message', 'Ingreso eliminado correctamente.');
        }
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function render()
    {
        $incomes = Income::query()
            ->when($this->search, function ($q) {
                $q->where('descripcion', 'like', "%{$this->search}%")
                  ->orWhere('notas', 'like', "%{$this->search}%");
            })
            ->latest('fecha')
            ->paginate(15);

        return view('livewire.income-manager', [
            'incomes' => $incomes,
        ])->layout('layouts.app');
    }
}
