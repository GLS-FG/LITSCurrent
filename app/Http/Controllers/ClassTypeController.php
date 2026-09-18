<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceTypeRequest;
use App\Models\ClassType;
use App\Models\ServiceClass;
use App\Models\ServiceMode;
use App\Models\ServiceType;

class ClassTypeController extends Controller
{
    public function index(ServiceType $serviceType, ServiceClass $serviceClass, ServiceMode $serviceMode)
    {
        return view('service-type.service-class.service-mode.class-type.index', [
            'serviceType' => $serviceType,
            'serviceClass' => $serviceClass,
            'serviceMode' => $serviceMode,
            'services' => $serviceMode->classTypes
        ]);
    }

    public function create(ServiceType $serviceType, ServiceClass $serviceClass, ServiceMode $serviceMode)
    {
        return view('service-type.service-class.service-mode.class-type.create', [
            'serviceType' => $serviceType,
            'serviceClass' => $serviceClass,
            'serviceMode' => $serviceMode,
        ]);
    }

    public function store(ServiceTypeRequest $request, ServiceType $serviceType, ServiceClass $serviceClass, ServiceMode $serviceMode)
    {

        ClassType::create(array_merge($request->validated(), ['service_mode_id' => $serviceMode->id]));
        return redirect()->route('service-types.service-classes.service-modes.class-types.index', [ 'service_type' => $serviceType, 'service_class' => $serviceClass, 'service_mode' => $serviceMode ])
            ->with('success', 'Se creó correctamente el class type.');
    }

    public function edit(ServiceType $serviceType, ServiceClass $serviceClass, ServiceMode $serviceMode, ClassType $classType)
    {
        return view('service-type.service-class.service-mode.class-type.edit', [
            'serviceType' => $serviceType,
            'serviceClass' => $serviceClass,
            'serviceMode' => $serviceMode,
            'classType' => $classType
        ]);
    }

    public function update(ServiceTypeRequest $request, ServiceType $serviceType, ServiceClass $serviceClass, ServiceMode $serviceMode, ClassType $classType)
    {
        $classType->update($request->validated());
        return redirect()->route('service-types.service-classes.service-modes.class-types.index', [ 'service_type' => $serviceType, 'service_class' => $serviceClass, 'service_mode' => $serviceMode ])
            ->with('success', 'Se actualizó correctamente la información del class type.');
    }

    public function destroy(ServiceType $serviceType, ServiceClass $serviceClass, ServiceMode $serviceMode, ClassType $classType)
    {
        $serviceName = $classType->name;
        $classType->delete();
        return back()->with('success', 'El class type ' . $serviceName . ' ha sido eliminado.');
    }
}
