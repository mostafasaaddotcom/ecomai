<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LahajatiPerformance extends Model
{
    use HasFactory;

    protected $fillable = [
        'lahajati_id',
        'name',
        'description',
    ];

    public function userPerformances(): HasMany
    {
        return $this->hasMany(UserLahajatiPerformance::class);
    }
}
