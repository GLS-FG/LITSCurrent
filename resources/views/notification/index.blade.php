@section('title', __('Notification Center'))
<x-layout-app>
    <section>
        <x-navigation.breadcrumbs :links="[__('Notification Center') => '#']" />
        <x-headings.without-action
            :title="__('Notification Center') . ' (' . count($notifications) . ')'"
        />
        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif
        <div class="mt-2 flow-root">
            <div class="shadow-lits-card rounded-md bg-white dark:bg-lits-blue-550">
                <div class="px-4 py-5 sm:p-4 border-b border-gray-200 dark:border-lits-blue-450">
                    <form method="GET" action="{{ route('notifications.index') }}" class="w-full block md:flex items-center gap-2">
                        <div class="flex-1 md:flex items-between gap-2">
                            <div class="-mr-px flex items-center px-0 mt-2 md:mt-0">
                                <div class="grid w-full grid-cols-1 relative">
                                    <input type="text" value="{{ request('code_search') }}" autocomplete="off" name="code_search" placeholder="{{__('indexes.search_orders')}}" class="col-start-1 row-start-1 block w-full rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-10 pl-10 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    <i class="fa-regular fa-magnifying-glass text-gray-400 dark:text-gray-500 pointer-events-none col-start-1 row-start-1 ml-3 self-center"></i>
                                    @if(request('code_search'))
                                        <a href="{{ route('notifications.index') }}" class="absolute right-3 size-5 self-center text-gray-400 dark:text-gray-500 hover:text-gray-600" aria-hidden="true" data-slot="icon">
                                            <i class="fa-regular fa-xmark"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="-mr-px flex items-center px-0 mt-2 md:mt-0">
                                <div class="grid w-full grid-cols-1 relative">
                                    <input type="text" value="{{ request('client_search') }}" autocomplete="off" name="client_search" placeholder="{{__('indexes.search_clients')}}" class="col-start-1 row-start-1 block w-full rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-10 pl-10 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    <i class="fa-regular fa-user-magnifying-glass text-gray-400 dark:text-gray-500 pointer-events-none col-start-1 row-start-1 ml-3 self-center"></i>
                                    @if(request('client_search'))
                                        <a href="{{ route('notifications.index') }}" class="absolute right-3 size-5 self-center text-gray-400 dark:text-gray-500 hover:text-gray-600" aria-hidden="true" data-slot="icon">
                                            <i class="fa-regular fa-xmark"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="-mr-px grow flex-1/3 shrink-0 flex items-center px-0 mt-2 md:mt-0">
                                <div class="grid w-full grid-cols-1 relative">
                                    <input type="text" value="{{ request('reference_search') }}" autocomplete="off" name="reference_search" placeholder="{{__('indexes.search_references')}}" class="col-start-1 row-start-1 block w-full rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-10 pl-10 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    <i class="fa-regular fa-file-magnifying-glass text-gray-400 dark:text-gray-500 pointer-events-none col-start-1 row-start-1 ml-3 self-center"></i>
                                    @if(request('reference_search'))
                                        <a href="{{ route('notifications.index') }}" class="absolute right-3 size-5 self-center text-gray-400 dark:text-gray-500 hover:text-gray-600" aria-hidden="true" data-slot="icon">
                                            <i class="fa-regular fa-xmark"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="-mr-px flex items-center px-0 mt-2 md:mt-0">
                                <div class="grid w-full grid-cols-1 relative">
                                    <input type="text" value="{{ request('service_search') }}" autocomplete="off" name="service_search" placeholder="Service Type" class="col-start-1 row-start-1 block w-full rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-10 pl-10 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    <i class="fa-regular fa-magnifying-glass text-gray-400 dark:text-gray-500 pointer-events-none col-start-1 row-start-1 ml-3 self-center"></i>
                                    @if(request('service_search'))
                                        <a href="{{ route('notifications.index') }}" class="absolute right-3 size-5 self-center text-gray-400 dark:text-gray-500 hover:text-gray-600" aria-hidden="true" data-slot="icon">
                                            <i class="fa-regular fa-xmark"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="-mr-px flex items-center px-0 mt-2 md:mt-0">
                                <div class="grid w-full grid-cols-1 relative">
                                    <input type="text" value="{{ request('status_search') }}" autocomplete="off" name="status_search" placeholder="{{__('indexes.search_status')}}" class="col-start-1 row-start-1 block w-full rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-10 pl-10 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    <i class="fa-solid fa-bars-progress text-gray-400 dark:text-gray-500 pointer-events-none col-start-1 row-start-1 ml-3 self-center"></i>
                                    @if(request('status_search'))
                                        <a href="{{ route('notifications.index') }}" class="absolute right-3 size-5 self-center text-gray-400 dark:text-gray-500 hover:text-gray-600" aria-hidden="true" data-slot="icon">
                                            <i class="fa-regular fa-xmark"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="w-full sm:w-auto mt-2 md:mt-0 flex flex-nowrap gap-2">
                            <button type="submit" class="inline-flex w-full justify-center items-center gap-x-1.5 rounded-md  px-3 py-2 text-sm  ring-1  ring-inset bg-white dark:bg-lits-blue-550 text-gray-900 dark:text-gray-50 ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 hover:cursor-pointer">
                                {{__('indexes.search')}}
                                <i class="fa-regular fa-magnifying-glass -mr-1"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <div class="inline-block min-w-full align-middle">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-gray-50 sm:pl-6"></th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-gray-50">Orden</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-gray-50">Cliente</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-gray-50">Referencia</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-gray-50">Estatus</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-gray-50">Eventos</th>
                                <th scope="col" class="relative py-3.5 pr-4 pl-3 sm:pr-6"><span class="sr-only">Acciones</span></th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 ">
                            @forelse($notifications as $notification)
                                <tr>
                                    <td class="py-4 pl-4 sm:pl-6">
                                        @if($notification->service->urgent)
                                            <i class="fa-regular fa-light-emergency-on text-2xl text-red-500 dark:text-red-400"></i>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4 text-sm whitespace-nowrap ">
                                        <a href="{{route('orders.shipments.show', ['order' => $notification->service->order->id, 'shipment' => $notification->service->id, 'activeTab' => 2])}}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900">{{ $notification->service->order->code }}</a>
                                        <p class="text-gray-500 dark:text-gray-400 text-xs">{{ $notification->service->order->createdBy->name }}</p>
                                    </td>
                                    <td class="px-3 py-4 text-gray-900 dark:text-gray-50 ">
                                        <div class="flex items-center">
                                            <div class="size-8 shrink-0 flex items-center justify-center rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700">
                                                <img alt="{{$notification->service->order->client->trade_name}}" src="{{ route('clients.logos', [ 'filename' => str_replace(".","_",str_replace("logos/", "", $notification->service->order->client->image))]) }}" />
                                            </div>
                                            <div class="ml-2">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-50">{{ $notification->service->order->client->trade_name }}</div>
                                                <div class="text-gray-500 dark:text-gray-400 text-xs">{{$notification->service->order->contact->name}}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $notification->service->reference }}
                                    </td>
                                    <td class="px-3 py-4 text-sm">
                                        <a
                                            href="{{route('orders.shipments.show', ['order' => $notification->service->order->id, 'shipment' => $notification->service->id, 'activeTab' => 2])}}"
                                            data-tippy-content="Ir a estatus"
                                            role="button"
                                        >
                                            @if($notification->service->latestLocation != null)
                                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium inset-ring {{$notification->service->latestLocation->status->color}}">{{$notification->service->latestLocation->status->name}}</span>
                                            @else
                                                <span class="whitespace-nowrap text-gray-500 dark:text-gray-400 text-xs">No actualizado</span>
                                            @endif
                                        </a>
                                    </td>
                                    <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-900 dark:text-gray-50">
                                        <ul class="space-y-1 mt-1.5">
                                            @foreach($notification->events as $event)
                                                <li class="flex space-x-2 text-sm items-center">
                                                    @if($event->done)
                                                        <i class="fa-solid fa-square-check text-green-500 dark:text-green-400"></i>
                                                        <p class="line-through text-gray-500 dark:text-gray-400">{{$event->title}}</p>
                                                    @else
                                                        <i class="fa-regular fa-square"></i>
                                                        <a href="{{route('orders.shipments.show', ['order' => $notification->service->order->id, 'shipment' => $notification->service->id, 'activeTab' => 2])}}" class="text-gray-900 dark:text-gray-50 hover:text-gray-700 hover:underline">{{ $event->title }}</a>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="py-4 pr-4 pl-3 text-sm whitespace-nowrap sm:pr-6">
                                        <div class="flex items-center gap-x-1">
                                            <a
                                                href="{{route('orders.shipments.show', ['order' => $notification->service->order->id, 'shipment' => $notification->service->id, 'activeTab' => 2])}}"
                                                data-tippy-content="Ver"
                                                role="button"
                                                class="size-7 shrink-0 rounded-md bg-blue-100 dark:bg-blue-500/15 flex items-center justify-center font-semibold text-blue-500 dark:text-blue-400 hover:text-blue-800 hover:bg-blue-200"
                                            >
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
                                        No hay notificaciones pendientes.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout-app>
