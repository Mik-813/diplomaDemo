<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Cache;

class SocialAuthController extends Controller
{
    public function redirect(Request $request): JsonResponse
    {
        $request->validate([
            'client_url' => 'required|url',
        ]);

        $state = Str::random(32);
        Cache::put('google_state_' . $state, $request->client_url, now()->addMinutes(15));

        return response()->json([
            'url' => Socialite::driver('google')->stateless()->with(['state' => $state, 'prompt' => 'select_account'])->redirect()->getTargetUrl(),
        ]);
    }

    public function callback(Request $request)
    {
        $state = $request->input('state');
        $clientUrl = $state ? Cache::pull('google_state_' . $state) : null;

        if (!$clientUrl) {
            return response()->json(['message' => 'Invalid or expired state'], 400);
        }

        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            $separator = str_contains($clientUrl, '?') ? '&' : '?';
            return redirect($clientUrl . $separator . 'error=auth_failed');
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            $user->google_id = $user->google_id ?? $googleUser->getId();
            $user->email_verified_at = $user->email_verified_at ?? now();
            $user->save();
        } else {
            $user = new User();
            $user->email = $googleUser->getEmail();
            $user->google_id = $googleUser->getId();
            $user->password = bcrypt(Str::random(32));
            $user->email_verified_at = now();
            $user->save();
        }

        $token = 'Bearer ' . $user->createToken('google-auth')->plainTextToken;

        $separator = str_contains($clientUrl, '?') ? '&' : '?';
        return redirect($clientUrl . $separator . 'token=' . urlencode($token));
    }
}