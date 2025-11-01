<?php
// logout.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Borrar variables de sesión
$_SESSION = [];

// Borrar cookie de sesión (si existe)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

// Redirigir a la página de inicio
header('Location: index.html');
exit;
