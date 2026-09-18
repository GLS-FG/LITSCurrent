<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceTypeRequest;
use App\Models\ServiceClass;
use App\Models\ServiceType;

class ServiceClassController extends Controller
{
    public function index(ServiceType $serviceType)
    {
        return view('service-type.service-class.index', [
            'serviceType' => $serviceType,
            'services' => $serviceType->serviceClasses
        ]);
    }

    public function create(ServiceType $serviceType)
    {
        return view('service-type.service-class.create', [
            'serviceType' => $serviceType
        ]);
    }

    public function store(ServiceTypeRequest $request, ServiceType $serviceType)
    {
        ServiceClass::create(array_merge($request->validated(), ['service_type_id' => $serviceType->id]));
        return redirect()->route('service-types.service-classes.index', [ 'service_type' => $serviceType ])
            ->with('success', 'Se creó correctamente el service class.');
    }

    public function edit(ServiceType $serviceType, ServiceClass $serviceClass)
    {
        return view('service-type.service-class.edit', [
            'serviceType' => $serviceType,
            'serviceClass' => $serviceClass
        ]);
    }

    public function update(ServiceTypeRequest $request, ServiceType $serviceType, ServiceClass $serviceClass)
    {
        $serviceClass->update($request->validated());
        return redirect()->route('service-types.service-classes.index', [ 'service_type' => $serviceType ])
            ->with('success', 'Se actualizó correctamente la información del service class.');
    }

    public function destroy(ServiceType $serviceType, ServiceClass $serviceClass)
    {
        $serviceClassName = $serviceClass->name;
        $serviceClass->delete();
        return back()->with('success', 'El service class ' . $serviceClassName . ' ha sido eliminado.');
    }
}
