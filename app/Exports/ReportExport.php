<?php

namespace App\Exports;

use App\Reports\Report;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Exporta cualquier reporte: usa las mismas filas que la pantalla (el reporte las
 * entrega ya armadas), así el Excel nunca difiere de lo que se ve.
 */
class ReportExport implements FromArray, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    public function __construct(private readonly Report $report)
    {
    }

    public function array(): array
    {
        return $this->report->exportRows();
    }

    public function headings(): array
    {
        return $this->report->headings();
    }

    public function title(): string
    {
        // Excel limita el nombre de hoja a 31 caracteres y no admite : \ / ? * [ ]
        return mb_substr(preg_replace('/[:\\\\\/?*\[\]]/', '', $this->report->title()), 0, 31);
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
