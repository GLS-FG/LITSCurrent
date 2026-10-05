<?php

namespace App\Reports;

use App\Models\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Los reportes son solo para Super Admin: lo garantiza el grupo de rutas
 * (role:Super Admin en routes/web.php) y el sidebar. Las consultas no filtran por rol.
 *
 * Ordenar, buscar y filtrar la tabla se hace aquí, en el servidor, sobre las filas ya
 * armadas por cada reporte. Así la pantalla y el Excel (que usa la misma clase y los
 * mismos parámetros de la URL) siempre muestran exactamente lo mismo y en el mismo orden.
 */
abstract class Report
{
    /** Opciones de tabla que llegan por URL: sort, dir, q y los filtros de columna de cada reporte. */
    protected array $table = [];

    private ?Collection $arranged = null;

    /**
     * Nombre del cliente igual que en el selector de "Crear orden" (add-order):
     * "razón social / nombre comercial".
     */
    public static function clientLabel(?Client $client): ?string
    {
        return $client ? $client->company_name.' / '.$client->trade_name : null;
    }

    /** @return array<int, string> */
    abstract public function headings(): array;

    /** @return array<int, array<int, mixed>> Filas planas para el Excel. */
    abstract public function exportRows(): array;

    abstract public function title(): string;

    /** Filas sin buscar, filtrar ni ordenar (en el orden natural del reporte). */
    abstract protected function rawRows(): Collection;

    /** @return array<string, callable(object): mixed> clave de columna => valor por el que se ordena (null = al final) */
    abstract protected function sortKeys(): array;

    /** @return array{0: string, 1: string} columna y dirección cuando no se pide otra */
    abstract protected function defaultSort(): array;

    /** Texto en el que busca el cuadro "Buscar en la tabla". */
    abstract protected function searchText(object $row): string;

    /** Filtros de columna propios de cada reporte (el buscador de texto se aplica aparte). */
    abstract protected function passesFilters(object $row): bool;

    /** Claves de los filtros de columna que usa este reporte (para saber si hay alguno activo). */
    abstract protected function columnFilterKeys(): array;

    /** Opciones del filtro "Estatus" (valor => etiqueta); vacío si el reporte no lo tiene. */
    public function statusOptions(): array
    {
        return [];
    }

    /** Filas que ve el usuario: buscadas, filtradas y ordenadas. Las usan la pantalla y el Excel. */
    public function rows(): Collection
    {
        return $this->arranged ??= $this->arrange($this->rawRows());
    }

    /** Total de filas del reporte antes de buscar/filtrar la tabla. */
    public function rowsTotal(): int
    {
        return $this->rawRows()->count();
    }

    public function tableOption(string $key): ?string
    {
        $value = $this->table[$key] ?? null;

        return ($value === null || $value === '') ? null : (string) $value;
    }

    public function sortKey(): string
    {
        $requested = $this->tableOption('sort');

        return $requested && array_key_exists($requested, $this->sortKeys())
            ? $requested
            : $this->defaultSort()[0];
    }

    public function sortDir(): string
    {
        $requested = $this->tableOption('dir');
        $requestedKeyIsValid = $this->tableOption('sort') && array_key_exists($this->tableOption('sort'), $this->sortKeys());

        if (in_array($requested, ['asc', 'desc'], true) && $requestedKeyIsValid) {
            return $requested;
        }

        return $this->defaultSort()[1];
    }

    public function hasColumnFilters(): bool
    {
        foreach ($this->columnFilterKeys() as $key) {
            if ($this->tableOption($key) !== null) {
                return true;
            }
        }

        return false;
    }

    /** ¿Hay búsqueda de texto o algún filtro de columna activo? */
    public function hasTableFilters(): bool
    {
        return $this->tableOption('q') !== null || $this->hasColumnFilters();
    }

    private function arrange(Collection $raw): Collection
    {
        $rows = $raw->values();

        $words = array_filter(preg_split('/\s+/', self::normalize((string) $this->tableOption('q'))));
        if ($words) {
            $rows = $rows->filter(function ($row) use ($words) {
                $haystack = self::normalize($this->searchText($row));

                foreach ($words as $word) {
                    if (! str_contains($haystack, $word)) {
                        return false;
                    }
                }

                return true;
            });
        }

        $rows = $rows->filter(fn ($row) => $this->passesFilters($row))->values();

        $value = $this->sortKeys()[$this->sortKey()];
        [$sortable, $empty] = $rows->partition(fn ($row) => $value($row) !== null);

        // Los vacíos van siempre al final, sin importar la dirección. El orden es estable,
        // así que a igualdad se conserva el orden natural del reporte.
        return $sortable
            ->sortBy($value, SORT_REGULAR, $this->sortDir() === 'desc')
            ->values()
            ->concat($empty->values());
    }

    /** Minúsculas y sin acentos, para que "aduana" encuentre "ADUANA" y "Almacén" encuentre "almacen". */
    protected static function normalize(string $text): string
    {
        return Str::lower(Str::ascii($text));
    }
}
