<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ShipmentHandling extends Component
{
    public $isEditable = false;
    public $shipment = false;
    public $oversize = false;
    public $hazardous = false;
    public $refrigerated = false;
    public $insurance = false;
    public $tarps = false;

    public function mount($shipment)
    {
        $this->shipment = $shipment;
        if(!Auth::user()->hasRole('Client') && $this->shipment->editable){
            $this->isEditable = true;
        }
        $this->oversize = $shipment->oversize == "Si";
        $this->hazardous = $shipment->hazardous_material == "Si";
        $this->refrigerated = $shipment->refrigerated == "Si";
        $this->insurance = $shipment->insurance == "Si";
        $this->tarps = $shipment->tarps == "Si";
    }

    public function updatedOversize($value)
    {
        $this->shipment->oversize = $value ? "Si" : "No";
        $this->shipment->save();
    }

    public function updatedHazardous($value)
    {
        $this->shipment->hazardous_material = $value ? "Si" : "No";
        $this->shipment->save();
    }

    public function updatedRefrigerated($value)
    {
        $this->shipment->refrigerated = $value ? "Si" : "No";
        $this->shipment->save();
    }

    public function updatedInsurance($value)
    {
        $this->shipment->insurance = $value ? "Si" : "No";
        $this->shipment->save();
    }

    public function updatedTarps($value)
    {
        $this->shipment->tarps = $value ? "Si" : "No";
        $this->shipment->save();
    }

    public function render()
    {
        return view('livewire.shipment-handling');
    }
}
