<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LahajatiDialect extends Model
{
    use HasFactory;

    protected $fillable = [
        'lahajati_id',
        'name',
        'description',
    ];

    public function userDialects(): HasMany
    {
        return $this->hasMany(UserLahajatiDialect::class);
    }
}
