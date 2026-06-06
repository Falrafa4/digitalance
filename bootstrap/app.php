<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $modelNames = [
            'Administrator' => 'Administrator',
            'Client' => 'Klien',
            'Freelancer' => 'Freelancer',
            'SkomdaStudent' => 'Siswa Skomda',
            'Service' => 'Layanan',
            'ServiceCategory' => 'Kategori Layanan',
            'Order' => 'Pesanan',
            'OrderAttachment' => 'Lampiran Pesanan',
            'Negotiation' => 'Negosiasi',
            'Offer' => 'Penawaran',
            'Transaction' => 'Transaksi',
            'Result' => 'Hasil Pekerjaan',
            'Review' => 'Ulasan',
            'Portofolio' => 'Portofolio',
            'Loker' => 'Lowongan Kerja',
            'LokerApplication' => 'Lamaran Lowongan',
            'Notification' => 'Notifikasi',
            'User' => 'Pengguna',
        ];
        
        $modelNotFoundResponse = function (ModelNotFoundException $e) use ($modelNames) {
            $model = class_basename($e->getModel()) ?: 'Resource';
            $label = $modelNames[$model] ?? $model;

            return response()->json([
                'success' => false,
                'message' => 'Data ' . $label . ' tidak ditemukan.',
            ], 404);
        };

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak terautentikasi',
                ], 401);
            }
        });
        
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) use ($modelNotFoundResponse) {
            if ($request->is('api/*')) {
                return $modelNotFoundResponse($e);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($modelNotFoundResponse) {
            if ($request->is('api/*')) {
                $previous = $e->getPrevious();

                if ($previous instanceof ModelNotFoundException) {
                    return $modelNotFoundResponse($previous);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Endpoint tidak ditemukan.',
                ], 404);
            }
        });
    })->create();
