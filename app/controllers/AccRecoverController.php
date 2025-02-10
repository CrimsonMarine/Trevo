<?php
namespace app\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use app\database\ConnectionSQL;

class AccRecoverController {
    public function recover(Request $request, Response $response) {
        $message = '';

        $pdo = ConnectionSQL::connect();
        $formData = $request->getParsedBody();
        $formType = $formData['form-type'] ?? null;

         if ($formType === 'recoverPassword') {
            $email = $formData['email'];
        
            $query = "SELECT id FROM users WHERE email = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$email]);
        
            if ($stmt->rowCount() == 0) {
                $message = "E-mail não encontrado";
            }

            $token = bin2hex(random_bytes(20));
            $hashT = hash('sha256', $token); 
            $expires = date("Y-m-d H:i:s", time() + 60 * 30);
        
            $query = "UPDATE users SET reset_tokenhash = ?, reset_token_expires_at = ? WHERE email = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$hashT, $expires, $email]);
        
            if ($stmt->rowCount() > 0) {
                $mail = require __DIR__ . "/../actions/mailer.php";
                $mail->setFrom("trevoemail@trevoapp.com");
                $mail->addAddress($email);
                $mail->Subject = "Password Reset";
                
                $domain = (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] === 'localhost') 
                        ? 'http://localhost' 
                        : 'https://www.trevoapp.com';
                $resetLink = "{$domain}/password-reset?token=$token";
                
                $mail->Body = <<<END
                Clique <a href="$resetLink">Aqui</a> para redefinir sua senha.
                END;
                
                try {
                    if ($mail->send()) {
                        $message = "Cheque sua caixa de entrada de e-mail para redefinir sua senha.";
                    } else {
                        $message = "Erro ao enviar o e-mail.";
                    }
                } catch (\Exception $e) {
                    $message = "Erro: {$mail->ErrorInfo}";
                }
            } else {
                $message = "E-mail não encontrado";
            }
        }
        view('account-recover', ['title' => 'Recuperação de Conta', 'hh' => 'Recuperação de Conta', 'message' => $message]);
        return $response;
    }

}