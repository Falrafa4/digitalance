<?php

namespace App\View\Composers;

use App\Models\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationComposer
{
    public function compose(View $view): void
    {
        $notifUser = Auth::guard('administrator')->user()
            ?? Auth::guard('client')->user()
            ?? Auth::guard('freelancer')->user();

        $notifRole = Auth::guard('administrator')->check() ? 'admin' : (Auth::guard('client')->check() ? 'client' : (Auth::guard('freelancer')->check() ? 'freelancer' : null));

        Notification::where('created_at', '<', now()->subDays(30))
            ->where('is_kept', false)
            ->delete();

        $notifNotifications = $notifRole && $notifUser
            ? Notification::where('role', $notifRole)
                ->where('user_id', $notifUser->id)
                ->latest()
                ->take(30)
                ->get()
            : collect();

        $view->with([
            'notifNotifications' => $notifNotifications,
            'notifUnreadCount' => $notifNotifications->where('is_read', false)->count(),
        ]);
    }
}