<?php

namespace App\Reports;

use App\Enums\OrderImportStatusEnum;
use App\Enums\OrderShipmentStatusEnum;
use App\Enums\WarehouseStorageStatusEnum;
use App\Models\OrderImport;
use App\Models\OrderShipment;
use App\Models\WarehouseStorage;
use Illuminate\Support\Facades\DB;

/**
 * Los tres tipos de servicio de una orden (service_types: 1 SHP, 2 CBR, 3 WHS).
 * Cada uno vive en su propia tabla y sus clases (service_classes.service_type_id)
 * son distintas: Embarque = DOM/INT/TRB/LOC, Aduana = CBP/SAT, Almacén = MTH.
 */
final class ServiceKind
{
    public function __construct(
        public readonly string $key,
        public readonly int $typeId,
        public readonly string $table,
        public readonly string $model,
        public readonly string $statusColumn,
        public readonly string $statusEnum,
        public readonly string $label,
        public readonly string $plural,
        public readonly string $openLabel,
        public readonly string $showRoute,
        public readonly string $showParam,
    ) {
    }

    /** @return array<string, self> en el orden en que se muestran */
    public static function all(): array
    {
        return [
            'ship' => new self('ship', 1, 'order_shipments', OrderShipment::class, 'order_shipment_status_id',
                OrderShipmentStatusEnum::class, 'Embarque', 'Embarques', 'Embarques activos', 'orders.shipments.show', 'shipment'),
            'customs' => new self('customs', 2, 'order_imports', OrderImport::class, 'order_import_status_id',
                OrderImportStatusEnum::class, 'Aduana', 'Aduanas', 'Aduanas activas', 'orders.imports.show', 'import'),
            'warehouse' => new self('warehouse', 3, 'warehouse_storages', WarehouseStorage::class, 'warehouse_storage_status_id',
                WarehouseStorageStatusEnum::class, 'Almacén', 'Almacenes', 'Almacenes activos', 'orders.warehouse-storages.show', 'warehouse_storage'),
        ];
    }

    public static function byType(?int $typeId): ?self
    {
        foreach (self::all() as $kind) {
            if ($kind->typeId === $typeId) {
                return $kind;
            }
        }

        return null;
    }

    /** Tipo efectivo: el elegido, o el de la clase si solo se eligió clase. */
    public static function resolveTypeId(?int $typeId, ?int $classId): ?int
    {
        if ($typeId) {
            return $typeId;
        }

        $resolved = $classId
            ? DB::table('service_classes')->where('id', $classId)->value('service_type_id')
            : null;

        return $resolved === null ? null : (int) $resolved;
    }

    /** Estatus que ya no cuentan como abiertos. */
    public function closedStatuses(): array
    {
        return [$this->statusEnum::CLOSED, $this->statusEnum::CANCELED];
    }
}
