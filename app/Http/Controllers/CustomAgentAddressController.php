<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomAgentAddressPostRequest;
use App\Http\Requests\CustomAgentAddressPutRequest;
use App\Models\CustomAgent;
use App\Models\CustomAgentAddress;

class CustomAgentAddressController extends Controller
{
    public function create(CustomAgent $customAgent)
    {
        return view('custom.agent.address.create', [ 'agent' => $customAgent ]);
    }

    public function store(CustomAgent $customAgent, CustomAgentAddressPostRequest $request)
    {
        $validated = $request->validated();
        CustomAgentAddress::create(array_merge($validated, ['custom_agent_id' => $customAgent->id ]));
        return redirect()->route('custom-agents.show', [ 'custom_agent' => $customAgent ])
            ->with('success', 'Se creó correctamente la dirección del agente aduanal.');
    }

    public function edit(CustomAgent $customAgent, CustomAgentAddress $address)
    {
        return view('custom.agent.address.edit', [ 'agent' => $customAgent, 'address' => $address ]);
    }

    public function update(CustomAgentAddressPutRequest $request, CustomAgent $customAgent, CustomAgentAddress $address)
    {
        $address->update($request->validated());
        return redirect()->route('custom-agents.show', [ 'custom_agent' => $customAgent ])
            ->with('success', 'Se actualizó correctamente la dirección del agente aduanal.');
    }

    public function destroy(CustomAgent $customAgent, CustomAgentAddress $address)
    {
        $address->delete();
        return back()->with('success', 'La dirección del agente aduanal ha sido eliminada correctamente.');
    }
}
