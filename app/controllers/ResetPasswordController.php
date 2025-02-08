<?php
namespace app\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use app\database\ConnectionSQL;

class ResetPasswordController {
    public function index(Request $request, Response $response) {
        $token = $_GET['token'];
        $hashT = hash('sha256', $token);
    
        $pdo = ConnectionSQL::connect();
    
        $query = "SELECT * FROM users WHERE reset_tokenhash = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$hashT]);
    
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
    
        $message = '';
        $viewData = ['title' => 'Redefinir Senha', 'token' => $token];
    
        if ($user === false) {
            return $response->withHeader('Location', '/')->withStatus(302);
        } else {
            $expiry = $user["reset_token_expires_at"];
            if ($expiry === null || strtotime($expiry) <= time()) {
                return $response->withHeader('Location', '/')->withStatus(302);
            } else {
                if ($request->getMethod() == 'POST') {
                    $formData = $request->getParsedBody();
                    $newPassword = $formData['password'] ?? '';
                    $confirmPassword = $formData['passwordRep'] ?? '';
    
                    if ($newPassword && $confirmPassword) {
                        if ($newPassword === $confirmPassword) {
                            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
                            $query = "UPDATE users 
                                      SET password_hash = ?, reset_tokenhash = NULL, reset_token_expires_at = NULL 
                                      WHERE id = ?";
                            $stmt = $pdo->prepare($query);
                            $stmt->execute([$hashedPassword, $user['id']]);
    
                            return $response->withHeader('Location', '/login')->withStatus(302);
                        } else {
                            $message = "As senhas não batem.";
                        }
                    } else {
                        $message = "Por favor, preencha ambos os campos de senha.";
                    }
                }
            }
        }
    
        $viewData['message'] = $message;
    
        view('password-reset', $viewData);
    
        return $response;
    }

}