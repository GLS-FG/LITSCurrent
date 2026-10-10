<div style="padding: 20px 0;border-bottom: 1px #d1d5dc solid;">
    <table style="width: 100%;">
        <tbody>
            <tr>
                <td style="width: 33.33%; text-align: center;">
                    <img style="width: 165px;height: auto; margin: 0 auto;" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path("/images/gls.png"))) }}" alt="GLSGroup"/>
                </td>
                <td style="width: 33.33%; font-size: 11px; line-height: 13px; white-space: nowrap; text-align: center;">
                    <p style="margin: 0">BILL OF LADING</p>
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
</div>
<div style="padding: 10px 0 25px 0;">
    <table style="width: 100%; border-collapse: collapse;">
        <tbody>
            <tr>
                <td style="width: 50px; padding: 5px 5px; font-size: 9px; line-height: 9px; white-space: nowrap; text-align: left; vertical-align: top">Ship From:</td>
                <td style="width: 250px; padding: 5px 5px 30px 5px; font-size: 11px; line-height: 12px; vertical-align: top;border: 1px #d1d5dc solid;">{!! nl2br($shipment->ship_from) !!}</td>
                <td style="width: 175px; padding: 5px 5px; font-size: 9px; line-height: 9px; white-space: nowrap; text-align: right; vertical-align: top">Ship To:</td>
                <td style="width: 250px; padding: 5px 5px 30px 5px; font-size: 11px; line-height: 12px; vertical-align: top;border: 1px #d1d5dc solid;">{!! nl2br($shipment->ship_to) !!}</td>
            </tr>
        </tbody>
    </table>
    <div style="height: 20px; width: 100%"></div>
    <table style="width: 100%; border-collapse: collapse;">
        <tbody>
            <tr>
                <td style="width: 300px;padding: 5px 5px; font-size: 8px; line-height: 12px; vertical-align: top;border: 1px #d1d5dc solid;text-align: left;">
                    All Freight Charges PPD/3rd party bill to :
                </td>
                <td style="width: 115px;"></td>
                <td style="width: 60px;"></td>
                <td style="width: 70px;padding: 5px 0; font-weight: bold; font-size: 8px; line-height: 12px; vertical-align: top;border: 1px #d1d5dc solid;text-align: center;">LITS #</td>
                <td style="width: 175px;padding: 5px 0; font-size: 8px; line-height: 12px; vertical-align: top;border: 1px #d1d5dc solid;text-align: center;">{{ $order->code }}</td>
            </tr>
            <tr>
                <td style="width: 300px;padding: 5px 5px; font-size: 8px; line-height: 12px; vertical-align: top;border: 1px #d1d5dc solid;text-align: left;">
                    GLS Forwarding Group, S.A de C.V
                </td>
                <td style="width: 115px;"></td>
                <td style="width: 60px;"></td>
                <td style="width: 70px;padding: 5px 0; font-weight: bold; font-size: 8px; line-height: 12px; vertical-align: top;border: 1px #d1d5dc solid;text-align: center;">Tracking</td>
                <td style="width: 175px;padding: 5px 0; font-size: 8px; line-height: 12px; vertical-align: top;border: 1px #d1d5dc solid;text-align: center;">{{ $shipment->tracking_number }}</td>
            </tr>
            <tr>
                <td style="width: 300px;padding: 5px 5px; font-size: 8px; line-height: 12px; vertical-align: top;border: 1px #d1d5dc solid;text-align: left;">
                    <p style="font-weight: bold; margin: 0 0 5px 0;">Contact:{{$order->createdBy?->name}}/ FERNANDO WALTERS</p>
                    <p style="margin: 0;">+52 6625202079 / +1 5208227113</p>
                </td>
                <td style="width: 115px;"></td>
                <td style="width: 60px;"></td>
                <td style="width: 70px;"></td>
                <td style="width: 175px;"></td>
            </tr>
        </tbody>
    </table>
</div>
<table style="width: 100%;  border-collapse: collapse">
    <tbody>
        <tr>
            <td style="width: 300px;padding: 20px 0; font-size: 8px; line-height: 10px; vertical-align: top; text-align: center; border: 1px #d1d5dc solid">
                <p style="font-weight: bold; margin: 0 0 5px 0">Ship ID /Customer PO / Reference No.</p>
                {{ $order->reference }}
            </td>
            <td style="width: 95px;padding: 20px 0; font-size: 8px; line-height: 10px; vertical-align: top; text-align: center; border: 1px #d1d5dc solid">
                <p style="font-weight: bold; margin: 0 0 5px 0">S-Class</p>
                {{ $shipment->serviceClass?->code }}
            </td>
            <td style="width: 95px;padding: 20px 0; font-size: 8px; line-height: 10px; vertical-align: top; text-align: center; border: 1px #d1d5dc solid">
                <p style="font-weight: bold; margin: 0 0 5px 0">S-Mode</p>
                {{ $shipment->serviceMode?->code }}
            </td>
            <td style="width: 135px;padding: 20px 0; font-size: 8px; line-height: 10px; vertical-align: top; text-align: center; border: 1px #d1d5dc solid">
                <p style="font-weight: bold; margin: 0 0 5px 0">C-Type</p>
                {{ $shipment->classType?->code }}
            </td>
            <td style="width: 95px;padding: 20px 0; font-size: 8px; line-height: 10px; vertical-align: top; text-align: center; border: 1px #d1d5dc solid">
                <p style="font-weight: bold; margin: 0 0 5px 0">S-Level</p>
                {{ $shipment->serviceLevel?->code }}
            </td>
        </tr>
        <tr>
            <td style="width: 300px;padding: 20px 0; font-size: 8px; line-height: 10px; vertical-align: top; text-align: center; border: 1px #d1d5dc solid">
                <p style="font-weight: bold; margin: 0 0 5px 0">Freight Charges</p>
                GLS Forwarding Group, S.A de C.V
            </td>
            <td style="width: 95px;padding: 20px 0; font-size: 8px; line-height: 10px; vertical-align: top; text-align: center; border: 1px #d1d5dc solid">
                <p style="font-weight: bold; margin: 0 0 5px 0">Transaction Date</p>
                {{ $shipment->created_at->toFormattedDateString() }}
            </td>
            <td colspan="3" style="width: 325px;padding: 20px 0; font-size: 8px; line-height: 10px; vertical-align: top; text-align: center; border: 1px #d1d5dc solid">
                <p style="font-weight: bold; margin: 0 0 5px 0">Client reference</p>
                {{ $shipment->reference }}
            </td>
        </tr>
    </tbody>
</table>
<table style="width: 100%;  border-collapse: collapse;margin-top: -1px">
    <tbody>
        <tr>
            <td style="width: 115px; padding: 10px 0; font-weight: bold; font-size: 8px; line-height: 9px; vertical-align: middle; text-align: center; border: 1px #d1d5dc solid">
                Cargo reference:
            </td>
            <td style="min-width: 70px; padding: 10px 0; font-weight: bold; font-size: 8px; line-height: 9px; vertical-align: middle; text-align: center; border: 1px #d1d5dc solid">
                Qty (ea)
            </td>
            <td style="min-width: 70px;padding: 10px 0; font-weight: bold; font-size: 8px; line-height: 9px; vertical-align: middle; text-align: center; border: 1px #d1d5dc solid">
                UOM
            </td>
            <td style="padding: 10px 0; font-weight: bold; font-size: 8px; line-height: 9px; vertical-align: middle; text-align: center; border: 1px #d1d5dc solid">
                Description
            </td>
            <td style="min-width: 70px; padding: 10px 0; font-weight: bold; font-size: 8px; line-height: 9px; vertical-align: middle; text-align: center; border: 1px #d1d5dc solid">
                Weight
            </td>
            <td style="padding: 10px 0; font-weight: bold; font-size: 8px; line-height: 9px; vertical-align: middle; text-align: center; border: 1px #d1d5dc solid">
                Dimensions
            </td>
            <td style="width: 95px; padding: 10px 0; font-weight: bold; font-size: 8px; line-height: 9px; vertical-align: middle; text-align: center; border: 1px #d1d5dc solid">
                Special handling
            </td>
        </tr>
        @foreach ($products as $product)
            <tr>
                <td style="width: 115px; padding: 15px 0; font-size: 8px; line-height: 9px; vertical-align: middle; text-align: center; border: 1px #d1d5dc solid;">
                    {{ $product->reference }}
                </td>
                <td style="padding: 15px 0; font-size: 8px; line-height: 9px; vertical-align: middle; text-align: center; border: 1px #d1d5dc solid;">
                    {{ (float)$product->quantity }}
                </td>
                <td style="padding: 15px 0; font-size: 8px; line-height: 9px; vertical-align: middle; text-align: center; border: 1px #d1d5dc solid;">
                    {{ $product->container }}
                </td>
                <td style="padding: 15px 0; font-size: 8px; line-height: 9px; vertical-align: middle; text-align: center; border: 1px #d1d5dc solid;">
                    {{ $product->product }}
                </td>
                <td style="padding: 15px 0; font-size: 8px; line-height: 9px; vertical-align: middle; text-align: center; border: 1px #d1d5dc solid;">
                    <p>{{$product->weight}} {{$product->weight_measure}}</p>
                </td>
                <td style="padding: 15px 0; font-size: 8px; line-height: 9px; vertical-align: middle; text-align: center; border: 1px #d1d5dc solid;">
                    @if($product->dimensions)
                        {{$product->dimensions}}
                    @else
                        {{(float)$product->length}} X {{(float)$product->width}} X {{(float)$product->height}} {{$product->unit_measure}}
                    @endif
                </td>
                <td style="padding: 15px 10px; font-size: 8px; line-height: 9px; vertical-align: middle; text-align: left; border-right: 1px #d1d5dc solid;">
                    @if ($loop->first)
                        @if($shipment->oversize == "Si")
                            <p style="margin-bottom: 5px">Overload: Yes</p>
                        @endif
                        @if($shipment->hazardous_material == "Si")
                            <p style="margin-bottom: 5px">Hazardous Material: Yes</p>
                        @endif
                        @if($shipment->refrigerated == "Si")
                            <p style="margin-bottom: 5px">Refrigerated: Yes</p>
                        @endif
                        @if($shipment->insurance == "Si")
                            <p style="margin-bottom: 5px">Insurance: Yes</p>
                        @endif
                        @if($shipment->tarps == "Si")
                            <p style="margin-bottom: 5px">Tarps: Yes</p>
                        @endif
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
        <tr>
            <td style="width: 115px; padding: 10px 10px; font-size: 8px; line-height: 9px; vertical-align: top; text-align: center; border-left: 1px #d1d5dc solid;">
            </td>
            <td colspan="5" style="padding: 10px 10px; font-size: 8px; line-height: 9px; vertical-align: middle; border: 1px #d1d5dc solid;">
                <p style="margin: 0;padding-bottom: 5px;">{!! nl2br($shipment->comments) !!}</p>
                <br/>
                <p style="font-weight: bold; margin: 0;padding-bottom: 5px;">Shipper Special Instructions:</p>
                <p style="margin: 0">{!! nl2br($shipment->instructions1) !!}</p>
                <br/>
                <p style="font-weight: bold; margin: 0;padding-bottom: 5px;">Consignee Special Instructions:</p>
                <p style="margin: 0">{!! nl2br($shipment->instructions2) !!}</p>
            </td>
            <td style="width: 115px; padding: 10px 10px; font-size: 8px; line-height: 9px; vertical-align: top; text-align: center; border-right: 1px #d1d5dc solid; border-top: 1px #d1d5dc solid;">
            </td>
        </tr>
        <tr>
            <td style="width: 115px; padding: 10px 10px; font-size: 8px; line-height: 9px; vertical-align: top; text-align: center; border-left: 1px #d1d5dc solid;border-bottom: 1px #d1d5dc solid;">
            </td>
            <td colspan="5" style="padding: 10px 10px; font-size: 8px; line-height: 9px; vertical-align: middle; border: 1px #d1d5dc solid;">
                <p style="font-weight: bold;margin: 0;">The Shipper certifies that the above named are properly classifued,described,marked,labeled and packaged, and are In proper condition for transportation, according to the applicable regulations of the Department Of Transportation</p>
                <br/>
                <table style="border-collapse: collapse">
                    <tbody>
                    <tr>
                        <td style="padding: 0 20px 0 0; white-space: nowrap;">Shipper Signature X</td>
                        <td style="padding: 10px 20px 0 0;">
                            <div style="width: 70px; border-bottom: 1px black solid"></div>
                        </td>
                        <td style="padding: 0 20px 0 10px; white-space: nowrap;">Date</td>
                        <td style="padding: 10px 20px 0 0;">
                            <div style="width: 50px; border-bottom: 1px black solid"></div>
                        </td>
                        <td style="padding: 0 20px 0 10px; white-space: nowrap;">Trailer #</td>
                        <td style="padding: 10px 20px 0 0;">
                            <div style="width: 70px; border-bottom: 1px black solid"></div>
                        </td>
                    </tr>
                    <tr><td style="padding: 10px 30px 0 10px; white-space: nowrap;">&nbsp;</td></tr>
                    <tr>
                        <td style="padding: 0 20px 0 0; white-space: nowrap;">Consignee Signature X</td>
                        <td style="padding: 10px 20px 0 0;">
                            <div style="width: 70px; border-bottom: 1px black solid"></div>
                        </td>
                        <td style="padding: 0 20px 0 10px; white-space: nowrap;">Date</td>
                        <td style="padding: 10px 20px 0 0;">
                            <div style="width: 50px; border-bottom: 1px black solid"></div>
                        </td>
                        <td style="padding: 0 20px 0 10px; white-space: nowrap;">Seal #</td>
                        <td style="padding: 10px 20px 0 0;">
                            <div style="width: 70px; border-bottom: 1px black solid"></div>
                        </td>
                    </tr>
                    <tr><td style="padding: 10px 30px 0 10px; white-space: nowrap;">&nbsp;</td></tr>
                    <tr>
                        <td style="padding: 0 20px 0 0; white-space: nowrap;">Driver Signature X</td>
                        <td style="padding: 10px 20px 0 0;">
                            <div style="width: 70px; border-bottom: 1px black solid"></div>
                        </td>
                        <td style="padding: 0 20px 0 10px; white-space: nowrap;">Date</td>
                        <td style="padding: 10px 20px 0 0;">
                            <div style="width: 50px; border-bottom: 1px black solid"></div>
                        </td>
                        <td style="padding: 0 20px 0 10px; white-space: nowrap;">Seal #</td>
                        <td style="padding: 10px 20px 0 0;">
                            <div style="width: 70px; border-bottom: 1px black solid"></div>
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    </tbody>
                </table>
            </td>
            <td style="width: 115px; padding: 10px 10px; font-size: 8px; line-height: 9px; vertical-align: top; text-align: center; border-right: 1px #d1d5dc solid;border-bottom: 1px #d1d5dc solid;">
            </td>
        </tr>
    </tbody>
</table>
