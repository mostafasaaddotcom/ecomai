<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPersonaFunnelScript extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'product_persona_id',
        'stage',
        'angle',
        'formula',
        'language',
        'tone',
        'content',
        'voice_link_url',
    ];

    protected $casts = [
        'stage' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productPersona(): BelongsTo
    {
        return $this->belongsTo(ProductPersona::class);
    }

    /**
     * Get formatted stage label
     */
    public function getStageLabel(): string
    {
        return match($this->stage) {
            'unaware' => __('products.stage_unaware'),
            'problem_aware' => __('products.stage_problem_aware'),
            'solution_aware' => __('products.stage_solution_aware'),
            'product_aware' => __('products.stage_product_aware'),
            'most_aware' => __('products.stage_most_aware'),
            default => $this->stage,
        };
    }

    /**
     * Get formatted content for display
     */
    public function getFormattedContent(int $length = 150): string
    {
        if (!$this->content) {
            return '';
        }

        return strlen($this->content) > $length
            ? substr($this->content, 0, $length) . '...'
            : $this->content;
    }
}
