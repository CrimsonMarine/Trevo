<?php
namespace app\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use app\database\ConnectionSQL;

class PostsUserController {
    public function postsu(Request $request, Response $response, array $args) {
        $userUrl = $args['userUrl'];

        $pdo = ConnectionSQL::connect();
        $stmt = $pdo->prepare("SELECT username, id, user_url FROM users WHERE user_url = :userUrl");
        $stmt->execute(['userUrl' => $userUrl]);

        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        $stmtPosts = $pdo->prepare("SELECT title, content, date, idAuthor, id, post_url FROM posts WHERE idAuthor = :idAuthor ORDER BY date DESC");

        $stmtPosts->execute(['idAuthor' => $user['id']]);
        
        $posts = $stmtPosts->fetchAll(\PDO::FETCH_ASSOC);

        view('posts-user', ['title' => 'Postagens de ' . $user['username'], 'posts' => $posts, 'username' => $user['username'], 'userId' => $user['id'], 'userUrl' => $user['user_url']]);
        return $response;
    }

}