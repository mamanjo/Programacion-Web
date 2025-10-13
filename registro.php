<?php
session_start();

// ✅ Recomendado: usar archivo de conexión central
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo_escuela']);
    $pass = $_POST['password'];
    $confirmar = $_POST['confirmar_password'];

    // 🔸 1. Verificar que las contraseñas coincidan
    if ($pass !== $confirmar) {
        echo "<script>alert('Las contraseñas no coinciden'); window.history.back();</script>";
        exit();
    }

    // 🔸 2. Verificar si el correo ya está registrado
    $check = $conn->prepare("SELECT id FROM usuarios WHERE correo_escuela = ?");
    $check->bind_param("s", $correo);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('Este correo ya está registrado'); window.history.back();</script>";
        $check->close();
        exit();
    }
    $check->close();

    // 🔸 3. Hashear la contraseña antes de insertar
    $hash = password_hash($pass, PASSWORD_DEFAULT);

    // 🔸 4. Insertar usuario
    $stmt = $conn->prepare("INSERT INTO usuarios (correo_escuela, password, fecha_registro) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $correo, $hash);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Registro exitoso. Ahora puedes iniciar sesión'); window.location.href='login.html';</script>";
    } else {
        echo "❌ Error al registrar: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
