<?php

namespace LivewireFilemanager\Filemanager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FilemanagerAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $callback = config('livewire-fileuploader.callbacks.access_check');

        if ($callback && is_callable($callback)) {
            $result = $callback($request);

            if ($result === false) {
                return response()->json(['message' => 'Access denied'], Response::HTTP_FORBIDDEN);
            }
        }

        return $next($request);
    }
}
