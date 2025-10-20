<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Activitylog\Facades\LogBatch;
use Symfony\Component\HttpFoundation\Response;

class BatchLogsActivity
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        LogBatch::startBatch();

        $response = $next($request);

        LogBatch::endBatch();

        return $response;
    }
}
