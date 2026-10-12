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
            new Navigation('clients.index', 'Clientes', 'fa-regular fa-user', 'Entidades'),
            new Navigation('users.index', 'Usuarios', 'fa-regular fa-address-card', 'Entidades'),
            new Navigation('customs.index', 'Aduanas', 'fa-regular fa-building-columns', 'Logística y operación'),
            new Navigation('custom-agents.index', 'Agencias aduanales', 'fa-regular fa-file-certificate', 'Logística y operación'),
            new Navigation('transportation-agencies.index', 'Transportistas', 'fa-regular fa-truck', 'Logística y operación'),
            new Navigation('warehouses.index', 'Almacenes', 'fa-regular fa-warehouse', 'Logística y operación'),
            new Navigation('petition-codes.index', 'Claves pedimento', 'fa-regular fa-lock', 'Catálogos'),
            new Navigation('cities.index', 'Ciudades', 'fa-regular fa-earth-americas', 'Catálogos'),
            new Navigation('addresses.index', 'Direcciones', 'fa-regular fa-location-dot', 'Catálogos'),
            new Navigation('service-types.index', 'Service Types', 'fa-regular fa-folder', 'Catálogos'),
            new Navigation('service-statuses.index', 'Estatus Servicio', 'fa-regular fa-arrow-progress', 'Catálogos'),
            new Navigation('document-types.index', 'Documentos', 'fa-regular fa-file-circle-info', 'Documentos'),
            new Navigation('private-types.index', 'Docs privados', 'fa-regular fa-file-lock', 'Documentos'),
        ];
    }
}
