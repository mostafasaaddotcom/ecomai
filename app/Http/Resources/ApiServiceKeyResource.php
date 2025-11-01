<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiServiceKeyResource extends JsonResource
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
            'openrouter_key' => $this->openrouter_key,
            'kie_key' => $this->kie_key,
            'lahajati_key' => $this->lahajati_key,
            'supabase_project_url' => $this->supabase_project_url,
            'supabase_service_role_key' => $this->supabase_service_role_key,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
