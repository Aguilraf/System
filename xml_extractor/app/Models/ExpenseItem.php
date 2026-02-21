<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_id',
        'cantidad',
        'descripcion',
        'precio_unitario',
        'importe',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'importe' => 'decimal:2',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
