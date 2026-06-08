<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected static function boot(): void
    {
        parent::boot();

        static::created(function (Notification $notification) {
            // Broadcast real-time notification via Reverb (covers all create() calls)
            event(new \App\Events\NotificationCreated($notification));
        });
    }

    protected $fillable = [
        'title',
        'message',
        'type',
        'category',
        'role',
        'user_id',
        'link',
        'group_key',
        'is_read',
        'read_at',
        'is_kept',
        'is_archived',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_kept' => 'boolean',
        'is_archived' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Daftar tipe notifikasi yang dipakai icon/color mapping di frontend.
     */
    public const TYPES = ['info', 'success', 'warning', 'danger', 'approved', 'rejected', 'sent'];

    /**
     * Scope: notifikasi milik role + user tertentu.
     */
    public function scopeForUser(Builder $query, ?string $role, $userId): Builder
    {
        if ($role === null || $userId === null) {
            return $query->whereRaw('1 = 0');
        }
        return $query->where('role', $role)->where('user_id', $userId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_archived', false);
    }

    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('is_archived', true);
    }

    public function scopeOfCategory(Builder $query, ?string $category): Builder
    {
        if ($category === null || $category === '' || $category === 'all') {
            return $query;
        }
        return $query->where('category', $category);
    }

    public function scopeKept(Builder $query): Builder
    {
        return $query->where('is_kept', true);
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    public function scopeRead(Builder $query): Builder
    {
        return $query->where('is_read', true);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if ($term === null || $term === '') {
            return $query;
        }
        $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $term) . '%';
        return $query->where(function ($q) use ($like) {
            $q->where('title', 'like', $like)->orWhere('message', 'like', $like);
        });
    }

    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'success', 'approved' => 'ri-checkbox-circle-fill',
            'danger', 'rejected' => 'ri-error-warning-fill',
            'warning' => 'ri-alert-fill',
            'sent' => 'ri-send-plane-fill',
            default => 'ri-information-fill',
        };
    }

    public function getIconColorAttribute(): string
    {
        return match ($this->type) {
            'success', 'approved' => 'bg-emerald-100 text-emerald-600',
            'danger', 'rejected' => 'bg-red-100 text-red-500',
            'warning' => 'bg-amber-100 text-amber-600',
            'sent' => 'bg-indigo-100 text-indigo-600',
            default => 'bg-blue-100 text-blue-600',
        };
    }

    public function markRead(): bool
    {
        if ($this->is_read) {
            return false;
        }
        return (bool) $this->forceFill([
            'is_read' => true,
            'read_at' => now(),
        ])->save();
    }

    public function toggleKeep(): bool
    {
        $this->is_kept = !$this->is_kept;
        return (bool) $this->save();
    }

    public function archive(): bool
    {
        if ($this->is_archived) {
            return false;
        }
        $this->is_archived = true;
        return (bool) $this->save();
    }

    public function unarchive(): bool
    {
        if (!$this->is_archived) {
            return false;
        }
        $this->is_archived = false;
        return (bool) $this->save();
    }

    public function toApiPayload(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'category' => $this->category,
            'is_read' => (bool) $this->is_read,
            'is_kept' => (bool) $this->is_kept,
            'is_archived' => (bool) $this->is_archived,
            'link' => $this->link,
            'icon' => $this->icon,
            'created_at' => optional($this->created_at)?->toIso8601String(),
            'created_human' => optional($this->created_at)?->diffForHumans(),
            'read_at' => optional($this->read_at)?->toIso8601String(),
        ];
    }

    /**
     * Dispatcher notifikasi dengan idempotency opsional.
     *
     * Jika `groupKey` diberikan dan notifikasi dengan kombinasi role+user+group_key
     * yang sama sudah pernah dibuat dalam 24 jam terakhir, fungsi ini mengembalikan
     * record yang sudah ada (mencegah duplikat).
     *
     * Controller existing yang sudah memakai `Notification::create([...])` tetap
     * bekerja tanpa perubahan; method ini hanya membantu controller baru
     * yang ingin menyertakan `category` dan `group_key` secara konsisten.
     */
    public static function dispatch(
        string $role,
        int $userId,
        string $title,
        string $message,
        string $type = 'info',
        ?string $category = null,
        ?string $link = null,
        ?string $groupKey = null,
    ): self {
        if ($groupKey !== null) {
            $existing = static::query()
                ->where('role', $role)
                ->where('user_id', $userId)
                ->where('group_key', $groupKey)
                ->where('created_at', '>=', Carbon::now()->subDay())
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        return static::create([
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'category' => $category,
            'role' => $role,
            'user_id' => $userId,
            'link' => $link,
            'group_key' => $groupKey,
            'is_read' => false,
            'is_kept' => false,
            'is_archived' => false,
        ]);
    }
}
