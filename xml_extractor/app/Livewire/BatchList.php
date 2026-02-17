<?php

namespace App\Livewire;

use App\Models\Batch;
use Livewire\Component;
use Livewire\WithPagination;

class BatchList extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.batch-list', [
            'batches' => Batch::latest()->paginate(10)
        ])->layout('layouts.app');
    }
}
