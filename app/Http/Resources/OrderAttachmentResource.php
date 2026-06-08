<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderAttachmentResource extends JsonResource
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
            'file_name' => $this->file_name,
            'file_path' => $this->file_path,
            'file_url' => asset('storage/'.$this->file_path),
            'mime_type' => $this->mime_type,
            'file_size' => $this->file_size,
            'uploaded_by' => $this->uploaded_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
