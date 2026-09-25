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
            RolesEnum::SUPERADMIN => 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 ring-blue-700/10 dark:ring-blue-400/20',
            RolesEnum::OPERATORADMIN => 'bg-cyan-50 dark:bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 ring-cyan-700/10 dark:ring-cyan-400/20',
            RolesEnum::OPERATOR => 'bg-pink-50 dark:bg-pink-500/10 text-pink-700 dark:text-pink-400 ring-pink-700/10 dark:ring-pink-400/20',
            RolesEnum::WAREHOUSE => 'bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 ring-green-700/10 dark:ring-green-400/20',
            RolesEnum::BILLING => 'bg-sky-50 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 ring-sky-700/10 dark:ring-sky-400/20',
            RolesEnum::CLIENT => 'bg-gray-50 dark:bg-gray-800/60 text-gray-700 dark:text-gray-300 ring-gray-700/10 dark:ring-gray-400/20',
        };
    }

    public function dotColor(): string
    {
        return match ($this) {
            RolesEnum::SUPERADMIN => 'bg-blue-500 dark:bg-blue-400',
            RolesEnum::OPERATORADMIN => 'bg-cyan-500 dark:bg-cyan-400',
            RolesEnum::OPERATOR => 'bg-pink-500 dark:bg-pink-400',
            RolesEnum::WAREHOUSE => 'bg-green-500 dark:bg-green-400',
            RolesEnum::BILLING => 'bg-sky-500 dark:bg-sky-400',
            RolesEnum::CLIENT => 'bg-gray-400 dark:bg-gray-500',
        };
    }

    public function textColor(): string
    {
        return match ($this) {
            RolesEnum::SUPERADMIN => 'text-blue-700 dark:text-blue-400',
            RolesEnum::OPERATORADMIN => 'text-cyan-700 dark:text-cyan-400',
            RolesEnum::OPERATOR => 'text-pink-700 dark:text-pink-400',
            RolesEnum::WAREHOUSE => 'text-green-700 dark:text-green-400',
            RolesEnum::BILLING => 'text-sky-700 dark:text-sky-400',
            RolesEnum::CLIENT => 'text-gray-700 dark:text-gray-300',
        };
    }
}
