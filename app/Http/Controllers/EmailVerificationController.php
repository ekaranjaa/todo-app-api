<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Verify your email']);
    }

    public function verify(Request $request)
    {
        if (
            intval($request->route('id')) === intval($request->user()->getKey()) &&
            $request->user()->markEmailAsVerified()
        ) {
            event(new Verified($request->user()));
        }

        return response()->json(['message' => 'Email verified!']);
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'User already has a verified email!'], 422);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Email verification notification has been resent']);
    }
}
