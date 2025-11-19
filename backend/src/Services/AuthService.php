<?php

namespace App\Services;

use App\Config\Database;
use DateInterval;
use DateTimeImmutable;
use PDO;

class AuthService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function attempt(string $email, string $password): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email AND active = 1 LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password'])) {
            return null;
        }
        $token = bin2hex(random_bytes(32));
        $config = require __DIR__ . '/../Config/config.php';
        $expiresAt = (new DateTimeImmutable('now'))
            ->add(new DateInterval('PT' . ($config['security']['token_ttl_minutes'] ?? 720) . 'M'))
            ->format('Y-m-d H:i:s');

        $insert = $this->db->prepare('INSERT INTO auth_tokens (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)');
        $insert->execute([
            'user_id' => $user['id'],
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);

        unset($user['password']);
        return ['token' => $token, 'user' => $user];
    }

    public function validateToken(string $token): ?array
    {
        $stmt = $this->db->prepare('SELECT u.id, u.name, u.email, u.role, u.active FROM auth_tokens t JOIN users u ON u.id = t.user_id WHERE t.token = :token AND t.expires_at > NOW()');
        $stmt->execute(['token' => $token]);
        $user = $stmt->fetch();
        if (!$user) {
            return null;
        }
        return $user;
    }

    public function logout(string $token): void
    {
        $stmt = $this->db->prepare('DELETE FROM auth_tokens WHERE token = :token');
        $stmt->execute(['token' => $token]);
    }
}
