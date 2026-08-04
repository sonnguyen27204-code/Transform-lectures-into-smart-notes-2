<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class EnsureStorageSymlink
{
    public function handle(Request $request, Closure $next): Response
    {
        $link = public_path('storage');
        $target = storage_path('app/public');

        if (!file_exists($link) && is_dir($target)) {
            try {
                if (windows_os()) {
                    // Windows: dùng junction hoặc copy nhẹ
                    @mkdir($link, 0755, true);
                } else {
                    symlink($target, $link);
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        return $next($request);
    }
}
