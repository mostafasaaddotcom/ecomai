<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMetaProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'ad_account_id',
        'facebook_page_id',
        'instagram_profile_id',
        'access_token',
        'facebook_pixel',
        'is_default',
        'campaigns_available_for_duplicate',
        'ad_sets_available_for_duplicate',
    ];

    protected function casts(): array
    {
        return [
            'access_token' => 'encrypted',
            'is_default' => 'boolean',
            'campaigns_available_for_duplicate' => 'array',
            'ad_sets_available_for_duplicate' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
