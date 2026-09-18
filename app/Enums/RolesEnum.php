<?php

namespace App\Enums;

enum RolesEnum: string implements ServiceState
{
    case SUPERADMIN = 'Super Admin';
    case OPERATORADMIN = 'Operator Admin';
    case OPERATOR = 'Operator';
    case WAREHOUSE = 'Warehouse';
    case BILLING = 'Billing';
    case CLIENT = 'Client';
    public function label(): string
    {
        return match ($this) {
            RolesEnum::SUPERADMIN => 'Administrador',
            RolesEnum::OPERATORADMIN => 'Operador Adm',
            RolesEnum::OPERATOR => 'Operador',
            RolesEnum::WAREHOUSE => 'Almacén',
            RolesEnum::BILLING => 'Facturación',
            RolesEnum::CLIENT => 'Cliente',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            RolesEnum::SUPERADMIN => 'bg-blue-50 text-blue-700 ring-blue-700/10',
            RolesEnum::OPERATORADMIN => 'bg-cyan-50 text-cyan-700 ring-cyan-700/10',
            RolesEnum::OPERATOR => 'bg-pink-50 text-pink-700 ring-pink-700/10',
            RolesEnum::WAREHOUSE => 'bg-green-50 text-green-700 ring-green-700/10',
            RolesEnum::BILLING => 'bg-sky-50 text-sky-700 ring-sky-700/10',
            RolesEnum::CLIENT => 'bg-gray-50 text-gray-700 ring-gray-700/10',
        };
    }
}
