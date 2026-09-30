<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ClassTypeController;
use App\Http\Controllers\ClientAddressController;
use App\Http\Controllers\ClientUserController;
use App\Http\Controllers\CloneController;
use App\Http\Controllers\CustomAgentAddressController;
use App\Http\Controllers\CustomAgentController;
use App\Http\Controllers\CustomDeclarationController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderImportLocationController;
use App\Http\Controllers\OrderImportPrivateDocumentController;
use App\Http\Controllers\OrderImportProductController;
use App\Http\Controllers\OrderPrivateDocumentController;
use App\Http\Controllers\OrderShipmentDriverController;
use App\Http\Controllers\OrderShipmentLocationController;
use App\Http\Controllers\OrderShipmentPrivateDocumentController;
use App\Http\Controllers\OrderShipmentProductController;
use App\Http\Controllers\OrderWarehouseStorageLocationController;
use App\Http\Controllers\OrderWarehouseStoragePrivateDocumentController;
use App\Http\Controllers\OrderWarehouseStorageProductController;
use App\Http\Controllers\PrivateDocumentController;
use App\Http\Controllers\PrivateDocumentTypeController;
use App\Http\Controllers\ServiceClassController;
use App\Http\Controllers\ServiceLevelController;
use App\Http\Controllers\ServiceModeController;
use App\Http\Controllers\ServiceStatusController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\ServiceTypeStatusController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\TransportationAgencyDocumentController;
use App\Http\Controllers\VehicleController;
use App\Http\Middleware\CheckApiKey;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AutocompleteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\OrderDocumentController;
use App\Http\Controllers\OrderImportController;
use App\Http\Controllers\OrderImportDocumentController;
use App\Http\Controllers\OrderProductController;
use App\Http\Controllers\OrderShipmentController;
use App\Http\Controllers\OrderShipmentDocumentController;
use App\Http\Controllers\OrderShipmentTransportationController;
use App\Http\Controllers\OrderWarehouseStorageController;
use App\Http\Controllers\OrderWarehouseStorageDocumentController;
use App\Http\Controllers\CustomController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\PetitionCodeController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\TransportationAgencyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Session;

Route::get('track-my-shipment', [TrackingController::class, 'show'])->name('tracking.show');

Route::get('/language/{locale}', function ($locale) {
    Session::put('locale', $locale);
    return redirect()->back();
})->name('language.switch');

Route::middleware('guest')->group(function () {
    Route::get('login', fn () => to_route('auth.create'))->name('login');
    Route::resource('auth', AuthController::class)->only(['create', 'store']);
    Route::get('forgot-password', [AuthController::class, 'passwordRequest'])->name('password.request');
    Route::post('forgot-password', [AuthController::class, 'passwordEmail'])->name('password.email');
    Route::get('reset-password/{token}', [AuthController::class, 'passwordReset'])->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'passwordUpdate'])->name('password.update');
});

Route::middleware(['guest', 'signed'])->group(function () {
    Route::get('/orders/{order}/shipments/{shipment}/drivers', [OrderShipmentDriverController::class, 'show'])->name('orders.shipments.drivers.show');
    Route::post('/orders/{order}/shipments/{shipment}/drivers', [OrderShipmentDriverController::class, 'store'])->name('orders.shipments.drivers.store');
});

Route::middleware(['auth', 'role:Super Admin|Operator Admin|Operator|Warehouse|Billing'])->group(function () {
    Route::resource('addresses', AddressController::class)->only(['index', 'create', 'show', 'store', 'edit', 'update', 'destroy']);
    Route::resource('cities', CityController::class)->only(['index', 'create', 'show', 'store', 'edit', 'update', 'destroy']);
    Route::resource('clients', ClientController::class)->only(['index', 'create', 'show', 'store', 'edit', 'update', 'destroy']);
    Route::resource('clients.users', ClientUserController::class)->only(['create', 'show', 'store', 'edit', 'update', 'destroy']);
    Route::resource('clients.addresses', ClientAddressController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('countries', CountryController::class)->only(['index', 'create', 'show', 'store', 'edit', 'update', 'destroy']);
    Route::resource('customs', CustomController::class)->only(['index', 'create', 'show', 'store', 'edit', 'update', 'destroy']);
    Route::resource('custom-agents', CustomAgentController::class)->only(['index', 'create', 'show', 'store', 'edit', 'update', 'destroy']);
    Route::resource('custom-agents.addresses', CustomAgentAddressController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('document-types', DocumentTypeController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('notifications', NotificationController::class)->only(['index']);
    Route::resource('private-types', PrivateDocumentTypeController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('orders', OrderController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('orders.documents', OrderDocumentController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::resource('orders.privates', OrderPrivateDocumentController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::resource('orders.imports', OrderImportController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('orders.imports.declarations', CustomDeclarationController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('orders.imports.documents', OrderImportDocumentController::class)->only(['index', 'create', 'store']);
    Route::resource('orders.imports.locations', OrderImportLocationController::class)->only(['store', 'update', 'destroy']);
    Route::resource('orders.imports.privates', OrderImportPrivateDocumentController::class)->only(['index', 'create', 'store']);
    Route::resource('orders.imports.products', OrderImportProductController::class)->only(['store']);
    Route::resource('orders.products', OrderProductController::class)->only(['store', 'update', 'destroy']);
    Route::resource('orders.shipments', OrderShipmentController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('orders.shipments.transportations', OrderShipmentTransportationController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('orders.shipments.documents', OrderShipmentDocumentController::class)->only(['index', 'create', 'store']);
    Route::resource('orders.shipments.locations', OrderShipmentLocationController::class)->only(['store', 'update', 'destroy']);
    Route::resource('orders.shipments.privates', OrderShipmentPrivateDocumentController::class)->only(['index', 'create', 'store']);
    Route::resource('orders.shipments.products', OrderShipmentProductController::class)->only(['store']);
    Route::resource('orders.warehouse-storages', OrderWarehouseStorageController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('orders.warehouse-storages.documents', OrderWarehouseStorageDocumentController::class)->only(['index', 'create', 'store']);
    Route::resource('orders.warehouse-storages.locations', OrderWarehouseStorageLocationController::class)->only(['store', 'update', 'destroy']);
    Route::resource('orders.warehouse-storages.privates', OrderWarehouseStoragePrivateDocumentController::class)->only(['index', 'create', 'store']);
    Route::resource('orders.warehouse-storages.products', OrderWarehouseStorageProductController::class)->only(['store']);
    Route::resource('petition-codes', PetitionCodeController::class)->only(['index', 'create', 'show', 'store', 'edit', 'update', 'destroy']);
    Route::resource('service-statuses', ServiceStatusController::class)->only(['index']);
    Route::resource('service-statuses.service-type-statuses', ServiceTypeStatusController::class)->only(['index', 'create', 'show', 'store', 'edit', 'update', 'destroy']);
    Route::resource('service-types', ServiceTypeController::class)->only(['index']);
    Route::resource('service-types.service-classes', ServiceClassController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('service-types.service-classes.service-modes', ServiceModeController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('service-types.service-classes.service-modes.class-types', ClassTypeController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('service-types.service-classes.service-modes.class-types.service-levels', ServiceLevelController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('states', StateController::class)->only(['index', 'create', 'show', 'store', 'edit', 'update', 'destroy']);
    Route::resource('transportation-agencies', TransportationAgencyController::class)->only(['index', 'create', 'show', 'store', 'edit', 'update', 'destroy']);
    Route::resource('transportation-agencies.documents', TransportationAgencyDocumentController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::resource('transportation-agencies.vehicles', VehicleController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('users', UserController::class)->only(['index', 'create', 'show', 'store', 'edit', 'update', 'destroy']);
    Route::resource('warehouses', WarehouseController::class)->only(['index', 'create', 'show', 'store', 'edit', 'update', 'destroy']);

    Route::get('/clones/{shipment}/orders/create', [CloneController::class, 'createOrder'])->name('clone.order.create');
    Route::get('/clones/{shipment}/orders/{order}/shipments/create', [CloneController::class, 'createShipment'])->name('clone.order.shipments.create');
    Route::get('/privates/{private}', [PrivateDocumentController::class, 'show'])->name('privates.show');
    Route::get('/privates/{private}/download', [PrivateDocumentController::class, 'download'])->name('privates.download');
    Route::get('/orders/{order}/imports/{import}/print-checklist', [OrderImportController::class, 'printChecklist'])->name('orders.imports.printCL');
    Route::get('/orders/{order}/shipments/{shipment}/print-checklist', [OrderShipmentController::class, 'printChecklist'])->name('orders.shipments.printCL');
    Route::get('/orders/{order}/shipments/{shipment}/preview-email-notification', [OrderShipmentController::class, 'previewNotify'])->name('orders.shipments.previewNotify');
    Route::get('/orders/{order}/warehouse-storages/{warehouse_storage}/print-checklist', [OrderWarehouseStorageController::class, 'printChecklist'])->name('orders.warehouse-storages.printCL');

    Route::post('/document-types/{document_type}/moveUp', [DocumentTypeController::class, 'moveUp'])->name('document-types.up');
    Route::post('/document-types/{document_type}/moveDown', [DocumentTypeController::class, 'moveDown'])->name('document-types.down');
    Route::post('/clones/{shipment}/orders', [CloneController::class, 'storeOrder'])->name('clone.order.store');
    Route::post('/clones/{shipment}/orders/{order}/shipments', [CloneController::class, 'storeShipment'])->name('clone.order.shipments.store');
    Route::post('/orders/{order}/copyProducts', [OrderController::class, 'copyProducts'])->name('orders.products.copy');
    Route::post('/orders/{order}/notifications', [OrderController::class, 'notify'])->name('orders.notify');
    Route::post('/orders/{order}/imports/{import}/notifications', [OrderImportController::class, 'notify'])->name('orders.imports.notify');
    Route::post('/orders/{order}/shipments/{shipment}/drivers-create', [OrderShipmentController::class, 'storeDriver'])->name('orders.shipments.drivers.create');
    Route::post('/orders/{order}/shipments/{shipment}/notifications', [OrderShipmentController::class, 'notify'])->name('orders.shipments.notify');
    Route::post('/orders/{order}/shipments/{shipment}/updateMap', [OrderShipmentController::class, 'updateMap'])->name('orders.shipments.map');
    Route::post('/orders/{order}/warehouse-storages/{warehouse_storage}/notifications', [OrderWarehouseStorageController::class, 'notify'])->name('orders.warehouse-storages.notify');
    Route::post('/service-statuses/{service_status}/service-type-statuses/{service_type_status}/moveUp', [ServiceTypeStatusController::class, 'moveUp'])->name('service-statuses.service-type-statuses.up');
    Route::post('/service-statuses/{service_status}/service-type-statuses/{service_type_status}/moveDown', [ServiceTypeStatusController::class, 'moveDown'])->name('service-statuses.service-type-statuses.down');
    Route::post('/user/{user}/resetPassword', [UserController::class, 'resetPassword'])->name('users.resetPassword');
    Route::post('/user/{user}/welcome', [UserController::class, 'sendWelcome'])->name('users.welcome');

    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update.status');
    Route::put('/orders/{order}/urgent', [OrderController::class, 'updateUrgent'])->name('orders.update.urgent');
    Route::put('/orders/{order}/imports/{import}/checklist', [OrderImportController::class, 'updateChecklistComments'])->name('orders.imports.update.checklist');
    Route::put('/orders/{order}/imports/{import}/status', [OrderImportController::class, 'updateStatus'])->name('orders.imports.update.status');
    Route::put('/orders/{order}/imports/{import}/declarations/{declaration}/inspection', [CustomDeclarationController::class, 'updateInspection'])->name('orders.imports.declarations.inspection');
    Route::put('/orders/{order}/shipments/{shipment}/checklist', [OrderShipmentController::class, 'updateChecklistComments'])->name('orders.shipments.update.checklist');
    Route::put('/orders/{order}/shipments/{shipment}/status', [OrderShipmentController::class, 'updateStatus'])->name('orders.shipments.update.status');
    Route::put('/orders/{order}/shipments/{shipment}/transportations/{transportation}/status', [OrderShipmentTransportationController::class, 'updateStatus'])->name('orders.shipments.transportations.update.status');
    Route::put('/orders/{order}/warehouse-storages/{warehouse_storage}/checklist', [OrderWarehouseStorageController::class, 'updateChecklistComments'])->name('orders.warehouse-storages.update.checklist');
    Route::put('/orders/{order}/warehouse-storages/{warehouse_storage}/status', [OrderWarehouseStorageController::class, 'updateStatus'])->name('orders.warehouse-storages.update.status');
});

Route::middleware('auth')->group(function () {
    Route::delete('logout', fn () => to_route('auth.destroy'))->name('logout');
    Route::delete('auth', [AuthController::class, 'destroy'])->name('auth.destroy');
    Route::get('/email/verify', [AuthController::class, 'verificationNotice'])->name('verification.notice');

    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/autocomplete/addresses', [AutocompleteController::class, 'addresses'])->name('autocomplete.addresses');
    Route::get('/autocomplete/cities', [AutocompleteController::class, 'cities'])->name('autocomplete.cities');
    Route::get('/autocomplete/contacts', [AutocompleteController::class, 'contacts'])->name('autocomplete.contacts');
    Route::get('/autocomplete/countries', [AutocompleteController::class, 'countries'])->name('autocomplete.countries');
    Route::get('/autocomplete/sevice-types', [AutocompleteController::class, 'serviceTypes'])->name('autocomplete.serviceTypes');
    Route::get('/autocomplete/states', [AutocompleteController::class, 'states'])->name('autocomplete.states');
    Route::get('/autocomplete/vehicles', [AutocompleteController::class, 'vehicles'])->name('autocomplete.vehicles');
    Route::get('/clients/logos/{filename}', [ClientController::class, 'logo'])->name('clients.logos');
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('/imports', [OrderImportController::class, 'index'])->name('imports.index');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/edit-data', [OrderController::class, 'editData'])->name('orders.edit-data');
    Route::get('/orders/{order}/imports/{import}', [OrderImportController::class, 'show'])->name('orders.imports.show');
    Route::get('/orders/{order}/imports/{import}/edit-data', [OrderImportController::class, 'editData'])->name('orders.imports.edit-data');
    Route::get('/orders/{order}/shipments/{shipment}', [OrderShipmentController::class, 'show'])->name('orders.shipments.show');
    Route::get('/orders/{order}/shipments/{shipment}/edit-data', [OrderShipmentController::class, 'editData'])->name('orders.shipments.edit-data');
    Route::get('/orders/{order}/shipments/{shipment}/bill-of-landing', [OrderShipmentController::class, 'showBOL'])->name('orders.shipments.bol');
    Route::get('/orders/{order}/shipments/{shipment}/print-bill-of-landing', [OrderShipmentController::class, 'printBOL'])->name('orders.shipments.print');
    Route::get('/orders/{order}/warehouse-storages/{warehouse_storage}', [OrderWarehouseStorageController::class, 'show'])->name('orders.warehouse-storages.show');
    Route::get('/orders/{order}/warehouse-storages/{warehouse_storage}/edit-data', [OrderWarehouseStorageController::class, 'editData'])->name('orders.warehouse-storages.edit-data');
    Route::get('/orders-history', [OrderController::class, 'history'])->name('orders.history');
    Route::get('/shipments', [OrderShipmentController::class, 'index'])->name('shipments.index');
    Route::get('/warehouse-storages', [OrderWarehouseStorageController::class, 'index'])->name('warehouse-storages.index');
});

Route::middleware(['auth', 'signed'])->group(function () {
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verificationVerify'])->name('verification.verify');
});

Route::middleware(['auth', 'throttle:6,1'])->group(function () {
    Route::post('/email/verification-notification', [AuthController::class, 'verificationSend'])->name('verification.send');
});

Route::prefix('api/v1')->middleware([CheckApiKey::class, 'throttle:api'])->group(function () {
    Route::get('/projects', [ApiController::class, 'projects']);
    Route::get('/orders', [ApiController::class, 'orders']);
});
