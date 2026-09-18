<div style="padding: 10px 0 25px 0;">
    <div style="padding: 20px 0 10px 0;border-bottom: 1px #000000 solid;">
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td style="width: 33.33%; text-align: center;">
                        <img style="width: 165px;height: auto; margin: 0 auto;" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path("/images/gls.png"))) }}" alt="GLSGroup"/>
                    </td>
                    <td style="width: 33.33%; font-size: 11px; line-height: 13px; white-space: nowrap; text-align: center;">
                        <p style="margin: 0">CHECKLIST</p>
                        <p style="margin: 0">Original Not Negotiable</p>
                    </td>
                    <td style="width: 33.33%; font-size: 9px; line-height: 11px; white-space: nowrap; text-align: center;">
                        <p style="margin: 0">GLS Forwarding Group, S.A de C.V.</p>
                        <p style="margin: 0">Bulevar Luis D. Colosio 671-PISO 6 OFICINA 601</p>
                        <p style="margin: 0">Hermosillo,Sonora 83249</p>
                        <p style="margin: 0">Ph:+1-520-259-4258 or +52-662-520-2079</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%;margin-top: 10px">
            <tbody>
                <tr>
                    <td style="width: 100%; font-size: 9px; line-height: 11px; white-space: nowrap; text-align: center;">
                        @if($service::class == \App\Models\OrderShipment::class)
                        <p style="margin: 0">{{$service->order->code}} - {{$service->tracking_code}} – {{$service->tracking_number}} - {{explode(' ', $service->order->createdBy->name)[0][0] ?? null}}{{explode(' ', $service->order->createdBy->name)[1][0] ?? null}}</p>
                        @endif
                        @if($service::class == \App\Models\OrderImport::class)
                            <p style="margin: 0">{{$service->order->code}} - {{$service->tracking_code}} – {{explode(' ', $service->order->createdBy->name)[0][0] ?? null}}{{explode(' ', $service->order->createdBy->name)[1][0] ?? null}}</p>
                        @endif
                        @if($service::class == \App\Models\WarehouseStorage::class)
                            <p style="margin: 0">{{$service->order->code}} - {{$service->tracking_code}} – {{explode(' ', $service->order->createdBy->name)[0][0] ?? null}}{{explode(' ', $service->order->createdBy->name)[1][0] ?? null}}</p>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="height: 20px; width: 100%"></div>
    <table style="width: 100%; border-collapse: collapse;">
        <tbody>
            <tr>
                <td style="width: 191.5px;padding: 5px 0; font-weight: bold; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;background-color: #d9d9d9;">Customer</td>
                <td style="width: 191.5px;padding: 5px 0; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;">{{ $order->client->company_name }}</td>
                <td style="width: 191.5px;padding: 5px 0; font-weight: bold; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;background-color: #d9d9d9;">User</td>
                <td style="width: 191.5px;padding: 5px 0; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;">{{ $order->contact->name }}</td>
            </tr>
            <tr>
                <td style="width: 191.5px;padding: 5px 0; font-weight: bold; font-size: 8px; line-height: 12px; vertical-align: top;border: 1px #000000 solid;text-align: center;background-color: #d9d9d9;">Reference</td>
                <td colspan="3" style="width: 574.5px;padding: 5px 0; font-size: 8px; line-height: 12px; vertical-align: top;border: 1px #000000 solid;text-align: center;">{{ $order->reference }}</td>
            </tr>
        </tbody>
    </table>
    @if($service::class == \App\Models\OrderShipment::class)
    <div style="height: 20px; width: 100%"></div>
    <table style="width: 100%; border-collapse: collapse;">
        <tbody>
            <tr>
                <td style="width: 191.5px;padding: 5px 0; font-weight: bold; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;background-color: #d9d9d9;">ETD</td>
                <td style="width: 191.5px;padding: 5px 0; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;">{{ $service->estimated_time_departure?->isoFormat('DD/MM/YYYY') }}</td>
                <td style="width: 191.5px;padding: 5px 0; font-weight: bold; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;background-color: #d9d9d9;">ATD</td>
                <td style="width: 191.5px;padding: 5px 0; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;">
                    @if($service->start_date)
                        {{ $service->start_date?->isoFormat('DD/MM/YYYY') }}
                    @else
                        <div style="display: flex;align-items: center; justify-content: center">
                            <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA2NDAgNjQwIiBzdHlsZT0id2lkdGg6IDE1cHg7IGhlaWdodDogMTVweDtmaWxsOiByZWQ7Ij48cGF0aCBkPSJNMTYwIDk2QzEyNC43IDk2IDk2IDEyNC43IDk2IDE2MEw5NiA0ODBDOTYgNTE1LjMgMTI0LjcgNTQ0IDE2MCA1NDRMNDgwIDU0NEM1MTUuMyA1NDQgNTQ0IDUxNS4zIDU0NCA0ODBMNTQ0IDE2MEM1NDQgMTI0LjcgNTE1LjMgOTYgNDgwIDk2TDE2MCA5NnpNMjMxIDIzMUMyNDAuNCAyMjEuNiAyNTUuNiAyMjEuNiAyNjQuOSAyMzFMMzE5LjkgMjg2TDM3NC45IDIzMUMzODQuMyAyMjEuNiAzOTkuNSAyMjEuNiA0MDguOCAyMzFDNDE4LjEgMjQwLjQgNDE4LjIgMjU1LjYgNDA4LjggMjY0LjlMMzUzLjggMzE5LjlMNDA4LjggMzc0LjlDNDE4LjIgMzg0LjMgNDE4LjIgMzk5LjUgNDA4LjggNDA4LjhDMzk5LjQgNDE4LjEgMzg0LjIgNDE4LjIgMzc0LjkgNDA4LjhMMzE5LjkgMzUzLjhMMjY0LjkgNDA4LjhDMjU1LjUgNDE4LjIgMjQwLjMgNDE4LjIgMjMxIDQwOC44QzIyMS43IDM5OS40IDIyMS42IDM4NC4yIDIzMSAzNzQuOUwyODYgMzE5LjlMMjMxIDI2NC45QzIyMS42IDI1NS41IDIyMS42IDI0MC4zIDIzMSAyMzF6Ii8+PC9zdmc+"  style="height: 20px;width: 20px;" alt="X"/>
                        </div>
                    @endif
                </td>
            </tr>
            <tr>
                <td style="width: 191.5px;padding: 5px 0; font-weight: bold; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;background-color: #d9d9d9;">ETA</td>
                <td style="width: 191.5px;padding: 5px 0; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;">{{ $service->estimated_time_arrival?->isoFormat('DD/MM/YYYY') }}</td>
                <td style="width: 191.5px;padding: 5px 0; font-weight: bold; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;background-color: #d9d9d9;">ATA</td>
                <td style="width: 191.5px;padding: 5px 0; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;">
                    @if($service->end_date)
                        {{ $service->end_date?->isoFormat('DD/MM/YYYY') }}
                    @else
                        <div style="display: flex;align-items: center; justify-content: center">
                            <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA2NDAgNjQwIiBzdHlsZT0id2lkdGg6IDE1cHg7IGhlaWdodDogMTVweDtmaWxsOiByZWQ7Ij48cGF0aCBkPSJNMTYwIDk2QzEyNC43IDk2IDk2IDEyNC43IDk2IDE2MEw5NiA0ODBDOTYgNTE1LjMgMTI0LjcgNTQ0IDE2MCA1NDRMNDgwIDU0NEM1MTUuMyA1NDQgNTQ0IDUxNS4zIDU0NCA0ODBMNTQ0IDE2MEM1NDQgMTI0LjcgNTE1LjMgOTYgNDgwIDk2TDE2MCA5NnpNMjMxIDIzMUMyNDAuNCAyMjEuNiAyNTUuNiAyMjEuNiAyNjQuOSAyMzFMMzE5LjkgMjg2TDM3NC45IDIzMUMzODQuMyAyMjEuNiAzOTkuNSAyMjEuNiA0MDguOCAyMzFDNDE4LjEgMjQwLjQgNDE4LjIgMjU1LjYgNDA4LjggMjY0LjlMMzUzLjggMzE5LjlMNDA4LjggMzc0LjlDNDE4LjIgMzg0LjMgNDE4LjIgMzk5LjUgNDA4LjggNDA4LjhDMzk5LjQgNDE4LjEgMzg0LjIgNDE4LjIgMzc0LjkgNDA4LjhMMzE5LjkgMzUzLjhMMjY0LjkgNDA4LjhDMjU1LjUgNDE4LjIgMjQwLjMgNDE4LjIgMjMxIDQwOC44QzIyMS43IDM5OS40IDIyMS42IDM4NC4yIDIzMSAzNzQuOUwyODYgMzE5LjlMMjMxIDI2NC45QzIyMS42IDI1NS41IDIyMS42IDI0MC4zIDIzMSAyMzF6Ii8+PC9zdmc+"  style="height: 20px;width: 20px;" alt="X"/>
                        </div>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
    @endif
    @if($service::class == \App\Models\OrderShipment::class)
    <div style="height: 20px; width: 100%"></div>
    <table style="width: 100%; border-collapse: collapse;">
        <tbody>
        @foreach ($service->transportations as $transportation)
            @if($loop->odd)
            <tr>
            @endif
                <td style="width: 191.5px;padding: 5px 0; font-weight: bold; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;background-color: #d9d9d9;">Supplier {{$loop->iteration}}</td>
                <td style="width: 191.5px;padding: 5px 0; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;">{{ $transportation->agency->name }}</td>
            @if($loop->even)
            </tr>
            @endif
        @endforeach
        @if(count($service->transportations) % 2 != 0)
                <td style="width: 191.5px;padding: 5px 0; font-weight: bold; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;background-color: #d9d9d9;"></td>
                <td style="width: 191.5px;padding: 5px 0; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;"></td>
            </tr>
        @endif
        </tbody>
    </table>
    @endif
    <div style="height: 20px; width: 100%"></div>
    <table style="width: 100%; border-collapse: collapse;">
        <tbody>
        <tr>
            <td colspan="4" style="width: 766px;padding: 5px 0; font-weight: bold; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;background-color: #d9d9d9;">Milestones</td>
        </tr>
        @foreach ($milestones as $milestone)
            @if($loop->odd)
                <tr>
                    @endif
                    <td style="width: 191.5px;padding: 5px 0; font-weight: bold; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;background-color: #d9d9d9;">{{ $milestone["status"] }}</td>
                    <td style="width: 191.5px;padding: 5px 0; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;">
                        <div style="display: flex;align-items: center; justify-content: center">
                            {{ $milestone["date"]->isoFormat('DD/MM/YYYY HH:mm') }}
                        </div>
                    </td>
                    @if($loop->even)
                </tr>
            @endif
        @endforeach
        @if(count($milestones) % 2 != 0)
            <td style="width: 191.5px;padding: 5px 0; font-weight: bold; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;background-color: #d9d9d9;"></td>
            <td style="width: 191.5px;padding: 5px 0; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;"></td>
            </tr>
        @endif
        </tbody>
    </table>
    <div style="height: 20px; width: 100%"></div>
    <table style="width: 100%; border-collapse: collapse;">
        <tbody>
            <tr>
                <td colspan="4" style="width: 766px;padding: 5px 0; font-weight: bold; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;background-color: #d9d9d9;">Documentos</td>
            </tr>
            @foreach ($documentTypes as $docType)
            @if($loop->odd)
            <tr>
            @endif
                <td style="width: 191.5px;padding: 5px 0; font-weight: bold; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;background-color: #d9d9d9;">{{ $docType->name }}</td>
                <td style="width: 191.5px;padding: 5px 0; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;">
                    @if($docType->name == 'BILL OF LADING')
                        <div style="display: flex;align-items: center; justify-content: center">
                            <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA2NDAgNjQwIiBzdHlsZT0id2lkdGg6IDE1cHg7IGhlaWdodDogMTVweDtmaWxsOiBncmVlbjsiPjxwYXRoIGQ9Ik00ODAgOTZDNTE1LjMgOTYgNTQ0IDEyNC43IDU0NCAxNjBMNTQ0IDQ4MEM1NDQgNTE1LjMgNTE1LjMgNTQ0IDQ4MCA1NDRMMTYwIDU0NEMxMjQuNyA1NDQgOTYgNTE1LjMgOTYgNDgwTDk2IDE2MEM5NiAxMjQuNyAxMjQuNyA5NiAxNjAgOTZMNDgwIDk2ek00MzggMjA5LjdDNDI3LjMgMjAxLjkgNDEyLjMgMjA0LjMgNDA0LjUgMjE1TDI4NS4xIDM3OS4yTDIzMyAzMjcuMUMyMjMuNiAzMTcuNyAyMDguNCAzMTcuNyAxOTkuMSAzMjcuMUMxODkuOCAzMzYuNSAxODkuNyAzNTEuNyAxOTkuMSAzNjFMMjcxLjEgNDMzQzI3Ni4xIDQzOCAyODMgNDQwLjUgMjg5LjkgNDQwQzI5Ni44IDQzOS41IDMwMy4zIDQzNS45IDMwNy40IDQzMC4yTDQ0My4zIDI0My4yQzQ1MS4xIDIzMi41IDQ0OC43IDIxNy41IDQzOCAyMDkuN3oiLz48L3N2Zz4="  style="height: 20px;width: 20px;" alt="check"/>
                            <span style="margin-left: 2px;">(1)</span>
                        </div>
                    @else
                        @if($docType->total > 0)
                            <div style="display: flex;align-items: center; justify-content: center">
                                <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA2NDAgNjQwIiBzdHlsZT0id2lkdGg6IDE1cHg7IGhlaWdodDogMTVweDtmaWxsOiBncmVlbjsiPjxwYXRoIGQ9Ik00ODAgOTZDNTE1LjMgOTYgNTQ0IDEyNC43IDU0NCAxNjBMNTQ0IDQ4MEM1NDQgNTE1LjMgNTE1LjMgNTQ0IDQ4MCA1NDRMMTYwIDU0NEMxMjQuNyA1NDQgOTYgNTE1LjMgOTYgNDgwTDk2IDE2MEM5NiAxMjQuNyAxMjQuNyA5NiAxNjAgOTZMNDgwIDk2ek00MzggMjA5LjdDNDI3LjMgMjAxLjkgNDEyLjMgMjA0LjMgNDA0LjUgMjE1TDI4NS4xIDM3OS4yTDIzMyAzMjcuMUMyMjMuNiAzMTcuNyAyMDguNCAzMTcuNyAxOTkuMSAzMjcuMUMxODkuOCAzMzYuNSAxODkuNyAzNTEuNyAxOTkuMSAzNjFMMjcxLjEgNDMzQzI3Ni4xIDQzOCAyODMgNDQwLjUgMjg5LjkgNDQwQzI5Ni44IDQzOS41IDMwMy4zIDQzNS45IDMwNy40IDQzMC4yTDQ0My4zIDI0My4yQzQ1MS4xIDIzMi41IDQ0OC43IDIxNy41IDQzOCAyMDkuN3oiLz48L3N2Zz4="  style="height: 20px;width: 20px;" alt="check"/>
                                <span style="margin-left: 2px;">({{ $docType->total }})</span>
                            </div>
                        @else
                            @if($docType->isRequired)
                                <div style="display: flex;align-items: center; justify-content: center">
                                    <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA2NDAgNjQwIiBzdHlsZT0id2lkdGg6IDE1cHg7IGhlaWdodDogMTVweDtmaWxsOiByZWQ7Ij48cGF0aCBkPSJNMTYwIDk2QzEyNC43IDk2IDk2IDEyNC43IDk2IDE2MEw5NiA0ODBDOTYgNTE1LjMgMTI0LjcgNTQ0IDE2MCA1NDRMNDgwIDU0NEM1MTUuMyA1NDQgNTQ0IDUxNS4zIDU0NCA0ODBMNTQ0IDE2MEM1NDQgMTI0LjcgNTE1LjMgOTYgNDgwIDk2TDE2MCA5NnpNMjMxIDIzMUMyNDAuNCAyMjEuNiAyNTUuNiAyMjEuNiAyNjQuOSAyMzFMMzE5LjkgMjg2TDM3NC45IDIzMUMzODQuMyAyMjEuNiAzOTkuNSAyMjEuNiA0MDguOCAyMzFDNDE4LjEgMjQwLjQgNDE4LjIgMjU1LjYgNDA4LjggMjY0LjlMMzUzLjggMzE5LjlMNDA4LjggMzc0LjlDNDE4LjIgMzg0LjMgNDE4LjIgMzk5LjUgNDA4LjggNDA4LjhDMzk5LjQgNDE4LjEgMzg0LjIgNDE4LjIgMzc0LjkgNDA4LjhMMzE5LjkgMzUzLjhMMjY0LjkgNDA4LjhDMjU1LjUgNDE4LjIgMjQwLjMgNDE4LjIgMjMxIDQwOC44QzIyMS43IDM5OS40IDIyMS42IDM4NC4yIDIzMSAzNzQuOUwyODYgMzE5LjlMMjMxIDI2NC45QzIyMS42IDI1NS41IDIyMS42IDI0MC4zIDIzMSAyMzF6Ii8+PC9zdmc+"  style="height: 20px;width: 20px;" alt="X"/>
                                </div>
                            @else
                                -
                            @endif
                        @endif
                    @endif
                </td>
            @if($loop->even)
            </tr>
            @endif
            @endforeach
            @if(count($documentTypes) % 2 != 0)
                <td style="width: 191.5px;padding: 5px 0; font-weight: bold; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;background-color: #d9d9d9;"></td>
                <td style="width: 191.5px;padding: 5px 0; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;"></td>
            </tr>
            @endif
        </tbody>
    </table>
    <div style="height: 20px; width: 100%"></div>
    <table style="width: 100%; border-collapse: collapse;">
        <tbody>
            <tr>
                <td style="width: 766px;padding: 5px 0; font-weight: bold; font-size: 8px; line-height: 12px; vertical-align: middle;border: 1px #000000 solid;text-align: center;background-color: #d9d9d9;">COMENTARIOS INTERNOS</td>
            </tr>
            <tr>
                <td style="width: 766px;padding: 5px 0; font-size: 8px; line-height: 12px; vertical-align: top;border: 1px #000000 solid;text-align: center;">{{ $service->checklist_comments }}</td>
            </tr>
        </tbody>
    </table>
</div>
