<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'cantidad',
        'descripcion',
        'total',
        'notas',
    ];

    protected $casts = [
        'fecha' => 'date',
        'cantidad' => 'decimal:2',
        'total' => 'decimal:2',
    ];
}
