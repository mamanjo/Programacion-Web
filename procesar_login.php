<?php
// Mostrar errores (útil para desarrollo)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// ✅ Conexión centralizada
require 'conexion.php';

// Procesar solo si se envió el formulario por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST['correo_escuela']) || empty($_POST['password'])) {
        die("Faltan campos del formulario.");
    }

    $correo = trim($_POST['correo_escuela']);
    $pass = $_POST['password'];

    // Consulta segura: traer id y password
    $stmt = $conn->prepare("SELECT id, password FROM usuarios WHERE correo_escuela = ?");
    if (!$stmt) {
        die("Error en la preparación: " . $conn->error);
    }

    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Obtener resultados (id y hash)
        $stmt->bind_result($id_usuario, $hash_guardado);
        $stmt->fetch();

        // Verificar contraseña
        if (password_verify($pass, $hash_guardado)) {

            // ✅ Guardar datos en sesión
            $_SESSION['usuario_id'] = $id_usuario;
            $_SESSION['correo_escuela'] = $correo;

            // ✅ Redirigir a la sección de alumnos
            header("Location: alumnos.html");
            exit();

        } else {
            echo "<script>alert('Contraseña incorrecta'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('Usuario no encontrado'); window.history.back();</script>";
        exit();
    }

    $stmt->close();
} else {
    echo "⚠️ Acceso no permitido directamente.";
}

$conn->close();
?>
