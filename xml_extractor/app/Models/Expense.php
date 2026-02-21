<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'has_invoice',
        'rfc',
        'nombre_emisor',
        'metodo_pago',
        'forma_pago',
        'subtotal',
        'isr',
        'iva',
        'descuento',
        'total',
        'invoice_path',
        'invoice_filename',
        'notas',
    ];

    protected $casts = [
        'fecha' => 'date',
        'has_invoice' => 'boolean',
        'subtotal' => 'decimal:2',
        'isr' => 'decimal:2',
        'iva' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(ExpenseItem::class);
    }
}
