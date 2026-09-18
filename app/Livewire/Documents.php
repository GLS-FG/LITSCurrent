<?php

namespace App\Livewire;

use App\Models\OrderImport;
use App\Models\OrderShipment;
use Livewire\Component;
use ZipArchive;

class Documents extends Component
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
            if ($this->order::class == OrderShipment::class) {
                $zipType = "Embarque";
            } else if ($this->order::class == OrderImport::class) {
                $zipType = "Aduana";
            } else {
                $zipType = "Almacen";
            }
            $zipFileName = 'Expediente_' . $this->order->order->code .'_' . $zipType . '.zip';
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

    public function render()
    {
        if ($this->sortBy != null) {
            if ($this->sortByDirection == "asc") {
                $this->documents = $this->order->documents->sortBy($this->sortBy);
            } else {
                $this->documents = $this->order->documents->sortByDesc($this->sortBy);
            }
        } else {
            $this->documents = $this->order->documents;
        }
        return view('livewire.documents');
    }
}
