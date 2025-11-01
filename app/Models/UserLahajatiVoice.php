<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLahajatiVoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lahajati_voice_id',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lahajatiVoice(): BelongsTo
    {
        return $this->belongsTo(LahajatiVoice::class);
    }
}
