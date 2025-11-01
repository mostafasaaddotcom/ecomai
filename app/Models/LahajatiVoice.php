<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LahajatiVoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'lahajati_id',
        'name',
        'gender',
        'description',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function userVoices(): HasMany
    {
        return $this->hasMany(UserLahajatiVoice::class);
    }
}
