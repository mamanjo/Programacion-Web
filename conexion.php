<?php
// conexion.php
// Archivo central para conectar con la base de datos MySQL (XAMPP)

$servername = "localhost";
$username = "root";
$password = ""; // vacío por defecto en XAMPP
$database = "edudata"; // 🔁 cambiá este nombre si tu base tiene otro

$conn = new mysqli($servername, $username, $password, $database);

// Verificamos la conexión
if ($conn->connect_error) {
    die("❌ Error de conexión a la base de datos: " . $conn->connect_error);
}

// Si todo va bien, no se muestra nada
// echo "✅ Conectado correctamente a la base de datos";
?>
