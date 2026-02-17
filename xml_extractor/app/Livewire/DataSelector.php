<?php

namespace App\Livewire;

use App\Models\Batch;
use App\Services\ExcelExportService;
use Livewire\Component;
use Illuminate\Support\Str;

class DataSelector extends Component
{
    public Batch $batch;
    public $availableColumns = [];
    public $selectedColumns = [];
    public $previewRows = [];
    public $selectAll = false;

    public function mount(Batch $batch)
    {
        $this->batch = $batch;
        $this->loadColumns();
        // Default selection: common fields
        $this->selectedColumns = ['Emisor.Rfc', 'Emisor.Nombre', 'Fecha', 'Total', 'Receptor.Rfc'];
        $this->updatePreview();
    }

    public function loadColumns()
    {
        // Scan up to 10 invoices to find all available keys
        $invoices = $this->batch->invoices()->limit(10)->get();
        $keys = [];
        foreach ($invoices as $invoice) {
            foreach (array_keys($invoice->extracted_data ?? []) as $key) {
                $keys[] = $key;
            }
        }
        $this->availableColumns = array_unique($keys);
        sort($this->availableColumns);
    }

    public function updatedSelectedColumns()
    {
        $this->updatePreview();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedColumns = $this->availableColumns;
        } else {
            $this->selectedColumns = [];
        }
        $this->updatePreview();
    }

    public function updatePreview()
    {
        // Get first 5 rows for preview
        $invoices = $this->batch->invoices()->limit(5)->get();
        $this->previewRows = $invoices->map(function ($invoice) {
            $row = [];
            foreach ($this->selectedColumns as $col) {
                // Formatting for preview only
                $row[$col] = $this->formatValue($invoice->extracted_data[$col] ?? '', $col);
            }
            return $row;
        })->toArray();
    }

    private function formatValue($value, $columnName)
    {
        if ($value === '' || $value === null) return '-';

        // Check if value looks like a number
        if (is_numeric($value)) {
            // Heuristic: If column name suggests money or quantity
            if (Str::contains(strtolower($columnName), ['total', 'subtotal', 'importe', 'monto', 'valor', 'base', 'impuesto', 'tasa', 'cuota'])) {
                return number_format((float)$value, 2);
            }
             // Simple quantity might be integer
            if (Str::contains(strtolower($columnName), ['cantidad'])) {
                 return number_format((float)$value, 2); // Or 0 decimals if preferred, but user said "amounts" usually implies money
            }
        }
        
        return $value;
    }

    public function export(ExcelExportService $exporter)
    {
        $invoices = $this->batch->invoices()->get()->map(function ($invoice) {
            $data = $invoice->extracted_data;
            // Apply formatting for Excel too? 
            // Usually Excel prefers raw numbers, but user asked for "format".
            // If we send raw numbers, Excel export class should handle setFormatCode.
            // For now, let's send formatted strings to ensure it looks exactly as requested, 
            // unless users need valid numbers for formulas. 
            // Given "turn file to excel to work on it", raw numbers are better.
            // But "darle formato" often means visual. 
            // Let's coerce to float if numeric so Excel sees it as number.
            
            foreach ($data as $key => $val) {
                 if (is_numeric($val) && Str::contains(strtolower($key), ['total', 'subtotal', 'importe', 'monto', 'valor', 'base', 'impuesto'])) {
                     $data[$key] = (float)$val; 
                 }
            }
            return $data;
        })->toArray();

        return $exporter->export($invoices, $this->selectedColumns, 'batch_' . $this->batch->id . '.xlsx');
    }

    public function render()
    {
        return view('livewire.data-selector')->layout('layouts.app');
    }
}
