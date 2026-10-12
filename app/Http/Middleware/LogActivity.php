<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Logue automatiquement les actions POST/PUT/DELETE significatives.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (Auth::check() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $action = $this->detectAction($request);
            if ($action) {
                ActivityLog::create([
                    'user_id'    => Auth::id(),
                    'action'     => $action['type'],
                    'detail'     => $action['detail'],
                    'ip_address' => $request->ip(),
                ]);
            }
        }

        return $response;
    }

    private function detectAction(Request $request): ?array
    {
        $path = $request->path();
        $method = $request->method();

        if (str_contains($path, 'bagages') && $method === 'POST') {
            return ['type' => 'Bagage', 'detail' => 'Enregistrement bagage via ' . $path];
        }
        if (str_contains($path, 'embarquement')) {
            return ['type' => 'Modification', 'detail' => 'Embarquement bagage via ' . $path];
        }
        if ($method === 'DELETE') {
            return ['type' => 'Suppression', 'detail' => 'Suppression via ' . $path];
        }
        if (in_array($method, ['PUT', 'PATCH'])) {
            return ['type' => 'Modification', 'detail' => 'Modification via ' . $path];
        }

        return null;
    }
}
