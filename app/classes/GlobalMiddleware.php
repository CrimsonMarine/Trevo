<?php

namespace app\classes;

use app\database\ConnectionRedis;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GlobalMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        if ($_SESSION['type-user'] === 'user') {
            $time = time();
            $timestamp = date('Y/m/d', $time);
            ConnectionRedis::setData('users', $_SESSION['user-info']['userId'], 'last_activity', [$timestamp]);
            
            $response = $handler->handle($request);

            return $response->withHeader('X-Global-Middleware', 'Active');
        }
        $response = $handler->handle($request);

        return $response->withHeader('X-Global-Middleware', 'Active');
    }
}
