<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UsersController extends Controller
{
    public function index()
    {
        $users = UserResource::collection(
            User::with(['role', 'availability', 'tasks'])->paginate(50)
        );

        return $users;
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($user->save()) {
            event(new Registered($user));
            return response()->json([
                'message' => 'Account created.',
                'model' => new UserResource($user)
            ]);
        }
    }

    public function update(Request $request, User $user, $id)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $user = $user::findOrFail($id);

        $user->update([
            'name' => $request->name
        ]);

        if ($user->save()) {
            return response()->json([
                'message' => 'User information updated.',
                'model' => new UserResource($user)
            ]);
        }
    }

    public function updateEmail(Request $request, User $user, $id)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = $user::findOrFail($id);

        $user->update([
            'email' => $request->email,
            'email_verified_at' => null
        ]);

        if ($user->save()) {
            event(new Registered($user));
            return response()->json([
                'message' => 'User email updated.',
                'model' => new UserResource($user)
            ]);
        }
    }

    public function updatePassword(Request $request, User $user, $id)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:8'
        ]);

        $user = $user::findOrFail($id);

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Incorrect password.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        if ($user->save()) {
            return response()->json(['message' => 'User password changed.']);
        }
    }

    public function delete(User $user, $id)
    {
        $user = $user::findOrFail($id);

        if ($user->delete()) {
            return response()->json(['message' => 'User deleted.']);
        }
    }
}
