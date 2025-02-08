<?php
namespace app\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use app\database\ConnectionSQL;
use app\database\ConnectionRedis;

class SettingsController {
    public function settings(Request $request, Response $response) {
        $message = '';
        $formData = $request->getParsedBody();
        $formType = $formData['form-type'] ?? null;
        $userId = $_SESSION['user-info']['userId'];
        if (!isset($userId) || $_SESSION['type-user'] !== 'user') {
            return $response->withHeader('Location', '/login')->withStatus(302);
        }
        $pdo = ConnectionSQL::connect();
        $stmt = $pdo->prepare("SELECT country, user_url FROM users WHERE id = :userId");
        $stmt->execute(['userId' => $userId]);

        $stmtCustomization = $pdo->prepare("SELECT usern_color, pear_elementColor1, pear_elementColor2 FROM custom_user WHERE UserId = :UserId");
        $stmtCustomization->execute(['UserId' => $userId]);

        $userCustomization = $stmtCustomization->fetch(\PDO::FETCH_ASSOC);
        $userInfo = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($formType === 'accountSetUsername') {
            $this->AccountSettings($formData, $pdo, $userId, $message);
            if ($message == '') {
                return $response->withHeader('Location', "/user/{$userInfo['user_url']}")->withStatus(302);
            }
        } else if ($formType === 'customSet') {
            $this->CustomSettings($formData, $pdo, $userId, $message);
            if ($message == '') {
                return $response->withHeader('Location', "/user/{$userInfo['user_url']}")->withStatus(302);
            }
        }   else if ($formType === 'recoverPassword') {
            $this->RecoverPassword($formData, $pdo, $userId, $message);
        }
        view('settings', [
            'title' => 'Configurações',
            'hh' => null,
            'message' => $message,
            'userInfo' => $userInfo,
            'username' => $_SESSION['user-info']['username'],
            'userCustomization' => $userCustomization
        ]);
        return $response;
    }
    private function AccountSettings(array $formData, $pdo, $userId, &$message) {
        $newUsername = trim($formData['username']);
        $newCountry = $formData['country'];
        $newBirthday = $formData['birthday'];
    
        if (strlen($newUsername) > 50) {
            $message = "Limite excedido de caracteres.";
            return;
        }
    
        try {
            if (!empty($newUsername)) {
                $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
                $stmtCheck->execute(['username' => $newUsername]);
                $usernameExists = $stmtCheck->fetchColumn();
        
                if ($usernameExists) {
                    $message = "Nome de usuário já está em uso.";
                    return;
                }
        
                $updateFields['username'] = $newUsername;
            }
        
            if ($newBirthday) {
                $updateFields['birthday'] = $newBirthday;
            }
        
            if ($newCountry) {
                $updateFields['country'] = $newCountry;
            }
        
            if (!empty($updateFields)) {
                $fieldAssignments = implode(', ', array_map(fn($key) => "$key = :$key", array_keys($updateFields)));
                $query = "UPDATE users SET $fieldAssignments WHERE id = :userId";
        
                $updateFields['userId'] = $userId;
        
                $stmt = $pdo->prepare($query);
                $stmt->execute($updateFields);
        
                ConnectionRedis::setData('users', $userId, 'last_activity', [date("Y-m-d", time())]);
            }
        
            $message = '';
        } catch (\PDOException $e) {
            $message = "Erro ao Definir Configurações: " . $e->getMessage();
            error_log("Erro ao Definir Configurações: " . $e->getMessage());
        }
    }

    private function RecoverPassword(array $formData, $pdo, $userId, &$message) {
        $email = $formData['email'];
        
        $query = "SELECT id FROM users WHERE email = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$email]);
    
        if ($stmt->rowCount() == 0) {
            $message = "E-mail não encontrado";
            return;
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
    
    
    private function CustomSettings(array $formData, $pdo, $userId, &$message) {
        $newUsernameColor = $formData['usern-color'];
        $pearElementColor = $formData['pear-element'];
        $pearElementColor1 = $formData['pear-element1'];

        try {
            if ($pearElementColor) {
                $updateFields['pear_elementColor1'] = $pearElementColor;
            }

            if ($pearElementColor1) {
                $updateFields['pear_elementColor2'] = $pearElementColor1;
            }

            if ($newUsernameColor) {
                $updateFields['usern_color'] = $newUsernameColor;
            }
        
            if (!empty($updateFields)) {
                $fieldAssignments = implode(', ', array_map(fn($key) => "$key = :$key", array_keys($updateFields)));
                $query = "UPDATE custom_user SET $fieldAssignments WHERE UserId = :userId";
        
                $updateFields['userId'] = $userId;
        
                $stmt = $pdo->prepare($query);
                $stmt->execute($updateFields);
        
                ConnectionRedis::setData('users', $userId, 'last_activity', [date("Y-m-d", time())]);
            }
        
            $message = '';
        } catch (\PDOException $e) {
            $message = "Erro ao Definir Configurações: " . $e->getMessage();
            error_log("Erro ao Definir Configurações: " . $e->getMessage());
        }
    }
    
}