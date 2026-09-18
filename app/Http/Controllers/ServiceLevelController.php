<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceTypeRequest;
use App\Models\ClassType;
use App\Models\ServiceClass;
use App\Models\ServiceLevel;
use App\Models\ServiceMode;
use App\Models\ServiceType;

class ServiceLevelController extends Controller
{
    public function index(ServiceType $serviceType, ServiceClass $serviceClass, ServiceMode $serviceMode, ClassType $classType)
    {
        return view('service-type.service-class.service-mode.class-type.service-level.index', [
            'serviceType' => $serviceType,
            'serviceClass' => $serviceClass,
            'serviceMode' => $serviceMode,
            'classType' => $classType,
            'services' => $classType->serviceLevels
        ]);
    }

    public function create(ServiceType $serviceType, ServiceClass $serviceClass, ServiceMode $serviceMode, ClassType $classType)
    {
        return view('service-type.service-class.service-mode.class-type.service-level.create', [
            'serviceType' => $serviceType,
            'serviceClass' => $serviceClass,
            'serviceMode' => $serviceMode,
            'classType' => $classType
        ]);
    }

    public function store(ServiceTypeRequest $request, ServiceType $serviceType, ServiceClass $serviceClass, ServiceMode $serviceMode, ClassType $classType)
    {

        ServiceLevel::create(array_merge($request->validated(), ['class_type_id' => $classType->id]));
        return redirect()->route('service-types.service-classes.service-modes.class-types.service-levels.index', [ 'service_type' => $serviceType, 'service_class' => $serviceClass, 'service_mode' => $serviceMode, 'class_type' => $classType ])
            ->with('success', 'Se creó correctamente el class type.');
    }

    public function edit(ServiceType $serviceType, ServiceClass $serviceClass, ServiceMode $serviceMode, ClassType $classType, ServiceLevel $serviceLevel)
    {
        return view('service-type.service-class.service-mode.class-type.service-level.edit', [
            'serviceType' => $serviceType,
            'serviceClass' => $serviceClass,
            'serviceMode' => $serviceMode,
            'classType' => $classType,
            'serviceLevel' => $serviceLevel
        ]);
    }

    public function update(ServiceTypeRequest $request, ServiceType $serviceType, ServiceClass $serviceClass, ServiceMode $serviceMode, ClassType $classType, ServiceLevel $serviceLevel)
    {
        $serviceLevel->update($request->validated());
        return redirect()->route('service-types.service-classes.service-modes.class-types.service-levels.index', [ 'service_type' => $serviceType, 'service_class' => $serviceClass, 'service_mode' => $serviceMode, 'class_type' => $classType ])
            ->with('success', 'Se actualizó correctamente la información del service level.');
    }

    public function destroy(ServiceType $serviceType, ServiceClass $serviceClass, ServiceMode $serviceMode, ClassType $classType, ServiceLevel $serviceLevel)
    {
        $serviceName = $serviceLevel->name;
        $serviceLevel->delete();
        return back()->with('success', 'El service level ' . $serviceName . ' ha sido eliminado.');
    }
}
