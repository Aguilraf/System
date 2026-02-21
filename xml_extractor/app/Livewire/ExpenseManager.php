<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\ExpenseItem;
use App\Services\XmlParserService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ExpenseManager extends Component
{
    use WithFileUploads, WithPagination;

    // Form visibility
    public $showForm = false;
    public $editingId = null;

    // Invoice toggle
    public $hasInvoice = false;
    public $invoiceFile = null;
    public $extractedFromInvoice = false;

    // Expense fields
    public $fecha;
    public $rfc = '';
    public $nombre_emisor = '';
    public $metodo_pago = '';
    public $forma_pago = '';
    public $subtotal = 0;
    public $isr = 0;
    public $iva = 0;
    public $descuento = 0;
    public $total = 0;
    public $notas = '';

    // Items (conceptos)
    public $items = [];

    // Confirmation modal
    public $showDeleteModal = false;
    public $deletingId = null;

    // View detail modal
    public $showDetailModal = false;
    public $viewingExpense = null;

    // Search
    public $search = '';

    protected $rules = [
        'fecha' => 'required|date',
        'total' => 'required|numeric|min:0',
        'items' => 'required|array|min:1',
        'items.*.descripcion' => 'required|string',
        'items.*.cantidad' => 'required|numeric|min:0.01',
        'items.*.precio_unitario' => 'required|numeric|min:0',
        'items.*.importe' => 'required|numeric|min:0',
    ];

    protected $messages = [
        'fecha.required' => 'La fecha es requerida.',
        'total.required' => 'El total es requerido.',
        'items.required' => 'Debe agregar al menos un concepto.',
        'items.min' => 'Debe agregar al menos un concepto.',
        'items.*.descripcion.required' => 'La descripción del concepto es requerida.',
        'items.*.cantidad.required' => 'La cantidad es requerida.',
    ];

    public function mount()
    {
        $this->fecha = now()->format('Y-m-d');
        $this->addItem();
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
        $this->hasInvoice = false;
        $this->invoiceFile = null;
        $this->extractedFromInvoice = false;
        $this->fecha = now()->format('Y-m-d');
        $this->rfc = '';
        $this->nombre_emisor = '';
        $this->metodo_pago = '';
        $this->forma_pago = '';
        $this->subtotal = 0;
        $this->isr = 0;
        $this->iva = 0;
        $this->descuento = 0;
        $this->total = 0;
        $this->notas = '';
        $this->items = [];
        $this->addItem();
        $this->resetErrorBag();
    }

    public function addItem()
    {
        $this->items[] = [
            'cantidad' => 1,
            'descripcion' => '',
            'precio_unitario' => 0,
            'importe' => 0,
        ];
    }

    public function removeItem($index)
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
            $this->recalculateTotal();
        }
    }

    public function updatedItems($value, $key)
    {
        // Auto-calculate importe when cantidad or precio_unitario changes
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $index = $parts[0];
            $field = $parts[1];

            if (in_array($field, ['cantidad', 'precio_unitario'])) {
                $cantidad = (float) ($this->items[$index]['cantidad'] ?? 0);
                $precio = (float) ($this->items[$index]['precio_unitario'] ?? 0);
                $this->items[$index]['importe'] = round($cantidad * $precio, 2);
                $this->recalculateTotal();
            }
        }
    }

    public function recalculateTotal()
    {
        $subtotal = collect($this->items)->sum(function ($item) {
            return (float) ($item['importe'] ?? 0);
        });

        if (!$this->hasInvoice) {
            $this->subtotal = round($subtotal, 2);
            $this->total = round($subtotal - (float)$this->descuento + (float)$this->iva - (float)$this->isr, 2);
        }
    }

    public function updatedHasInvoice()
    {
        if (!$this->hasInvoice) {
            $this->invoiceFile = null;
            $this->extractedFromInvoice = false;
            $this->rfc = '';
            $this->nombre_emisor = '';
            $this->metodo_pago = '';
            $this->forma_pago = '';
        }
    }

    public function updatedInvoiceFile()
    {
        if (!$this->invoiceFile) return;

        $extension = strtolower($this->invoiceFile->getClientOriginalExtension());

        if ($extension === 'xml') {
            $this->extractFromXml();
        }
        // For PDF files, just store it - data must be entered manually
    }

    private function extractFromXml()
    {
        try {
            $parser = app(XmlParserService::class);
            $content = $this->invoiceFile->get();
            $data = $parser->parse($content);

            if (empty($data)) {
                $this->addError('invoiceFile', 'No se pudieron extraer datos del archivo XML.');
                return;
            }

            // Extract main fields
            $this->fecha = $this->extractField($data, ['Fecha']) ?
                substr($this->extractField($data, ['Fecha']), 0, 10) : $this->fecha;
            $this->rfc = $this->extractField($data, ['Emisor.Rfc', 'Emisor.rfc']) ?? '';
            $this->nombre_emisor = $this->extractField($data, ['Emisor.Nombre', 'Emisor.nombre']) ?? '';
            $this->metodo_pago = $this->extractField($data, ['MetodoPago', 'metodoPago']) ?? '';
            $this->forma_pago = $this->extractField($data, ['FormaPago', 'formaPago']) ?? '';
            $this->subtotal = (float) ($this->extractField($data, ['SubTotal', 'subTotal']) ?? 0);
            $this->descuento = (float) ($this->extractField($data, ['Descuento', 'descuento']) ?? 0);
            $this->total = (float) ($this->extractField($data, ['Total', 'total']) ?? 0);

            // Extract taxes
            $this->iva = 0;
            $this->isr = 0;

            // Look for traslados (IVA) and retenciones (ISR) in the flattened data
            foreach ($data as $key => $value) {
                if (preg_match('/Traslado.*Importe/i', $key) && is_numeric($value)) {
                    // Check if it's IVA (002)
                    $impuestoKey = str_replace('Importe', 'Impuesto', $key);
                    $impuesto = $data[$impuestoKey] ?? '';
                    if ($impuesto === '002' || stripos($key, 'iva') !== false) {
                        $this->iva += (float) $value;
                    }
                }
                if (preg_match('/Retencion.*Importe/i', $key) && is_numeric($value)) {
                    $impuestoKey = str_replace('Importe', 'Impuesto', $key);
                    $impuesto = $data[$impuestoKey] ?? '';
                    if ($impuesto === '001' || stripos($key, 'isr') !== false) {
                        $this->isr += (float) $value;
                    }
                }
            }

            // Extract conceptos (items)
            $this->items = [];
            $conceptoIndex = 0;
            $foundItems = false;

            foreach ($data as $key => $value) {
                // Match Concepto patterns like Conceptos.Concepto.0.Descripcion or Conceptos.Concepto.Descripcion
                if (preg_match('/Concepto[s]?\.?(?:Concepto\.)?(\d+)?\.?Descripcion/i', $key)) {
                    $foundItems = true;
                    // Get the prefix to find sibling fields
                    $prefix = preg_replace('/Descripcion$/i', '', $key);

                    $this->items[] = [
                        'cantidad' => (float) ($data[$prefix . 'Cantidad'] ?? $data[$prefix . 'cantidad'] ?? 1),
                        'descripcion' => $value,
                        'precio_unitario' => (float) ($data[$prefix . 'ValorUnitario'] ?? $data[$prefix . 'valorUnitario'] ?? 0),
                        'importe' => (float) ($data[$prefix . 'Importe'] ?? $data[$prefix . 'importe'] ?? 0),
                    ];
                }
            }

            if (!$foundItems) {
                // Try single Concepto without index
                $desc = $this->extractField($data, ['Conceptos.Concepto.Descripcion', 'Concepto.Descripcion']);
                if ($desc) {
                    $this->items[] = [
                        'cantidad' => (float) ($this->extractField($data, ['Conceptos.Concepto.Cantidad', 'Concepto.Cantidad']) ?? 1),
                        'descripcion' => $desc,
                        'precio_unitario' => (float) ($this->extractField($data, ['Conceptos.Concepto.ValorUnitario', 'Concepto.ValorUnitario']) ?? 0),
                        'importe' => (float) ($this->extractField($data, ['Conceptos.Concepto.Importe', 'Concepto.Importe']) ?? 0),
                    ];
                }
            }

            if (empty($this->items)) {
                $this->addItem();
            }

            $this->extractedFromInvoice = true;

        } catch (\Exception $e) {
            $this->addError('invoiceFile', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    private function extractField(array $data, array $possibleKeys): ?string
    {
        foreach ($possibleKeys as $key) {
            if (isset($data[$key])) {
                return $data[$key];
            }
        }
        return null;
    }

    public function save()
    {
        $this->validate();

        $expenseData = [
            'fecha' => $this->fecha,
            'has_invoice' => $this->hasInvoice,
            'rfc' => $this->rfc ?: null,
            'nombre_emisor' => $this->nombre_emisor ?: null,
            'metodo_pago' => $this->metodo_pago ?: null,
            'forma_pago' => $this->forma_pago ?: null,
            'subtotal' => $this->subtotal,
            'isr' => $this->isr,
            'iva' => $this->iva,
            'descuento' => $this->descuento,
            'total' => $this->total,
            'notas' => $this->notas ?: null,
        ];

        // Handle file upload
        if ($this->hasInvoice && $this->invoiceFile && !is_string($this->invoiceFile)) {
            $path = $this->invoiceFile->store('invoices', 'public');
            $expenseData['invoice_path'] = $path;
            $expenseData['invoice_filename'] = $this->invoiceFile->getClientOriginalName();
        }

        if ($this->editingId) {
            $expense = Expense::findOrFail($this->editingId);
            $expense->update($expenseData);
            $expense->items()->delete();
        } else {
            $expense = Expense::create($expenseData);
        }

        // Save items
        foreach ($this->items as $item) {
            if (!empty($item['descripcion'])) {
                $expense->items()->create([
                    'cantidad' => (float) ($item['cantidad'] ?? 1),
                    'descripcion' => $item['descripcion'],
                    'precio_unitario' => (float) ($item['precio_unitario'] ?? 0),
                    'importe' => (float) ($item['importe'] ?? 0),
                ]);
            }
        }

        $this->closeForm();
        session()->flash('message', $this->editingId ? 'Egreso actualizado correctamente.' : 'Egreso registrado correctamente.');
    }

    public function edit($id)
    {
        $expense = Expense::with('items')->findOrFail($id);

        $this->editingId = $expense->id;
        $this->fecha = $expense->fecha->format('Y-m-d');
        $this->hasInvoice = $expense->has_invoice;
        $this->rfc = $expense->rfc ?? '';
        $this->nombre_emisor = $expense->nombre_emisor ?? '';
        $this->metodo_pago = $expense->metodo_pago ?? '';
        $this->forma_pago = $expense->forma_pago ?? '';
        $this->subtotal = $expense->subtotal;
        $this->isr = $expense->isr;
        $this->iva = $expense->iva;
        $this->descuento = $expense->descuento;
        $this->total = $expense->total;
        $this->notas = $expense->notas ?? '';

        $this->items = $expense->items->map(function ($item) {
            return [
                'cantidad' => (float) $item->cantidad,
                'descripcion' => $item->descripcion,
                'precio_unitario' => (float) $item->precio_unitario,
                'importe' => (float) $item->importe,
            ];
        })->toArray();

        if (empty($this->items)) {
            $this->addItem();
        }

        $this->showForm = true;
    }

    public function viewDetail($id)
    {
        $this->viewingExpense = Expense::with('items')->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->viewingExpense = null;
    }

    public function confirmDelete($id)
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->deletingId) {
            Expense::findOrFail($this->deletingId)->delete();
            $this->showDeleteModal = false;
            $this->deletingId = null;
            session()->flash('message', 'Egreso eliminado correctamente.');
        }
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function render()
    {
        $expenses = Expense::with('items')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('nombre_emisor', 'like', "%{$this->search}%")
                          ->orWhere('rfc', 'like', "%{$this->search}%")
                          ->orWhere('notas', 'like', "%{$this->search}%")
                          ->orWhereHas('items', function ($q2) {
                              $q2->where('descripcion', 'like', "%{$this->search}%");
                          });
                });
            })
            ->latest('fecha')
            ->paginate(15);

        return view('livewire.expense-manager', [
            'expenses' => $expenses,
        ])->layout('layouts.app');
    }
}
