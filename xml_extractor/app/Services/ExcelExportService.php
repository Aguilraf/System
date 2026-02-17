<?php

namespace App\Services;

use App\Exports\DynamicInvoiceExport;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExportService
{
    /**
     * Export data to Excel.
     *
     * @param array $invoicesData Array of flattened data arrays.
     * @param array $columns Selected columns to export.
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(array $invoicesData, array $columns, string $filename = 'invoices.xlsx')
    {
        return Excel::download(new DynamicInvoiceExport($invoicesData, $columns), $filename);
    }
}
