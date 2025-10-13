<?php
// borrar_alumno.php - elimina un alumno por id
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// ⚠️ Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.html');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Conexión
$servername = "localhost";
$username = "root";
$password = "";
$database = "edudata_db";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: ver_alumnos.php');
    exit;
}

// ✅ Borrar solo si pertenece al usuario
$stmt = $conn->prepare("DELETE FROM alumnos WHERE id = ? AND usuario_id = ?");
$stmt->bind_param('ii', $id, $usuario_id);
$stmt->execute();

header('Location: ver_alumnos.php');
exit;
?>
