<?php

namespace App\Microsoft;

use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class OauthClient
{
    public function getMicrosoftRedirectURL(): string
    {
        $tenant = Config::get('services.microsoft.tenant_id', 'common');

        $query = http_build_query([
            'client_id' => Config::get('services.microsoft.client_id'),
            'response_type' => 'code',
            'redirect_uri' => route('oauth.microsoft.callback'),
            'response_mode' => 'query',
            'scope' => 'offline_access https://graph.microsoft.com/.default',
            'state' => csrf_token(),
        ]);

        return "https://login.microsoftonline.com/{$tenant}/oauth2/v2.0/authorize?{$query}";
    }

    public function getSmtpCredentials(): array
    {
        try {
            $tenant = Config::get('services.microsoft.tenant_id', 'common');

            $response = Http::asForm()
                ->acceptJson()
                ->post(
                    "https://login.microsoftonline.com/{$tenant}/oauth2/v2.0/token",
                    [
                        'client_id' => config('services.microsoft.client_id'),
                        'client_secret' => config('services.microsoft.client_secret'),
                        'code' => request('code'),
                        'redirect_uri' => route('oauth.microsoft.callback'),
                        'grant_type' => 'authorization_code',
                        'scope' => 'openid profile User.Read SMTP.Send Mail.Send offline_access',
                    ]
                );

            if (! $response->successful()) {
                throw new Exception('Token API failed: '.$response->body());
            }

            $tokenData = $response->json();

            if (empty($tokenData['access_token'])) {
                throw new Exception('Access token missing in response');
            }

            $accessToken = $tokenData['access_token'];
            $refreshToken = $tokenData['refresh_token'] ?? null;
            $expiresIn = $tokenData['expires_in'] ?? 3600;

            $userResponse = Http::withToken($accessToken)
                ->acceptJson()
                ->get('https://graph.microsoft.com/v1.0/me');

            if (! $userResponse->successful()) {
                throw new Exception('User API failed: '.$userResponse->body());
            }

            $user = $userResponse->json();

            if (empty($user['mail']) && empty($user['userPrincipalName'])) {
                throw new Exception('User email not found in Microsoft response');
            }

            return [
                'smtp_type' => 'oauth',
                'smtp_provider' => 'microsoft',
                'smtp_email' => $user['mail'] ?? $user['userPrincipalName'],
                'smtp_access_token' => encrypt($accessToken),
                'smtp_refresh_token' => encrypt($refreshToken),
                'smtp_token_expires_at' => now()->addSeconds($expiresIn),
            ];

        } catch (RequestException $e) {
            throw new Exception('HTTP Request failed: '.$e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Microsoft OAuth Error: '.$e->getMessage());
        }
    }
}
