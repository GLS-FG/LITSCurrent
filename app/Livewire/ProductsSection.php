<?php

namespace App\Livewire;

use App\Livewire\Forms\ProductForm;
use App\Models\Incoterm;
use App\Models\OrderImport;
use App\Models\OrderProduct;
use App\Models\OrderShipment;
use App\Models\WarehouseStorage;
use App\View\Helpers\Service;
use Livewire\Component;

class ProductsSection extends Component
{
    public $order;
    public $isStore = true;
    public $openForm = false;
    public $openDelete = false;
    public $product = null;
    public $productName = "";
    public $orderType = "";
    public ProductForm $form;

    public function mount(OrderShipment|OrderImport|WarehouseStorage $order)
    {
        $this->order = $order;
        if ($order instanceof WarehouseStorage) {
            $this->orderType = "warehouse_storage";
        } else if ($order instanceof OrderImport) {
            $this->orderType = "import";
        } else if ($order instanceof OrderShipment) {
            $this->orderType = "shipment";
        } else {
            $this->orderType = "export";
        }
    }

    public function render()
    {
        $incoterms = Incoterm::orderBy('id', 'desc')->get();
        $this->form->incoterm_id = $incoterms->first()->id;
        return view('livewire.products-section', [
            'order' => $this->order,
            'products' => $this->order->products,
            'services' => $this->getOrderServices(),
            'incoterms' => $incoterms,
        ]);
    }

    public function create()
    {
        $this->form->reset();
        $this->openForm = true;
        $this->isStore = true;
    }

    public function edit($id)
    {
        $this->form->reset();
        $this->openForm = true;
        $this->product = OrderProduct::find($id);
        $this->form->product = $this->product->product;
        $this->form->weight = $this->product->weight;
        $this->form->container = $this->product->container;
        $this->form->quantity = $this->product->quantity;
        $this->form->value = $this->product->value;
        $this->form->reference = $this->product->reference;
        $this->form->dimensions = $this->product->dimensions;
        $this->form->height = $this->product->height;
        $this->form->width = $this->product->width;
        $this->form->length = $this->product->length;
        $this->form->unit_measure = $this->product->unit_measure;
        $this->form->weight_measure = $this->product->weight_measure;
        $this->form->incoterm_id = $this->product->incoterm_id;
        $this->isStore = false;
    }

    public function delete($id)
    {
        $this->openDelete = true;
        $this->product = OrderProduct::find($id);
        $this->productName = $this->product->product . " / " . $this->product->reference;
    }

    public function destroy()
    {
        $this->product->delete();
        $this->product = null;
        $this->openDelete = false;
    }

    public function store($storeType)
    {
        $validated = $this->validate();
        $product = new OrderProduct();
        $product->order_id = $this->order->order->id;
        $product->product = $validated['product'];
        $product->weight = $validated['weight'] == "" ? null : $validated['weight'];
        $product->container = $validated['container'];
        $product->quantity = $validated['quantity'] == "" ? null : $validated['quantity'];
        $product->value = $validated['value'] == "" ? null : $validated['value'];
        $product->reference = $validated['reference'];
        $product->dimensions = $validated['dimensions'];
        $product->height = $validated['height'] == "" ? null : $validated['height'];
        $product->width = $validated['width'] == "" ? null : $validated['width'];
        $product->length = $validated['length'] == "" ? null : $validated['length'];
        $product->unit_measure = $validated['unit_measure'];
        $product->weight_measure = $validated['weight_measure'];
        $product->incoterm_id = $validated['incoterm_id'];
        $this->order->products()->save($product);
        $this->form->reset();
        if($storeType == 'single') {
            $this->openForm = false;
        } else {
            $this->dispatch('saved');
        }
    }

    public function update()
    {
        $validated = $this->validate();
        $this->product->product = $validated['product'];
        $this->product->weight = $validated['weight'] == "" ? null : $validated['weight'];
        $this->product->container = $validated['container'];
        $this->product->quantity = $validated['quantity'] == "" ? null : $validated['quantity'];
        $this->product->value = $validated['value'] == "" ? null : $validated['value'];
        $this->product->reference = $validated['reference'];
        $this->product->dimensions = $validated['dimensions'];
        $this->product->height = $validated['height'] == "" ? null : $validated['height'];
        $this->product->width = $validated['width'] == "" ? null : $validated['width'];
        $this->product->length = $validated['length'] == "" ? null : $validated['length'];
        $this->product->unit_measure = $validated['unit_measure'];
        $this->product->weight_measure = $validated['weight_measure'];
        $this->product->incoterm_id = $validated['incoterm_id'];
        $this->product->update();
        $this->form->reset();
        $this->openForm = false;
    }

    private function getOrderServices(): array
    {
        $order = $this->order->order;
        $suborder = $this->order;
        $services = [];
        foreach ($order->storages as $storage) {
            if ($suborder instanceof WarehouseStorage) {
                if($storage->id != $suborder->id && count($storage->products) > 0) {
                    $services[] = new Service($storage, 'Almacén', 'orders.warehouse-storages.', 'warehouse_storage', $storage->warehouse_storage_status_id, $storage->created_at);
                }
            } else {
                if (count($storage->products) > 0){
                    $services[] = new Service($storage, 'Almacén', 'orders.warehouse-storages.', 'warehouse_storage', $storage->warehouse_storage_status_id, $storage->created_at);
                }
            }
        }
        foreach ($order->imports as $import) {
            if ($suborder instanceof OrderImport) {
                if($import->id != $suborder->id && count($import->products) > 0) {
                    $services[] = new Service($import, 'Aduana', 'orders.imports.', 'import', $import->order_import_status_id, $import->created_at);
                }
            } else {
                if (count($import->products) > 0){
                    $services[] = new Service($import, 'Aduana', 'orders.imports.', 'import', $import->order_import_status_id, $import->created_at);
                }
            }
        }
        foreach ($order->shipments as $shipment) {
            if ($suborder instanceof OrderShipment) {
                if($shipment->id != $suborder->id && count($shipment->products) > 0) {
                    $services[] = new Service($shipment, 'Embarque', 'orders.shipments.', 'shipment', $shipment->order_shipment_status_id, $shipment->created_at);
                }
            } else {
                if (count($shipment->products) > 0){
                    $services[] = new Service($shipment, 'Embarque', 'orders.shipments.', 'shipment', $shipment->order_shipment_status_id, $shipment->created_at);
                }
            }
        }
        usort($services, function ($a, $b) {
            return $a->createdAt->timestamp - $b->createdAt->timestamp;
        });
        return $services;
    }
}
