<?php

namespace App\View\Helpers;

class AdminNavigation
{
    /**
     * @return array<int, Navigation>
     */
    public static function items(): array
    {
        return [
            new Navigation('dashboard', 'Dashboard', 'fa-regular fa-house'),
            new Navigation('clients.index', 'Clientes', 'fa-regular fa-user'),
            new Navigation('customs.index', 'Aduanas', 'fa-regular fa-building-columns'),
            new Navigation('custom-agents.index', 'Agencias aduanales', 'fa-regular fa-file-certificate'),
            new Navigation('transportation-agencies.index', 'Transportistas', 'fa-regular fa-truck'),
            new Navigation('warehouses.index', 'Almacenes', 'fa-regular fa-warehouse'),
            new Navigation('petition-codes.index', 'Claves pedimento', 'fa-regular fa-lock'),
            new Navigation('cities.index', 'Ciudades', 'fa-regular fa-earth-americas'),
            new Navigation('addresses.index', 'Direcciones', 'fa-regular fa-location-dot'),
            new Navigation('users.index', 'Usuarios', 'fa-regular fa-address-card'),
            new Navigation('service-types.index', 'Service Types', 'fa-regular fa-folder'),
            new Navigation('service-statuses.index', 'Estatus Servicio', 'fa-regular fa-arrow-progress'),
            new Navigation('document-types.index', 'Documentos', 'fa-regular fa-file-circle-info'),
            new Navigation('private-types.index', 'Docs privados', 'fa-regular fa-file-lock'),
        ];
    }
}
