<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductAnalysisResource extends JsonResource
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
            'core_function_and_use' => $this->core_function_and_use,
            'features' => $this->features,
            'benefits' => $this->benefits,
            'problems' => $this->problems,
            'goals' => $this->goals,
            'emotions' => $this->emotions,
            'objections' => $this->objections,
            'faqs' => $this->faqs,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
