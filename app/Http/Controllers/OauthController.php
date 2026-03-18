<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Microsoft\Facades\Microsoft;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class OauthController extends Controller
{
    public function redirectMicrosoft(): RedirectResponse
    {
        $redirectURL = Microsoft::getMicrosoftRedirectURL();
        Session::put('oauth_state', csrf_token());

        return redirect($redirectURL);
    }

    public function callbackMicrosoft(): mixed
    {
        if (request('state') !== session('oauth_state')) {
            abort(403, 'Invalid state');
        }

        $admin = Auth::user();

        $existingCredentials = $admin->custom_fields ?? [];

        try {
            $response = Microsoft::getSmtpCredentials();
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
        }

        $admin->update([
            'custom_fields' => array_merge($existingCredentials, $response),
        ]);

        return redirect()->route('filament.admin.pages.my-profile');
    }
}
