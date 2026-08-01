<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserWriter;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);

        return Inertia::render('users/index', [
            'users' => UserResource::collection(User::whereNot('id', 1)->orderBy('name')->get())->toArray($request),
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('create', User::class);

        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        UserWriter::create($validated);

        return redirect()->back();
    }

    public function update(Request $request, User $user)
    {
        Gate::authorize('update', User::class);
        abort_if($user->id === $request->user()->id, 403, 'Use your own profile settings to edit your account.');

        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        UserWriter::update($user, $validated);

        return redirect()->back();
    }

    public function destroy(Request $request, User $user)
    {
        Gate::authorize('delete', User::class);
        abort_if($user->id === $request->user()->id, 403, 'You cannot delete your own account here.');
        abort_if($user->id === 1, 403, 'The superadmin account cannot be deleted.');

        $user->delete();

        return redirect()->back();
    }
}
