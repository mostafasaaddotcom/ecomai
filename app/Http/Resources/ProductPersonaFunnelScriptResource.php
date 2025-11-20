<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductPersonaFunnelScriptResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'product_persona_id' => $this->product_persona_id,
            'stage' => $this->stage,
            'angle' => $this->angle,
            'formula' => $this->formula,
            'language' => $this->language,
            'tone' => $this->tone,
            'content' => $this->content,
            'voice_link_url' => $this->voice_link_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
