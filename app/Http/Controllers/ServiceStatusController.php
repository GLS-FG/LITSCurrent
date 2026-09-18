<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use Illuminate\Http\Request;

class ServiceStatusController extends Controller
{
    public function index()
    {
        $services = ServiceType::all();
        return view('service-status.index', [
            'services' => $services
        ]);
    }
}
