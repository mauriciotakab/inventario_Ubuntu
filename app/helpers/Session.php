<?php
class Session {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0, 'path' => '/', 'secure' => false, // true si usas HTTPS
                'httponly' => true, 'samesite' => 'Lax'
            ]);
            session_start();
        }
    }
    public static function regen(): void { session_regenerate_id(true); }

    public static function setUser(array $u): void {
        self::start();
        $_SESSION['user_id'] = (int)$u['id'];
        $_SESSION['username'] = $u['username'];
        $_SESSION['nombre'] = $u['nombre_completo'] ?? $u['nombre'] ?? $u['username'];
        $_SESSION['role'] = $u['role']; // 'Administrador'|'Almacen'|'Empleado'
    }

    public static function user(): ?array {
        self::start();
        if (!isset($_SESSION['user_id'])) return null;
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? null,
            'nombre' => $_SESSION['nombre'] ?? null,
            'role' => $_SESSION['role'] ?? null,
        ];
    }

    public static function requireLogin($roles = null) {
        self::start();
        if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
        if ($roles) {
            $role = $_SESSION['role'] ?? '';
            if (is_array($roles)) { if (!in_array($role, $roles, true)) { header("Location: dashboard.php?no_access=1"); exit(); } }
            else { if ($role !== $roles) { header("Location: dashboard.php?no_access=1"); exit(); } }
        }
    }

    public static function logout(): void {
        self::start();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time()-42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function csrfToken(): string {
        self::start();
        if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
        return hash_hmac('sha256', $_SESSION['csrf'], 'takab_csrf_key_change_me');
    }
    public static function checkCsrf(string $t): bool { return hash_equals(self::csrfToken(), $t); }

    // Constantes de roles para evitar “magia” en strings
    public const R_ADMIN = 'Administrador';
    public const R_ALMACEN = 'Almacen';
    public const R_EMPLEADO = 'Empleado';
}
