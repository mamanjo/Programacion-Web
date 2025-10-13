<?php
// Mostrar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start(); // ✅ Necesario para usar $_SESSION

// ⚠️ Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Conexión a la base
$servername = "localhost";
$username = "root";
$password = "";
$database = "edudata_db";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
  die("Error de conexión: " . $conn->connect_error);
}

// ✅ Traer solo los alumnos del usuario logueado
$stmt = $conn->prepare("SELECT * FROM alumnos WHERE usuario_id = ? ORDER BY fecha_registro DESC");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Lista de Alumnos Registrados</title>
  <link rel="stylesheet" href="ver_alumnos.css" />
</head>
<body>
  <h1 class="titulo-lista">👨‍🏫 Lista de Alumnos Registrados</h1>

  <?php if ($result->num_rows > 0): ?>
    <div class="tabla-wrap">
      <table class="tabla-alumnos">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre completo</th>
            <th>DNI</th>
            <th>Correo electrónico</th>
            <th>Fecha de nacimiento</th>
            <th>Curso</th>
            <th>Teléfono de emergencia</th>
            <th>Nacionalidad</th>
            <th>Dirección</th>
            <th>Materia más difícil</th>
            <th>Obra social</th>
            <th>Turno</th>
            <th>Observaciones</th>
            <th>Fecha de registro</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['id']) ?></td>
              <td><?= htmlspecialchars($row['nombre']) ?></td>
              <td><?= htmlspecialchars($row['dni']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td><?= htmlspecialchars($row['fecha_nacimiento']) ?></td>
              <td><?= htmlspecialchars($row['curso']) ?></td>
              <td><?= htmlspecialchars($row['telefono_emergencia']) ?></td>
              <td><?= htmlspecialchars($row['nacionalidad']) ?></td>
              <td><?= htmlspecialchars($row['direccion']) ?></td>
              <td><?= htmlspecialchars($row['materia_dificultosa']) ?></td>
              <td><?= htmlspecialchars($row['obra_social']) ?></td>
              <td><?= htmlspecialchars($row['turno']) ?></td>
              <td><?= htmlspecialchars($row['observaciones']) ?></td>
              <td><?= htmlspecialchars($row['fecha_registro']) ?></td>
              <td>
                <a href="editar_alumno.php?id=<?= urlencode($row['id']) ?>" class="accion-editar">Editar</a>
                <a href="borrar_alumno.php?id=<?= urlencode($row['id']) ?>" class="accion-borrar" onclick="return confirm('¿Seguro que quieres eliminar este alumno?');">Eliminar</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p class="sin-alumnos">📭 No hay alumnos registrados todavía.</p>
  <?php endif; ?>

  <a href="alumnos.html" class="volver-link">← Volver al formulario</a>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
