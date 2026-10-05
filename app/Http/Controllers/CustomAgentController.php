<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomAgentPostRequest;
use App\Http\Requests\CustomAgentPutRequest;
use App\Models\Country;
use App\Models\Custom;
use App\Models\CustomAgent;
use App\Models\CustomAgentAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomAgentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $orderBy = $request->get('ordering_by');
        $agents = CustomAgent::when($search, fn ($query, $search) => $query
            ->where('name', 'like', '%' . $search . '%')
            ->orWhere('last_name', 'like', '%' . $search . '%')
            ->orWhere('patent', 'like', '%' . $search . '%')
            ->orWhere('phone1', 'like', '%' . $search . '%')
        )
            ->when($orderBy, fn ($query, $orderBy) => $query
                ->orderBy($orderBy, request('ordering_rule', 'desc')),
                fn ($query) => $query
                    ->orderBy('created_at', 'desc')
            )
            ->paginate(20)->withQueryString();
        return view('custom.agent.index', [
            'agents' => $agents
        ]);
    }

    public function create()
    {
        return view('custom.agent.create', [ 'customs' => Custom::all(), 'countries' => Country::select('id', 'name')->orderBy('name')->get() ]);
    }

    public function store(CustomAgentPostRequest $request)
    {
        $validated = $request->validated();
        $newAgentData = $request->safe()->only(['company_name', 'name', 'last_name', 'patent', 'phone1']);
        $newAddress = $request->safe()->only(['address_type', 'street_name', 'street_no', 'neighborhood', 'postal_code', 'city_id', 'state_id', 'country_id']);
        $agent = DB::transaction(function () use ($newAgentData, $newAddress) {
            $newAgent = CustomAgent::create($newAgentData);
            CustomAgentAddress::create(array_merge($newAddress, ['custom_agent_id' => $newAgent->id ]));
            return $newAgent;
        }, 5);
        return redirect()->route('custom-agents.show', [ 'custom_agent' => $agent->id ])
            ->with('success', 'Se creó correctamente el agente.');
    }

    public function show(CustomAgent $customAgent)
    {
        return view('custom.agent.show', [ 'agent' => $customAgent ]);
    }

    public function edit(CustomAgent $customAgent)
    {
        return view('custom.agent.edit', [ 'agent' => $customAgent, 'customs' => Custom::all() ]);
    }

    public function update(CustomAgentPutRequest $request, CustomAgent $customAgent)
    {
        $validated = $request->validated();
        $updateAgentData = $request->safe()->only(['company_name', 'name', 'last_name', 'patent', 'phone1']);
        $customAgent->update($updateAgentData);
        return redirect()->route('custom-agents.show', [ 'custom_agent' => $customAgent ])
            ->with('success', 'Se actualizó correctamente la información del agente aduanal.');
    }

    public function destroy(CustomAgent $customAgent)
    {
        $customAgent->delete();
        return back()->with('success', 'El agente aduanal ha sido eliminado.');
    }
}
