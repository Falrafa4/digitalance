<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Result extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'file_url', 'result_mode', 'note', 'message', 'version'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function isExternalLink(): bool
    {
        return $this->result_mode === 'link'
            || Str::startsWith((string) $this->file_url, ['http://', 'https://']);
    }

    public function downloadUrl(): string
    {
        if (!$this->file_url) {
            return '#';
        }

        return $this->isExternalLink()
            ? $this->file_url
            : asset('storage/' . $this->file_url);
    }

    public function fileLabel(): string
    {
        if (!$this->file_url) {
            return 'Tidak ada file';
        }

        if ($this->isExternalLink()) {
            return parse_url($this->file_url, PHP_URL_HOST) ?: $this->file_url;
        }

        return basename($this->file_url);
    }

    public function fileIcon(): string
    {
        if ($this->isExternalLink()) {
            return 'ri-external-link-line';
        }

        $extension = strtolower(pathinfo((string) $this->file_url, PATHINFO_EXTENSION));

        return match ($extension) {
            'pdf' => 'ri-file-pdf-line',
            'jpg', 'jpeg', 'png', 'gif', 'webp' => 'ri-image-line',
            'zip', 'rar' => 'ri-file-zip-line',
            'doc', 'docx' => 'ri-file-word-line',
            default => 'ri-file-line',
        };
    }

    public function fileActionLabel(): string
    {
        return $this->isExternalLink() ? 'Buka Link' : 'Unduh File';
    }
}
