<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'credit',
        'locale',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
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
            'credit' => 'decimal:2',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the products for the user.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the API service keys for the user.
     */
    public function apiServiceKeys(): HasOne
    {
        return $this->hasOne(ApiServiceKey::class);
    }

    /**
     * Get the Lahajati voices for the user.
     */
    public function lahajatiVoices(): HasMany
    {
        return $this->hasMany(UserLahajatiVoice::class);
    }

    /**
     * Get the Lahajati performances for the user.
     */
    public function lahajatiPerformances(): HasMany
    {
        return $this->hasMany(UserLahajatiPerformance::class);
    }

    /**
     * Get the Lahajati dialects for the user.
     */
    public function lahajatiDialects(): HasMany
    {
        return $this->hasMany(UserLahajatiDialect::class);
    }

    /**
     * Get the stores for the user.
     */
    public function stores(): HasMany
    {
        return $this->hasMany(UserStore::class);
    }

    /**
     * Get the meta profiles for the user.
     */
    public function metaProfiles(): HasMany
    {
        return $this->hasMany(UserMetaProfile::class);
    }

    /**
     * Get the ad creatives for the user.
     */
    public function adCreatives(): HasMany
    {
        return $this->hasMany(AdCreative::class);
    }

    /**
     * Check if the current token has admin abilities.
     */
    public function hasAdminToken(): bool
    {
        $token = $this->currentAccessToken();

        if (!$token) {
            return false;
        }

        // Only tokens with explicit 'admin:*' ability are admin tokens
        return $token->can('admin:*');
    }

    /**
     * Check if the user/token can perform an action on a resource.
     *
     * @param mixed $resource The resource to check (e.g., Product model)
     * @param string $ownerKey The key to check ownership (default: 'user_id')
     */
    public function canAccessResource($resource, string $ownerKey = 'user_id'): bool
    {
        // Admin tokens can access everything
        if ($this->hasAdminToken()) {
            return true;
        }

        // Check if resource belongs to this user
        return $resource->{$ownerKey} === $this->id;
    }
}
