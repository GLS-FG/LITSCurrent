<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceTypeStatusPostRequest;
use App\Http\Requests\ServiceTypeStatusPutRequest;
use App\Models\ServiceType;
use App\Models\ServiceTypeStatus;
use App\Models\ServiceTypeStatusMode;
use Illuminate\Http\Request;

class ServiceTypeStatusController extends Controller
{
    public function index(ServiceType $serviceStatus)
    {
        return view('service-status.status.index', [
            'serviceType' => $serviceStatus,
            'statuses' => $serviceStatus->statuses
        ]);
    }

    public function create(ServiceType $serviceStatus)
    {
        return view('service-status.status.create', [
            'serviceType' => $serviceStatus
        ]);
    }

    public function store(ServiceTypeStatusPostRequest $request, ServiceType $serviceStatus)
    {
        $validated = $request->validated();
        $serviceTypeStatus = new ServiceTypeStatus();
        $maxValue = ServiceTypeStatus::where('serviceable_id', $serviceStatus->id)->max('order_number');
        $serviceTypeStatus->name = $validated['name'];
        $serviceTypeStatus->color = $validated['color'];
        $serviceTypeStatus->order_number = $maxValue + 1;
        $serviceStatus->statuses()->save($serviceTypeStatus);
        foreach ($validated['permissions'] as $permission) {
            ServiceTypeStatusMode::create(['service_mode_id' => $permission, 'service_type_status_id' => $serviceTypeStatus->id]);
        }
        return redirect()->route('service-statuses.service-type-statuses.show', [ 'service_status' => $serviceStatus, 'service_type_status' => $serviceTypeStatus ])
            ->with('success', 'Se creó correctamente el estatus del servicio.');
    }

    public function show(ServiceType $serviceStatus, ServiceTypeStatus  $serviceTypeStatus)
    {
        $modes = [];
        foreach ($serviceTypeStatus->modes as $mode) {
            $modes[] = $mode->id;
        }
        return view('service-status.status.show', [
            'serviceType' => $serviceStatus,
            'serviceTypeStatus' => $serviceTypeStatus,
            'modes' => $modes
        ]);
    }

    public function edit(ServiceType $serviceStatus, ServiceTypeStatus  $serviceTypeStatus)
    {
        $modes = [];
        foreach ($serviceTypeStatus->modes as $mode) {
            $modes[] = $mode->id;
        }
        return view('service-status.status.edit', [
            'serviceType' => $serviceStatus,
            'serviceTypeStatus' => $serviceTypeStatus,
            'modes' => $modes
        ]);
    }

    public function update(ServiceTypeStatusPutRequest $request, ServiceType $serviceStatus, ServiceTypeStatus  $serviceTypeStatus)
    {
        $validated = $request->validated();
        $serviceTypeStatus->name = $validated['name'];
        $serviceTypeStatus->color = $validated['color'];
        $serviceTypeStatus->save();
        $serviceTypeStatus->modes()->detach();
        foreach ($validated['permissions'] as $permission) {
            ServiceTypeStatusMode::create(['service_mode_id' => $permission, 'service_type_status_id' => $serviceTypeStatus->id]);
        }
        return redirect()->route('service-statuses.service-type-statuses.show', [ 'service_status' => $serviceStatus, 'service_type_status' => $serviceTypeStatus ])
            ->with('success', 'Se actualizó correctamente el estatus del servicio.');
    }

    public function destroy(ServiceType $serviceStatus, ServiceTypeStatus  $serviceTypeStatus)
    {
        $serviceTypeStatus->modes()->detach();
        $serviceTypeStatus->delete();
        return redirect()->route('service-statuses.service-type-statuses.index', [ 'service_status' => $serviceStatus ])
            ->with('success', 'Se eliminó correctamente el estatus del servicio.');
    }

    public function moveUp(Request $request, ServiceType $serviceStatus, ServiceTypeStatus  $serviceTypeStatus)
    {
        $upRecord = ServiceTypeStatus::where('order_number', '<', $serviceTypeStatus->order_number)->orderBy('order_number', 'desc')->first();
        $upNumber = $upRecord->order_number;
        $currentNumber = $serviceTypeStatus->order_number;
        $serviceTypeStatus->update(['order_number' => $upNumber]);
        $upRecord->update(['order_number' => $currentNumber]);
        return redirect()->route('service-statuses.service-type-statuses.index', [ 'service_status' => $serviceStatus ])
            ->with('success', 'Se editó correctamente el orden de los estatus de servicio.');
    }

    public function moveDown(Request $request, ServiceType $serviceStatus, ServiceTypeStatus  $serviceTypeStatus)
    {
        $downRecord = ServiceTypeStatus::where('order_number', '>', $serviceTypeStatus->order_number)->orderBy('order_number', 'asc')->first();
        $downNumber = $downRecord->order_number;
        $currentNumber = $serviceTypeStatus->order_number;
        $serviceTypeStatus->update(['order_number' => $downNumber]);
        $downRecord->update(['order_number' => $currentNumber]);
        return redirect()->route('service-statuses.service-type-statuses.index', [ 'service_status' => $serviceStatus ])
            ->with('success', 'Se editó correctamente el orden de los estatus de servicio.');
    }
}
