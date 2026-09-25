<?php

namespace App\Livewire\Forms;

use App\Models\Address;
use Livewire\Attributes\Validate;
use Livewire\Form;

class AddressForm extends Form
{
    #[Validate('required|string|min:3')]
    public $contact_name = '';

    #[Validate('required|min:3|max:256')]
    public $name = '';

    #[Validate('required|min:3|max:256')]
    public $trade_name = '';

    #[Validate('nullable|email')]
    public $email = '';

    #[Validate('nullable|min:10|numeric')]
    public $phone = '';

    #[Validate('required|string|regex:/^[\p{L}\p{N}\s.,\/#-]+$/u')]
    public $address = '';

    #[Validate('nullable|string|min:3|max:100')]
    public $neighborhood = '';

    #[Validate('nullable|string|min:3|max:15')]
    public $postal_code = '';

    #[Validate('required|exists:cities,id')]
    public $city_id;

    #[Validate('required|exists:states,id')]
    public $state_id;

    #[Validate('required|exists:countries,id')]
    public $country_id;

    #[Validate('nullable|string')]
    public $link = '';

    #[Validate('required|min:3|max:256')]
    public $location_name = '';

    #[Validate('required|numeric')]
    public $latitude = '';

    #[Validate('required|numeric')]
    public $longitude = '';

    public function messages(): array
    {
        return [
            'address.regex' => 'La "Calle y número" solo permite letras, números, espacios y los caracteres . , - / #',
        ];
    }

    public function store()
    {
        $this->validate();
        Address::create($this->all());
        $this->reset();
    }
}
