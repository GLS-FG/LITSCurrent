<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatePostRequest;
use App\Http\Requests\StatePutRequest;
use App\Models\State;
use Illuminate\Http\Request;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $states = State::when(
            $search,
            fn ($query, $search) => $query
                ->where('name', 'like', '%' . $search . '%')
                ->orWhere('short_name', 'like', '%' . $search . '%')
        )->paginate(20);
        return view('state.index', [
            'states' => $states
        ]);
    }

    public function create()
    {
        return view('state.create');
    }

    public function store(StatePostRequest $request)
    {
        $state = State::create($request->validated());
        return redirect()->route('states.show', [ 'state' => $state ]);
    }

    public function show(State $state)
    {
        return view('state.show', compact('state'));
    }

    public function edit(State $state)
    {
        return view('state.edit', compact('state'));
    }

    public function update(StatePutRequest $request, State $state)
    {
        $state->update($request->validated());
        return redirect()->route('states.show', [ 'state' => $state ])
            ->with('success', 'Se actualizó correctamente la información del estado.');
    }

    public function destroy(State $state)
    {
        $state->delete();
        return back()->with('success', 'El estado fue eliminado con éxito.');
    }
}
