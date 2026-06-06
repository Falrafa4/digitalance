<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FreelancerResource extends JsonResource
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
            'student_id' => $this->student_id,
            'nis' => $this->skomda_student->nis,
            'name' => $this->skomda_student->name,
            'email' => $this->skomda_student->email,
            'phone' => $this->skomda_student->phone,
            'class' => $this->skomda_student->class,
            'major' => $this->skomda_student->major,
            'bio' => $this->bio,
            'profile_photo' => $this->profile_photo,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
