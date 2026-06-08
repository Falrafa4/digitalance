<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'client_id' => $this->client_id,
            'freelancer_id' => $this->freelancer_id,
            'loker_application_id' => $this->loker_application_id,
            'brief' => $this->brief,
            'status' => $this->status,
            'agreed_price' => $this->agreed_price,
            'deadline' => $this->deadline,
            'service' => $this->whenLoaded('service', fn () => [
                'id' => $this->service?->id,
                'title' => $this->service?->title,
                'description' => $this->service?->description,
                'price_min' => $this->service?->price_min,
                'price_max' => $this->service?->price_max,
                'delivery_time' => $this->service?->delivery_time,
                'status' => $this->service?->status,
                'category' => $this->service?->relationLoaded('category') || $this->service?->relationLoaded('service_category') ? [
                    'id' => $this->service?->category?->id,
                    'name' => $this->service?->category?->name,
                ] : null,
                'freelancer' => $this->service?->relationLoaded('freelancer') ? [
                    'id' => $this->service?->freelancer?->id,
                    'name' => $this->service?->freelancer?->skomda_student?->name,
                    'email' => $this->service?->freelancer?->skomda_student?->email,
                    'status' => $this->service?->freelancer?->status,
                ] : null,
            ]),
            'client' => $this->whenLoaded('client', fn () => [
                'id' => $this->client?->id,
                'name' => $this->client?->name,
                'email' => $this->client?->email,
                'phone' => $this->client?->phone,
                'profile_photo' => $this->client?->profile_photo,
            ]),
            'freelancer' => $this->whenLoaded('freelancer', fn () => [
                'id' => $this->freelancer?->id,
                'name' => $this->freelancer?->skomda_student?->name,
                'email' => $this->freelancer?->skomda_student?->email,
                'status' => $this->freelancer?->status,
                'profile_photo' => $this->freelancer?->profile_photo,
            ]),
            'loker_application' => $this->whenLoaded('lokerApplication', fn () => [
                'id' => $this->lokerApplication?->id,
                'freelancer_id' => $this->lokerApplication?->freelancer_id,
                'proposal' => $this->lokerApplication?->proposal,
                'proposed_price' => $this->lokerApplication?->proposed_price,
                'status' => $this->lokerApplication?->status,
                'loker' => $this->lokerApplication?->relationLoaded('loker') ? [
                    'id' => $this->lokerApplication?->loker?->id,
                    'title' => $this->lokerApplication?->loker?->title,
                    'status' => $this->lokerApplication?->loker?->status,
                ] : null,
                'freelancer' => $this->lokerApplication?->relationLoaded('freelancer') ? [
                    'id' => $this->lokerApplication?->freelancer?->id,
                    'name' => $this->lokerApplication?->freelancer?->skomda_student?->name,
                    'email' => $this->lokerApplication?->freelancer?->skomda_student?->email,
                    'status' => $this->lokerApplication?->freelancer?->status,
                ] : null,
            ]),
            'attachments' => $this->whenLoaded('attachments', fn () => OrderAttachmentResource::collection($this->attachments)),
            'negotiations' => $this->whenLoaded('negotiations'),
            'offers' => $this->whenLoaded('offers'),
            'transactions' => $this->whenLoaded('transactions'),
            'results' => $this->whenLoaded('results'),
            'review' => $this->whenLoaded('review'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
