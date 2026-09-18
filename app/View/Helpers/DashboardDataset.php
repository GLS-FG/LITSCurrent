<?php

namespace App\View\Helpers;

class DashboardDataset
{
    public string $label;
    public array $data;
    public string|array $borderColor;
    public string|array $backgroundColor;

    public function __construct(string $label, array $data, string|array $borderColor, string|array $backgroundColor)
    {
        $this->label = $label;
        $this->data = $data;
        $this->borderColor = $borderColor;
        $this->backgroundColor = $backgroundColor;
    }
}
