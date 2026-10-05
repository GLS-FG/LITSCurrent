<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\Client;
use App\Models\ServiceClass;
use App\Reports\ActivityByClientReport;
use App\Reports\OpenPendingReport;
use App\Reports\Report;
use App\Reports\ServiceKind;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public const OPEN = 'open';
    public const ACTIVITY = 'activity';

    public function index(Request $request)
    {
        $report = $this->makeReport($request);
        $reportKey = $this->reportKey($request);

        return view('report.index', [
            'report' => $report,
            'reportKey' => $reportKey,
            'clients' => Client::orderBy('company_name')->orderBy('trade_name')->get(['id', 'trade_name', 'company_name']),
            'serviceTypes' => collect(ServiceKind::all())->map(fn (ServiceKind $k) => ['id' => $k->typeId, 'name' => $k->label])->values(),
            'serviceClasses' => ServiceClass::orderBy('name')->get(['id', 'name', 'service_type_id'])
                ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name, 'type' => $c->service_type_id])->values(),
            'openViews' => $reportKey === self::OPEN ? OpenPendingReport::views($report->typeId) : null,
            'openCounts' => $reportKey === self::OPEN ? $report->counts() : null,
        ]);
    }

    public function export(Request $request)
    {
        $report = $this->makeReport($request);
        $name = $this->reportKey($request) === self::OPEN
            ? 'pendientes-abiertos-'.$report->view.'-'.Carbon::today()->format('Ymd')
            : 'actividad-por-cliente-'.$report->from->format('Ymd').'-'.$report->to->format('Ymd');

        return Excel::download(new ReportExport($report), $name.'.xlsx');
    }

    private function reportKey(Request $request): string
    {
        return $request->query('report') === self::ACTIVITY ? self::ACTIVITY : self::OPEN;
    }

    /** Arma el reporte con los filtros validados; la pantalla y el Excel usan exactamente el mismo. */
    private function makeReport(Request $request): Report
    {
        $typeId = $request->integer('service_type_id') ?: null;

        $data = $request->validate([
            'report' => ['nullable', 'in:'.self::OPEN.','.self::ACTIVITY],
            'view' => ['nullable', 'in:'.implode(',', array_keys(OpenPendingReport::views()))],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'service_type_id' => ['nullable', 'integer', 'exists:service_types,id'],
            // Tabla: orden, búsqueda y filtros de columna (el Excel los recibe igual por la URL)
            'sort' => ['nullable', 'string', 'max:30'],
            'dir' => ['nullable', 'in:asc,desc'],
            'q' => ['nullable', 'string', 'max:100'],
            'min_days' => ['nullable', 'integer', 'min:0', 'max:3650'],
            'status' => ['nullable', 'string', 'max:100'],
            'min_orders' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'only_active' => ['nullable', 'in:1'],
            'min_close_days' => ['nullable', 'numeric', 'min:0', 'max:3650'],
            // La clase debe pertenecer al tipo de servicio elegido (si se eligió uno)
            'service_class_id' => ['nullable', 'integer', Rule::exists('service_classes', 'id')
                ->when($typeId, fn ($rule) => $rule->where('service_type_id', $typeId))],
        ]);

        $clientId = $data['client_id'] ?? null;
        $serviceTypeId = $data['service_type_id'] ?? null;
        $serviceClassId = $data['service_class_id'] ?? null;
        $table = Arr::only($data, ['sort', 'dir', 'q', 'min_days', 'status', 'min_orders', 'only_active', 'min_close_days']);

        if ($this->reportKey($request) === self::OPEN) {
            // Si la subvista pedida no aplica con el filtro de servicio, se usa la primera que sí
            $effectiveType = ServiceKind::resolveTypeId($serviceTypeId, $serviceClassId);
            $allowed = array_keys(OpenPendingReport::views($effectiveType));
            $view = in_array($data['view'] ?? null, $allowed, true) ? $data['view'] : $allowed[0];

            return new OpenPendingReport($view, $clientId, $serviceTypeId, $serviceClassId, $table);
        }

        return new ActivityByClientReport(
            isset($data['from']) ? Carbon::parse($data['from']) : Carbon::today()->startOfYear(),
            isset($data['to']) ? Carbon::parse($data['to']) : Carbon::today(),
            $clientId,
            $serviceTypeId,
            $serviceClassId,
            $table,
        );
    }
}
