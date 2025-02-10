<?php 

namespace app\classes;

use app\database\ConnectionSQL;

class User {
    public array $user = [];
    public array $userCustomization = [];
    private $pdo;

    public function __construct() {
        $this->pdo = ConnectionSQL::connect();
    }

    public function DatabaseUser(string $column, string $value): ?array {
        $validColumns = ['username', 'id', 'user_url', 'birthday', 'profile_picture', 'created_at', 'country'];
        
        if (!in_array($column, $validColumns)) {
            throw new \Exception("Coluna inválida para busca");
        }

        $stmt = $this->pdo->prepare("SELECT username, id, user_url, birthday, profile_picture, created_at, country FROM users WHERE $column = :value");
        $stmt->execute(['value' => $value]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$user) {
            throw new \Exception("Usuário não encontrado");
        }

        if ($user['profile_picture'] === null || $user['profile_picture'] == '') {
            $user['profile_picture'] = '/assets/img/defaultp.png';
        }

        $this->user = $user;

        return $user;
    }

    public function DatabaseUserCustomization(): ?array {
        if (empty($this->user['id'])) {
            throw new \Exception("ID do usuário não definido");
        }

        $stmt = $this->pdo->prepare("SELECT usern_color, pear_elementColor1, pear_elementColor2, backgroundImage, pfpBorder, pfpBorderRadius FROM custom_user WHERE UserId = :UserId");
        $stmt->execute(['UserId' => $this->user['id']]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
}
