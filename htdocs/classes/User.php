<?php
class User {
    private $pdo;
    private $sessionStarted = false;

    public function __construct(Database $db) {
        $this->pdo = $db->getPdo();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            $this->sessionStarted = true;
        }
    }

    public function login($username, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['senha'] === $password) {
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['nivel'] = $user['nivel'];
            return $user['nivel'];
        }
        return false;
    }

    public function loginAsGuest() {
        $_SESSION['usuario'] = 'cliente_anonimo';
        $_SESSION['nivel'] = 'cliente';
        return 'cliente';
    }

    public function checkAccess($requiredLevel) {
        return isset($_SESSION['nivel']) && $_SESSION['nivel'] === $requiredLevel;
    }

    public function redirect($url) {
        header("Location: $url");
        exit;
    }
}
?>