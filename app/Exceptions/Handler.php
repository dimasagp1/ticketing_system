<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e): void {
            if (app()->bound('request')) {
                /** @var Request $request */
                $request = request();

                Log::error('Unhandled exception captured', [
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'ip' => $request->ip(),
                    'user_id' => optional($request->user())->id,
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                ]);
            }
        });

        $this->renderable(function (NotFoundHttpException|ModelNotFoundException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Data atau halaman tidak ditemukan.'], 404);
            }

            return response()->view('errors.404', [], 404);
        });

        $this->renderable(function (AuthorizationException|HttpException $e, Request $request) {
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 403;

            if ($status !== 403) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Anda tidak memiliki akses ke halaman ini.'], 403);
            }

            return response()->view('errors.403', [], 403);
        });

        $this->renderable(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesi berakhir. Silakan login ulang.'], 419);
            }

            return response()->view('errors.419', [], 419);
        });

        $this->renderable(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() !== 429) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Terlalu banyak permintaan. Coba lagi nanti.'], 429);
            }

            return response()->view('errors.429', [], 429);
        });

        $this->renderable(function (QueryException $e, Request $request) {
            $errorCode = strtoupper(substr(md5($e->getMessage() . microtime(true)), 0, 8));

            Log::error('Database query error', [
                'error_code' => $errorCode,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'message' => $e->getMessage(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Terjadi gangguan sistem. Silakan coba beberapa saat lagi.',
                    'error_code' => $errorCode,
                ], 500);
            }

            return response()->view('errors.500', ['errorCode' => $errorCode], 500);
        });

        $this->renderable(function (Throwable $e, Request $request) {
            if (config('app.debug')) {
                return null;
            }

            $errorCode = strtoupper(substr(md5($e->getMessage() . microtime(true)), 0, 8));

            Log::error('Fatal application error', [
                'error_code' => $errorCode,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Terjadi gangguan sistem. Silakan coba lagi.',
                    'error_code' => $errorCode,
                ], 500);
            }

            return response()->view('errors.500', ['errorCode' => $errorCode], 500);
        });
    }
}
