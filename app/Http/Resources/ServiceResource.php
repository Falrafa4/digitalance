<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'category_id' => $this->category_id,
            'freelancer_id' => $this->freelancer_id,
            'title' => $this->title,
            'description' => $this->description,
            'price_min' => $this->price_min,
            'price_max' => $this->price_max,
            'delivery_time' => $this->delivery_time,
            'status' => $this->status,
            'reject_reason' => $this->reject_reason,
            'category' => $this->whenLoaded('category', fn() => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
            ]),
            'freelancer' => $this->whenLoaded('freelancer', fn() => [
                'id' => $this->freelancer?->id,
                'name' => $this->freelancer?->skomda_student?->name,
                'email' => $this->freelancer?->skomda_student?->email,
                'profile_photo' => $this->freelancer?->profile_photo,
                'status' => $this->freelancer?->status,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
