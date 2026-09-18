<?php

namespace App\Livewire;

use App\Livewire\Forms\AddressForm;
use App\Models\Address;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class SearchAddresses extends Component
{
    use WithPagination;

    public $search;
    public $openSearch = false;
    public $createAddress = false;
    public $type;
    public $countries;
    public $states;
    public $cities;
    public $showCountriesDropdown;
    public $showStatesDropdown;
    public $showCitiesDropdown;
    public AddressForm $form;

    public function mount($type = null)
    {
        $this->type = $type;
        $this->showCountriesDropdown = false;
        $this->showStatesDropdown = false;
        $this->showCitiesDropdown = false;
        $this->countries = collect();
        $this->states = collect();
        $this->cities = collect();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedFormCountry()
    {
        if (strlen($this->form->country) < 1) {
            $this->countries = collect();
            $this->showCountriesDropdown = false;
            return;
        }
        $this->countries = Country::select('id', 'name')
            ->where( 'name', 'like' , '%' . $this->form->country . '%')
            ->orderBy('name')
            ->limit(10)
            ->get();
        $this->form->country_id = null;
        $this->showCountriesDropdown = true;
    }

    public function updatedFormState()
    {
        if (strlen($this->form->state) < 1) {
            $this->states = collect();
            $this->showCountriesDropdown = false;
            return;
        }
        $this->states = State::select('id', 'name')
            ->where( 'country_id', $this->form->country_id)
            ->where( 'name', 'like' , '%' . $this->form->state . '%')
            ->orderBy('name')
            ->limit(10)
            ->get();
        $this->form->state_id = null;
        $this->showStatesDropdown = true;
    }

    public function updatedFormCity()
    {
        if (strlen($this->form->city) < 1) {
            $this->cities = collect();
            $this->showCitiesDropdown = false;
            return;
        }
        $this->cities = City::select('id', 'name')
            ->where( 'state_id', $this->form->state_id)
            ->where( 'name', 'like' , '%' . $this->form->city . '%')
            ->orderBy('name')
            ->limit(20)
            ->get();
        $this->form->city_id = null;
        $this->showCitiesDropdown = true;
    }

    public function startCreateAddress()
    {
        $this->createAddress = true;
    }

    public function cancelCreateAddress()
    {
        $this->form->reset();
        $this->createAddress = false;
    }

    public function save()
    {
        $this->search = $this->form->name;
        $this->form->store();
        $this->createAddress = false;
    }

    #[On('country-selected')]
    public function countrySelected(int $id, String $name)
    {
        $this->form->country_id = $id;
        $this->form->country = $name;
        $this->form->reset(['state_id', 'state', 'city_id', 'city']);
        $this->showCountriesDropdown = false;
    }

    #[On('state-selected')]
    public function stateSelected(int $id, String $name)
    {
        $this->form->state_id = $id;
        $this->form->state = $name;
        $this->form->reset(['city_id', 'city']);
        $this->showStatesDropdown = false;
    }

    #[On('city-selected')]
    public function citySelected(int $id, String $name)
    {
        $this->form->city_id = $id;
        $this->form->city = $name;
        $this->showCitiesDropdown = false;
    }

    public function selectAddress(Address $address)
    {
        $this->openSearch = false;
        $neighborhood = "";
        if($address->neighborhood){
            $neighborhood = ', ' . $address->neighborhood;
        }
        $fullAddress = $address->name . PHP_EOL .
                       $address->address . $neighborhood . PHP_EOL .
                       $address->city->name . ', ' . $address->state->name. ' ' . $address->postal_code . ' ' . $address->country->name . PHP_EOL .
                       $address->contact_name;
        $data = [
            'type' => $this->type,
            'id' => $address->id,
            'address_name' => $address->name,
            'country_id' => $address->country_id,
            'country_name' => $address->country->name,
            'state_id' => $address->state_id,
            'state_name' => $address->state->name,
            'city_id' => $address->city_id,
            'city_name' => $address->city->name,
            'link' => $address->link,
            'full_address' => $fullAddress
        ];
        $this->dispatch('address-selected', address: $data);
    }

    public function render()
    {
        $addresses = Address::where('nickname', 'like', '%' . $this->search . '%')
            ->orWhere('name', 'like', '%' . $this->search . '%')
            ->orWhere('contact_name', 'like', '%' . $this->search . '%')
            ->orderBy('nickname')
            ->paginate(5);
        return view('livewire.search-addresses', [
            'addresses' => $addresses
        ]);
    }
}
