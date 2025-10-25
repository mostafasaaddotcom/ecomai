<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCopy extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'angle',
        'type',
        'formula',
        'language',
        'tone',
        'content',
        'voice_url_link',
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
     * Get the user that owns the product copy.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product that this copy belongs to.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get formatted type label.
     */
    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'ugc' => 'User Generated Content',
            'expert' => 'Expert Voice',
            'background_voice' => 'Background Voice',
            default => ucfirst($this->type),
        };
    }

    /**
     * Get formatted content for display (truncated).
     */
    public function getFormattedContent(int $length = 150): string
    {
        if (empty($this->content)) {
            return '';
        }

        return strlen($this->content) > $length
            ? substr($this->content, 0, $length).'...'
            : $this->content;
    }
}
