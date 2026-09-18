<?php

namespace App\View\Helpers;

use App\Enums\ServiceState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Service
{
    public Model $service;
    public string $serviceType;
    public string $route;
    public string $slug;
    public ServiceState $status;
    public Carbon $createdAt;

    public function __construct(Model $service, string $serviceType, string $route, string $slug, ServiceState $status, Carbon $createdAt)
    {
        $this->service = $service;
        $this->serviceType = $serviceType;
        $this->route = $route;
        $this->slug = $slug;
        $this->status = $status;
        $this->createdAt = $createdAt;
    }
}
