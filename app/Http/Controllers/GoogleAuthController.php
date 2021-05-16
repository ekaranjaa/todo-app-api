<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function authenticate()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callback()
    {
        $user = Socialite::driver('google')->stateless()->user();

        return $this->createUser($user->user);
    }

    private function createUser($user)
    {
        $user = User::firstOrCreate([
            'email' => $user['email']
        ], [
            'name' => $user['name'],
            'avatar' => $user['picture'],
            'provider' => 'google',
            'provider_user_id' => $user['id'],
            'email_verified_at' => $user['email_verified'] ? now() : null,
            'password' => Hash::make($user['id']),
        ]);

        $token = $user->createToken($user['email'])->plainTextToken;

        return redirect(env('CLIENT_APP_URL') . '/callback?token=' . $token);
    }
}
