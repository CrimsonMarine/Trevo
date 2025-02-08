<?php
namespace app\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use app\database\ConnectionSQL;
use app\database\ConnectionRedis;

class LoginController {
    public function login(Request $request, Response $response) {
        $data = $request->getParsedBody();

        if (!empty($data['submit'])) {
            $email = $data['email'];
            $password = $data['password'];

            $pdo = ConnectionSQL::connect();

            try {
                $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE email = :email");
                $stmt->execute(['email' => $email]);
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);

                if (empty($user)) {
                    $message = "E-mail não encontrado.";
                } else {
                    if (password_verify($password, $user['password_hash'])) {
                        ConnectionRedis::setData('users', $user['id'], 'status', ['online']);
                        $time = time();
                        ConnectionRedis::setData('users', $user['id'], 'last_activity', [date("Y-m-d", $time)]);
                        $_SESSION['user-info'] = [
                            'userId' => $user['id']
                        ];

                        header("Location: /");
                        exit;
                    } else {
                        $message = "Senha incorreta.";
                    }
                }
            } catch (\Exception $e) {
                $message = "Erro ao fazer login: " . $e->getMessage();
            }
        }

        view('login', [
            'title' =>'Entrar',
            'hh' => 'Login',
            'message' => $message ?? null,
        ]);

        return $response;
    }
}