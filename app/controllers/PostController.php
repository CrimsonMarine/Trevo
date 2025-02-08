<?php
namespace app\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use app\database\ConnectionSQL;

class PostController {
    public function post(Request $request, Response $response, array $args) {
        $postUrl = $args['postUrl'];

        $pdo = ConnectionSQL::connect();

        $stmt = $pdo->prepare("SELECT content, title, post_url, date, idAuthor, id FROM posts WHERE post_url = :post_url");
        $stmt->execute(['post_url' => $postUrl]);

        $post = $stmt->fetch(\PDO::FETCH_ASSOC);

        $Userstmt = $pdo->prepare("SELECT username, id, user_url FROM users WHERE id = :id");
        $Userstmt->execute(['id' => $post['idAuthor']]);

        $user = $Userstmt->fetch(\PDO::FETCH_ASSOC);

        view('post', ['post' => $post, 'user' => $user, 'title' => 'Trevo']);
        return $response;
    }

}