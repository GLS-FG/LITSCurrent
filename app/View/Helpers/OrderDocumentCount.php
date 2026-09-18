<?php

namespace App\View\Helpers;

class OrderDocumentCount
{
    public string $name;
    public int $total;
    public int $isRequired;

    public function __construct(string $name, int $total, bool $isRequired)
    {
        $this->name = $name;
        $this->total = $total;
        $this->isRequired = $isRequired;
    }
}
