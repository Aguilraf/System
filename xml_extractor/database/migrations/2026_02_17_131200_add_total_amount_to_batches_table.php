<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->decimal('total_amount', 15, 2)->default(0)->after('total_files');
        });

        // Backfill existing batches
        $batches = \Illuminate\Support\Facades\DB::table('batches')->get();
        foreach ($batches as $batch) {
            $invoices = \Illuminate\Support\Facades\DB::table('invoices')->where('batch_id', $batch->id)->get();
            $total = 0;
            foreach ($invoices as $invoice) {
                // extracted_data is stored as JSON string in DB
                $data = json_decode($invoice->extracted_data, true);
                if (isset($data['Total']) && is_numeric($data['Total'])) {
                    $total += (float)$data['Total'];
                }
            }
            \Illuminate\Support\Facades\DB::table('batches')
                ->where('id', $batch->id)
                ->update(['total_amount' => $total]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn('total_amount');
        });
    }
};
