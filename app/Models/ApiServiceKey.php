<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiServiceKey extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'openrouter_key',
        'kie_key',
        'lahajati_key',
        'supabase_project_url',
        'supabase_service_role_key',
    ];

    /**
     * Get the user that owns the API service keys.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
