<?php

namespace app\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use app\database\ConnectionSQL;
use app\database\ConnectionRedis;

class CreateAccController
{
    public function create(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        if (!empty($data['submit'])) {
            $username = $data['username'];
            $email = $data['email'];
            $password = $data['password'];
            $confirmPassword = $data['confirmpassword'];
            $birthday = $data['birthday'];
            $country = $data['country'];

            $pdo = ConnectionSQL::connect();

            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
                $stmt->execute(['username' => $username, 'email' => $email]);

                if ($stmt->rowCount() > 0) {
                    $message = "E-mail ou Nome de Usuário já em uso.";
                } else {
                    if (strlen($username) > 50) {
                        $message = "Limite excedido de caracteres.";
                    } else {
                        if ($password === $confirmPassword) {
                            if ($country === "no-say") {
                                $country = "NULL";
                            }
                            $userId = generateRandomString(11);
                            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                            $query = "INSERT INTO users (id, username, email, birthday, user_url, country, password_hash) VALUES (:id, :username, :email, :birthday, :user_url, :country, :password)";
                            $stmt = $pdo->prepare($query);
                            $stmt->execute([
                                'id' => $userId,
                                'username' => $username,
                                'email' => $email,
                                'birthday' => $birthday,
                                'user_url' => generateRandomString(15),
                                'country' => $country,
                                'password' => $hashedPassword,
                            ]);

                            $queryCustomization = "INSERT INTO custom_user (id, UserId, usern_color, pear_elementColor1, pear_elementColor2, pfpBorder, pfpBorderRadius) VALUES (:id, :UserId, :usern_color, :pear_elementColor1, :pear_elementColor2, :pfpBorder, :pfpBorderRadius)";
                            $stmtCustomization = $pdo->prepare($queryCustomization);
                            $stmtCustomization->execute([
                                'id' => generateRandomString(11),
                                'UserId' => $userId,
                                'usern_color' => '#4f4f4f',
                                'pear_elementColor1' => '#dedede',
                                'pear_elementColor2' => '#ffffff',
                                'pfpBorder' => '#858585',
                                'pfpBorderRadius' => '6px'
                            ]);
    
                            $message = "Registro Concluído.";
    
                            ConnectionRedis::setData('users', $userId, 'status', ['online']);
                            $time = time();
                            ConnectionRedis::setData('users', $userId, 'last_activity', [date("Y-m-d", $time)]);
    
                            $_SESSION['user-info'] = ['userId' => $userId];
                            
                            
                            header("Location: /");
                            exit;
                        
                        } else {
                            $message = "As senhas não batem.";
                        }
                    }
                }
            } catch (\PDOException $e) {
                $message = "Erro ao criar conta: " . $e->getMessage();
            }
        }

        view('create-account', [
            'title' => 'Criar Conta',
            'hh' => 'Cadastrar',
            'message' => $message ?? null,
        ]);

        return $response;
    }
}
