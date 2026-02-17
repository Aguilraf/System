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
    public $isProcessing = false;

    public function save(XmlParserService $parser)
    {
        $this->validate([
            'files.*' => 'required', // Relaxed validation for debugging. XML mime types vary (text/xml, application/xml).
        ]);

        $this->isProcessing = true;

        $batch = Batch::create([
            'name' => 'Carga ' . now()->format('d/m/Y H:i'),
            'status' => 'processing',
            'total_files' => count($this->files),
        ]);

        foreach ($this->files as $file) {
            $content = $file->get();
            $data = $parser->parse($content);
            $uuid = $data['cfdi:Complemento.tfd:TimbreFiscalDigital.UUID'] ?? 
                    ($data['Complemento.TimbreFiscalDigital.UUID'] ?? Str::uuid());

            Invoice::create([
                'batch_id' => $batch->id,
                'uuid' => $uuid,
                'filename' => $file->getClientOriginalName(),
                'xml_content' => $content,
                'extracted_data' => $data,
            ]);
        }

        $batch->update(['status' => 'ready']);
        $this->isProcessing = false;

        return redirect()->to('/batch/' . $batch->id);
    }

    public function render()
    {
        return view('livewire.upload-batch')->layout('layouts.app');
    }
}
