<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\City;
use App\Models\ClassType;
use App\Models\Client;
use App\Models\Country;
use App\Models\Custom;
use App\Models\CustomAgent;
use App\Models\CustomAgentAddress;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Incoterm;
use App\Models\Order;
use App\Models\OrderImport;
use App\Models\OrderProduct;
use App\Models\OrderShipment;
use App\Models\PetitionCode;
use App\Models\PrivateDocumentType;
use App\Models\ServiceClass;
use App\Models\ServiceLevel;
use App\Models\ServiceLocation;
use App\Models\ServiceMode;
use App\Models\ServiceType;
use App\Models\ServiceTypeStatus;
use App\Models\ServiceTypeStatusMode;
use App\Models\State;
use App\Models\Transportation;
use App\Models\TransportationAgency;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Warehouse;
use App\Models\WarehouseStorage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    private $roles = ['Super Admin', 'Operator Admin', 'Operator', 'Warehouse', 'Billing', 'Client'];
    private $glsUsers = [
        ['name' => 'María Chávez', 'email' => 'echavez@glsgroup.com.mx', 'role' => 'Billing'],
        ['name' => 'Carolina Montenegro', 'email' => 'aux.facturacion@glsgroup.com.mx', 'role' => 'Billing'],
        ['name' => 'Fernando Escobar', 'email' => 'almacen@glsgroup.com.mx', 'role' => 'Warehouse'],
        ['name' => 'Juan Bernal', 'email' => 'jbernal@glsgroup.com.mx', 'role' => 'Operator'],
        ['name' => 'Alonso Bustamante', 'email' => 'afigueroa@glsgroup.com.mx', 'role' => 'Operator'],
        ['name' => 'Fernanda Malagon', 'email' => 'mfmalagon@glsgroup.com.mx', 'role' => 'Operator Admin'],
        ['name' => 'Susana Molina', 'email' => 'smolina@glsgroup.com.mx', 'role' => 'Operator Admin'],
        ['name' => 'Silvia Lopez', 'email' => 'slopez@glsgroup.com.mx', 'role' => 'Operator Admin'],
        ['name' => 'Luis Cristobal', 'email' => 'administrador@glsgroup.com.mx', 'role' => 'Super Admin'],
        ['name' => 'David Ayala', 'email' => 'dayala@glsgroup.com.mx', 'role' => 'Super Admin'],
        ['name' => 'Fernando Walters', 'email' => 'fwalters@glsgroup.com.mx', 'role' => 'Super Admin'],
        ['name' => 'Juan Coronado', 'email' => 'jcoronado@glsgroup.com.mx', 'role' => 'Super Admin'],
    ];

    public function run(): void
    {
        $this->command->info("Start seeding");
        $this->seedRoles();
        $this->seedCatalogs();
        $this->seedClients();
        $this->seedUsers();
        $this->seedOrders();
        $this->command->info("End seeding");
    }

    private function seedRoles(): void
    {
        $this->command->info("Seeding roles");
        foreach ($this->roles as $role) {
            if (!Role::where('name', $role)->exists()) {
                Role::create(['name' => $role]);
            }
        }
        $this->command->info("End seeding roles");
    }

    private function seedUsers(): void
    {
        $this->command->info("Seeding users from old");
        $oldClients = DB::connection('litsold')->table('clientes')->get();
        $oldUsers = DB::connection('litsold')->table('users')->get();
        foreach ($oldUsers as $oldUser) {
            $user = new User();
            $user->id = $oldUser->id;
            $user->name = $oldUser->name;
            $user->email = $oldUser->email;
            $user->password = $oldUser->password;
            $user->remember_token = $oldUser->remember_token;
            $user->created_at = $oldUser->created_at;
            $user->updated_at = $oldUser->updated_at;
            $user->deleted_at = $oldUser->deleted_at;
            if($oldUser->id == 1){
                $user->name = 'Fabian Lopez';
                $user->email = 'flopez@reveltecnologia.com';
                $user->password = '$2y$12$lBHlahc3hYbCSCSE3Op4nOy3PJUAIJcOgV6zoTkh9Ad966vVRyviW';
            }
            $oldClient = $oldClients->where('user_id', $oldUser->id)->first();
            if ($oldClient != null) {
                $user->client_id = $oldClient->id;
            }
            $user->save();
            $user->syncRoles([]);
            if($oldUser->admin == 1) {
                $user->assignRole('Super Admin');
            } else {
                $user->assignRole('Client');
            }
        }
        $this->command->info("End seeding users from old");
        $this->command->info("Seeding new era users");
        foreach ($this->glsUsers as $glsUser) {
            $user = User::firstOrCreate(
                ['email' => $glsUser['email']],
                ['name' => $glsUser['name'], 'password' => 'password']
            );
            $user->syncRoles([]);
            $user->assignRole($glsUser['role']);
        }
        $this->command->info("End seeding new era users");
    }

    private function seedCatalogs(): void
    {
        $this->seedLocations();
        $this->seedStatuses();
        $this->seedServiceTypes();
        $this->seedPetitionCodes();
        $this->seedWarehouses();
        $this->seedTransportAgencies();
        $this->seedVehicles();
        $this->seedIncoterm();
        $this->seedCustoms();
        $this->seedCustomAgents();
        $this->seedCustomAgentAddresses();
        $this->seedAddresses();
        $this->seedServiceTypeStatuses();
        $this->seedServiceTypeStatusModes();
        $this->seedDocumentTypes();
        $this->seedPrivateDocumentTypes();
    }

    private function seedOrders(): void
    {
        $this->seedPrimaryOrders();
        $this->seedWarehouseStorages();
        $this->seedImports();
        $this->seedExports();
        $this->seedShipments();
        $this->seedShipmentsLocations();
        $this->seedShipmentsTransportations();
        $this->seedOrdersProducts();
        $this->seedDocuments();
    }

    private function seedPrimaryOrders(): void
    {
        $this->command->info("Seeding orders");
        $oldOrders = DB::connection('litsold')->table('ordenes')
            ->join('clientes', 'clientes.id', '=', 'ordenes.cliente_id')
            ->select('ordenes.*', 'clientes.user_id')
            ->get();
        foreach ($oldOrders as $oldOrder) {
            $order = new Order();
            $order->id = $oldOrder->id;
            $order->client_id = $oldOrder->cliente_id;
            $order->order_status_id = $oldOrder->estatus_id;
            $order->reference = $oldOrder->referencia;
            $order->currency_code = $oldOrder->currency;
            $order->code = $oldOrder->orden;
            $order->created_at = $oldOrder->created_at;
            $order->updated_at = $oldOrder->updated_at;
            $order->user_id = 1;
            $order->contact_id = $oldOrder->user_id;
            $order->save();
        }
        $this->command->info("End seeding orders");
    }

    private function seedWarehouseStorages(): void
    {
        $this->command->info("Seeding warehouse orders");
        $oldOrders = DB::connection('litsold')->table('warehouses')
            ->join('ordenes', 'ordenes.id', '=', 'warehouses.orden_id')
            ->select('warehouses.*', 'ordenes.orden', 'ordenes.estatus_id AS orden_estatus_id')
            ->get();
        foreach ($oldOrders as $oldOrder) {
            $order = new WarehouseStorage();
            $order->id = $oldOrder->id;
            $order->order_id = $oldOrder->orden_id;
            $order->reference = $oldOrder->referencia;
            $order->comments = $oldOrder->comentarios;
            $order->warehouse_id = $oldOrder->almacen_id;
            $order->payment_date = $oldOrder->fechaPago;
            if($oldOrder->estatus_id != 1){
                $order->warehouse_storage_status_id = 12;
            } else {
                $order->warehouse_storage_status_id = $oldOrder->estatus_id;
            }
            $order->created_at = $oldOrder->created_at;
            $order->updated_at = $oldOrder->updated_at;
            $order->currency_code = $oldOrder->currency;
            $order->tracking_code = $oldOrder->orden;
            $order->save();
        }
        $this->command->info("End seeding warehouse orders");
    }

    private function seedImports(): void
    {
        $this->command->info("Seeding imports");
        $oldOrders = DB::connection('litsold')->table('importaciones')
            ->join('ordenes', 'ordenes.id', '=', 'importaciones.orden_id')
            ->select('importaciones.*', 'ordenes.orden')
            ->get();
        foreach ($oldOrders as $oldOrder) {
            $order = new OrderImport();
            $order->order_id = $oldOrder->orden_id;
            $order->petition = $oldOrder->pedimento;
            $order->petition_code_id = $oldOrder->clave_pedimento_id;
            $order->petition_date = $oldOrder->fechaPedimento;
            $order->comments = $oldOrder->comentarios;
            $order->custom_id = $oldOrder->aduana_id;
            $order->reference = $oldOrder->referencia;
            $order->payment_date = $oldOrder->fechaPago;
            $order->total = $oldOrder->total;
            $order->order_import_status_id = $oldOrder->estatus_id;
            $order->currency_code = $oldOrder->currency;
            $order->created_at = $oldOrder->created_at;
            $order->updated_at = $oldOrder->updated_at;
            $order->custom_agent_id = $oldOrder->agente_id;
            $order->tracking_code = $oldOrder->orden;
            $order->save();
        }
        $this->command->info("End seeding imports");
    }

    private function seedExports(): void
    {
        $this->command->info("Seeding exports");
        $oldOrders = DB::connection('litsold')->table('exportaciones')
            ->join('ordenes', 'ordenes.id', '=', 'exportaciones.orden_id')
            ->select('exportaciones.*', 'ordenes.orden')
            ->get();
        foreach ($oldOrders as $oldOrder) {
            $order = new OrderImport();
            $order->order_id = $oldOrder->orden_id;
            $order->petition = $oldOrder->pedimento;
            $order->petition_code_id = $oldOrder->clave_pedimento_id;
            $order->petition_date = $oldOrder->fechaPedimento;
            $order->comments = $oldOrder->comentarios;
            $order->custom_id = $oldOrder->aduana_id;
            $order->reference = $oldOrder->referencia;
            $order->payment_date = $oldOrder->fechaPago;
            $order->total = $oldOrder->total;
            $order->order_import_status_id = $oldOrder->estatus_id;
            $order->currency_code = $oldOrder->currency;
            $order->created_at = $oldOrder->created_at;
            $order->updated_at = $oldOrder->updated_at;
            $order->custom_agent_id = $oldOrder->agente_id;
            $order->tracking_code = $oldOrder->orden;
            $order->save();
        }
        $this->command->info("End seeding exports");
    }

    private function seedShipments(): void
    {
        $this->command->info("Seeding shipments");
        $oldOrders = DB::connection('litsold')->table('embarques')
            ->join('ordenes', 'ordenes.id', '=', 'embarques.orden_id')
            ->leftJoin('ciudades AS oc', 'oc.id', '=', 'embarques.origen')
            ->leftJoin('estados AS oe', 'oe.id', '=', 'oc.estado_id')
            ->leftJoin('ciudades AS dc', 'dc.id', '=', 'embarques.destino')
            ->leftJoin('estados AS de', 'de.id', '=', 'dc.estado_id')
            ->select('embarques.*', 'ordenes.orden', 'oc.id AS o_ciudad_id', 'oc.estado_id AS o_estado_id', 'oe.pais_id AS o_pais_id', 'dc.id AS d_ciudad_id', 'dc.estado_id AS d_estado_id', 'de.pais_id AS d_pais_id')
            ->get();
        foreach ($oldOrders as $oldOrder) {
            $order = new OrderShipment();
            $order->id = $oldOrder->id;
            $order->order_id = $oldOrder->orden_id;
            $order->custom_id = $oldOrder->aduana_id;
            $order->petition = $oldOrder->pedimento;
            $order->petition_code_id = $oldOrder->clave_pedimento_id;
            $order->petition_date = $oldOrder->fechaPedimento;
            $order->comments = $oldOrder->comentarios;
            $order->start_date = $oldOrder->fechaInicio;
            $order->end_date = $oldOrder->fechaFin;
            $order->reference = $oldOrder->referencia;
            $order->payment_date = $oldOrder->fechaPago;
            $order->total = $oldOrder->total;
            $order->tracking_code = $oldOrder->orden;
            $order->instructions1 = $oldOrder->envio;
            $order->instructions2 = $oldOrder->consignia;
            $order->order_shipment_status_id = $oldOrder->estatus_id;
            $order->ship_to = $oldOrder->shipto;
            $order->ship_from = $oldOrder->shipfrom;
            $order->invoice = $oldOrder->factura;
            $order->currency_code = $oldOrder->currency;
            $order->created_at = $oldOrder->created_at;
            $order->updated_at = $oldOrder->updated_at;
            $order->custom_agent_id = $oldOrder->agente_id;
            $order->tracking_number = $oldOrder->tracking;
            $order->estimated_time_departure = $oldOrder->fechaInicio;
            $order->estimated_time_arrival = $oldOrder->fechaFin;
            $order->origin_city_id = $oldOrder->o_ciudad_id;
            $order->origin_state_id = $oldOrder->o_estado_id;
            $order->origin_country_id = $oldOrder->o_pais_id;
            $order->destination_city_id = $oldOrder->d_ciudad_id;
            $order->destination_state_id = $oldOrder->d_estado_id;
            $order->destination_country_id = $oldOrder->d_pais_id;
            $order->save();
        }
        $this->command->info("End seeding shipments");
    }

    private function seedDocuments(): void
    {
        $this->command->info("Seeding documents");
        $offset = 0;
        $limit = 10000;
        $max = DB::connection('litsold')->table('archivos')->count();
        while ($offset < $max) {
            $this->command->info("Query documents " . $offset);
            $oldOrders = DB::connection('litsold')->table('archivos')
                ->skip($offset)
                ->take($limit)
                ->get();
            $this->command->info("End query documents " . $offset);
            foreach ($oldOrders as $oldOrder) {
                $originalName = str_replace('public', 'attachments', $oldOrder->nombreOriginal);
                $sizeLabel = round($oldOrder->size/1000000, 2) . " MB";
                $documentableType = match ($oldOrder->ordenservicio_type) {
                    "App\Embarque" => "App\Models\OrderShipment",
                    "App\Warehouse" => "App\Models\WarehouseStorage",
                    default => "App\Models\OrderImport",
                };
                $order = new Document();
                $order->id = $oldOrder->id;
                $order->name = $oldOrder->nombre;
                $order->original_name = $originalName;
                $order->size_bytes = $oldOrder->size;
                $order->size_label = $sizeLabel;
                $order->mime_type = $oldOrder->type;
                $order->document_type_id = $oldOrder->tipo_documento_id;
                $order->documentable_type = $documentableType;
                $order->documentable_id = $oldOrder->ordenservicio_id;
                $order->created_at = $oldOrder->created_at;
                $order->updated_at = $oldOrder->updated_at;
                $order->save();
            }
            $offset += $limit;
        }
        $this->command->info("End seeding documents");
    }

    private function seedShipmentsLocations(): void
    {
        $this->command->info("Seeding shipments locations");
        $oldOrders = DB::connection('litsold')->table('geolocalizacion_embarque AS ge')
            ->join('geolocalizaciones AS g', 'g.id', '=', 'ge.geolocalizacion_id')
            ->select('ge.estatus_geolocalizacion_id', 'ge.embarque_id', 'ge.created_at', 'ge.updated_at', 'g.lat', 'g.lng', 'g.fechaHora', 'g.nombre')
            ->get();
        foreach ($oldOrders as $oldOrder) {
            $order = new ServiceLocation();
            $order->orderable_type = 'App\Models\OrderShipment';
            $order->orderable_id = $oldOrder->embarque_id;
            $order->service_type_status_id = $oldOrder->estatus_geolocalizacion_id;
            $order->name = $oldOrder->nombre;
            $order->latitude = $oldOrder->lat;
            $order->longitude = $oldOrder->lng;
            $order->location_date = $oldOrder->fechaHora;
            $order->created_at = $oldOrder->created_at;
            $order->updated_at = $oldOrder->updated_at;
            $order->save();
        }
        $this->command->info("End seeding shipments locations");
    }

    private function seedLocations(): void
    {
        $this->command->info("Seeding countries");
        $oldCountries = DB::connection('litsold')->table('paises')->get();
        foreach ($oldCountries as $oldCountry) {
            $country = new Country();
            $country->id = $oldCountry->id;
            $country->code = $oldCountry->clave;
            $country->name = $oldCountry->nombre;
            $country->created_at = $oldCountry->created_at;
            $country->updated_at = $oldCountry->updated_at;
            $country->save();
        }
        $this->command->info("End seeding countries");

        $this->command->info("Seeding states");
        $oldStates = DB::connection('litsold')->table('estados')->get();
        foreach ($oldStates as $oldState) {
            $state = new State();
            $state->id = $oldState->id;
            $state->country_id = $oldState->pais_id;
            $state->name = $oldState->nombre;
            $state->short_name = $oldState->abreviado;
            $state->created_at = $oldState->created_at;
            $state->updated_at = $oldState->updated_at;
            $state->save();
        }
        $this->command->info("End seeding states");

        $this->command->info("Seeding cities");
        $oldCities = DB::connection('litsold')->table('ciudades')->get();
        foreach ($oldCities as $oldCity) {
            $city = new City();
            $city->id = $oldCity->id;
            $city->state_id = $oldCity->estado_id;
            $city->name = $oldCity->nombre;
            $city->created_at = $oldCity->created_at;
            $city->updated_at = $oldCity->updated_at;
            $city->save();
        }
        $this->command->info("End seeding cities");
    }

    private function seedShipmentsTransportations(): void
    {
        $this->command->info("Seeding shipments transportations");
        $oldOrders = DB::connection('litsold')->table('transportaciones')
            ->join('embarques', 'embarques.transportacion_id', '=', 'transportaciones.id')
            ->select('transportaciones.*', 'embarques.id as embarque_id')
            ->get();
        foreach ($oldOrders as $oldOrder) {
            $order = new Transportation();
            $order->transportation_type = $oldOrder->tipoTransporte;
            $order->plates = $oldOrder->placas;
            $order->transportation_agency_id = $oldOrder->agencia_id;
            $order->driver = $oldOrder->chofer;
            $order->created_at = $oldOrder->created_at;
            $order->updated_at = $oldOrder->updated_at;
            $order->transportation_status_id = $oldOrder->estatus_id;
            $order->order_shipment_id = $oldOrder->embarque_id;
            $order->save();
        }
        $this->command->info("End seeding shipments transportations");
    }

    private function seedOrdersProducts(): void
    {
        $this->command->info("Seeding orders products");

        $oldOrders = DB::connection('litsold')->table('mercancias')
            ->join('importaciones', 'importaciones.orden_id', '=', 'mercancias.orden_id')
            ->select('mercancias.*', 'importaciones.id as servicio_id')
            ->get();
        foreach ($oldOrders as $oldOrder) {
            $order = new OrderProduct();
            $order->order_id = $oldOrder->orden_id;
            $order->product = $oldOrder->mercancia;
            $order->dimensions = $oldOrder->dimensiones;
            $order->weight = $oldOrder->peso;
            $order->quantity = $oldOrder->cantidad;
            $order->container = $oldOrder->tipoTransporte;
            $order->value = $oldOrder->valor;
            $order->insured = $oldOrder->seguro;
            $order->warehouse_id = $oldOrder->almacen;
            $order->created_at = $oldOrder->created_at;
            $order->updated_at = $oldOrder->updated_at;
            $order->serviceable_type = 'App\Models\OrderImport';
            $order->serviceable_id = $oldOrder->servicio_id;
            $order->save();
        }

        $oldOrders = DB::connection('litsold')->table('mercancias')
            ->join('warehouses', 'warehouses.orden_id', '=', 'mercancias.orden_id')
            ->select('mercancias.*', 'warehouses.id as servicio_id')
            ->get();
        foreach ($oldOrders as $oldOrder) {
            $order = new OrderProduct();
            $order->order_id = $oldOrder->orden_id;
            $order->product = $oldOrder->mercancia;
            $order->dimensions = $oldOrder->dimensiones;
            $order->weight = $oldOrder->peso;
            $order->quantity = $oldOrder->cantidad;
            $order->container = $oldOrder->tipoTransporte;
            $order->value = $oldOrder->valor;
            $order->insured = $oldOrder->seguro;
            $order->warehouse_id = $oldOrder->almacen;
            $order->created_at = $oldOrder->created_at;
            $order->updated_at = $oldOrder->updated_at;
            $order->serviceable_type = 'App\Models\WarehouseStorage';
            $order->serviceable_id = $oldOrder->servicio_id;
            $order->save();
        }

        $offset = 0;
        $limit = 10000;
        $max = 39000;
        while ($offset < $max) {
            $this->command->info("Query products shipments " . $offset);
            $oldOrders = DB::connection('litsold')->table('mercancias')
                ->join('embarques', 'embarques.orden_id', '=', 'mercancias.orden_id')
                ->select('mercancias.*', 'embarques.id as servicio_id')
                ->skip($offset)
                ->take($limit)
                ->get();
            $this->command->info("End query products shipments " . $offset);
            foreach ($oldOrders as $oldOrder) {
                $order = new OrderProduct();
                $order->order_id = $oldOrder->orden_id;
                $order->product = $oldOrder->mercancia;
                $order->dimensions = $oldOrder->dimensiones;
                $order->weight = $oldOrder->peso;
                $order->quantity = $oldOrder->cantidad;
                $order->container = $oldOrder->tipoTransporte;
                $order->value = $oldOrder->valor;
                $order->insured = $oldOrder->seguro;
                $order->warehouse_id = $oldOrder->almacen;
                $order->created_at = $oldOrder->created_at;
                $order->updated_at = $oldOrder->updated_at;
                $order->serviceable_type = 'App\Models\OrderShipment';
                $order->serviceable_id = $oldOrder->servicio_id;
                $order->save();
            }
            $offset += $limit;
        }

        $this->command->info("End seeding orders products");
    }

    private function seedStatuses(): void
    {
        $migrations = [
            ['class' => 'App\Models\OrderExportStatus', 'filter' => 'exportacion'],
            ['class' => 'App\Models\OrderImportStatus', 'filter' => 'importacion'],
            ['class' => 'App\Models\OrderShipmentStatus', 'filter' => 'embarque'],
            ['class' => 'App\Models\OrderStatus', 'filter' => 'orden'],
            ['class' => 'App\Models\TransportationStatus', 'filter' => 'transporte'],
            ['class' => 'App\Models\WarehouseStorageStatus', 'filter' => 'almacen'],
        ];
        $activeStatuses = [1, 10, 12];

        foreach ($migrations as $migration) {
            $className = $migration['class'];
            $filter = $migration['filter'];
            $this->command->info("Seeding " . $className);
            $oldStatuses = DB::connection('litsold')->table('estatus')->where($filter, 1)->get();
            foreach ($oldStatuses as $oldStatus) {
                $status = new $className();
                $status->id = $oldStatus->id;
                $status->name = $oldStatus->estatus;
                $status->created_at = $oldStatus->created_at;
                $status->updated_at = $oldStatus->updated_at;
                if(!in_array($status->id, $activeStatuses)){
                    $status->deleted_at = Carbon::now();
                }
                $status->save();
            }
            $this->command->info("End seeding " . $className);
        }
    }

    private function seedServiceTypes(): void
    {
        $serviceTypes = [
            [1,'SHP','SHP (SHIPMENT)'],
            [2,'CBR','CBR (CUSTOMS BROKER)'],
            [3,'WHS','WHS (WAREHOUSE)'],
        ];
        $this->command->info("Seeding Service Types");
        foreach ($serviceTypes as $sType) {
            $serviceType = new ServiceType();
            $serviceType->id = $sType[0];
            $serviceType->code = $sType[1];
            $serviceType->name = $sType[2];
            $serviceType->save();
        }
        $this->command->info("End seeding Service Types");

        $serviceClasses = [
            [1,'DOM','DOM (DOMESTIC)',1,null],
            [2,'INT','INT (INTERNATIONAL)',1,null],
            [3,'TRB','TRB (TRANSBORDER)',1,null],
            [4,'LOC','LOC (LOCAL)',1,null],
            [7,'CBP','CBP (USA CUSTOMS)',2,64],
            [8,'SAT','SAT (MEXICO CUSTOMS)',2,156],
            [9,'MTH','MTH (MATERIAL HANDLING)',3,null]
        ];
        $this->command->info("Seeding Service Classes");
        foreach ($serviceClasses as $sClass) {
            $serviceClass = new ServiceClass();
            $serviceClass->id = $sClass[0];
            $serviceClass->code = $sClass[1];
            $serviceClass->name = $sClass[2];
            $serviceClass->service_type_id = $sClass[3];
            $serviceClass->customs_country_id = $sClass[4];
            $serviceClass->save();
        }
        $this->command->info("End seeding Service Classes");

        $serviceModes = [
            [1,'AIR','AIR (AIR)',1,null,'fa-regular fa-plane'],
            [2,'GRD','GRD (GROUND)',1,null,'fa-regular fa-truck'],
            [3,'AIR','AIR (AIR)',2,null,'fa-regular fa-plane'],
            [4,'SEA','SEA (SEA/OCEAN)',2,null,'fa-regular fa-anchor'],
            [5,'AIR','AIR (AIR)',3,null,'fa-regular fa-plane'],
            [6,'GRD','GRD (GROUND)',3,null,'fa-regular fa-truck'],
            [7,'GRD','GRD (GROUND)',4,null,'fa-regular fa-truck'],
            [8,'TST','TestMode',1,'2025-10-31 17:13:44','fa-regular fa-truck'],
            [9,'EXP','EXP (EXPORT)',7,null,'fa-regular fa-arrow-up-from-square'],
            [10,'IMP','IMP (IMPORT)',7,null,'fa-regular fa-arrow-down-to-square'],
            [11,'TRA','TRA (TRANSIT)',7,null,'fa-regular fa-file-certificate'],
            [12,'EXP','EXP (EXPORT)',8,null,'fa-regular fa-arrow-up-from-square'],
            [13,'IMP','IMP (IMPORT)',8,null,'fa-regular fa-arrow-down-to-square'],
            [14,'SAA','SAA (SUBMIT AN APPLICATION)',8,null,'fa-regular fa-file-certificate'],
            [15,'STO','STO (STORAGE)',9,null,'fa-regular fa-shelves'],
            [16,'VAS','VAS (VALUE ADDED SERVICES)',9,null,'fa-regular fa-forklift'],
            [17,'SAA','SAA (SUBMIT AN APPLICATION)',7,null,'fa-regular fa-file-certificate']
        ];
        $this->command->info("Seeding Service Modes");
        foreach ($serviceModes as $sMode) {
            $serviceMode = new ServiceMode();
            $serviceMode->id = $sMode[0];
            $serviceMode->code = $sMode[1];
            $serviceMode->name = $sMode[2];
            $serviceMode->service_class_id = $sMode[3];
            $serviceMode->deleted_at = $sMode[4];
            $serviceMode->icon = $sMode[5];
            $serviceMode->save();
        }
        $this->command->info("End seeding Service Modes");

        $classTypes = [
            [1,'OBC','OBC (ON-BOARD COURIER)',1,null],
            [2,'PCL','PCL (PARCEL)',1,null],
            [3,'FTL','FTL (FULL TRUCK LOAD)',2,null],
            [4,'LTL','LTL (LESS TRUCK LOAD)',2,null],
            [5,'OBC','OBC (ON-BOARD COURIER)',3,null],
            [6,'PCL','PCL (PARCEL)',3,null],
            [7,'FCL','FCL (FULL CONTAINER LOAD)',4,null],
            [8,'LCL','LCL (LESS CONTAINER LOAD)',4,null],
            [9,'OBC','OBC (ON-BOARD COURIER)',5,null],
            [10,'PCL','PCL (PARCEL)',5,null],
            [11,'FTL','FTL (FULL TRUCK LOAD)',6,null],
            [12,'LTL','LTL (LESS TRUCK LOAD)',6,null],
            [13,'FTL','FTL (FULL TRUCK LOAD)',7,null],
            [14,'SED','SED (SHIPPER EXPORT DECLARATION)',9,null],
            [15,'ENT','ENT (ENTRY)',10,null],
            [16,'INB','INB (INBOND)',11,null],
            [17,'TEM','TEM (TEMPORALLY)',12,null],
            [18,'ETR','ETR (TEMPORALLY-FIXED ASSETS)',12,'2026-02-23 08:54:56'],
            [19,'DEF','DEF (DEFINITIVE)',12,null],
            [20,'FTZ','FTZ (FREE TRADE ZONE)',12,null],
            [21,'FTZ','FTZ (FREE TRADE ZONE)',13,null],
            [22,'DEF','DEF (DEFINITIVE)',13,null],
            [23,'TEM','TEM (TEMPORALLY)',13,null],
            [24,'ITR','ITR (TEMPORALLY-FIXED ASSETS)',13,'2026-02-23 08:51:21'],
            [25,'SAT','SAT (MEXICO CUSTOMS)',14,null],
            [26,'SE','SE (MINISTRY OF ECONOMY)',14,null],
            [27,'FTL','FTL (FULL TRUCK LOAD)',15,null],
            [28,'LTL','LTL (LESS TRUCK LOAD)',15,null],
            [29,'LBL','LBL (LABEL)',16,null],
            [30,'PRP','PRP (PACKAGE REPAIR)',16,null],
            [31,'RPG','RPG (REPACKAGING)',16,null],
            [32,'V1','VIRTUAL TEMPORAL',13,'2025-11-06 18:40:05'],
            [33,'ISF','ISF (IMPORTER SECURITY FILING)',17,null],
            [34,'BBK','BBK (BREAK BULK)',4,null]
        ];
        $this->command->info("Seeding Class Types");
        foreach ($classTypes as $cType) {
            $classType = new ClassType();
            $classType->id = $cType[0];
            $classType->code = $cType[1];
            $classType->name = $cType[2];
            $classType->service_mode_id = $cType[3];
            $classType->deleted_at = $cType[4];
            $classType->save();
        }
        $this->command->info("End seeding Class Types");

        $serviceLevels = [
            [1,'-','-',1,null],
            [2,'1ST','1ST (NEXT DAY)',2,null],
            [3,'2ND','2ND (2 DAY AIR)',2,null],
            [4,'3RD','3RD (3 DAY AIR)',2,null],
            [5,'BXT','BXT (BOX TRUCK)',3,null],
            [6,'DST','DST (DOUBLE SEMI-TRAILER)',3,null],
            [7,'FTB','FTB (FLATBED)',3,null],
            [8,'LBO','LBO (LOWBOY)',3,null],
            [9,'RAB','RAB (RABON)',3,null],
            [10,'SMU','SMU (SMALL UNIT)',3,null],
            [11,'SDK','SDK (STEPDECK)',3,null],
            [12,'TON','TON (TONELADA)',3,null],
            [13,'TOR','TOR (TORTON)',3,null],
            [14,'TRM','TRM (3.5 TON)',3,null],
            [15,'VAN','VAN (SMALL VAN)',3,null],
            [16,'PRY','PRY  (PRIORITY)',4,null],
            [17,'STD','STD (STANDARD)',4,null],
            [18,'-','-',5,null],
            [19,'1ST','1ST (EXPRESS )',6,'2026-03-04 18:25:47'],
            [20,'PRY','PRY (PRIORITY)',6,null],
            [21,'STD','STD (STANDARD)',6,null],
            [22,'C20','C20 (20 FT CONTAINER)',7,null],
            [23,'C40','C40 (40 FT CONTAINER)',7,null],
            [24,'FRK','FRK (FLAT RACK)',7,null],
            [25,'OTP','OTP (OPEN TOP)',7,null],
            [26,'RRO','RRO (ROLL IN ROLL OUT)',7,null],
            [27,'PLT','PLT (PALLET)',8,null],
            [28,'-','-',9,null],
            [29,'1ST','1ST (EXPRESS )',10,'2026-03-04 18:44:54'],
            [30,'PRY','PRY (PRIORITY)',10,null],
            [31,'STD','STD (STANDARD)',10,null],
            [32,'BXT','BXT (BOX TRUCK)',11,null],
            [33,'DST','DST (DOUBLE SEMI-TRAILER)',11,null],
            [34,'FTB','FTB (FLATBED)',11,null],
            [35,'ECO','ECO (ECONOMIC)',12,'2026-03-04 18:51:27'],
            [36,'PRY','PRY (PRIORITY)',12,null],
            [37,'BXT','BXT (BOX TRUCK)',13,null],
            [38,'DST','DST (DOUBLE SEMI-TRAILER)',13,null],
            [39,'FTB','FTB (FLATBED)',13,null],
            [40,'LBO','LBO (LOWBOY)',13,null],
            [41,'PRY','PRY (PRIORITY)',13,'2025-11-05 00:07:41'],
            [42,'RAB','RAB (RABON)',13,null],
            [43,'SMU','SMU (SMALL UNIT)',13,null],
            [44,'SDK','SDK (STEPDECK)',13,null],
            [45,'TON','TON (TONELADA)',13,null],
            [46,'TOR','TOR (TORTON)',13,null],
            [47,'TRM','TRM (3.5 TON)',13,null],
            [48,'VAN','VAN (SMALL VAN)',13,null],
            [49,'FULL','FULL (DOUBLE SEMI-TRAILER)',13,'2025-11-05 00:08:42'],
            [50,'-','-',14,null],
            [51,'-','-',15,null],
            [52,'TST','TST-',15,'2025-10-31 18:36:30'],
            [53,'-','-',16,null],
            [54,'BA','BA (RETURNED SAME CONDITION)',17,null],
            [55,'CP','Claves de pedimento',18,null],
            [56,'-','-',19,null],
            [57,'-','-',20,null],
            [58,'-','-',21,null],
            [59,'-','-',22,null],
            [60,'-','-',23,null],
            [61,'CP','Claves de pedimento',24,null],
            [62,'-','-',25,null],
            [63,'-','-',26,null],
            [64,'BLK','BLK (BULK)',27,null],
            [65,'CRT','CRT (CRATE)',27,null],
            [66,'CTN','CTN (CARTON)',27,null],
            [67,'DRM','DRM (DRUM)',27,null],
            [68,'MIX','MIX (MIXED)',27,null],
            [69,'PLT','PLT (PALLET)',27,null],
            [70,'BLK','BLK (BULK)',28,null],
            [71,'CRT','CRT (CRATE)',28,null],
            [72,'CTN','CTN (CARTON)',28,null],
            [73,'DRM','DRM (DRUM)',28,null],
            [74,'MIX','MIX (MIXED)',28,null],
            [75,'PLT','PLT (PALLET)',28,null],
            [76,'PDT','PDT(PRODUCT LABEL)',29,null],
            [77,'SPG','SPG(SHIPPING LABEL)',29,null],
            [78,'CRT','CRT (CRATE)',30,null],
            [79,'BLK','BLK (BULK)',31,null],
            [80,'CRT','CRT (CRATE)',31,null],
            [81,'CTN','CTN (CARTON)',31,null],
            [82,'DRM','DRM (DRUM)',31,null],
            [83,'PLT','PLT (PALLET)',31,null],
            [84,'LBO','LBO (LOWBOY)',11,null],
            [85,'RAB','RAB (RABON)',11,null],
            [86,'SMU','SMU (SMALL UNIT)',11,null],
            [87,'SDK','SDK (STEPDECK)',11,null],
            [88,'TON','TON (TONELADA)',11,null],
            [89,'TOR','TOR (TORTON)',11,null],
            [90,'TRM','TRM (3.5 TON)',11,null],
            [91,'VAN','VAN (SMALL VAN)',11,null],
            [92,'CP','2',21,'2026-02-09 18:43:36'],
            [93,'J4','RETURN OF FOREIGN GOODS',21,'2026-02-21 08:36:45'],
            [94,'M3','M3 (ENTRY OF GOODS-RAW MAT)',21,null],
            [95,'M4','M4 (ENTRY OF GOODS-FIXED ASSETS)',21,null],
            [96,'J4','J4 (RETURN OF FOREIGN GOODS)',20,null],
            [97,'A1','A1 (DEFINITIVE)',22,null],
            [98,'A3','A3 (REGULARIZATION)',22,null],
            [99,'T1','T1 (PARCEL SERVICE)',22,null],
            [100,'K1','K1 (RETURN OF DEFINITIVE EXPORT)',22,null],
            [101,'F4','F4 (CHANGE OF REGIME-RAW MAT)',22,null],
            [102,'F5','F5 (CHANGE OF REGIME-FIXED ASSETS)',22,null],
            [103,'R1','R1 (RECTIFICATION)',22,null],
            [104,'IN','IN (RAW MAT)',23,null],
            [105,'AF','AF (FIXED ASSETS)',23,null],
            [106,'V1','V1 (VIRTUAL)',23,null],
            [107,'V1','V1 (VIRTUAL)',19,null],
            [108,'A1','A1 (DEFINITIVE)',19,null],
            [109,'RT','RT (RETURN)',19,null],
            [110,'BM','BM (TRANSFORMATION OR REPAIR)',17,null],
            [111,'BO','BO (FIXED ASSETS FOR REPAIR OR REPLACE)',17,null],
            [112,'-','-',17,null],
            [113,'VAX','VAX (VAN XL)',3,null],
            [114,'CRT','CRT (CRATE)',8,null],
            [115,'DRM','DRM (DRUM)',8,null],
            [116,'BLK','BLK (BULK)',8,null],
            [117,'MIX','MIX (MIXED)',8,null],
            [118,'OVD','OVD (OVER DIMENSION)',34,null],
            [119,'OVW','OVW (OVER WEIGHT)',34,null],
            [120,'VAX','VAX (VAN XL)',11,null],
            [121,'STD','STD (STANDARD)',12,null],
            [122,'VAX','VAX (VAN XL)',13,null]
        ];
        $this->command->info("Seeding Service Levels");
        foreach ($serviceLevels as $sLevel) {
            $serviceLevel = new ServiceLevel();
            $serviceLevel->id = $sLevel[0];
            $serviceLevel->code = $sLevel[1];
            $serviceLevel->name = $sLevel[2];
            $serviceLevel->class_type_id = $sLevel[3];
            $serviceLevel->deleted_at = $sLevel[4];
            $serviceLevel->save();
        }
        $this->command->info("End seeding Service Levels");
    }

    private function seedClients(): void
    {
        $this->command->info("Seeding clients");
        $oldClients = [
            [2,'Misael','Gomez Encinas','GLS','Prueba','','','misael.gomez@unison.mx','2100100100','2018-03-27 18:18:12','2024-05-15 09:59:08','2026-02-10 21:01:18'],
            [3,'ADRIAN','MURRIETA','LATECOERE MEXICO','LMXD',' LME120516HJA','','adrian.murrieta@latecoere.aero','6622231942','2018-03-27 23:32:27','2024-01-31 11:21:56',null],
            [4,'PEDRO','APODACA','BI METALS MEXICO S DE RL DE CV','BIMETALS','E110715V45','','PEDRO.APODACA@bimetalsmexico.com','6623197421','2018-04-04 03:26:44','2023-04-26 16:00:19','2026-02-10 21:01:19'],
            [5,'DANIEL ALEJANDRO','HINOJOSA SAENZ','HS TECHNOLOGIES','HS TECHNOLOGIES','HISD840708JD8','','Ing.daniel.hinojosa@gmail.com','6623012129','2018-04-05 01:52:28','2021-01-06 17:10:09','2026-02-10 21:01:19'],
            [6,'Alejandro','Diaz','COBRE DEL MAYO','CDM','CMA911003HR6','','thomas.felix@cobredelmayo.com','6471012944','2018-04-18 21:51:55','2018-04-18 21:51:55',null],
            [7,'Alberto','Garcia','RADIALL AEROSPACE MÉXICO','RADIALL','SSP860611GM7','','silvia.lara@radiall.com','6441747247','2018-05-03 08:16:09','2018-12-05 10:05:55','2026-02-10 21:01:20'],
            [8,'Marco','Ruiz','MAR LUBRICACION ESPECIALIZADA SA DE CV','MAR LUBRICACIONES','MLE070112IW9','','marco_ruiz@marlubricacion.com','6624715615','2018-05-26 10:04:51','2018-05-26 10:04:51','2026-02-10 21:01:20'],
            [9,'Veronica','Partida','GLS FORWARDING GROUP','GLS GROUP','','','vpartida@glsgroup.com.mx','6624674936','2018-05-28 12:04:09','2018-05-28 12:04:09','2026-02-10 21:01:20'],
            [10,'RICARDO','VERBER','FLSMIDTH S.A DE C.V','FLSMIDTH','FSM970929MZ4','','Ricardo.Verber@flsmidth.com','8115554569','2018-07-22 21:59:05','2019-07-25 08:59:48','2026-02-10 21:01:21'],
            [11,'Gloria','Espinoza','FEDERAL ELECTRONICS','FEDERAL','SSP860611GM7','','GEspinoza@federalelec.com','6621020704','2018-07-27 16:59:59','2025-12-05 09:21:54',null],
            [12,'PEDRO','APODACA','PASECO SAPI DE CV','PASECO SAPI DE CV','PAS120209P33','','pedro.apodaca@cobredelmayo.com','6623197571','2018-10-23 06:18:39','2018-10-23 06:18:39','2026-02-10 21:01:21'],
            [13,'CARLOS','VALENCIA','CONSULTORIA GEOLOGICA GV SC','CGG GEOLOGICA','CGG1306039Y5','','direccion@cggv.com.mx','6621820236','2019-02-28 13:25:11','2019-02-28 13:25:11','2026-02-10 21:01:21'],
            [14,'Carolina','Velazquez','MINERA COSALA','AMERICAS GOLD','GOAM800129A5A','','cvelazquez@americas-gold.com','6699835050','2019-03-20 05:35:58','2019-03-20 05:35:58',null],
            [15,'JUAN PEDRO','CASTRO','AMBSIL SA DE CV','AMBIENTESSIL','AMB101008NT3','6624297354','Compras@ambientessil.com','6621148914','2019-04-25 15:30:28','2020-08-12 15:53:48','2026-02-10 21:01:22'],
            [16,'Alan','Mercadante','GLS FORWARDING GROUP SA DE CV','GLSGROUP','','','amercadante@glsgroup.com.mx','6621493390','2019-04-26 09:06:02','2019-04-26 09:06:02','2026-02-10 21:01:22'],
            [17,'MARA','IBARRA','QUIRIEGO GOLD','QUIRIEGO','QGO070625RJA','','mara@quiriegogd.com','6622104670','2019-05-24 15:54:43','2019-05-24 15:54:43',null],
            [18,'CRISPIN','FRAUSTO','MOGROUP','MOGROUP','MMM9602224PO','','crispin.frausto@mogroup.com','4626225192','2019-05-29 13:46:06','2021-03-16 12:25:07',null],
            [19,'Adriana','Alvarado','FLSMIDTH MEXICO','FLSMIDTH  MINA','FSM970929MZ4','','Adriana.Alvarado@FLSmidth.com','8113955771','2019-09-05 11:06:58','2019-09-05 11:06:58',null],
            [20,'Fernando','Walters','GLS FORWARDING GROUP SA DE CV','GLSGROUP','GFG161015D4A','','fwalters@glsgroup.com.mx','6622592324','2019-11-04 10:54:01','2019-11-04 10:54:01','2026-02-10 21:01:23'],
            [21,'ERIK','GASTELUM','RADIALL MEXICO','RADIALL OBREGON','SSP860611GM7','','silvia.lara@radiall.com','6444112219','2019-11-04 17:20:52','2022-01-20 17:51:09',null],
            [22,'GUSTAVO','WYLD','BL HARBERT INTERNATIONAL LLC','BL HARBERT','BH160116HQ5','','dayala@glsgroup.com.mx','6624708776','2019-12-16 13:54:45','2025-09-18 11:50:28','2026-02-10 21:01:24'],
            [23,'Katia','Aguilar','FLSMIDTH/ KATIA','FLSMIDTH/ KATIA','','','Katia.Aguilar@FLSmidth.com','8118338209','2019-12-18 06:01:16','2019-12-18 06:01:16','2026-02-10 21:01:24'],
            [24,'EMMANUEL','PONCE','FLSMIDTH / EMMANUEL','FLSMIDTH/  EMMANUEL','','','emmanuel.ponce@flsmidth.com','8110019571','2020-01-28 12:54:28','2020-01-28 12:54:28','2026-02-10 21:01:25'],
            [25,'ESTEFANY','LAM','SIEMENS MFG','SIEMENS','SSP860611GM7','','mcons@siemensmfg.com','6621896297','2020-03-10 15:08:52','2020-03-10 15:08:52',null],
            [26,'ANA PAULA','FIERRO','IDMM MACHINNING','IDMM MACHINNING','SON7405152Y2','','isis.perez@idmm-machining.com','6442044989','2020-03-20 11:25:36','2020-03-20 11:25:36',null],
            [27,'ERIC','RIVERA','SISTEMA DE SELLADO ECOLOGICO S.A. DE C.V','SSEMEX','SSE110221LDA','','svalencia@ssemex.com','6622250936','2020-06-16 23:53:22','2021-02-17 13:46:03','2026-02-10 21:01:25'],
            [28,'STEPHANY','VIZCARRA','FLSMIDTH CEMENT/  FULLER TECHNOLOGIES','FULLER','FCM231102GL0','','Stephany.vizcarra@flsmidth.com','8117424975','2020-06-18 16:11:33','2024-01-08 16:50:13',null],
            [29,'SERGIO','VALENCIA CORONADO','RINHO MINING SOLUTIONS','RINHO MINING','RMS190530J59','','sergio.valencia@rhinoms.com','6622250936','2020-08-03 13:42:35','2020-08-03 13:42:35',null],
            [30,'DIANA PATRICIA','CORONADO','GLS FORWARDING GROUP','GLSGROUP','','','DCORONADO@GLSGROUP.COM.MX','6623772222','2020-08-25 18:31:55','2020-08-25 18:31:55','2026-02-10 21:01:27'],
            [31,'DULCE','RODRIGUEZ','GLS FORWARDING GROUP','GLSGROUP','GFG161015D4A','','logistica@glsgroup.com.mx','6621898831','2020-09-03 17:43:06','2023-05-12 11:33:10','2026-02-10 21:01:27'],
            [32,'ERIKA','ROMERO','BACANORA MINERALS','BACANORA MINERALS','GFG161015D4A','','Erika.romero@bacanoraminerals.net','6621890242','2020-09-11 14:35:15','2020-09-11 14:35:15','2026-02-10 21:01:27'],
            [33,'MARCELA','CAMARENA','MOGROUP / MARCELA','MOGROUP / MARCELA','','','marcela.camarena@mogroup.com','4621079414','2021-01-13 11:53:03','2021-01-13 11:53:03','2026-02-10 21:01:28'],
            [34,'Alejandro','Ibarra','APCO DESARROLLLOS','APCO','','','A.Ibarra@gmpuma.com','6622045216','2021-02-16 09:44:04','2022-06-16 21:27:55','2026-02-10 21:01:28'],
            [35,'JENNIFER','AGUADO','MOGROUP / JENNIFER','MOGROUP / JENNIFER','','','jennifer.aguado@mogroup.com','4621522063','2021-02-16 11:16:48','2021-02-16 11:16:48','2026-02-10 21:01:28'],
            [36,'Diego','Rodriguez','GLS FORWARDING GROUP','GLSGROUP','GFG161015D4A','','sistemas@glsgroup.com.mx','6622592324','2021-02-26 09:23:24','2021-03-03 10:06:35',null],
            [37,'Prueba','Prueba','GLS COMPANY.','GLS COMPANY.','','','diegoisaac.98@gmail.com','6621191726','2021-03-04 09:44:27','2021-03-04 09:44:27','2026-02-10 21:01:29'],
            [38,'LILIANA','VIZACAINO','METSO ELIMINAR','METSO ','','','liliana.vizcaino@metso.com','6622592324','2021-03-05 14:47:42','2025-02-13 11:22:07','2026-02-10 21:01:29'],
            [39,'ANTONIA','GOMEZ','BL HARBERT NOGALES ELIMINAR','BL HARBERT NOGALES','BH160116HQ5','','agomez@blharbert.com','6313044942','2021-04-21 10:16:03','2021-04-21 10:16:03','2026-02-10 21:01:29'],
            [40,'EMMANUEL','AGUIRRE F','APPLIED TECHNICAL SERVICES CORPORATION','APPLIED TECHNICAL SERVICES CORPORATION','','','eaguirre@atscorp.net','6622899700','2021-05-18 16:46:32','2021-05-18 16:46:32','2026-02-10 21:01:29'],
            [41,'GLS','Group','GLS Group','GLS COMPANY.','','','diegoisaac_98@hotmail.com','6621786789','2021-06-21 10:55:16','2021-06-21 10:55:16','2026-02-10 21:01:30'],
            [42,'VICTORIA','RANGEL','FLSMIDTH/ VICTORIA','FLSMIDTH/ VICTORIA','','','Victoria.Rangel@flsmidth.com','8411129967','2021-08-10 15:31:33','2021-08-10 15:31:33','2026-02-10 21:01:30'],
            [43,'LUIS','PIÑEYRO','MOGROUP /  LUIS PIÑEYRO','MOGROUP /  LUIS PIÑEYRO','','','luis.pineyro@mogroup.com','4448588136','2021-08-24 15:47:40','2021-08-24 15:47:40','2026-02-10 21:01:30'],
            [44,'MACARENA','GUERRERO','BYLSA DRILLING','BYLSADRILLING / MACARENA',' BDR111115HE7','','macarenaguerrero@bylsadrilling.com','6629346736','2021-09-07 15:21:16','2023-02-28 15:50:13',null],
            [45,'ELIZABETH','LEAL','FLSMIDTH/ELIZABETH','FLSMIDTH/ELIZABETH','','','Elizabeth.Leal@FLSmidth.com','8181862334','2021-11-02 09:50:23','2021-11-03 10:51:43','2026-02-10 21:01:31'],
            [46,'HUGO','GARCIA','FLSMIDTH/HUGO','FLSMIDTH SA DE CV','FSM970929MZ4','','Hugo.Garcia@flsmidth.com','8119093785','2022-01-05 16:25:52','2022-01-05 16:25:52','2026-02-10 21:01:31'],
            [47,'FAUSTO','HOPKINS','M3 ENGINEERING & TECHNOLOGY CORPORATION','M3 TUCSON','','','Fausto.Hopkins@m3eng.com','5204457338','2022-02-21 13:14:26','2022-02-21 13:15:11','2026-02-10 21:01:31'],
            [48,'MICHELLE','ESPITIA','FLSMIDTH SA DE CV','FLSMIDTH','FSM970929MZ4','','Michelle.Espitia@FLSmidth.com','4495520263','2022-03-15 18:08:45','2022-03-15 18:08:45','2026-02-10 21:01:32'],
            [49,'JOSE LUIS','FELIX','CIPRIA MINERALIA ','CIPRIA','BME110715V45','','jose.felix@cipriamineralia.com','6421520873','2022-03-23 11:04:56','2022-03-23 11:04:56',null],
            [50,'ALAN','MERCADANTE','ELECTRO CONTROLES DEL NOROESTE','ELECTRO CONTROLES (ECN)','ECN910416TV','','alan.mercadante@ecnautomation.com','6622918336','2022-05-26 12:14:08','2022-05-26 12:14:08','2026-02-10 21:01:32'],
            [51,'DIEGO','SALCIDO','STANLEY BLACK AND DECKER','STANLEY BLACK AND DECKER','','','Diego.Salcido@sbdinc.com','6371375319','2022-06-10 08:26:54','2022-06-10 08:26:54','2026-02-10 21:01:33'],
            [52,'Ana Montserrat','Torres','FLSMIDTH / ANA MONTSERRAT','FLSMIDTH','','','Montserrat.Torres@FLSmidth.com','8411129967','2022-06-17 10:21:56','2022-06-29 13:58:59','2026-02-10 21:01:33'],
            [53,'CESAR EDUARDO','JASSO GONZALEZ','FLSMIDTH / CESAR JASSO','FLSMIDTH / CESAR JASSO','','','Cesar.Jasso@FLSmidth.com','2281782029','2022-07-13 12:17:14','2025-06-17 16:02:38','2026-02-10 21:01:33'],
            [54,'Joaquin','Prieto','FIGEAC AERO CHIHUAHUA','FIGEAC AERO CHIHUAHUA','','','Joaquin.prieto@figeac-aero.com','6142780647','2022-09-26 08:27:50','2022-09-26 08:27:50','2026-02-10 21:01:33'],
            [55,'LILIBETH','ICEDO','CREATION TECHNOLOGIES','CTECH','SSP860611GM7','','lilibeth.icedo@creationtech.com','6621898280','2022-10-13 12:40:41','2022-10-13 12:40:41',null],
            [56,'JORGE','COTA','CREATION TECHNOLOGIES / EXPORTACION','CREATION TECHNOLOGIES / EXPORTACION','','','JORGE.COTA@CREATIONTECH.COM','6624601072','2022-10-13 12:42:03','2022-10-13 12:42:03','2026-02-10 21:01:34'],
            [57,'JESSICA','HERNANDEZ','FLSmidth Inc. ELIMINAR','FLSmidth Inc.','','','Jessica.Hernandez@FLSmidth.com','3853022285','2022-12-09 08:55:00','2022-12-09 08:55:00','2026-02-10 21:01:34'],
            [58,'MARICELA','CONS','SIEMENS MFG','SIEMENS MFG','','','mcons@siemensmfg.com','6621896297','2022-12-14 08:47:28','2022-12-14 08:47:28','2026-02-10 21:01:35'],
            [59,'ROBERTO','CASTILLO','METSO','METSO','MMM9602224P0','','juan.castillo@mogroup.com','4626325124','2023-02-28 15:48:52','2023-02-28 15:48:52',null],
            [60,'DAVID','AYALA','AT ENGINE MEXICO','AT ENGINE MEXICO','AEM161014GJ9','','alan.mercadante@atengine.mx','6622918336','2023-04-26 16:04:56','2024-01-09 11:10:23','2026-02-10 21:01:35'],
            [61,'ROBERTO','PROAÑO','MINERALES DE TARACHI','MINERALRES DE TARACHI','MTA110419Q8A','','Roberto@mtarachi.com','6621240911','2023-06-21 13:41:19','2023-06-21 13:41:19',null],
            [62,'SALMA','TRUJILLO','FIGEAC AERO CHIHUAHUA','FIGEAC AERO CHIHUAHUA','','','salma.trujillo@figeac-aero.com','6142220413','2023-09-01 16:20:49','2023-09-01 16:20:49','2026-02-10 21:01:36'],
            [63,'JESSICA','HERNANDEZ','FLSMIDTH SALT LAKE CITY','FLSMIDTH SALT LAKE CITY','230606560','','Jessica.Hernandez@FLSmidth.com.MX','3853022285','2023-10-04 15:14:46','2023-10-04 15:14:46','2026-02-10 21:01:36'],
            [64,'MARISOL','FLORES','FIGEAC AERO CHIHUAHUA','FIGEAC AERO CHIHUAHUA','','','marisol.flores@figeac-aero.com','6143956545','2023-10-11 13:03:27','2023-10-11 13:03:27','2026-02-10 21:01:36'],
            [65,'RICARDO','MARTINEZ GOMEZ','FLSMIDTH MEXICO','FLSMIDTH S.A DE C.V','FSM970929MZ4','','Ricardo.Martinez@FLSmidth.com','8120850701','2023-10-20 17:37:31','2023-10-20 17:37:31','2026-02-10 21:01:36'],
            [66,'CLARISSA','AGUIRRE','FIGEAC AERO CHIHUAHUA','FIGEAC AERO CHIHUAHUA','','','clarissa.aguirre@figeac-aero.com','5216142220413','2023-12-06 11:40:17','2023-12-06 11:40:17','2026-02-10 21:01:37'],
            [67,'CARLOS ALEJANDRO','ROMERO','LATECOERE MEXICO','LMXA','LME120516HJA','','carlos.romero@latecoere.aero','6625220720','2024-01-18 12:41:20','2024-01-18 12:41:20',null],
            [68,'BRUNO','COVARRUBIAS','LATELEC MEXICO','LMXI','AEA120516DA3','','bruno.covarrubias@latecoere.aero','6621007475','2024-01-31 08:02:56','2024-01-31 08:02:56',null],
            [69,'ABIGAIL','KARR','FLSMIDTH CEMENT/  ABIGAIL','FLSMIDTH CEMENT/  ABIGAIL','FCM231102GL0','','abigail.karr@flsmidth.com','8120850736','2024-02-16 14:18:42','2024-02-16 14:18:42','2026-02-10 21:01:38'],
            [70,'OMAR','MUÑOZ','Q3 ELECTROMECANICOS','Q3 ELECTROMECANICOS','QEL141024V2A','','omunoz@q3electromecanicos.com','6623260473','2024-03-06 15:02:40','2024-03-06 15:02:40','2026-02-10 21:01:38'],
            [71,'VANESSA','ALVAREZ','BL HARBERT NOGALES','BL HARBERT NOGALES','EEU930201289','','vsalvarez@blharbert.com','6624708776','2024-03-19 11:47:07','2024-03-19 11:47:07',null],
            [72,'ANDREA','OROZCO','METSO ELIMINAR','METSO','','','andrea.orozco@metso.com','4622511348','2024-03-27 08:28:33','2024-03-27 08:28:33','2026-02-10 21:01:38'],
            [73,'PAIGE','MCALLISTER','ZUST BACHMEIER INC.','ZUST BACHMEIER INC.','26-4427159','','paige.mcallister@zust.com','141053656','2024-04-06 10:06:25','2024-04-06 11:31:59',null],
            [74,'CRISTIAN EMMANUEL','SOSA SOTO','CRISTIAN EMMANUEL SOSA SOTO','CRISTIAN EMMANUEL SOSA SOTO','','','cristian.ess@gmail.com','6621748896','2024-05-14 08:58:35','2024-05-14 08:58:35','2026-02-10 21:01:39'],
            [75,'KIM','SCHANTZ','ATELIER4','ATELIER4','XEXX010101000','','kim.schantz@atelier4.com','8455581556','2024-05-15 13:36:20','2024-05-15 13:36:20',null],
            [76,'VALERIA','AVALOS','FLSMIDTH MEXICO','FLSMIDTH S.A DE C.V','FSM970929MZ4','','juliavaleria.avalos@flsmidth.com','8112761127','2024-06-20 17:38:10','2024-06-20 17:38:10','2026-02-10 21:01:39'],
            [77,'KENIA','MARTINEZ','APRILE MTO MEXICO','APRILE','AMM040927J69','','k.martinez@mx.aprilenet.com','5569700436','2024-06-21 16:50:55','2024-06-21 16:50:55',null],
            [78,'CAROLINA','RUIZ','FLSMIDTH CEMENT','FLSMIDTH CEMENT','FCM231102GL0','','Carolina.Ruiz@flsmidth.com','8120850599','2024-06-28 13:07:20','2024-06-28 13:07:20','2026-02-10 21:01:40'],
            [79,'DAREN','FREEMAN','CORSET STORY','CORSET STORY','','','fernandowalterp95@gmail.com','2058372473','2024-07-18 16:20:03','2024-07-18 16:20:03','2026-02-10 21:01:40'],
            [80,'BETSABE','BARRIENTOS','KYUNGSHIN MEXICO','KYUNGSHIN MEXICO','HAN230404NK5','','betsabe.barrientosob@kyungshin.co.kr','6441348338','2024-08-28 17:43:25','2024-08-28 17:43:25',null],
            [81,'IAN','BOSTON','SAVI PRECISION ENGINEERING','SAVI PRECISION ENGINEERING','SPE180703446','','Ian.w.boston@gmail.com','3464689803','2024-09-02 11:42:36','2024-09-02 11:42:36',null],
            [82,'DANIEL','GUTIERREZ','GE AEROSPACE','GE AEROSPACE','AEM161014GJ9','','daniel.gutierrez@atengine.mx','6624744775','2024-09-02 18:41:33','2024-09-02 18:41:33',null],
            [83,'AZUCENA','V','SISTEMA DE SELLADO ECOLOGICO','SISTEMA DE SELLADO ECOLOGICO','SSE110221LDA','','ventas@ssemex.com','6620000000','2024-10-18 11:40:13','2024-10-18 11:40:13',null],
            [84,'DENISSE','LOPEZ','FLSMIDTH CEMENT','FLSMIDTH CEMENT','FCM231102GL0','','Denisse.Lopez@FLSmidth.com','8182543465','2025-01-09 11:58:55','2025-01-09 11:58:55','2026-02-10 21:01:42'],
            [85,'OSMAREL','BUSTOS','KAESER COMPRESORES','KAESER COMPRESORES','','','osmarel.bustos@kaeser.com','4425211491','2025-03-21 12:39:59','2025-03-21 12:39:59','2026-02-10 21:01:42'],
            [86,'MARIA','ROLÓN SÁNCHEZ','TTI INDUSTRIAL DEL CENTRO','TTI','TTI890413HD4','','maria.rolon@ttiindustrial.com','4921450147','2025-04-16 12:09:04','2025-04-16 12:09:04',null],
            [87,'SARALY','KASTEN','L&H INDUSTRIAL MEXICO','L&H','LSM080208AM0','','SKasten@lnh.net','6453405451','2025-04-28 10:26:42','2025-04-28 10:26:42',null],
            [88,'HILLARI','MONSIVAIS','WATSON MARLOW','WATSON MARLOW','WAT090224EL4','','hillari.monsivais@wmfts.com','6621966751','2025-04-28 14:26:50','2025-04-28 14:26:50',null],
            [89,'REGINA','SALAZAR MENDOZA','VITAL HEALTH GLOBAL','VITAL HEALTH GLOBAL','LRD211122G35','','regina.salazar@vital-health-global.com','6623043673','2025-07-15 09:59:04','2025-07-15 09:59:04',null],
            [90,'DEYVI','LUGO','FLOW GASKETS MEXICO','FLOW GASKETS MEXICO','FRE0307048Q7','','deyvi.lugo@fgm4.com','6624300477','2025-07-30 15:44:20','2025-07-30 15:44:20',null],
            [91,'ZULLY','ESCAMILLA','FLSMIDTH / ZULLY ESCAMILLA','FLSMIDTH / ZULLY ESCAMILLA','','','Zully.Escamilla@FLSmidth.com','8110166346','2025-08-05 08:47:45','2025-08-05 10:04:17','2026-02-10 21:01:44'],
            [92,'ROBERTO','ZEPEDA','MAQUINADOS MILL LATHE DE OBREGON','MYT','MML101213Q32','','rzepeda@myt.mx','6441253213','2025-08-22 13:44:36','2025-08-22 13:44:36',null],
            [93,'Sandra','Montoya','FLSMIDTH/ SANDRA MONTOYA','FLSMIDTH/ SANDRA MONTOYA','','','SandraMaria.Montoya@FLSmidth.com','8120850733','2025-09-10 16:50:46','2025-09-10 16:50:46','2026-02-10 21:01:44'],
            [94,'ROSA LINDA','COVARRUBIAS','FLSMIDTH/ ROSA COVARRUBIAS','FLSMIDTH/ ROSA COVARRUBIAS','','','RosaLinda.Covarrubias@FLSmidth.com','8181751999','2025-09-10 16:51:59','2025-09-11 10:53:03','2026-02-10 21:01:44'],
            [95,'Viviana','Cruz','FLSMIDTH/ VIVIANA CRUZ','FLSMIDTH/ VIVIANA CRUZ','','','Viviana.Cruz@FLSmidth.com','8181751999','2025-09-10 16:58:42','2025-09-10 16:58:42','2026-02-10 21:01:45'],
            [96,'Mauricio','Leal','FLSMIDTH/ MAURICIO LEAL','FLSMIDTH/ MAURICIO LEAL','','','Mauricio.Leal@FLSmidth.com','8181751999','2025-09-10 16:59:20','2025-09-10 16:59:20','2026-02-10 21:01:45'],
            [97,'DAYRA','REYES ARENAS','VITAL HEALTH GLOBAL','VITAL HEALTH GLOBAL','','','dayra.reyes@vital-health-global.com','6623535241','2025-09-26 15:41:32','2025-09-26 15:41:32','2026-02-10 21:01:45'],
            [98,'BRENDA','HERNANDEZ','GROUND EFFECTS','GROUND EFFECTS','GND EFFECTS MX','','bhernandez@gfxltd.com','8445061900','2025-11-26 11:24:44','2025-11-26 11:24:44',null],
            [99,'ADRIAN JONAS','CRUZ','EQUIPOS Y MAQUINARIA EN MOVIMIENTO','EQUIPOS Y MAQUINARIA EN MOVIMIENTO','EMM051021E4A','','activos@estebansanchez.net','6623220198','2025-12-09 14:59:27','2025-12-09 14:59:27',null],
            [100,'ANNA','MORALES','FLSMIDTH CEMENT','FLSMIDTH CEMENT','FCM231102GL0','','anna.morales@flsmidth.com','8123553504','2025-12-11 12:44:28','2025-12-11 12:44:28','2026-02-10 21:01:46'],
            [101,'THOMAS MATHEW','FELIX','CIPRIA MINERALIA S DE RL DE CV','CIPRIA MINERALIA S DE RL DE CV','','','thomas.felix@cobredelmayo.com','6471012944','2025-12-18 14:10:25','2025-12-19 11:31:00','2026-02-10 21:01:46'],
            [102,'CRISTINA','CHÁVEZ','LATELEC ELIMINAR','LATELEC','','','cristina.chavez@latecoere.aero','6623197756','2025-12-18 16:21:48','2025-12-18 16:21:48','2026-02-10 21:01:47'],
            [103,'CRISTAL','MARTINEZ','LATELEC ELIMINAR','LATELEC','','','cristal.martinez@latecoere.aero','6623407038','2026-01-08 18:12:11','2026-01-08 18:12:11','2026-02-10 21:01:47'],
            [104,'MIKE','TERAN','FLSMIDTH INC.','FLSMIDTH SLC','','','Mike.Teran@FLSmidth.com','8114891734','2026-01-16 10:01:24','2026-01-16 10:01:24',null],
            [105,'CLAUDIA FERNANDA','MUNOZ TORRES','FLSMIDTH S.A DE C.V','FLSMIDTH','','','ClaudiaFernanda.Munoz@FLSmidth.com','6621966751','2026-01-24 11:09:24','2026-01-24 11:09:24','2026-02-10 21:01:47'],
            [106,'LUIS','RUIZ','GDL Y RD SPA','GDL Y RD SPA','','','luis.ruiz@gdl-rd.cl','56232520639','2026-02-16 10:54:26','2026-02-16 10:55:11',null]
        ];
        foreach ($oldClients as $oldClient) {
            $client = new Client();
            $client->id = $oldClient[0];
            $client->name = $oldClient[1];
            $client->last_name = $oldClient[2];
            $client->company_name = $oldClient[3];
            $client->trade_name = $oldClient[4];
            $client->federal_tax_id = $oldClient[5];
            $client->national_id = $oldClient[6];
            $client->email = $oldClient[7];
            $client->phone1 = $oldClient[8];
            $client->created_at = $oldClient[9];
            $client->updated_at = $oldClient[10];
            $client->deleted_at = $oldClient[11];
            $client->save();
        }
        $this->command->info("End seeding clients");
    }

    private function seedPetitionCodes(): void
    {
        $oldCodes = [
            [1,'NA','No aplica','No aplica','2025-09-05 08:46:20'],
            [2,'A1','EXPORTACIÓN DEFINITIVA','Salida de mercancías del territorio nacional para permanecer en el extranjero por tiempo ilimitado.','2025-10-08 14:55:56'],
            [3,'A1','IMPORTACIÓN O EXPORTACIÓN DEFINITIVA','IMPORTACIÓN O EXPORTACIÓN DEFINITIVA',null],
            [4,'NA','NA','No aplica / Campo no requerido / No despliega informacion necesaria para la operacion','2025-09-18 18:15:53'],
            [5,'V1','TRANSFERENCIAS DE MERCANCÍAS','IMPORTACIÓN TEMPORAL VIRTUAL; INTRODUCCIÓN VIRTUAL A DEPOSITO FISCAL O A RECINTO FISCALIZADO ESTRATÉGICO; RETORNO VIRTUAL; EXPORTACIÓN VIRTUAL DE PROVEEDORES NACIONALES.',null],
            [6,'A3','REGULARIZACIÓN DE MERCANCÍAS (IMPORTACIÓN DEFINITIVA).','REGULARIZACIÓN DE MERCANCÍAS (IMPORTACIÓN DEFINITIVA).',null],
            [7,'T1','IMPORTACIÓN Y EXPORTACIÓN POR EMPRESAS DE MENSAJERÍA Y PAQUETERÍA.','IMPORTACIÓN Y EXPORTACIÓN POR EMPRESAS DE MENSAJERÍA Y PAQUETERÍA.',null],
            [8,'G9','TRANSFERENCIA DE MERCANCÍAS DE RECINTO FISCALIZADO ESTRATÉGICO','TRANSFERENCIA DE MERCANCÍAS DE RECINTO FISCALIZADO ESTRATÉGICO NO COLINDANTE CON LA ADUANA (RETIRO VIRTUAL PARA IMPORTACIÓN DEFINITIVA POR RESIDENTES EN TERRITORIO NACIONAL).',null],
            [9,'BO','EXPORTACIÓN TEMPORAL PARA REPARACIÓN O SUSTITUCIÓN Y RETORNO AL PAÍS',"'EXPORTACIÓN TEMPORAL PARA REPARACIÓN O SUSTITUCIÓN Y RETORNO AL PAÍS (IMMEX, RFE U OPERADOR ECONOMICO AUTORIZADO'",null],
            [10,'H1','RETORNO DE MERCANCÍAS EN SU MISMO ESTADO.','RETORNO DE MERCANCÍAS EN SU MISMO ESTADO.',null],
            [11,'F4','CAMBIO DE RÉGIMEN DE INSUMOS O DE MERCANCÍA EXPORTADA TEMPORALMENTE.','CAMBIO DE RÉGIMEN DE INSUMOS O DE MERCANCÍA EXPORTADA TEMPORALMENTE. (MATERIA PRIMA)',null],
            [12,'F5','CAMBIO DE RÉGIMEN DE MERCANCÍAS DE IMPORTACIÓN TEMPORAL A DEFINITIVA.','CAMBIO DE RÉGIMEN DE MERCANCÍAS DE IMPORTACIÓN TEMPORAL A DEFINITIVA. (ACTIVO FIJO)',null],
            [13,'IN','IMPORTACIÓN TEMPORAL DE MATERIA PRIMA (IMMEX).',"'IMPORTACIÓN TEMPORAL DE BIENES QUE SERÁN SUJETOS A TRANSFORMACIÓN, ELABORACIÓN O REPARACIÓN (IMMEX). MATERIA PRIMA'",null],
            [14,'AF','IMPORTACIÓN TEMPORAL DE ACTIVO FIJO (IMMEX).','IMPORTACIÓN TEMPORAL DE BIENES DE ACTIVO FIJO (IMMEX). ACTIVO FIJO',null],
            [15,'RT','RETORNO DE MERCANCÍAS (IMMEX).','RETORNO DE MERCANCÍAS (IMMEX).',null],
            [16,'J4','RETORNO DE MERCANCÍAS EXTRANJERAS (RFE).','RETORNO DE MERCANCÍAS EXTRANJERAS (RFE).',null],
            [17,'M4','INTRODUCCIÓN DE ACTIVO FIJO (RFE).','INTRODUCCIÓN DE ACTIVO FIJO (RFE).',null],
            [18,'M5','INTRODUCCIÓN DE MERCANCÍA NACIONAL O NACIONALIZADA (RFE).','INTRODUCCIÓN DE MERCANCÍA NACIONAL O NACIONALIZADA (RFE).',null],
            [19,'R1','RECTIFICACIÓN DE PEDIMENTOS.','RECTIFICACIÓN DE PEDIMENTOS.',null]
        ];
        $this->command->info("Seeding petition codes");
        foreach ($oldCodes as $oldCode) {
            $country = new PetitionCode();
            $country->id = $oldCode[0];
            $country->code = $oldCode[1];
            $country->description = $oldCode[2];
            $country->application_assumptions = $oldCode[3];
            $country->deleted_at = $oldCode[4];
            $country->save();
        }
        $this->command->info("End petition codes");
    }

    private function seedWarehouses(): void
    {
        $this->command->info("Seeding warehouses");
        $oldWarehouses = [
            [1,'GLSGROUP WAREHOUSE HERMOSILLO','FERNANDO WALTERS','6624674936','2026-02-03 00:00:00'],
            [2,'GLSGROUP WAREHOUSE NOGALES','FERNANDO WALTERS','6621966751','2026-02-03 00:00:00'],
            [3,'GLSGROUP WAREHOUSE MANZANILLO','FERNANDO WALTERS','6621898831','2026-02-03 00:00:00'],
            [4,'GLSGROUP WAREHOUSE VERACRUZ','FERNANDO WALTERS','6621898831','2026-02-03 00:00:00'],
            [5,'GLSGROUP WAREHOUSE ALTAMIRA','FERNANDO WALTERS','6621898831','2026-02-03 00:00:00'],
            [6,'WALTERSWAREHOUSE(WW)','VICENTE FERNANDEZ','123456789','2026-02-16 17:43:29'],
            [7,'GLS GROUP ALMACEN / HERMOSILLO','FERNANDO ESCOBAR','662 104 8839',null],
            [8,'GLS GROUP ALMACEN / MANZANILLO','FERNANDO WALTERS','662 196 6751',null],
            [9,'GLS GROUP ALMACEN / ALTAMIRA','FERNANDO WALTERS','662 196 6751',null],
            [10,'GLS GROUP ALMACEN / VERACRUZ','FERNANDO WALTERS','662 196 6751',null],
            [11,'GLS GROUP ALMACEN / NOGALES','FERNANDO WALTERS','662 196 6751',null],
            [12,'GLS GROUP ALMACEN / NUEVO LAREDO','FERNANDO WALTERS','662 196 6751',null],
            [13,'GLS GROUP WAREHOUSE / NOGALES','FERNANDO WALTERS','662 196 6751',null],
            [14,'GLS GROUP WAREHOUSE / LAREDO','FERNANDO WALTERS','662 196 6751',null]
        ];
        foreach ($oldWarehouses as $oldWarehouse) {
            $warehouse = new Warehouse();
            $warehouse->id = $oldWarehouse[0];
            $warehouse->name = $oldWarehouse[1];
            $warehouse->contact_name = $oldWarehouse[2];
            $warehouse->phone1 = $oldWarehouse[3];
            $warehouse->deleted_at = $oldWarehouse[4];
            $warehouse->save();
        }
        $this->command->info("End seeding warehouses");
    }

    private function seedTransportAgencies(): void
    {
        $this->command->info("Seeding transport agencies");
        $oldTransportations = [
            [1,'GLSGROUP','JOSE IBARRA','6622592324',null,'4005','GFGS','2026-01-31 13:17:37',null,'GLSGROUP'],
            [2,'GLSGROUP-D','Dulce Rodriguez','6622592324',null,null,null,'2026-01-31 13:17:43',null,'GLSGROUP-D'],
            [3,'WaltersTransport','fer','5202754651','123456789','6545','wowo','2026-01-22 16:35:39',null,'WaltersTransport'],
            [4,'ALFONSO SANCHEZ','Teresa Sanchez','631 181 9409',null,'10NG','TXOS',null,null,'TRANSPORTES AS'],
            [5,'TRANSPORTES ESPECIALIZADOS BORTONI','GERARDO BORTONI','866 112 3096','866 136 3509','3TCW',null,null,'TEB970730EL3','TEBSA'],
            [6,'OSCAR ALEJANDRO MUÑOZ RESENDEZ','GUADALUPE RESENDEZ','867 987 9324','(956) 679-0126',null,null,null,'MURO9202142J7','TRANSPORTES RESENDEZ'],
            [7,'TRANSPORTES GAR GOM DEL NOROESTE','CAROLINA GARGIA','662 257 1505','662 193 0116','2YUH',null,null,'TGG001127B55','GARGOM'],
            [8,'FRANCISCO CARLOS VERDUGO DOUMERC','FRANCISCO VERDUGO','631 110 0384',null,'2V9S','RVZE',null,'VEDF861211LM1','TDV USA'],
            [9,'EXPRESS CASA BLANCA DE NUEVO LAREDO','ERNESTO CASABLANCA','867 729 0961','867 727 0191','32Q0','ECBN',null,'ECB160929K70','CASABLANCA'],
            [10,'VANESSA ECHEAGARAY ARMENTA','VIANEY GAYTÁN','314 103 8190','314 149 230','3HVC',null,null,'EEAV840213JL0','GO GROUP'],
            [11,'TEODORO CARLOS BARRON MARTINEZ','Nemecio Hernández','833 231 9932','833 683 6664','3FKX',null,null,'BAMT810605V24','TRADE LOGISTICS'],
            [12,'.','.','.',null,null,null,'2026-01-30 16:38:53',null,'TRANSPORTES ASNT'],
            [13,'TRANSPORTES ASNT','NOEL TEJEDA','631 181 9409','631 944 2089','3YOC','TLZW',null,'TAS240322373','TRANSPORTES ASNT'],
            [14,'OCTAVIO ANDRADE CORELLA','OMAR OROZCO LEYVA','631 157 2252','631 124 5313','0353','ANDC',null,'AACO561221ATA','TRANSPORTES OA'],
            [15,'AUTOTRANSPORTES DE CARGA TRESGUERRAS','LITHAY ROMERO','662 290 7808',null,null,null,null,'ACT6808066SA','TRESGUERRAS'],
            [16,'MAERSK MEXICO','Servicio al Cliente','5571000230',null,null,null,null,'DK53139655','MAERSK'],
            [17,'ALFONSO SANCHEZ','Teresa Sanchez','631 181 9409',null,'10NG','TXOS','2026-02-27 08:15:29',null,'TRANSPORTES AS'],
            [18,'GLS DELIVERY EXPRESS','FERNANDO WALTERS','6621966751',null,null,null,null,'GFG161015D4A','GLS EXPRESS']
        ];
        foreach ($oldTransportations as $oldTransportation) {
            $transportation = new TransportationAgency();
            $transportation->id = $oldTransportation[0];
            $transportation->name = $oldTransportation[1];
            $transportation->contact_name = $oldTransportation[2];
            $transportation->phone1 = $oldTransportation[3];
            $transportation->phone2 = $oldTransportation[4];
            $transportation->caat_code = $oldTransportation[5];
            $transportation->scac_code = $oldTransportation[6];
            $transportation->deleted_at = $oldTransportation[7];
            $transportation->rfc = $oldTransportation[8];
            $transportation->company_name = $oldTransportation[9];
            $transportation->save();
        }
        $this->command->info("End transport agencies");
    }

    private function seedVehicles(): void
    {
        $this->command->info("Seeding vehicles");
        $oldVehicles = [
            [1,'29','123456','VAN','123456789QWE','MEXICO',3,'2026-01-22 12:19:52','DODGE RAM','2024'],
            [2,'T01','84AR3E','RABON','3BKHHY8X5MF320990','MEXICANA',3,null,'KENWORTH','2021'],
            [3,'T01','84AR3E','RABON','3BKHHY8X5MF320990','MEXICANA',4,null,'KENWORTH','2021'],
            [4,'T02','08AX3G','RABON','3HAMMMMN0GL228365','MEXICANA',4,null,'INTERNATIONAL','2016'],
            [5,'T03','05AK2C','RABON','1GDJ6C1355F514181','MEXICANA',4,null,'GMC','2005'],
            [6,'T04','618SU6','TONELADA','1GBJG31U531141605','MEXICANA',4,null,'CHEVROLET','2003'],
            [7,'T05','38EM1C','RABON','4GTJ6F1304F700326','MEXICANA',4,null,'ISUZU','2004'],
            [8,'T06','99AD4A','RABON','1FVACWDC87HY45648','MEXICANA',4,null,'FREIGHTLINER','2007'],
            [9,'T07','24AG3K','RABON','1HTMMAAN68H575337','MEXICANA',4,null,'INTERNATIONAL','2008'],
            [10,'T08','63EP9T','RABON','1HTMMAAM06H196760','MEXICANA',4,null,'INTERNATIONAL','2006'],
            [11,'T09','VC45207','VAN','1GCEG15X731232509','MEXICANA',4,null,'CHEVROLET','2003'],
            [12,'T010','VD42093','ESTACAS','3N6CD15SX4K128484','MEXICANA',4,null,'NISSAN','2004'],
            [13,'T11','19AX1G','TONELADA','1FDWF3G66KEE70149','MEXICANA',4,null,'FORD','2019'],
            [14,'T12','83AM2G','RABON','3BKHHY8X5MF320603','MEXICANA',4,null,'KENWORTH','2021'],
            [15,'T100','VD84565','VAN','1GCGG25C681199814','MEXICANA',4,null,null,'2008'],
            [16,'T101','VD83895','VAN','1GCGG25C281134765','MEXICANA',4,null,null,'2008'],
            [17,'T102','VD84562','TORNADO','93CCM80C3JB133664','MEXICANA',4,null,null,'2018'],
            [18,'T103','VXE111A','VAN','1GCZGGBA3A1169636','MEXICANA',4,null,null,'2010'],
            [19,'T200','88AM2G','TRAILER','1FUJGLDRXBSAR4496','MEXICANA',4,null,null,'2011'],
            [20,'T201','56AL5U','TRAILER','1FUJGLDRXBSAR4497','MEXICANA',4,null,null,'2011'],
            [21,'T202','82AL5U','TRAILER','4V4NC9TH3BN282339','MEXICANA',4,null,null,'2011'],
            [22,'T203','69AR3E','TRAILER','1FUJGLDR4DSBT3523','MEXICANA',4,null,null,'2013'],
            [23,'T204','42EP1E','TRAILER','3AKJGLBG7ESFJ1206','MEXICANA',4,null,null,'2014'],
            [24,'T13','49AN6V','TONELADA','1FDUF5GTXNEC74163','MEXICANA',4,null,'FORD F550',null],
            [25,'T104','VF00820','VAN','WF0RS5HP0MTD06006','MEXICANA',4,null,'FORD',null],
            [26,'T115','UV9312A','VAN','LZWNNNGM8PC821379','MEXICANA',4,null,'FORD',null],
            [27,'T14','72AX3G','RABON REFRIGERADO','1HTMMML6GH2815692','MEXICANA',4,null,null,'2016'],
            [28,'T16','VTT230A','TOYOTA COROLLA',null,'MEXICANA',4,null,'TOYOTA COROLLA','2018'],
            [29,'T15','50AX6F','RABÓN','3ALACWDT9GDHS8895','MEXICANA',4,null,'FREIGHTLINER M2016','2016'],
            [30,'T17','VC4339A','VAN',null,'MEXICANA',4,null,'FORD TRANSIT','2025'],
            [31,'T18','VD9654A','VAN',null,'MEXICANA',4,null,'FORD TRANSIT','2025'],
            [32,'T19','30BH9F','TONELADA',null,'MEXICANA',4,null,null,null],
            [33,'T20','VF4062A','VAN','1FTBW3XVXGKB10112','MEXICANA',4,null,'FORD TRANSIT','2016'],
            [34,'T21','VF4119A','VAN','WV1DAASK1RX080406','MEXICANA',4,null,'CADDY','2024'],
            [35,'11','VE13360','VAN','VF37R9HFXJJ529054',null,7,null,'PEUGEOT','2018'],
            [36,'0 9','VE02911','VAN','VF37H9HE5HJ703861',null,7,null,'PEUGEOT','2017'],
            [37,'TC-03','UN7371A','ELAM','3EP2CD2B3NE000345	BG04255059',null,7,null,null,'2022'],
            [38,'48102','294WN1','PLATAFORMA',null,null,8,null,null,null],
            [39,'48108','28106F','PLATAFORMA',null,null,8,null,null,null],
            [40,'48103','52TY7M','PLATAFORMA TIPO Z',null,null,8,null,null,null],
            [41,'T40','775DU8','PLATAFORMA TIPO Z TRACTOR','3WKAD60X5YF506513',null,8,null,'KENWORTH','2000'],
            [42,'22','842UC3','PLATAFORMA 48',null,null,8,null,null,null],
            [43,'C5','843UC3','PLATAFORMA 45',null,null,8,null,null,null],
            [44,'1225','72UW7L','PLATAFORMA 53',null,null,9,null,null,null],
            [45,'1229','01UD8R','PLATAFORMA 48',null,null,9,null,null,null],
            [46,'F461','Y77952','PLATAFORMA 53',null,null,9,null,null,null],
            [47,'1203','03UD8R','PLATAFORMA 48',null,null,9,null,null,null],
            [48,'F461','Y77952','PLATAFORMA 53',null,null,9,null,null,null],
            [49,'1203','Y77952','PLATAFORMA 53',null,null,9,null,null,null],
            [50,'1205','75UW7L','PLATAFORMA 53',null,null,9,null,null,null],
            [51,'1210','84UD8R','PLATAFORMA 48',null,null,9,null,null,null],
            [52,'1204','99UC9S','PLATAFORMA 48',null,null,9,null,null,null],
            [53,'1215','120B576','PLATAFORMA 53',null,null,9,null,null,null],
            [54,'1115','69UW7L','CAMA BAJA',null,null,9,null,null,null],
            [55,'BM151','242399H','PLATAFORMA 53',null,null,9,null,null,null],
            [56,'1215','74UW7L','PLATAFORMA 53',null,null,9,null,null,null],
            [57,'BM119','W65211','PLATAFORMA 53',null,null,9,null,null,null],
            [58,'TC-174','45AN7L',null,null,null,11,null,null,null],
            [59,'.','FH2126A',null,null,null,10,null,null,null],
            [60,'.','58BA6K',null,null,null,10,null,null,null],
            [61,'.','FG6341A',null,null,null,10,null,null,null],
            [62,'.','80BF7E',null,null,null,10,null,null,null],
            [63,'.','48AP9D',null,null,null,10,null,null,null],
            [64,'.','FE58612',null,null,null,10,null,null,null],
            [65,'.','FE-7109-A',null,null,null,10,null,null,null],
            [66,'.','FE-2921-A',null,null,null,10,null,null,null],
            [67,'.','FF6735A',null,null,null,10,null,null,null],
            [68,'.','FH2722A',null,null,null,10,null,null,null],
            [69,'.','14AX3Y',null,null,null,10,null,null,null],
            [70,'.','03BK8D',null,null,null,10,null,null,null],
            [71,'.','FH82207',null,null,null,10,null,null,null],
            [72,'.','FG7448A',null,null,null,10,null,null,null],
            [73,'.','FH8313A',null,null,null,10,null,null,null],
            [74,'.','FH8472A',null,null,null,10,null,null,null],
            [75,'.','191AS2',null,null,null,10,null,null,null],
            [76,'.','FH8551A',null,null,null,10,null,null,null],
            [77,'.','FF-84142',null,null,null,10,null,null,null],
            [78,'.','14AX3Y',null,null,null,10,'2026-01-30 13:39:59',null,null],
            [79,'.','67AP7M',null,null,null,10,null,null,null],
            [80,'.','41BL8K',null,null,null,10,null,null,null],
            [81,'.','96BK5H',null,null,null,10,null,null,null],
            [82,'.','82-BK-6N',null,null,null,10,null,null,null],
            [83,'.','96BK5H',null,null,null,10,'2026-01-30 13:38:49',null,null],
            [84,'.','FG7448A',null,null,null,10,'2026-01-30 13:40:13',null,null],
            [85,'.','57AW8X',null,null,null,11,null,null,null],
            [86,'.','K94AVY',null,null,null,11,null,null,null],
            [87,'.','60AP7W',null,null,null,11,null,null,null],
            [88,'.','K94AVY',null,null,null,11,null,null,null],
            [89,'.','88AY2X',null,null,null,11,null,null,null],
            [90,'BD39','768WN1','CAJA REFRIGERADA','1UYVS2533SU432707','MEXICANA',13,null,null,'1995'],
            [91,'BD40','767WN1','CAJA REFRIGERADA','1PT01ANHXW9001016','MEXICANA',13,null,null,'1998'],
            [92,'534988','T1A4BFA','CAJA SECA','1GRAA06225J609768','AMERICANA',13,null,null,'2005'],
            [93,'T5301','04353J','CAJA SECA','1UYVS25317P105406','AMERICANA',13,null,null,'2006'],
            [94,'T5304','ZEA7AHA','CAJA SECA','1L01A532951156133','AMERICANA',13,null,null,'2004'],
            [95,'BD42','130XA3','CAJA REFRIGERADA','1UYVS2538RU129013','MEXICANA',13,null,null,'1994'],
            [96,'BD42','130XA3','CAJA REFRIGERADA','1UYVS2538RU129013','MEXICANA',13,'2026-01-30 17:22:24',null,'1994'],
            [97,'T5312','D0A1D2A','CAJA SECA','1L01A532XY1147025','AMERICANA',13,null,null,'2000'],
            [98,'T5302','PTA01Y','CAJA SECA','3H3V532C24T147044','AMERICANA',13,null,null,'2003'],
            [99,'T5306','01A5WH','CAJA SECA','1JJV532W9XL523964','AMERICANA',13,null,null,'1999'],
            [100,'T5305','JWA7CC','CAJA SECA','T5305 1JJV532W05L954271','AMERICANA',13,null,null,'2005'],
            [101,'T5310','XVA5PF','CAJA SECA',null,'AMERICANA',13,null,null,null],
            [102,'T5308','XRA5PF','CAJA SECA','1UYVS25325P525902','AMERICANA',13,null,null,'2005'],
            [103,'T5303','T1A4BFA','CAJA SECA','1GRAA06225J609768','AMERICANA',13,null,null,'2005'],
            [104,'TR5307','9WA0SD','CAJA REFRIGERADA','1GRAA06256W700702','AMERICANA',13,null,null,'2006'],
            [105,'T5309','XTA2PF','CAJA SECA','1GRAA0623XS018832','AMERICANA',13,null,null,'1999'],
            [106,'T5311','WPA6XH','CAJA SECA','1JJV532W35L954278','AMERICANA',13,null,null,'2005'],
            [107,'TR5313','CFA52L','CAJA REFRIGERADA',null,'AMERICANA',13,null,null,null],
            [108,'T5315','P2A51Y','CAJA SECA',null,'AMERICANA',13,null,null,null],
            [109,'T5316','P0A71Y','CAJA SECA 53 FT',null,'AMERICANA',13,null,null,null],
            [110,'1154','2946159','CAJA SECA','1UYVS2539H3983416','AMERICANA',13,null,null,'2016'],
            [111,'10309','0TA1RW','CAJA SECA',null,'AMERICANA',13,null,null,null],
            [112,'TR5318','PBA21Y','CAJA REFRIGERADA',null,'AMERICANA',13,null,null,null],
            [113,'T5319','40UF1M','CAJA SECA',null,'MEXICANA',13,null,null,null],
            [114,'BD51','94UA6D','CAJA REFRIGERADA',null,'MEXICANA',13,null,null,'1994'],
            [115,'TR5317','PWA41Y','CAJA REFRIGERADA',null,'AMERICANA',13,null,null,null],
            [116,'T4814','PYA01Y','PLATAFORMA 48 FT',null,'AMERICANA',13,null,null,null],
            [117,'OAP5319','90755F','PLATAFORMA 48´',null,null,14,null,null,null],
            [118,'OAP03','010WA8','PLATAFORMA',null,null,14,null,null,null],
            [119,'OAP13','83868D','PLATAFORMA 48 FT',null,null,14,null,null,null],
            [120,'FG7373A','FG7373A','PICK UP',null,null,10,null,null,null],
            [121,'LTL','GLST-123','LTL',null,null,15,null,null,null],
            [122,'29','VE28435','VAN (SMALL)','VF37R9HF7KJ52489',null,18,null,'PEUGEOT - PARTNER','2018'],
            [123,'17','UM3776A','PICKUP-PLATAFORMA','3C7WRAKT5NG159261',null,18,null,'RAM - 2500','2022'],
            [124,'12','VC6701A','VAN (LARGE)','WF0RS5HF9RZM53756',null,18,null,'FORD-TRANSIT','2024']
        ];
        foreach ($oldVehicles as $oldVehicle) {
            $vehicle = new Vehicle();
            $vehicle->id = $oldVehicle[0];
            $vehicle->eco_number = $oldVehicle[1];
            $vehicle->plates = $oldVehicle[2];
            $vehicle->vehicle_type = $oldVehicle[3];
            $vehicle->serial_number = $oldVehicle[4];
            $vehicle->origin = $oldVehicle[5];
            $vehicle->transportation_agency_id = $oldVehicle[6];
            $vehicle->deleted_at = $oldVehicle[7];
            $vehicle->vehicle_brand = $oldVehicle[8];
            $vehicle->vehicle_model = $oldVehicle[9];
            $vehicle->save();
        }
        $this->command->info("End vehicles");
    }

    private function seedIncoterm(): void
    {
        $this->command->info("Seeding incoterms");
        $oldIncoterms = DB::connection('litsold')->table('incoterm')->get();
        foreach ($oldIncoterms as $oldIncoterm) {
            $incoterm = new Incoterm();
            $incoterm->id = $oldIncoterm->id;
            $incoterm->code = $oldIncoterm->incoterm;
            $incoterm->name = $oldIncoterm->descripcion;
            $incoterm->created_at = $oldIncoterm->created_at;
            $incoterm->updated_at = $oldIncoterm->updated_at;
            $incoterm->save();
        }
        $this->command->info("End seeding incoterms");
    }

    private function seedCustoms(): void
    {
        $this->command->info("Seeding customs");
        $oldCustoms = [
            [1,'230','0','NOGALES, SONORA.',1933,26,156,null],
            [2,'010','0','ACAPULCO, ACAPULCO DE JUÁREZ, GUERRERO.',367,12,156,null],
            [3,'232','2','AEROPUERTO INTERNACIONAL GENERAL IGNACIO PESQUEIRA GARCÍA, HERMOSILLO, SONORA.',1920,26,156,null],
            [4,'430','0','VERACRUZ, VERACRUZ.',2275,30,156,null],
            [5,'160','0','MANZANILLO, COLIMA.',77,6,156,null],
            [6,'400','0','TIJUANA, BAJA CALIFORNIA.',15,2,156,null],
            [7,'201','1','dagvfaesf',1920,26,156,'2026-01-28 16:14:22'],
            [8,'480','0','GUADALAJARA, TLAJOMULCO DE ZÚÑIGA, JALISCO.',628,14,156,null],
            [9,'850','0','AEROPUERTO INTERNACIONAL FELIPE ÁNGELES, SANTA LUCÍA, ZUMPANGO, ESTADO DE MÉXICO.',2551,15,156,null],
            [10,'110','0','ENSENADA, BAJA CALIFORNIA.',12,2,156,null],
            [11,'120','0','GUAYMAS, SONORA.',1919,26,156,null],
            [12,'240','0','NUEVO LAREDO, TAMAULIPAS.',2006,28,156,null],
            [13,'810','0','ALTAMIRA, TAMAULIPAS.',1982,28,156,null],
            [14,'510','0','LÁZARO CÁRDENAS, MICHOACÁN.',833,16,156,null],
            [15,'800','0','COLOMBIA, NUEVO LEÓN.',952,19,156,null],
            [16,'402','2','AEROPUERTO INTERNACIONAL DENOMINADO ABELARDO L. RODRÍGUEZ, TIJUANA, BAJA CALIFORNIA.',15,2,156,null],
            [17,'470','0','AEROPUERTO INTERNACIONAL DE LA CIUDAD DE MÉXICO.',2551,15,156,null],
            [18,'2304','0','LAREDO, TEXAS, ESTADOS UNIDOS',2461,77,64,null],
            [19,'2604',null,'NOGALES, ARIZONA',2459,35,64,null]
        ];
        foreach ($oldCustoms as $oldCustom) {
            $custom = new Custom();
            $custom->id = $oldCustom[0];
            $custom->code = $oldCustom[1];
            $custom->section = $oldCustom[2];
            $custom->denomination = $oldCustom[3];
            $custom->city_id = $oldCustom[4];
            $custom->state_id = $oldCustom[5];
            $custom->country_id = $oldCustom[6];
            $custom->deleted_at = $oldCustom[7];
            $custom->save();
        }
        $this->command->info("End customs");
    }

    private function seedCustomAgents(): void
    {
        $this->command->info("Seeding custom agents");
        $oldCustomsAgents = [
            [1,'MAGALY VERONICA','CAVAZOS REJON','1550','6622592324',null,'CAVAZOS REJON'],
            [2,'JESUS ENRIQUE','VALVERDE CAZARES','3644','6622592324',null,'VALVERDE CAZARES'],
            [3,'CARLOS ALBERTO','RAMIREZ GONZALEZ','3452','5580371705','2026-01-30 18:12:12',null],
            [4,'CARLOS ALBERTO','RAMIREZ GONZALEZ','3452','5580371705','2026-01-30 18:12:03',null],
            [5,'CARLOS ALBERTO','RAMIREZ GONZALEZ','3452','5580371705','2026-01-30 18:12:07',null],
            [6,'CARLOS ALBERTO','RAMIREZ GONZALEZ','3452','5580371705',null,'CAVAZOS REJON'],
            [7,'NOEMI ARELI','MALDONADO SANTOS','1721','5580371705',null,'CAVAZOS REJON'],
            [8,'JONATHAN','DIAZ CASTRO','3259','5580371705',null,'EXIMIN'],
            [9,'ANDRES','RUFFO DE ALBA','3930','6646475990',null,'RUFFO DE ALBA'],
            [10,'MANUEL ANTONIO','CARRILLO GONZALEZ','3351','5580371705',null,'CAVAZOS REJON'],
            [11,'ALEJANDRO','CHAPELA COTA','3886','5580371705',null,'CAVAZOS REJON'],
            [12,'FORTINO','FERNÁNDEZ ESPINOSA','3065','5580371705',null,'CAVAZOS REJON'],
            [13,'MIGUEL ROBLES','ARENAS RAMIREZ','1678','6221090856',null,'SEGROVE'],
            [14,'ORLANDO MURRIETA','MURRIETA','8XP','15209408381',null,'OEM BROKERAGE & TRADE']
        ];
        foreach ($oldCustomsAgents as $oldCustomAgent) {
            $customAgent = new CustomAgent();
            $customAgent->id = $oldCustomAgent[0];
            $customAgent->name = $oldCustomAgent[1];
            $customAgent->last_name = $oldCustomAgent[2];
            $customAgent->patent = $oldCustomAgent[3];
            $customAgent->phone1 = $oldCustomAgent[4];
            $customAgent->deleted_at = $oldCustomAgent[5];
            $customAgent->company_name = $oldCustomAgent[6];
            $customAgent->save();
        }
        $this->command->info("End custom agents");
    }

    private function seedCustomAgentAddresses(): void
    {
        $this->command->info("Seeding custom agent addresses");
        $oldCustomsAgents = [
            [1,1,0,'AV 615','4','U.H SAN JUAN DE ARAGON','83000',269,9,156],
            [12,2,0,null,null,null,null,1933,26,156],
            [13,3,0,'AQUILES SERDAN PRIMER PISO DESPACHO 101 Y102','690','CENTRO','91700',2275,30,156],
            [14,4,0,'AQUILES SERDAN PRIMER PISO DESPACHO 101 Y102','690','CENTRO','91700',2275,30,156],
            [15,5,0,'AQUILES SERDAN PRIMER PISO DESPACHO 101 Y102','690','CENTRO','91700',2275,30,156],
            [16,6,0,'AQUILES SERDAN PRIMER PISO DESPACHO 101 Y102','690','CENTRO','91700',2275,30,156],
            [17,7,0,'AV 615','4','U.H SAN JUAN DE ARAGON','83000',269,9,156],
            [18,8,0,"'AV ÁLVARO OBREGON , PARQUE INDUSTRIAL'",'5100-A','PARQUE INDUSTRIAL','83455',1945,26,156],
            [19,9,0,'FRAY MAYORGA 1000','1','CARITA DE OTAY','22430',15,2,156],
            [20,10,0,'AV 615','4','U.H SAN JUAN DE ARAGON','83000',269,9,156],
            [21,11,0,'AV 615','4','U.H SAN JUAN DE ARAGON','83000',269,9,156],
            [22,12,0,'AV 615','4','U.H SAN JUAN DE ARAGON','83000',269,9,156],
            [23,13,0,'AV JULIO RAMON LUBBERT SELDNER','15-E','CENTRO','85400',1919,26,156],
            [24,14,0,'Camino Fuste Sahuarita','165 W','NA','85629',2472,35,64]
        ];
        foreach ($oldCustomsAgents as $oldCustomAgent) {
            $customAgent = new CustomAgentAddress();
            $customAgent->id = $oldCustomAgent[0];
            $customAgent->custom_agent_id = $oldCustomAgent[1];
            $customAgent->address_type = $oldCustomAgent[2];
            $customAgent->street_name = $oldCustomAgent[3];
            $customAgent->street_no = $oldCustomAgent[4];
            $customAgent->neighborhood = $oldCustomAgent[5];
            $customAgent->postal_code = $oldCustomAgent[6];
            $customAgent->city_id = $oldCustomAgent[7];
            $customAgent->state_id = $oldCustomAgent[8];
            $customAgent->country_id = $oldCustomAgent[9];
            $customAgent->save();
        }
        $this->command->info("End custom agent addresses");
    }

    private function seedAddresses(): void
    {
        $this->command->info("Seeding addresses");
        $oldAddresses = [
            [9,'ALMACEN','CAPIN VYBORNY',null,null,null,'949 W BELL RD B',null,'85621',2459,35,64,'CAPIN'],
            [10,'ALMACEN','COLLECTRON INTERNATIONAL',null,null,null,'235 N FREEPORT DR',null,'85621',2459,35,64,'COLLECTRON'],
            [11,'LILIBETH ICEDO','CREATION TECHNOLOGIES',null,'lilibeth.icedo@creationtech.com',null,"'CALLE MERIDIANO, LATITUD ORIENTE 1'",null,'83175',1920,26,156,'CREATION TECH'],
            [12,'ANGEL BALLEZA','SERVICIOS LOGISTICOS CUESTA',null,'aballeza@slc.mx',null,'INTERNACIONAL 294','PARQUE INDUSTRIAL MULTIPARK','66633',953,19,156,'CUESTA'],
            [13,'ALMACEN','CAPIN VYBORNY (ENERGY)',null,null,null,'1480 N INDUSTRIAL PARK DR',null,'85621',2459,35,64,'ENERGY'],
            [14,'ALMACEN','FEDERAL ELECTRONICS INC',null,null,null,'OBRERO MUNDIAL 15','SAHUARO','83210',1920,26,156,'FEDERAL'],
            [15,'RECIBO','FEDEX NOGALES AZ',null,null,null,'1319 N INDUSTRIAL PARK DR','-','85621',2459,35,64,'FEDEX NOGALES AZ'],
            [16,'ALMACEN','GAMAS XCEL',null,null,null,'911 N INDUSTRIAL PARK AVE',null,'85621',2459,35,64,'GAMAS XCEL'],
            [17,'FERNANDO ESCOBAR','GLS ALMACEN HERMOSILLO',null,'ALMACEN@GLSGROUP.COM.MX',null,'AV. PERIMETRAL NTE. BODEGA #21','CASA BONITA','83175',1920,26,156,'GLS ALMACEN HERMOSILLO'],
            [18,'ALMACEN','GLS ALMACEN NOGALES',null,null,null,'LOS CHAPANECOS 42',null,'84066',1933,26,156,'GLS ALMACEN NOGALES'],
            [19,'ALMACEN','JOFFROY GLOBAL LAREDO',null,null,null,'10218 CROSSROADS LOOP',null,'78045',2461,77,64,'JOFFROY LAREDO'],
            [20,'ALMACEN','JOFFROY GLOBAL NOGALES',null,null,null,'1251 N INDUSTRIAL PARK AVE',null,'85621',2459,35,64,'JOFFROY NOGALES'],
            [21,'BETSABE BARRIENTOS','KYUNGSHIN MEXICO',null,'betsabe.barrientosob@kyungshin.co.kr',null,'BLVRD CAMINO REAL 255 OTE',null,'85190',1908,26,156,'KYUNGSHIN'],
            [22,'CARLOS ROMERO','LATECOERE LMXA',null,'carlos.romero@latecoere.aero',null,'PIERRE GEORGES LATECEORE 1',null,'83320',1920,26,156,'LATECOERE LMXA'],
            [23,'ALMACEN','LATECOERE LMXC',null,null,null,'PIERRE GEORGES LATECEORE 1',null,'83320',1920,26,156,'LATECOERE LMXC'],
            [24,'ADRIAN MURRIETA','LATECOERE LMXD',null,'adrian.murrieta@latecoere.aero',null,'4 RUE GEORGE PIERRE',null,'83000',1920,26,156,'LATECOERE LMXD'],
            [25,'BRUNO COVARRUBIAS','LATECOERE LMXI',null,'bruno.covarrubias@latecoere.aero',null,'PIERRE GEORGES LATECEORE 1',null,'83320',1920,26,156,'LATELEC'],
            [26,'HUGO GARCIA','CENTRO DE SERVCIOS ZACATECAS (LSU)',null,'Hugo.Garcia@flsmidth.com',null,'BLVD DE LA PLATA 1121',null,'98519',2482,32,156,'LSU'],
            [27,'RECIBO','METAL FINISHING CO CHIHUAHUA',null,'embarques@metalfinishingco.com',null,'AV. NICOLAS GOGOL 11332',null,'31136',217,8,156,'MFCO CHIHUAHUA'],
            [28,'ALMACEN','MINERA MEDIA LUNA',null,'Radio.Control@torexgold.com',null,'DOMICILIO CONOCIDO ATZCALA S/N',null,'40591',383,12,156,'MINERA MEDIA LUNA'],
            [29,'ALMACEN','MAQUINADOS Y TECNOLOGIA',null,'Almacen2@myt.mx',null,'AMISTAD NO. 1221',null,'85210',1908,26,156,'MYT'],
            [30,'IGNACIO FIERRO','OPERADORA DE MINAS E INSTALACIONES MINERAS',null,'ignacio.fierro@mm.gmexico.com',null,'AVENIDA JUAREZ NO 4',null,'84620',1909,26,156,'OMIMSA'],
            [31,'SAMUEL','PROFAMAQ',null,'controldecalidad@profamaq.mx',null,'CALLE LA PEDRERA NO. 16',null,'54500',2681,15,156,'PROFAMAQ'],
            [32,'---','PUERTO ALTAMIRA',null,null,null,'-',null,'89603',1982,28,156,'PUERTO ALTAMIRA'],
            [33,'---','PUERTO ENSENADA',null,null,null,'-','-','22800',12,2,156,'PUERTO ENSENADA'],
            [34,'---','PUERTO MANZANILLO',null,null,null,'-','-','28239',77,6,156,'PUERTO MANZANILLO'],
            [35,'---','PUERTO VERACRUZ',null,null,null,'-','-','91891',2275,30,156,'PUERTO VERACRUZ'],
            [36,'JUAN LOZOYA','RADIALL',null,'juan.lozoya@radiall.com',null,'CIRCUITO DEL PARQUE Y C/OBREROS S/N',null,'85065',1908,26,156,'RADIALL'],
            [37,'FRANCISCO ZAVALA','VALVERDE WAREHOUSE',null,'almacen@valverde.mx',null,'1451 N INDUSTRIAL PARK DR STE 2',null,'85621',2459,35,64,'VALVERDE WAREHOUSE'],
            [38,'BEATRIZ MEJIA','BAP AEROSPACE DE MEXICO',null,'beatriz@bapaerospace.com',null,'C. MAQUILADORAS 101',null,'22444',15,2,156,'BAP'],
            [39,'JULIA GOMEZ','HYTEK FINISHES CO.',null,'Julia.Gomez@hytekfinishes.com',null,'8127 S 216TH ST',null,'98032',2480,81,64,'HYTEK'],
            [40,'ALFREDO RAMIREZ','MAIS SOPORTE INDUSTRIAL Y COMERCIAL',null,'aramirez@maisind.com',null,'UNO PONIENTE 19090-A',null,'22444',15,2,156,'MAIS'],
            [41,'VICTOR CERVANTES','MINERA PENMONT LH',null,'recepcion_herradura@fresnilloplc.com',null,'DOM. CONOCIDO EJIDO JUAN ALVAREZ',null,'83600',1907,26,156,'MINERA PENMONT LH'],
            [42,'---','AEROPUERTO INTERNACIONAL DE LA CIUDAD DE MÉXICO',null,null,null,'AVENIDA CAPITAN CARLOS LEON S/N',null,'15620',281,9,156,'AICM'],
            [43,'---','AEROPUERTO INTERNACIONAL FELIPE ANGELES',null,null,null,'CIRCUITO EXTERIOR MEXIQUENSE KM. 33',null,'55640',776,15,156,'AIFA'],
            [44,'---','AIRPORT SEA TAC',null,null,null,'17801 INTERNATIONAL BLVD',null,'98158',2596,81,64,'AIRPORT SEA TAC'],
            [45,'JERIMY CARRANSA','AVIATION EQUIPMENT PROCESSING',null,'jerimy.carransa@aveprocessing.com',null,'1571 MACARTHUR BLVD',null,'92626',2544,37,64,'AVIATION'],
            [46,'HENRY ZETO','BRALCO METALS',null,'hszeto@bralco.com',null,'15090 NORTHAM ST',null,'90638',2619,37,64,'BRALCO'],
            [47,'JOSE LUIS FELIX','CIPRIA MINERALIA',null,'jose.felix@cipriamineralia.com',null,'ALMACEN PLANTA FLOTACION LOCALIDAD PIEDRAS VERDESS/N',null,'85779',1893,26,156,'CIPRIA MINERALIA'],
            [48,'THOMAS FELIX','COBRE DEL MAYO',null,'thomas.felix@cobredelmayo.com',null,'ALMACEN PLANTA FLOTACION LOCALIDAD PIEDRAS VERDESS/N',null,'85779',1893,26,156,'COBRE DEL MAYO'],
            [49,'AIDE GARCIA','DANHIL DE MEXICO',null,'aide.garcia@danhilcontainers.com',null,'PROGRESO 130',null,'66648',953,19,156,'DANHIL'],
            [50,'HECTOR ARAIZA','DIGRAMEX',null,'araiza74@hotmail.com',null,'AVENIDA GRAL. ESTEBAN CANTU 400-1',null,'21360',13,2,156,'DIGRAMEX'],
            [51,'Benjamin Hodges','DWS5 DC-PLANT',null,'hodgeben@amazon.com',null,'6617 ASSOCIATED BLVD',null,'98203',2601,81,64,'DWS5'],
            [52,'EDGAR QUINTANA','FLEXTRONICS GUADALAJARA SOUTH',null,'edgar.quintana@flex.com',null,'PROL. LOPEZ MATEOS SUR 2915 KM 6.5',null,'45640',570,14,156,'FLEXTRONICS'],
            [53,'ALMACEN','GLS ALMACEN CDMX',null,null,null,'AV. F.F.C.C. INDUSTRIAL 133',null,'15530',281,9,156,'GLS ALMACEN CDMX'],
            [54,'BRENDA HERNANDEZ','GROUND EFFECTS',null,'bhernandez@gfxltd.com',null,'BLVD. FUSION 95',null,'83297',1920,26,156,'GROUND EFFECTS'],
            [55,'ALMACEN','GUZMOR',null,null,null,'1270 N INDUSTRIAL PARK AVE D',null,'85621',2459,35,64,'GUZMOR'],
            [56,'JESSICA BUCIO','HOLCIM APAXCO',null,'jessicadamaris.buciomendez@holcim.com',null,'AVENIDA INDUSTRIAL S/N',null,'55660',666,15,156,'HOLCIM APAXCO'],
            [57,'ALMACEN','HOLCIM MACUSPANA',null,'seguimiento.mx@holcim.com',null,'CARRETERA VILLA HERMOSA A ESCARCEGA KM. 68.5 EJIDO',null,'86700',1974,27,156,'HOLCIM MACUSPANA'],
            [58,'ALMACEN','HOLCIM RAMOS ARIZPE',null,null,null,'CARR. SALTILLO-MONTERREY KM 23.5',null,'25900',59,5,156,'HOLCIM RAMOS ARIZPE'],
            [59,'ALMACEN','L M BROKERAGE & LOGISTICS INC',null,null,null,'1385 N MARIPOSA RD SUITE A',null,'85621',2459,35,64,'LM BROKERAGE'],
            [60,'ALMACEN','The Metal Finishing Company Inc',null,'ups@metalfinishingco.com',null,'721 E MURDOCK ST',null,'67214',2463,49,64,'MFCO WICHITA'],
            [61,'ALMACEN','RAVISA DISTRIBUTION CENTER',null,null,null,'13485 S UNITEC DR',null,'78045',2461,77,64,'RAVISA'],
            [62,'ALMACEN','SECOMEX',null,null,null,'1590 W CALLE PLATA SUITE A',null,'85621',2459,35,64,'SECOMEX'],
            [63,'MARICELA CONS','SIEMENS MANUFACTURING',null,'mcons@siemensmfg.com',null,"'AVE JOSE MENDOZA, ESQ ISRRAEL'",null,'83175',1920,26,156,'SIEMENS'],
            [64,'EDGAR HERNANDEZ','TTI INDUSTRIAL',null,'edgar.hernandez@ttiindustrial.com',null,'RAMON VALDEZ RAMIREZ 965',null,'83179',1920,26,156,'TTI INDUSTRIAL HERMOSILLO'],
            [65,'MARGARITA MARINOVA','UNITED PERFORMANCE METALS',null,'mmarinova@upmet.uk',null,"'BALLYHARRY BUSINESS PARK, 6 BERKSHIRE ROAD'",null,'00000',2707,115,185,'UPM UK'],
            [66,'LISBETH MARQUEZ','WORLDWIDE PRODUCTS INTERNATIONAL MEXICO',null,'lisbeth@wpim.com.mx',null,'BLVD GOBERNADOR BRAULIO MALDONADO #1149',null,'21390',13,2,156,'WORLDWIDE PRODUCTS'],
            [67,'MELISSA SEGOVIA','ZAT LOGISTICS',null,'MSego@scangl.com',null,'AV. CARLOS SALINAS DE GORTARI 353',null,'66600',953,19,156,'ZAT LOGISTICS'],
            [68,'CAPIN VYBORNY','CAPIN VYBORNY',null,'dayala@live.com','123458455','CAPIN VYBORNY','CAPIN VYBORNY','85243',1920,26,156,'CAPIN VYBORNY']
        ];
        foreach ($oldAddresses as $oldAddress) {
            $address = new Address();
            $address->id = $oldAddress[0];
            $address->contact_name = $oldAddress[1];
            $address->name = $oldAddress[2];
            $address->nickname = $oldAddress[3];
            $address->email = $oldAddress[4];
            $address->phone = $oldAddress[5];
            $address->address = $oldAddress[6];
            $address->neighborhood = $oldAddress[7];
            $address->postal_code = $oldAddress[8];
            $address->city_id = $oldAddress[9];
            $address->state_id = $oldAddress[10];
            $address->country_id = $oldAddress[11];
            $address->trade_name = $oldAddress[12];
            $address->save();
        }
        $this->command->info("End addresses");
    }

    private function seedServiceTypeStatuses(): void
    {
        $this->command->info("Seeding service type statuses");
        $oldServiceStatuses = [
            [1,'PICKED UP','App\Models\ServiceType',1,null],
            [2,'Arrived at interim','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [3,'Delayed en route to interim','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [4,'En route to Project','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [5,'Arrived at destination','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [6,'Delayed en route to destination','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [7,'Out for delivery','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [8,'Consolidating shipments per consignee','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [9,'Holding on dock for Customs clearance at destination','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [10,'Undeliverable','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [11,'Appointment required at destination','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [12,'Holding on dock for cartage carrier at destination','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [13,'All short','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [14,"'Returned to dock, no attempt to deliver'",'App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [15,'Awaiting unloading at consignee','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [16,'Attempted delivery','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [17,'Delivered part short','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [18,'Refused delivery','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [19,'Refused for damage','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [20,'DELIVERED','App\Models\ServiceType',1,null],
            [21,'AT ORIGIN','App\Models\ServiceType',1,null],
            [22,'Shipment has been canceled','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [23,'Overage','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [24,'Final delivery pending review','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [25,'Transfer','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [26,'Delivery Receipt Image available','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [27,'BOL Image available','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [28,'Recorded in system','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [29,'Possible Delay Notification','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [30,'Follow-up on time','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [31,'Follow-up delayed','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [32,'Late but no possible delay notification sent','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [33,'Late','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [34,'International shipment to Mexico has been tendered to the broker','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [35,'International shipment from Mexico has been tendered to the broker','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [36,'At interim','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [37,'AT DESTINATION','App\Models\ServiceType',1,null],
            [38,'Estatus llegada','App\Models\ServiceType',1,'2026-01-31 14:28:31'],
            [39,'REQUESTED DATE','App\Models\ServiceType',1,null],
            [40,'TRUCK ASSIGNED','App\Models\ServiceType',1,null],
            [41,'IN STRANSIT','App\Models\ServiceType',1,null],
            [42,'ARRIVED AT TERMINAL','App\Models\ServiceType',1,null],
            [43,'CUSTOMS CLEARANCE IN-PROCESS','App\Models\ServiceType',1,null],
            [44,'CUSTOMS CLEARANCE DONE','App\Models\ServiceType',1,null],
            [45,'DELAYED','App\Models\ServiceType',1,null],
            [46,'CANCELLED','App\Models\ServiceType',1,null],
            [47,'ARRIVED AT TERMINAL','App\Models\ServiceType',2,null],
            [48,'PENDING DOCUMENTS','App\Models\ServiceType',2,null],
            [49,'DOCUMENTS COLLECTED','App\Models\ServiceType',2,null],
            [50,'CUSTOMS CLEARANCE IN PROCESS','App\Models\ServiceType',2,null],
            [51,'CUSTOMS CLEARANCE DONE','App\Models\ServiceType',2,null],
            [52,'DELAYED','App\Models\ServiceType',2,null],
            [53,'CANCELLED','App\Models\ServiceType',2,null],
            [54,'RECEIVING UNDER INSPECTION','App\Models\ServiceType',3,null],
            [55,'RECEIVING DISCREPANCY FOUND (OSD)','App\Models\ServiceType',3,null],
            [56,'RECEIVED AT WAREHOUSE','App\Models\ServiceType',3,null],
            [57,'REPACKAGING / LABELING','App\Models\ServiceType',3,'2026-03-04 17:37:06'],
            [58,'RECEIVED DISPATCH ORDER','App\Models\ServiceType',3,null],
            [59,'PICKING DISPATCH ORDER','App\Models\ServiceType',3,null],
            [60,'READY FOR DISPATCH','App\Models\ServiceType',3,null],
            [61,'DISPATCHED','App\Models\ServiceType',3,null],
            [62,'IN PROCESS','App\Models\ServiceType',3,null],
            [63,'DONE','App\Models\ServiceType',3,null]
        ];
        foreach ($oldServiceStatuses as $oldStatus) {
            $status = new ServiceTypeStatus();
            $status->id = $oldStatus[0];
            $status->name = $oldStatus[1];
            $status->serviceable_type = $oldStatus[2];
            $status->serviceable_id = $oldStatus[3];
            $status->deleted_at = $oldStatus[4];
            $status->save();
        }
        $this->command->info("End service type statuses");
    }

    private function seedServiceTypeStatusModes(): void
    {
        $this->command->info("Seeding service type status modes");
        $oldServiceStatuses = [
            [20,1],
            [20,2],
            [20,3],
            [20,4],
            [20,5],
            [20,6],
            [20,7],
            [41,1],
            [41,2],
            [41,3],
            [41,4],
            [41,5],
            [41,6],
            [41,7],
            [1,1],
            [1,2],
            [1,3],
            [1,4],
            [1,5],
            [1,6],
            [1,7],
            [21,1],
            [21,2],
            [21,3],
            [21,4],
            [21,5],
            [21,6],
            [21,7],
            [37,1],
            [37,2],
            [37,3],
            [37,4],
            [37,5],
            [37,6],
            [37,7],
            [39,1],
            [39,2],
            [39,3],
            [39,4],
            [39,5],
            [39,6],
            [39,7],
            [40,1],
            [40,2],
            [40,3],
            [40,4],
            [40,5],
            [40,6],
            [40,7],
            [42,1],
            [42,2],
            [42,3],
            [42,4],
            [42,5],
            [42,6],
            [42,7],
            [43,1],
            [43,2],
            [43,3],
            [43,4],
            [43,5],
            [43,6],
            [43,7],
            [44,1],
            [44,2],
            [44,3],
            [44,4],
            [44,5],
            [44,6],
            [44,7],
            [45,1],
            [45,2],
            [45,3],
            [45,4],
            [45,5],
            [45,6],
            [45,7],
            [46,1],
            [46,2],
            [46,3],
            [46,4],
            [46,5],
            [46,6],
            [46,7],
            [47,9],
            [47,10],
            [47,11],
            [47,17],
            [47,12],
            [47,13],
            [47,14],
            [49,9],
            [49,10],
            [49,11],
            [49,17],
            [49,12],
            [49,13],
            [49,14],
            [48,9],
            [48,10],
            [48,11],
            [48,17],
            [48,12],
            [48,13],
            [48,14],
            [50,9],
            [50,10],
            [50,11],
            [50,17],
            [50,12],
            [50,13],
            [50,14],
            [51,9],
            [51,10],
            [51,11],
            [51,17],
            [51,12],
            [51,13],
            [51,14],
            [52,9],
            [52,10],
            [52,11],
            [52,17],
            [52,12],
            [52,13],
            [52,14],
            [53,9],
            [53,10],
            [53,11],
            [53,17],
            [53,12],
            [53,13],
            [53,14],
            [54,15],
            [55,15],
            [56,15],
            [58,15],
            [59,15],
            [60,15],
            [61,15],
            [62,16],
            [63,16]
        ];
        foreach ($oldServiceStatuses as $oldStatus) {
            $status = new ServiceTypeStatusMode();
            $status->service_type_status_id = $oldStatus[0];
            $status->service_mode_id = $oldStatus[1];
            $status->save();
        }
        $this->command->info("End service type status modes");
    }

    private function seedDocumentTypes(): void
    {
        $this->command->info("Seeding document types");
        $oldDocuments = [
            [1,'MATERIAL SAFETY DATA SHEET',null,1,1,1,10],
            [2,'Archivo de pago','2026-01-29 17:14:05',0,0,0,1],
            [3,'PROOF OF DELIVERY',null,1,0,1,9],
            [4,'BILL OF LADING',null,1,1,1,1],
            [5,'Carpeta cliente','2026-01-29 17:12:15',0,0,0,1],
            [6,'Cuenta de gastos','2026-01-29 17:09:47',0,0,0,1],
            [7,'Detalle de cove','2026-01-29 17:09:27',0,0,0,1],
            [8,'Documentos entrada','2026-01-29 17:08:58',0,0,0,1],
            [9,'Documentos Orden de carga','2026-01-29 17:08:01',0,0,0,1],
            [10,'Hoja de calculo','2026-01-29 17:07:05',0,0,0,1],
            [11,'DECLARATION OF VALUE (MDV)',null,0,1,0,18],
            [12,'PEDIMENTO',null,1,1,0,2],
            [13,'CUSTOMS PRE-INSPECTION',null,0,1,0,19],
            [14,'COMMERCIAL INVOICE',null,1,1,1,5],
            [15,'Filial PDF','2026-01-07 18:12:52',0,0,0,1],
            [16,'PHOTOS',null,1,1,1,7],
            [17,'PACKING LIST',null,1,1,1,3],
            [18,'XML Factura','2026-01-29 17:01:42',0,0,0,1],
            [19,'ADP',null,0,1,0,20],
            [20,'XML Cove','2026-01-29 17:04:16',0,0,0,1],
            [21,'OTHER',null,1,1,1,13],
            [22,'PURCHASE ORDER',null,1,1,1,11],
            [23,'TECHNICAL DATA SHEET',null,1,1,1,12],
            [24,'LETTER 3.1.8',null,0,1,0,14],
            [25,'GOODS RECEIPT',null,0,0,1,17],
            [26,'ENTRY',null,1,1,0,8],
            [27,'INBOND',null,1,1,0,6],
            [28,'SHIPPER EXPORT',null,0,1,0,16],
            [29,'DODA',null,1,1,0,4],
            [30,'CUSTOMER FOLDER',null,0,1,0,15]
        ];
        foreach ($oldDocuments as $oldDocument) {
            $document = new DocumentType();
            $document->id = $oldDocument[0];
            $document->name = $oldDocument[1];
            $document->deleted_at = $oldDocument[2];
            $document->shipments = $oldDocument[3];
            $document->customs = $oldDocument[4];
            $document->warehouse = $oldDocument[5];
            $document->order_number = $oldDocument[6];
            $document->save();
        }
        $this->command->info("End document types");
    }

    private function seedPrivateDocumentTypes(): void
    {
        $this->command->info("Seeding private document types");
        $oldDocuments = [
            [1,'CARTA PORTE'],
            [2,'PHOTOS'],
            [3,'COTIZACION CLIENTE']
        ];
        foreach ($oldDocuments as $oldDocument) {
            $document = new PrivateDocumentType();
            $document->id = $oldDocument[0];
            $document->name = $oldDocument[1];
            $document->save();
        }
        $this->command->info("End private document types");
    }
}
