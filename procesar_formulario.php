<?php
// Mostrar errores (útil para desarrollo)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start(); // ✅ Necesario para usar $_SESSION

// ✅ Verificar que el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    die("⚠️ No estás logueado. <a href='login.html'>Inicia sesión</a>");
}

$usuario_id = $_SESSION['usuario_id'];

// 1️⃣ Conexión a la base de datos
require 'conexion.php'; // ✅ recomendable centralizar

// 2️⃣ Obtener datos del formulario (método POST)
$nombre = $_POST['nombre'] ?? '';
$dni = $_POST['dni'] ?? '';
$email = $_POST['email'] ?? '';
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
$curso = $_POST['curso'] ?? '';
$telefono_emergencia = $_POST['telefono_emergencia'] ?? '';
$nacionalidad = $_POST['nacionalidad'] ?? '';
$direccion = $_POST['direccion'] ?? '';
$materia_dificultosa = $_POST['materia_dificultosa'] ?? '';
$obra_social = $_POST['obra_social'] ?? '';
$turno = $_POST['turno'] ?? '';
$observaciones = $_POST['observaciones'] ?? '';

// 3️⃣ Preparar consulta SQL incluyendo usuario_id
$stmt = $conn->prepare("INSERT INTO alumnos 
(nombre, dni, email, fecha_nacimiento, curso, telefono_emergencia, nacionalidad, direccion, materia_dificultosa, obra_social, turno, observaciones, usuario_id)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
    "ssssssssssssi",
    $nombre,
    $dni,
    $email,
    $fecha_nacimiento,
    $curso,
    $telefono_emergencia,
    $nacionalidad,
    $direccion,
    $materia_dificultosa,
    $obra_social,
    $turno,
    $observaciones,
    $usuario_id
);

// 4️⃣ Ejecutar la consulta
if ($stmt->execute()) {
    // Éxito: redirigir con mensaje
    header("Location: ver_alumnos.php?registro=exitoso");
    exit;
} else {
    echo "Error al registrar alumno: " . $stmt->error;
}

// 5️⃣ Cerrar conexiones
$stmt->close();
$conn->close();
?>
