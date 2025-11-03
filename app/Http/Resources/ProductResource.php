<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'description_user' => $this->description_user,
            'description_ai' => $this->description_ai,
            'main_image_url' => $this->main_image_url ? asset('storage/' . $this->main_image_url) : null,
            'type' => $this->type,
            'store_link_url' => $this->store_link_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
