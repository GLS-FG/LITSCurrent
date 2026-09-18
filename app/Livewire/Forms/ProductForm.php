<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class ProductForm extends Form
{
    #[Validate('required|max:191')]
    public $product;

    #[Validate('required|max:191')]
    public $reference;

    #[Validate('nullable|numeric|gt:0|required_without:dimensions')]
    public $length;

    #[Validate('nullable|numeric|gt:0|required_without:dimensions')]
    public $width;

    #[Validate('nullable|numeric|gt:0|required_without:dimensions')]
    public $height;

    #[Validate('nullable|max:20')]
    public $unit_measure = 'IN';

    #[Validate('nullable|max:191|required_without:height,width,length')]
    public $dimensions;

    #[Validate('nullable|max:191')]
    public $weight;

    #[Validate('required|max:20')]
    public $weight_measure = 'KG';

    #[Validate('nullable|max:191')]
    public $container = 'Pallet';

    #[Validate('nullable|numeric|gt:0')]
    public $quantity;

    #[Validate('nullable|numeric')]
    public $value;

    #[Validate('required|exists:incoterms,id')]
    public $incoterm_id;
}
