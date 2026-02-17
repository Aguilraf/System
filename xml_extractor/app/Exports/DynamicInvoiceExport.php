<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class DynamicInvoiceExport implements FromCollection, WithHeadings
{
    protected $invoices;
    protected $columns;

    /**
     * @param array $invoices Array of flattened invoice data (from Invoice models)
     * @param array $columns List of keys to export (e.g., ['Emisor.Rfc', 'Total'])
     */
    public function __construct(array $invoices, array $columns)
    {
        $this->invoices = $invoices;
        $this->columns = $columns;
    }

    public function collection()
    {
        $data = [];
        foreach ($this->invoices as $invoice) {
            $row = [];
            foreach ($this->columns as $column) {
                // Determine if we are handling array fields (Conceptos)
                // If the user selects 'Conceptos.Concepto.Descripcion', and there are multiple,
                // we might need to duplicate rows or join values.
                // For simplicity Phase 1: We pull the exact key if it exists.
                // If it doesn't exist (e.g. Concepto.1 doesn't exist for this invoice), null.
                
                // Advanced: If column is 'Conceptos.Concepto.*.Descripcion' - not supported yet.
                // User selects specific keys available in the union of all invoices.
                
                $row[] = $invoice[$column] ?? '';
            }
            $data[] = $row;
        }

        return new Collection($data);
    }

    public function headings(): array
    {
        return $this->columns;
    }
}
