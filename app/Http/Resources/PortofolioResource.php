<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PortofolioResource extends JsonResource
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
            'service_id' => $this->service_id,
            'title' => $this->title,
            'description' => $this->description,
            'media_url' => $this->media_url,
            'media_asset_url' => $this->media_url
                ? (filter_var($this->media_url, FILTER_VALIDATE_URL) ? $this->media_url : asset('storage/' . $this->media_url))
                : null,
            'service' => $this->whenLoaded('service', fn() => [
                'id' => $this->service?->id,
                'title' => $this->service?->title,
                'status' => $this->service?->status,
                'category' => $this->service?->relationLoaded('category') ? [
                    'id' => $this->service?->category?->id,
                    'name' => $this->service?->category?->name,
                ] : null,
                'freelancer' => $this->service?->relationLoaded('freelancer') ? [
                    'id' => $this->service?->freelancer?->id,
                    'name' => $this->service?->freelancer?->skomda_student?->name,
                    'email' => $this->service?->freelancer?->skomda_student?->email,
                ] : null,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
