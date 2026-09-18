<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceModeRequest;
use App\Models\ServiceClass;
use App\Models\ServiceMode;
use App\Models\ServiceType;

class ServiceModeController extends Controller
{
    public function index(ServiceType $serviceType, ServiceClass $serviceClass)
    {
        return view('service-type.service-class.service-mode.index', [
            'serviceType' => $serviceType,
            'serviceClass' => $serviceClass,
            'services' => $serviceClass->serviceModes
        ]);
    }

    public function create(ServiceType $serviceType, ServiceClass $serviceClass)
    {
        return view('service-type.service-class.service-mode.create', [
            'serviceType' => $serviceType,
            'serviceClass' => $serviceClass,
        ]);
    }

    public function store(ServiceModeRequest $request, ServiceType $serviceType, ServiceClass $serviceClass)
    {

        ServiceMode::create(array_merge($request->validated(), ['service_class_id' => $serviceClass->id]));
        return redirect()->route('service-types.service-classes.service-modes.index', [ 'service_type' => $serviceType, 'service_class' => $serviceClass ])
            ->with('success', 'Se creó correctamente el service class.');
    }

    public function edit(ServiceType $serviceType, ServiceClass $serviceClass, ServiceMode $serviceMode)
    {
        return view('service-type.service-class.service-mode.edit', [
            'serviceType' => $serviceType,
            'serviceClass' => $serviceClass,
            'serviceMode' => $serviceMode
        ]);
    }

    public function update(ServiceModeRequest $request, ServiceType $serviceType, ServiceClass $serviceClass, ServiceMode $serviceMode)
    {
        $serviceMode->update($request->validated());
        return redirect()->route('service-types.service-classes.service-modes.index', [ 'service_type' => $serviceType, 'service_class' => $serviceClass ])
            ->with('success', 'Se actualizó correctamente la información del service class.');
    }

    public function destroy(ServiceType $serviceType, ServiceClass $serviceClass, ServiceMode $serviceMode)
    {
        $serviceName = $serviceMode->name;
        $serviceMode->delete();
        return back()->with('success', 'El service mode ' . $serviceName . ' ha sido eliminado.');
    }
}
