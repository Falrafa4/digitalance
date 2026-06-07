<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SkomdaStudentResource extends JsonResource
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
            'nis' => $this->nis,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'class' => $this->class,
            'major' => $this->major,
            'avatar' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'is_registered' => (bool) ($this->is_registered ?? false),
            'has_freelancer' => $this->relationLoaded('freelancer') ? $this->freelancer !== null : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
