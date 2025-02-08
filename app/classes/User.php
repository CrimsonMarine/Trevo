<?php 

namespace app\classes;

use app\database\ConnectionSQL;

class User {
    public array $user;
    public array $userCustomization;
    private $pdo;

    public function __construct() {
        $this->pdo = ConnectionSQL::connect();
        
        $this->user = $this->DatabaseUser();

        if (!$this->user) {
            throw new \Exception("Usuário não encontrado");
        }

        $this->userCustomization = $this->DatabaseUserCustomization();
    }

    private function DatabaseUser(): ?array {
        $userId = $_SESSION['user-info']['userId'] ?? null;

        if (!$userId) {
            return null;
        }

        $stmt = $this->pdo->prepare("SELECT username, id, user_url FROM users WHERE id = :userId");
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    private function DatabaseUserCustomization(): ?array {
        $stmtCustomization = $this->pdo->prepare("SELECT usern_color FROM custom_user WHERE UserId = :UserId");
        $stmtCustomization->execute(['UserId' => $this->user['id']]);
        return $stmtCustomization->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
}
