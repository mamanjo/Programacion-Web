<?php 
session_start();

// Eliminar todas las variables de sesión
$_SESSION = array();

// Destruir la sesión
session_destroy();

// Redirigir al index
header("Location: index.html");
exit();
?>
