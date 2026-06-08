<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LokerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $applications = $this->whenLoaded('applications');
        $myApplication = null;
        $hasApplied = false;

        if ($user?->getRole() === 'freelancer' && $applications) {
            $myApplicationModel = $this->applications->firstWhere('freelancer_id', $user->id);
            $hasApplied = $myApplicationModel !== null;
            $myApplication = $myApplicationModel ? (new LokerApplicationResource($myApplicationModel))->toArray($request) : null;
        }

        return [
            'id' => $this->id,
            'client_id' => $this->client_id,
            'category_id' => $this->category_id,
            'title' => $this->title,
            'description' => $this->description,
            'budget_min' => $this->budget_min,
            'budget_max' => $this->budget_max,
            'deadline' => $this->deadline,
            'status' => $this->status,
            'client' => $this->whenLoaded('client', fn () => [
                'id' => $this->client?->id,
                'name' => $this->client?->name,
                'email' => $this->client?->email,
                'phone' => $this->client?->phone,
                'profile_photo' => $this->client?->profile_photo,
            ]),
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
                'description' => $this->category?->description,
            ]),
            'applications_count' => $this->relationLoaded('applications') ? $this->applications->count() : null,
            'applications' => $this->when(
                $this->relationLoaded('applications') && in_array($user?->getRole(), ['administrator', 'client'], true),
                fn () => $this->applications->map(fn ($application) => (new LokerApplicationResource($application))->toArray($request))->values()
            ),
            'has_applied' => $user?->getRole() === 'freelancer' ? $hasApplied : null,
            'my_application' => $user?->getRole() === 'freelancer' ? $myApplication : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
