<?php

namespace App\Livewire\Forms;

use App\Models\Address;
use App\Models\State;
use Livewire\Attributes\Validate;
use Livewire\Form;

class AddressForm extends Form
{
    #[Validate('required|string|min:3')]
    public $contact_name = '';

    #[Validate('required|max:256')]
    public $name = '';

    #[Validate('required|max:256')]
    public $trade_name = '';

    #[Validate('nullable|email')]
    public $email = '';

    #[Validate('nullable|min:10|numeric')]
    public $phone = '';

    #[Validate('required|string')]
    public $address = '';

    #[Validate('nullable|string|max:100')]
    public $neighborhood = '';

    #[Validate('nullable|string|max:15')]
    public $postal_code = '';

    #[Validate('required|string')]
    public $city = '';

    #[Validate('required|exists:cities,id')]
    public $city_id;

    #[Validate('required|string')]
    public $state = '';

    #[Validate('required|exists:states,id')]
    public $state_id;

    #[Validate('required|string')]
    public $country = '';

    #[Validate('required|exists:countries,id')]
    public $country_id;

    #[Validate('nullable|string')]
    public $link = '';

    #[Validate('required|max:256')]
    public $location_name = '';

    #[Validate('required|numeric')]
    public $latitude = '';

    #[Validate('required|numeric')]
    public $longitude = '';

    public function setState(State $state)
    {
        $this->state = $state->name;
        $this->state_id = $state->id;
    }

    public function store()
    {
        $this->validate();
        Address::create($this->all());
        $this->reset();
    }
}
