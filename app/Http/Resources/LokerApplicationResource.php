<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LokerApplicationResource extends JsonResource
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
            'loker_id' => $this->loker_id,
            'freelancer_id' => $this->freelancer_id,
            'proposal' => $this->proposal,
            'proposed_price' => $this->proposed_price,
            'status' => $this->status,
            'loker' => $this->whenLoaded('loker', fn () => [
                'id' => $this->loker?->id,
                'client_id' => $this->loker?->client_id,
                'category_id' => $this->loker?->category_id,
                'title' => $this->loker?->title,
                'description' => $this->loker?->description,
                'budget_min' => $this->loker?->budget_min,
                'budget_max' => $this->loker?->budget_max,
                'deadline' => $this->loker?->deadline,
                'status' => $this->loker?->status,
                'category' => $this->loker?->relationLoaded('category') ? [
                    'id' => $this->loker?->category?->id,
                    'name' => $this->loker?->category?->name,
                ] : null,
                'client' => $this->loker?->relationLoaded('client') ? [
                    'id' => $this->loker?->client?->id,
                    'name' => $this->loker?->client?->name,
                    'email' => $this->loker?->client?->email,
                    'phone' => $this->loker?->client?->phone,
                    'profile_photo' => $this->loker?->client?->profile_photo,
                ] : null,
            ]),
            'freelancer' => $this->whenLoaded('freelancer', fn () => [
                'id' => $this->freelancer?->id,
                'status' => $this->freelancer?->status,
                'profile_photo' => $this->freelancer?->profile_photo,
                'skomda_student' => $this->freelancer?->relationLoaded('skomda_student') ? [
                    'id' => $this->freelancer?->skomda_student?->id,
                    'name' => $this->freelancer?->skomda_student?->name,
                    'email' => $this->freelancer?->skomda_student?->email,
                    'phone' => $this->freelancer?->skomda_student?->phone,
                ] : null,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
