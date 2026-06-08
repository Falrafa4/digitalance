<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NegotiationResource extends JsonResource
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
            'order_id' => $this->order_id,
            'sender' => $this->sender,
            'message' => $this->message,
            'proposed_price' => $this->proposed_price,
            'reason' => $this->reason,
            'description' => $this->description,
            'status' => $this->status,
            'order' => $this->whenLoaded('order', fn () => [
                'id' => $this->order?->id,
                'service_id' => $this->order?->service_id,
                'client_id' => $this->order?->client_id,
                'freelancer_id' => $this->order?->freelancer_id,
                'brief' => $this->order?->brief,
                'status' => $this->order?->status,
                'agreed_price' => $this->order?->agreed_price,
                'deadline' => $this->order?->deadline,
                'client' => $this->order?->relationLoaded('client') ? [
                    'id' => $this->order?->client?->id,
                    'name' => $this->order?->client?->name,
                    'email' => $this->order?->client?->email,
                    'phone' => $this->order?->client?->phone,
                ] : null,
                'service' => $this->order?->relationLoaded('service') ? [
                    'id' => $this->order?->service?->id,
                    'title' => $this->order?->service?->title,
                    'status' => $this->order?->service?->status,
                    'category' => $this->order?->service?->relationLoaded('category') ? [
                        'id' => $this->order?->service?->category?->id,
                        'name' => $this->order?->service?->category?->name,
                    ] : null,
                    'service_category' => $this->order?->service?->relationLoaded('service_category') ? [
                        'id' => $this->order?->service?->service_category?->id,
                        'name' => $this->order?->service?->service_category?->name,
                    ] : null,
                    'freelancer' => $this->order?->service?->relationLoaded('freelancer') ? [
                        'id' => $this->order?->service?->freelancer?->id,
                        'name' => $this->order?->service?->freelancer?->skomda_student?->name,
                        'email' => $this->order?->service?->freelancer?->skomda_student?->email,
                        'status' => $this->order?->service?->freelancer?->status,
                    ] : null,
                ] : null,
                'offers' => $this->order?->relationLoaded('offers') ? $this->order?->offers : null,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
