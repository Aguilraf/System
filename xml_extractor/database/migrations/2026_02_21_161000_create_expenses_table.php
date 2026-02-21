<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->boolean('has_invoice')->default(false);
            // Invoice-specific fields (nullable when no invoice)
            $table->string('rfc')->nullable();
            $table->string('nombre_emisor')->nullable();
            $table->string('metodo_pago')->nullable();
            $table->string('forma_pago')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('isr', 12, 2)->default(0);
            $table->decimal('iva', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            // File storage
            $table->string('invoice_path')->nullable();
            $table->string('invoice_filename')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
