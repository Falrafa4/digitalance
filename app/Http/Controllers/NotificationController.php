<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NotificationController extends Controller
{
    /**
     * Identifikasi guard yang sedang aktif + user model.
     * Mengembalikan [role|null, user|null].
     *
     * @return array{0: string|null, 1: object|null}
     */
    protected function context(): array
    {
        if (auth('administrator')->check()) {
            return ['admin', auth('administrator')->user()];
        }
        if (auth('client')->check()) {
            return ['client', auth('client')->user()];
        }
        if (auth('freelancer')->check()) {
            return ['freelancer', auth('freelancer')->user()];
        }
        return [null, null];
    }

    /**
     * Authorization: pastikan notifikasi milik user yang sedang login.
     */
    protected function owns(Notification $notification): bool
    {
        [$role, $user] = $this->context();
        if (!$role || !$user) {
            return false;
        }
        return $notification->role === $role && (int) $notification->user_id === (int) $user->id;
    }

    /**
     * Halaman riwayat notifikasi (Blade view).
     */
    public function index(Request $request)
    {
        [$role] = $this->context();
        
        if (!$role) {
            abort(403);
        }
        
        return match ($role) {
            'administrator' => redirect()->route('admin.dashboard'),
            'client' => redirect()->route('client.dashboard'),
            'freelancer' => redirect()->route('freelancer.dashboard'),
            default => redirect()->route('login'),
        };
    }

    /**
     * Endpoint polling untuk drawer.
     * Mengembalikan notifikasi terbaru + jumlah unread.
     */
    public function poll(Request $request): JsonResponse
    {
        [$role, $user] = $this->context();
        if (!$role || !$user) {
            return response()->json(['unread_count' => 0, 'notifications' => []]);
        }

        $limit = (int) min(50, max(1, (int) $request->query('limit', 10)));

        $notifications = Notification::query()
            ->forUser($role, $user->id)
            ->active()
            ->latest()
            ->take($limit)
            ->get();

        $unreadCount = Notification::query()
            ->forUser($role, $user->id)
            ->active()
            ->unread()
            ->count();

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications->map(fn (Notification $n) => $n->toApiPayload())->values(),
        ]);
    }

    /**
     * List untuk refresh drawer dengan filter, kategori, dan pencarian.
     */
    public function list(Request $request): JsonResponse
    {
        [$role, $user] = $this->context();
        if (!$role || !$user) {
            return response()->json(['data' => [], 'unread_count' => 0]);
        }

        $filter = (string) $request->query('filter', 'all');
        $category = $request->query('category');
        $q = $request->query('q');
        $limit = (int) min(50, max(1, (int) $request->query('limit', 20)));

        $query = Notification::query()
            ->forUser($role, $user->id)
            ->ofCategory($category)
            ->search($q);

        switch ($filter) {
            case 'unread':
                $query->active()->unread();
                break;
            case 'kept':
                $query->active()->kept();
                break;
            case 'archived':
                $query->archived();
                break;
            default:
                $query->active();
        }

        $notifications = $query->latest()->take($limit)->get();
        $baseQuery = Notification::query()->forUser($role, $user->id);
        $unreadCount = (clone $baseQuery)->active()->unread()->count();
        $keptCount = (clone $baseQuery)->active()->kept()->count();
        $archivedCount = (clone $baseQuery)->archived()->count();
        $allCount = (clone $baseQuery)->active()->count();

        return response()->json([
            'data' => $notifications->map(fn (Notification $n) => $n->toApiPayload())->values(),
            'unread_count' => $unreadCount,
            'kept_count' => $keptCount,
            'archived_count' => $archivedCount,
            'all_count' => $allCount,
        ]);
    }

    /**
     * Tandai SATU notifikasi sebagai dibaca.
     * Setelah update, redirect/JSON tergantung caller.
     */
    public function markRead(Request $request, Notification $notification)
    {
        if (!$this->owns($notification)) {
            abort(403);
        }

        $notification->markRead();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'notification' => $notification->toApiPayload(),
            ]);
        }

        // Jika ada link, redirect ke link; jika tidak, kembali.
        return $notification->link
            ? redirect()->to($notification->link)
            : redirect()->back();
    }

    /**
     * Tandai ulang sebagai BELUM dibaca (toggle balik).
     */
    public function markUnread(Request $request, Notification $notification)
    {
        if (!$this->owns($notification)) {
            abort(403);
        }

        $notification->forceFill(['is_read' => false, 'read_at' => null])->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'notification' => $notification->toApiPayload(),
            ]);
        }

        return redirect()->back();
    }

    /**
     * Tandai semua notifikasi aktif milik user sebagai sudah dibaca.
     */
    public function markAllRead(Request $request)
    {
        [$role, $user] = $this->context();
        $count = 0;
        if ($role && $user) {
            $count = Notification::query()
                ->forUser($role, $user->id)
                ->active()
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'updated' => $count]);
        }

        return redirect()->back()->with('success', "{$count} notifikasi ditandai sudah dibaca.");
    }

    /**
     * Toggle bookmark (disimpan/dilepas).
     */
    public function toggleKeep(Request $request, Notification $notification)
    {
        if (!$this->owns($notification)) {
            abort(403);
        }

        $notification->toggleKeep();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_kept' => (bool) $notification->is_kept,
                'notification' => $notification->toApiPayload(),
            ]);
        }

        return redirect()->back();
    }

    /**
     * Arsipkan (sembunyikan dari drawer, tetap ada di history).
     */
    public function archive(Request $request, Notification $notification)
    {
        if (!$this->owns($notification)) {
            abort(403);
        }

        $notification->archive();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'notification' => $notification->toApiPayload()]);
        }

        return redirect()->back()->with('success', 'Notifikasi diarsipkan.');
    }

    /**
     * Batal arsip.
     */
    public function unarchive(Request $request, Notification $notification)
    {
        if (!$this->owns($notification)) {
            abort(403);
        }

        $notification->unarchive();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'notification' => $notification->toApiPayload()]);
        }

        return redirect()->back()->with('success', 'Notifikasi dikembalikan dari arsip.');
    }

    /**
     * Hapus permanen satu notifikasi.
     */
    public function destroy(Request $request, Notification $notification)
    {
        if (!$this->owns($notification)) {
            abort(403);
        }

        $id = $notification->id;
        $notification->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'id' => $id]);
        }

        return redirect()->back()->with('success', 'Notifikasi dihapus.');
    }

    /**
     * Aksi massal:
     *   action = mark_read | mark_unread | archive | unarchive | keep | unkeep | delete
     *   ids    = array of notification id
     */
    public function bulk(Request $request)
    {
        $validated = $request->validate([
            'action' => ['required', Rule::in(['mark_read', 'mark_unread', 'archive', 'unarchive', 'keep', 'unkeep', 'delete'])],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        [$role, $user] = $this->context();
        if (!$role || !$user) {
            abort(403);
        }

        $base = Notification::query()
            ->forUser($role, $user->id)
            ->whereIn('id', $validated['ids']);

        $count = 0;
        switch ($validated['action']) {
            case 'mark_read':
                $count = (clone $base)->where('is_read', false)
                    ->update(['is_read' => true, 'read_at' => now()]);
                break;
            case 'mark_unread':
                $count = (clone $base)->where('is_read', true)
                    ->update(['is_read' => false, 'read_at' => null]);
                break;
            case 'archive':
                $count = (clone $base)->where('is_archived', false)
                    ->update(['is_archived' => true]);
                break;
            case 'unarchive':
                $count = (clone $base)->where('is_archived', true)
                    ->update(['is_archived' => false]);
                break;
            case 'keep':
                $count = (clone $base)->where('is_kept', false)
                    ->update(['is_kept' => true]);
                break;
            case 'unkeep':
                $count = (clone $base)->where('is_kept', true)
                    ->update(['is_kept' => false]);
                break;
            case 'delete':
                $count = (clone $base)->delete();
                break;
        }

        return response()->json(['success' => true, 'updated' => (int) $count]);
    }

    /**
     * Bersihkan semua notifikasi aktif milik user.
     * Default: arsipkan semua (soft). Opsi ?hard=1 atau body hard=1 untuk hapus permanen.
     */
    public function clearAll(Request $request)
    {
        [$role, $user] = $this->context();
        if (!$role || !$user) {
            abort(403);
        }

        $hard = filter_var(
            $request->query('hard', $request->input('hard', false)),
            FILTER_VALIDATE_BOOLEAN
        );

        $base = Notification::query()->forUser($role, $user->id)->active();

        if ($hard) {
            $count = (clone $base)->delete();
            $msg = "{$count} notifikasi dihapus permanen.";
        } else {
            $count = (clone $base)->update(['is_archived' => true]);
            $msg = "{$count} notifikasi diarsipkan.";
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'updated' => (int) $count]);
        }

        return redirect()->back()->with('success', $msg);
    }
}
