<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['batch_id', 'uuid', 'filename', 'xml_content', 'extracted_data'];

    protected $casts = [
        'extracted_data' => 'array',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
