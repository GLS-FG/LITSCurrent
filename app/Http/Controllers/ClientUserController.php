<?php

namespace App\Http\Controllers;

use App\Enums\RolesEnum;
use App\Http\Requests\UserPostRequest;
use App\Http\Requests\UserPutRequest;
use App\Models\Client;
use App\Models\User;
use Spatie\Permission\Models\Role;

class ClientUserController extends Controller
{
    public function create(Client $client)
    {
        return view('client.user.create',[
            'roles' => Role::select('name', 'id')->get(),
            'client' => $client
        ]);
    }

    public function store(Client $client, UserPostRequest $request)
    {
        $validated = $request->validated();
        $data = $request->safe()->except(['role_id']);
        $user = User::create(array_merge($data, ['client_id' => $client->id ]));
        $role = Role::findById($validated['role_id']);
        $user->assignRole($role);
        return redirect()->route('clients.show', [ 'client' => $client ])
            ->with('success', 'Se creó correctamente el contacto.');
    }

    public function show(Client $client, User $user)
    {
        return view('client.user.show', [
            'user' => $user,
            'client' => $client
        ]);
    }

    public function edit(Client $client, User $user)
    {
        $primaryRole = $user->roles->pluck('id')->first();
        return view('client.user.edit',[
            'user' => $user,
            'client' => $client,
            'clients' => Client::select('id', 'name', 'last_name', 'company_name')->get(),
            'primaryRole' => $primaryRole,
            'roles' => Role::select('name', 'id')->whereNot('name', RolesEnum::CLIENT)->get()
        ]);
    }

    public function update(Client $client, User $user, UserPutRequest $request) {
        $beforeRole = $user->roles->pluck('name')->first();
        $user->removeRole($beforeRole);
        $validated = $request->validated();
        $data = $request->safe()->except(['role_id', 'password']);
        if($validated['password'] != null) {
            $data = $request->safe()->except(['role_id']);
        }
        $user->update($data);
        $role = Role::findById($validated['role_id']);
        $user->assignRole($role);
        return redirect()->route('clients.users.show', [ 'client' => $client, 'user' => $user ])
            ->with('success', 'Se actualizó correctamente el usuario.');
    }
}
