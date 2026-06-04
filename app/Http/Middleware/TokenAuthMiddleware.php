<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expectedToken = config('app.dashboard_token');

        if (empty($expectedToken)) {
            abort(500, 'Security token is not configured in the application.');
        }

        $token = $request->query('token');

        if ($token && $token === $expectedToken) {
            session(['dashboard_authenticated' => true]);
            
            // Redirect to the same path without the token query param to clean the browser URL bar (supports ngrok)
            return redirect()->to($request->getPathInfo());
        }

        if (session('dashboard_authenticated') === true) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return redirect()->to('/login')->with('error', 'Access denied. Please enter a valid security token.');
    }
}
