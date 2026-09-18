<?php

namespace App\Http\Controllers;

use App\Models\ServiceClass;
use App\Models\ServiceType;
use Illuminate\Http\Request;

class ServiceTypeController extends Controller
{
    public function index()
    {
        $services = ServiceType::all();
        return view('service-type.index', [
            'services' => $services
        ]);
    }
}
