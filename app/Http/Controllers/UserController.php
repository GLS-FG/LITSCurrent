<?php

namespace App\Http\Controllers;

use App\Enums\RolesEnum;
use App\Http\Requests\UserPostRequest;
use App\Http\Requests\UserPutRequest;
use App\Models\Client;
use App\Models\User;
use App\Notifications\UserWelcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $orderBy = $request->get('ordering_by');
        $allRolesExceptClient = Role::whereNot('name', RolesEnum::CLIENT)->get();
        $users = User::role($allRolesExceptClient)
        ->when($search, fn ($query, $search) => $query
            ->where('email', 'like', '%' . $search . '%')
            ->orWhere('name', 'like', '%' . $search . '%')
        )
            ->when($orderBy, fn ($query, $orderBy) => $query
                ->orderBy($orderBy, request('ordering_rule', 'desc')),
                fn ($query) => $query
                    ->orderBy('created_at', 'desc')
            )
            ->paginate(20)->withQueryString();
        return view('user.index', [
            'users' => $users
        ]);
    }

    public function create()
    {
        return view('user.create',[
            'roles' => Role::select('name', 'id')->whereNot('name', RolesEnum::CLIENT)->get()
        ]);
    }

    public function store(UserPostRequest $request)
    {
        $validated = $request->validated();
        $data = $request->safe()->except(['role_id']);
        $user = User::create($data);
        $role = Role::findById($validated['role_id']);
        $user->assignRole($role);
        return redirect()->route('users.show', [ 'user' => $user ])
            ->with('success', 'Se creó correctamente el usuario.');
    }

    public function show(User $user)
    {
        return view('user.show', [ 'user' => $user ]);
    }

    public function edit(User $user)
    {
        $primaryRole = $user->roles->pluck('id')->first();
        return view('user.edit',[
            'user' => $user,
            'primaryRole' => $primaryRole,
            'roles' => Role::select('name', 'id')->whereNot('name', RolesEnum::CLIENT)->get()
        ]);
    }

    public function update(User $user, UserPutRequest $request) {
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
        return redirect()->route('users.show', [ 'user' => $user ])
            ->with('success', 'Se actualizó correctamente el usuario.');
    }

    public function resetPassword(User $user) {
        $status = Password::sendResetLink(['email' => $user->email]);
        return $status === Password::ResetLinkSent
            ? back()->with(['success' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function sendWelcome(User $user) {
        $token = Password::broker()->createToken($user);
        $url = route('password.reset', [ 'token' => $token, 'email' => $user->email ]);
        $user->notify(new UserWelcome($url));
        return back()->with(['success' => "Se envió con éxito el correo de bienvenida al sistema al usuario"]);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'El usuario fue eliminado con éxito.');
    }
}
