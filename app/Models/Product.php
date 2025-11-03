<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'description_user',
        'description_ai',
        'main_image_url',
        'type',
        'price',
        'store_link_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => 'string',
        ];
    }

    /**
     * Get the user that owns the product.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product analysis for the product.
     */
    public function analysis(): HasOne
    {
        return $this->hasOne(ProductAnalysis::class);
    }

    /**
     * Get the product copies for the product.
     */
    public function copies(): HasMany
    {
        return $this->hasMany(ProductCopy::class);
    }

    /**
     * Get the product images for the product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Get the ad creatives for the product.
     */
    public function adCreatives(): HasMany
    {
        return $this->hasMany(AdCreative::class);
    }

    /**
     * Check if the product has an analysis.
     */
    public function hasAnalysis(): bool
    {
        return $this->analysis()->exists();
    }
}
