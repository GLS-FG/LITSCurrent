@props(['user'])
@php
    $roleEnum = $user->getRoleNames()->map(fn ($role) => \App\Enums\RolesEnum::from($role))->first();
    // Solo sesiones abiertas: Laravel borra las expiradas, no hay historial de accesos.
    $sessions = auth()->user()->can('update', $user) ? $user->activeSessions() : collect();
@endphp
@if($roleEnum)
    <section>
        <h2 class="mb-3 text-xs font-semibold text-gray-400 dark:text-gray-500">Qué puede hacer</h2>
        <ul class="grid gap-2 text-sm sm:grid-cols-2 lg:grid-cols-3">
            @foreach($roleEnum->capabilities() as $label => $allowed)
                <li class="flex items-center gap-2.5 {{ $allowed ? 'text-gray-900 dark:text-gray-50' : 'text-gray-400 dark:text-gray-500' }}">
                    <span class="size-1.5 flex-none rounded-full {{ $allowed ? 'bg-green-500 dark:bg-green-400' : 'bg-gray-300 dark:bg-gray-600' }}"></span>
                    {{ $label }}
                </li>
            @endforeach
        </ul>
    </section>
@endif

{{-- Sesiones activas --}}
@can('update', $user)
    <section>
        <div class="border-b border-gray-200 dark:border-lits-blue-450">
            <span class="-mb-px inline-block border-b-2 border-lits-red-500 pb-2.5 text-sm font-semibold text-lits-red-500">Sesiones activas</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-lits-blue-450">
                        <th scope="col" class="py-2.5 pr-3 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Dispositivo</th>
                        <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Navegador</th>
                        <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">IP</th>
                        <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Última actividad</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $session)
                        @php $agent = \App\View\Helpers\UserAgentSummary::parse($session->user_agent); @endphp
                        <tr class="border-b border-gray-100 dark:border-lits-blue-450/60 hover:bg-gray-50 dark:hover:bg-lits-blue-550/60">
                            <td class="py-3.5 pr-3 text-sm text-gray-900 dark:text-gray-50 whitespace-nowrap">
                                {{ $agent['device'] }}
                                @if($session->id === session()->getId())
                                    <span class="ml-2 inline-flex items-center gap-1.5 text-xs font-medium text-green-700 dark:text-green-400">
                                        <span class="size-1.5 rounded-full bg-green-500 dark:bg-green-400"></span>
                                        Esta sesión
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-3.5 text-sm text-gray-500 dark:text-gray-400">{{ $agent['browser'] }}</td>
                            <td class="px-3 py-3.5 text-sm tabular-nums text-gray-500 dark:text-gray-400">{{ $session->ip_address ?? '—' }}</td>
                            <td class="px-3 py-3.5 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">{{ \Illuminate\Support\Carbon::createFromTimestamp($session->last_activity)->isoFormat('D MMM, HH:mm') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Este usuario no tiene sesiones abiertas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endcan
