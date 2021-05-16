<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function index(Request $request)
    {
        $user = new UserResource(
            User::with(['todos'])->findOrFail($request->user()->id)
        );

        return $user;
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['No such user.'],
            ]);
        }

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Incorrect password.'],
            ]);
        }

        return $user->createToken($request->email)->plainTextToken;
    }

    public function register(Request $request)
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

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $user = $user::findOrFail($request->user()->id);

        $user->update([
            'name' => $request->name
        ]);

        if ($user->save()) {
            return response()->json([
                'message' => 'Account information updated.',
                'model' => new UserResource($user)
            ]);
        }
    }

    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:8'
        ]);

        $user = $user::findOrFail($request->user()->id);

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Incorrect password.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        if ($user->save()) {
            return response()->json(['message' => 'Password changed.']);
        }
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)]);
        } else {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }
    }

    public function delete(Request $request, User $user)
    {
        $user = $user::findOrFail($request->user()->id);

        if ($user->delete()) {
            return response()->json(['message' => 'Account deleted.']);
        }
    }
}
