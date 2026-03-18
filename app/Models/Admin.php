<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AdminFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class Admin extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<AdminFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'custom_columns' => 'array',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function applyCustomSmtpConfig(): bool
    {
        $accessToken = $this->custom_fields['smtp_access_token'] ?? null;
        $email = $this->custom_fields['smtp_email'] ?? null;

        if (empty($accessToken) || empty($email)) {
            return false;
        }

        $overrides = array_filter([
            'scheme' => Config::get('mail.mailers.custom_smtp.scheme'),
            'url' => Config::get('mail.mailers.custom_smtp.url'),
            'host' => Config::get('mail.mailers.custom_smtp.host'),
            'port' => Config::get('mail.mailers.custom_smtp.port'),

            'username' => $email,
            'password' => $accessToken,

            'timeout' => $this->custom_fields['smtp_timeout'] ?? null,
            'local_domain' => $this->custom_fields['smtp_local_domain'] ?? null,
        ], static fn ($value) => ! is_null($value) && $value !== '');

        if ($overrides === []) {
            return false;
        }

        $base = config('mail.mailers.smtp', []);

        Config::set('mail.mailers.custom_smtp', array_replace($base, $overrides));

        return true;
    }

    public function getGeneratedPassword($accessToken, $email): ?string
    {
        $authString = "user={$email}\x01auth=Bearer {$accessToken}\x01\x01";

        return base64_encode($authString);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        $avatarColumn = config('filament-edit-profile.avatar_column', 'avatar_url');

        return $this->$avatarColumn ? Storage::disk('public')->url($this->$avatarColumn) : null;
    }
}
