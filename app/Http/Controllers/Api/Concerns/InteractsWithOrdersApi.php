<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderAttachment;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

trait InteractsWithOrdersApi
{
    private function baseOrderQuery(): Builder
    {
        return Order::query()->with($this->defaultRelations());
    }

    /**
     * @return array<int, string>
     */
    private function defaultRelations(): array
    {
        return [
            'service.category',
            'service.service_category',
            'service.freelancer.skomda_student',
            'client',
            'freelancer.skomda_student',
            'transactions',
            'attachments',
            'lokerApplication.loker',
            'lokerApplication.freelancer.skomda_student',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function detailRelations(): array
    {
        return [
            ...$this->defaultRelations(),
            'negotiations.order.client',
            'negotiations.order.service.category',
            'negotiations.order.service.service_category',
            'negotiations.order.service.freelancer.skomda_student',
            'offers',
            'results',
            'review',
        ];
    }

    private function scopeOrdersForUser(Builder $query, $user): Builder
    {
        if ($user?->getRole() === 'client') {
            $query->where('client_id', $user->id);
        } elseif ($user?->getRole() === 'freelancer') {
            $query->where(function ($orderQuery) use ($user) {
                $orderQuery->where('freelancer_id', $user->id)
                    ->orWhereHas('service', fn ($serviceQuery) => $serviceQuery->where('freelancer_id', $user->id));
            });
        }

        return $query;
    }

    private function ensureApprovedFreelancer(Request $request): ?JsonResponse
    {
        $freelancer = $request->user();

        if (! $freelancer) {
            return $this->errorResponse('Tidak terautentikasi', 401);
        }

        if ($freelancer->status !== 'Approved') {
            return $this->errorResponse(
                'Akses terbatas. Mohon ajukan verifikasi ke admin melalui panduan onboarding.',
                403
            );
        }

        return null;
    }

    private function ensureOrderableService(Service $service): ?JsonResponse
    {
        if ($service->status !== 'Approved') {
            return $this->errorResponse('Layanan tidak tersedia.', 422);
        }

        if (! $service->freelancer_id || ! $service->freelancer || $service->freelancer->status !== 'Approved') {
            return $this->errorResponse('Layanan tidak tersedia.', 422);
        }

        return null;
    }

    private function ensureCheckoutableOrder(Order $order): ?JsonResponse
    {
        if (! $this->orderHasPayableCounterparty($order)) {
            return $this->errorResponse('Transaksi hanya tersedia untuk order client dan freelancer.', 422);
        }

        if (! in_array($order->status, ['Pending', 'Negotiated'], true)) {
            return $this->errorResponse('Pembayaran tidak dapat diproses.', 422);
        }

        if (! $order->agreed_price) {
            return $this->errorResponse('Belum ada harga yang disepakati.', 422);
        }

        return null;
    }

    private function orderHasPayableCounterparty(Order $order): bool
    {
        return (bool) ($order->freelancer_id || $order->service?->freelancer_id);
    }

    /**
     * @param  array<int, UploadedFile>  $files
     * @return array<int, OrderAttachment>
     */
    private function storeUploadedAttachments(Order $order, array $files, string $uploadedBy): array
    {
        $existingCount = $order->attachments()->count();
        $remaining = max(0, 10 - $existingCount);
        $stored = [];

        foreach (array_slice($files, 0, $remaining) as $file) {
            if (! $file) {
                continue;
            }

            $path = $file->store('order-attachments', 'public');

            $stored[] = OrderAttachment::create([
                'order_id' => $order->id,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => $uploadedBy,
            ]);
        }

        return $stored;
    }

    /**
     * @return array<string, string>
     */
    private function paymentMethodLabels(): array
    {
        return [
            'qris' => 'QRIS',
            'va_bca' => 'BCA Virtual Account',
            'va_mandiri' => 'Mandiri Virtual Account',
            'va_bri' => 'BRI Virtual Account',
        ];
    }

    /**
     * @return array<string, float|int>
     */
    private function checkoutSummary(Order $order): array
    {
        $price = (float) $order->agreed_price;
        $platformFee = $price * 0.1;

        return [
            'agreed_price' => $price,
            'platform_fee' => $platformFee,
            'total' => $price + $platformFee,
        ];
    }

    private function projectTitle(Order $order): string
    {
        return $order->service?->title
            ?? $order->lokerApplication?->loker?->title
            ?? ('Order #'.$order->id);
    }

    private function notifyFreelancer(?int $freelancerId, string $title, string $message, string $type, string $link): void
    {
        if (! $freelancerId) {
            return;
        }

        Notification::create([
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'role' => 'freelancer',
            'user_id' => $freelancerId,
            'link' => $link,
        ]);
    }

    private function notifyClient(?int $clientId, string $title, string $message, string $type, string $link): void
    {
        if (! $clientId) {
            return;
        }

        Notification::create([
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'role' => 'client',
            'user_id' => $clientId,
            'link' => $link,
        ]);
    }
}
