<?php
namespace AdrianTilita\ResourceExposer\Middleware;

use AdrianTilita\ResourceExposer\Bridge\ConfigBridge;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RouteMiddleware
{
    /**
     * Handle BasicAuth for given routes
     * @param $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        if ($request->headers->has('authorization')) {
            $authorizationCredentials = ConfigBridge::getInstance()
                ->get(ConfigBridge::CONFIG_KEY_AUTHORIZATION);

            if ($authorizationCredentials['username'] !== $request->getUser() &&
                $authorizationCredentials['password'] !== $request->getPassword()) {
                return new JsonResponse(
                    "Authentication failed!",
                    Response::HTTP_UNAUTHORIZED
                );
            }
        }
        return $next($request);
    }
}