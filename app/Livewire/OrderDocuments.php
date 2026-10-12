<?php

namespace App\Livewire;

use Livewire\Component;
use ZipArchive;

class OrderDocuments extends Component
{
    public $order;
    public $selectAll = false;
    public $showError = false;
    public $sortBy = null;
    public $sortByDirection = "asc";
    public $documents = [];
    public $files = [];

    public function mount($order)
    {
        $this->order = $order;
    }

    public function changeSort($sortBy)
    {
        if($this->sortBy == $sortBy){
            if ($this->sortByDirection == "desc") {
                $sortBy = null;
            }
            $this->sortByDirection = "desc";
        } else {
            $this->sortByDirection = "asc";
        }
        $this->sortBy = $sortBy;
    }

    public function updatedSelectAll($value)
    {
        $this->showError = false;
        if ($value) {
            $this->files = $this->documents->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->files = [];
        }
    }

    public function updatedFiles()
    {
        $this->showError = false;
        $this->selectAll = count($this->files) === count($this->documents);
    }

    public function download()
    {
        if (count($this->files) < 2) {
            $this->showError = true;
        } else {
            $zipFileName = 'Expediente_' . $this->order->code . '.zip';
            $zip = new ZipArchive();
            $zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE);
            foreach ($this->documents as $document) {
                if(in_array($document->id, $this->files)){
                    $filePath = storage_path('app/private/' . $document->name);
                    $zip->addFile($filePath, $document->documentType->name . '-' . $document->original_name);
                }
            }
            $zip->close();
            return response()->download($zipFileName)->deleteFileAfterSend();
        }
    }

    /**
     * A qué registro pertenece cada documento, indexado por "tipo:id" del documentable.
     * Se arma con los servicios que la orden ya tiene cargados (los mismos que alimentan
     * allDocuments()), sin una consulta extra por documento.
     */
    public function serviceMap(): array
    {
        $map = [
            $this->order->getMorphClass() . ':' . $this->order->id => ['slug' => 'order', 'label' => __('Order'), 'code' => null],
        ];
        $groups = [
            ['shipments', 'shipment', __('Shipment')],
            ['storages', 'warehouse', __('Warehouse')],
            ['imports', 'customs', __('Custom')],
            ['exports', 'customs', __('Export')],
        ];
        foreach ($groups as [$relation, $slug, $label]) {
            foreach ($this->order->$relation as $service) {
                $map[$service->getMorphClass() . ':' . $service->id] = [
                    'slug' => $slug,
                    'label' => $label,
                    'code' => $service->tracking_code,
                ];
            }
        }
        return $map;
    }

    public function serviceFor($document, ?array $map = null): array
    {
        $map ??= $this->serviceMap();
        return $map[$document->documentable_type . ':' . $document->documentable_id]
            ?? ['slug' => 'order', 'label' => __('Order'), 'code' => null];
    }

    public function render()
    {
        $documents = $this->order->allDocuments();
        $map = $this->serviceMap();
        if ($this->sortBy === 'service') {
            $key = fn ($document) => ($info = $this->serviceFor($document, $map))['label'] . ' ' . $info['code'];
            $documents = $this->sortByDirection == "asc" ? $documents->sortBy($key) : $documents->sortByDesc($key);
        } elseif ($this->sortBy != null) {
            $documents = $this->sortByDirection == "asc" ? $documents->sortBy($this->sortBy) : $documents->sortByDesc($this->sortBy);
        }
        $this->documents = $documents;
        return view('livewire.order-documents', ['serviceMap' => $map]);
    }
}
