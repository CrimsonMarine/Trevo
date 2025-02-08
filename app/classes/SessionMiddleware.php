<?php

namespace app\classes;

use app\database\ConnectionSQL;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SessionMiddleware implements MiddlewareInterface {
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'httponly' => true,
                'secure' => false,
                'samesite' => 'Strict'
            ]);

            session_start();
            session_regenerate_id(false);
        }
        
        if (isset($_SESSION['user-info']['userId'])) {
            $userId = $_SESSION['user-info']['userId'];
    
            $pdo = ConnectionSQL::connect();
            $stmt = $pdo->prepare("SELECT username, user_url FROM users WHERE id = :userId");

            $stmt->execute(['userId' => $userId]);
    
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                $_SESSION['user-info']['username'] = htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8');
                $_SESSION['user-info']['user_url'] = htmlspecialchars($user['user_url'] ?? '', ENT_QUOTES, 'UTF-8');
                $_SESSION['type-user'] = 'user';
            } else {
                $_SESSION['type-user'] = 'guest';
                $_SESSION['user-info']['username'] = null;
                $_SESSION['user-info']['user_url'] = null;
            }
            
    
        } else {
            $_SESSION['type-user'] = 'guest';
        }
    
        return $handler->handle($request);
    }
}