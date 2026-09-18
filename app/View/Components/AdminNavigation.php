<?php

namespace App\View\Components;

use App\View\Helpers\Navigation;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AdminNavigation extends Component
{
    public array $navigation;

    public function __construct()
    {
        $dashboard = new Navigation('dashboard', 'Dashboard', 'fa-regular fa-house');
        $clients = new Navigation('clients.index', 'Clientes', 'fa-regular fa-user');
        $customs = new Navigation('customs.index', 'Aduanas', 'fa-regular fa-building-columns');
        $customAgents = new Navigation('custom-agents.index', 'Agencias aduanales', 'fa-regular fa-file-certificate');
        $agencies = new Navigation('transportation-agencies.index', 'Transportistas', 'fa-regular fa-truck');
        $warehouses = new Navigation('warehouses.index', 'Almacenes', 'fa-regular fa-warehouse');
        $petitionCodes = new Navigation('petition-codes.index', 'Claves pedimento', "fa-regular fa-lock");
        $cities = new Navigation('cities.index', 'Ciudades', "fa-regular fa-earth-americas");
        $addresses = new Navigation('addresses.index', 'Direcciones', "fa-regular fa-location-dot");
        $users = new Navigation('users.index', 'Usuarios', 'fa-regular fa-address-card');
        $services = new Navigation('service-types.index', 'Service Types', 'fa-regular fa-folder');
        $geostatuses = new Navigation('service-statuses.index', 'Estatus Servicio', 'fa-regular fa-arrow-progress');
        $documentTypes = new Navigation('document-types.index', 'Documentos', 'fa-regular fa-file-circle-info');
        $privateDocumentTypes = new Navigation('private-types.index', 'Docs privados', 'fa-regular fa-file-lock');
        $this->navigation = array($dashboard, $clients, $customs, $customAgents, $agencies, $warehouses, $petitionCodes, $cities, $addresses, $users, $services, $geostatuses, $documentTypes, $privateDocumentTypes);
    }

    public function render(): View|Closure|string
    {
        return view('components.admin-navigation');
    }
}
