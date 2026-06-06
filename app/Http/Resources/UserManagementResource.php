<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserManagementResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'status' => $this->status,
            'profile_photo_path' => $this->profile_photo,
            'profile_photo_url' => $this->profile_photo ? asset('storage/' . $this->profile_photo) : null,
            'avatar_path' => $this->avatar,
            'avatar_url' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'created_at' => $this->created_at,
        ];
    }
}
