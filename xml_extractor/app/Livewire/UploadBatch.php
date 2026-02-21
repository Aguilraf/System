<?php

namespace App\Livewire;

use App\Models\Batch;
use App\Models\Invoice;
use App\Services\XmlParserService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class UploadBatch extends Component
{
    use WithFileUploads;

    public $files = [];
    public $sfiles = []; // Staged files with metadata
    public $isProcessing = false;
    public $showConfirmationModal = false;
    public $processTotal = 0.0;

    public function updatedFiles()
    {
        $this->isProcessing = true;
        $parser = app(XmlParserService::class);

        foreach ($this->files as $file) {
            try {
                $content = $file->get();
                $data = $parser->parse($content);
                $uuid = $data['cfdi:Complemento.tfd:TimbreFiscalDigital.UUID'] ?? 
                        ($data['Complemento.TimbreFiscalDigital.UUID'] ?? null);

                $status = 'valid';
                $message = '';
                $duplicateBatch = null;
                $duplicateBatchId = null;

                if ($uuid) {
                    $existing = Invoice::where('uuid', $uuid)->with('batch')->first();
                    if ($existing) {
                        $status = 'duplicate';
                        $duplicateBatch = $existing->batch ? $existing->batch->name : 'Trabajo desconocido';
                        $duplicateBatchId = $existing->batch ? $existing->batch->id : null;
                        $message = "Duplicado en: $duplicateBatch";
                    }
                } else {
                    $status = 'error';
                    $message = 'No se encontró UUID';
                }

                $this->sfiles[] = [
                    'file' => $file, // Temporary file object
                    'name' => $file->getClientOriginalName(),
                    'uuid' => $uuid,
                    'status' => $status, // valid, duplicate, error
                    'message' => $message,
                    'duplicate_batch_id' => $duplicateBatchId,
                    'amount' => isset($data['Total']) && is_numeric($data['Total']) ? (float)$data['Total'] : 0,
                    'selected' => $status === 'valid', // Default select valid ones
                ];

            } catch (\Exception $e) {
                $this->sfiles[] = [
                    'file' => $file,
                    'name' => $file->getClientOriginalName(),
                    'uuid' => null,
                    'status' => 'error',
                    'message' => 'Error al leer XML',
                    'selected' => false,
                ];
            }
        }
        
        // Reset raw files input to allow adding more if needed
        $this->files = []; 
        $this->isProcessing = false;
    }

    public function removeFile($index)
    {
        unset($this->sfiles[$index]);
        $this->sfiles = array_values($this->sfiles);
    }

    public function toggleSelection($index)
    {
        $this->sfiles[$index]['selected'] = !$this->sfiles[$index]['selected'];
    }

    public function triggerSave()
    {
        $selectedFiles = collect($this->sfiles)->where('selected', true);
        
        if ($selectedFiles->isEmpty()) {
            $this->addError('files', 'Debes seleccionar al menos un archivo para procesar.');
            return;
        }

        $this->processTotal = $selectedFiles->sum('amount');
        $this->showConfirmationModal = true;
    }

    public function save(XmlParserService $parser)
    {
        $selectedFiles = collect($this->sfiles)->where('selected', true);
        
        if ($selectedFiles->isEmpty()) {
            $this->addError('files', 'Debes seleccionar al menos un archivo para procesar.');
            return;
        }

        $this->isProcessing = true;

        $batch = Batch::create([
            'name' => 'Carga ' . now()->format('d/m/Y H:i'),
            'status' => 'processing',
            'total_files' => 0, // Will update at end
        ]);

        $count = 0;
        $batchTotal = 0;

        foreach ($selectedFiles as $sfile) {
            $file = $sfile['file'];
            $content = $file->get();
            $data = $parser->parse($content);
            $uuid = $sfile['uuid'] ?? Str::uuid(); // Should have UUID if valid

            // Double check duplicate just in case (race condition or manual select of duplicate)
            // But if user manually selected duplicate, we might want to allow it? 
            // Constraint says "no se guarda", so let's skip strict duplicates even if selected, 
            // OR assume user knows what they are doing if they checked it (but they can't check duplicates in UI easily).
            // Let's stick to strict skip for safety or just trust the staging.
            // Requirement said "Duplicado no se guarda".
            
            $existing = Invoice::where('uuid', $uuid)->exists();
            if ($existing) continue; 

            \Illuminate\Support\Facades\Log::info("Processing file: " . $sfile['name']);
            Invoice::create([
                'batch_id' => $batch->id,
                'uuid' => $uuid,
                'filename' => $sfile['name'],
                'xml_content' => $content,
                'extracted_data' => $data,
            ]);
            
            // Calculate total for this invoice
            $invoiceTotal = isset($data['Total']) && is_numeric($data['Total']) ? (float)$data['Total'] : 0;
            $batchTotal += $invoiceTotal;
            
            $count++;
        }

        $batch->update([
            'status' => 'ready',
            'total_files' => $count,
            'total_amount' => $batchTotal,
        ]);

        $this->isProcessing = false;

        return redirect()->to('/xml/batch/' . $batch->id);
    }

    public function render()
    {
        return view('livewire.upload-batch')->layout('layouts.app');
    }
}
