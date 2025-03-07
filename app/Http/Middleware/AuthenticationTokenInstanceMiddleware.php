<?php

namespace App\Http\Middleware;

use App\Models\Instance;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationTokenInstanceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['message' => 'Token not found'], 401);
        }

        $instance = Instance::query()->where('token', $token)->first();

        if( $instance->client->plan->quantity_messages > 0 && $instance->client->plan->quantity_messages <= $instance->messages()->count()) {
            return response()->json(['message' => 'Você atingiu o limite de mensagens do seu plano'], 403);
        }

        if (!$instance) {
            return response()->json(['message' => 'Instance not found'], 404);
        }

        $request->merge([
            'instance' => $instance
        ]);

        return $next($request);
    }
}
