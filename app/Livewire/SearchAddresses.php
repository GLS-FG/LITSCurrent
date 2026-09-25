<?php

namespace App\Livewire;

use App\Livewire\Forms\AddressForm;
use App\Models\Address;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
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
    public AddressForm $form;

    public function mount($type = null)
    {
        $this->type = $type;
        $this->countries = Country::select('id', 'name')->orderBy('name')->get();
        $this->states = collect();
        $this->cities = collect();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedFormCountryId($value)
    {
        $this->form->state_id = null;
        $this->form->city_id = null;
        $this->states = $value
            ? State::select('id', 'name')->where('country_id', $value)->orderBy('name')->get()
            : collect();
        $this->cities = collect();
    }

    public function updatedFormStateId($value)
    {
        $this->form->city_id = null;
        $this->cities = $value
            ? City::select('id', 'name')->where('state_id', $value)->orderBy('name')->get()
            : collect();
    }

    public function startCreateAddress()
    {
        $this->createAddress = true;
    }

    public function cancelCreateAddress()
    {
        $this->form->reset();
        $this->states = collect();
        $this->cities = collect();
        $this->createAddress = false;
    }

    public function save()
    {
        $this->search = $this->form->name;
        $this->form->store();
        $this->states = collect();
        $this->cities = collect();
        $this->createAddress = false;
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
