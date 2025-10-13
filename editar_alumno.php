<?php
// editar_alumno.php - editar y actualizar datos de un alumno
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// ⚠️ Verificar sesión activa
if (!isset($_SESSION['usuario_id'])) {
  header("Location: login.html");
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
  die('Error de conexión: ' . $conn->connect_error);
}

// ID del alumno
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
  die('ID inválido');
}

// ✅ Verificar que el alumno pertenece al usuario logueado
$verificar = $conn->prepare("SELECT * FROM alumnos WHERE id = ? AND usuario_id = ? LIMIT 1");
$verificar->bind_param('ii', $id, $usuario_id);
$verificar->execute();
$result = $verificar->get_result();

if ($result->num_rows === 0) {
  die('⚠️ No tienes permiso para editar este alumno.');
}

$alumno = $result->fetch_assoc();
$verificar->close();

// ✅ Si se envió el formulario, procesar la actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = $_POST['nombre'] ?? '';
  $dni = $_POST['dni'] ?? '';
  $email = $_POST['email'] ?? '';
  $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
  $curso = $_POST['curso'] ?? '';
  $telefono_emergencia = $_POST['telefono_emergencia'] ?? '';
  $nacionalidad = $_POST['nacionalidad'] ?? '';
  $direccion = $_POST['direccion'] ?? '';
  $materia_dificultosa = $_POST['materia_dificultosa'] ?? '';
  $obra_social = $_POST['obra_social'] ?? '';
  $turno = $_POST['turno'] ?? '';
  $observaciones = $_POST['observaciones'] ?? '';

  $sql = "UPDATE alumnos 
          SET nombre = ?, dni = ?, email = ?, fecha_nacimiento = ?, curso = ?, telefono_emergencia = ?, nacionalidad = ?, direccion = ?, materia_dificultosa = ?, obra_social = ?, turno = ?, observaciones = ?
          WHERE id = ? AND usuario_id = ?";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param(
    'ssssssssssssis',
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
    $id,
    $usuario_id
  );

  if ($stmt->execute()) {
    // ✅ Redirigir directamente a la lista después de guardar
    header("Location: ver_alumnos.php?editado=1");
    exit;
  } else {
    $error = 'Error al actualizar: ' . $stmt->error;
  }
  $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Editar Alumno</title>
  <link rel="stylesheet" href="alumnos.css" />
  <style>
    .container { max-width: 700px; margin: 24px auto; padding: 16px; }
    label { display:block; margin-bottom:10px; color: var(--muted); }
    input, textarea, select { width:100%; padding:8px; border-radius:6px; background:#1e293b; color:var(--text); border:none; }
    .error { color: #fca5a5; margin-bottom:12px; }
    .acciones { margin-top:12px; }
    .acciones button, .acciones a { margin-right:8px; }
  </style>
</head>
<body>
  <div class="container">
    <h1 class="titulo-principal">Editar Alumno</h1>

    <?php if (!empty($error)): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
      <label>Nombre completo
        <input type="text" name="nombre" value="<?= htmlspecialchars($alumno['nombre']) ?>" required />
      </label>

      <label>DNI
        <input type="text" name="dni" value="<?= htmlspecialchars($alumno['dni']) ?>" required />
      </label>

      <label>Correo electrónico
        <input type="email" name="email" value="<?= htmlspecialchars($alumno['email']) ?>" required />
      </label>

      <label>Fecha de nacimiento
        <input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($alumno['fecha_nacimiento']) ?>" />
      </label>

      <label>Curso
        <input type="text" name="curso" value="<?= htmlspecialchars($alumno['curso']) ?>" />
      </label>

      <label>Teléfono de emergencia
        <input type="tel" name="telefono_emergencia" value="<?= htmlspecialchars($alumno['telefono_emergencia']) ?>" />
      </label>

      <label>Nacionalidad
        <input type="text" name="nacionalidad" value="<?= htmlspecialchars($alumno['nacionalidad']) ?>" />
      </label>

      <label>Dirección
        <input type="text" name="direccion" value="<?= htmlspecialchars($alumno['direccion']) ?>" />
      </label>

      <label>Materia más dificultosa
        <input type="text" name="materia_dificultosa" value="<?= htmlspecialchars($alumno['materia_dificultosa']) ?>" />
      </label>

      <label>Obra social
        <input type="text" name="obra_social" value="<?= htmlspecialchars($alumno['obra_social']) ?>" />
      </label>

      <label>Turno
        <select name="turno">
          <option value="manana" <?= $alumno['turno'] === 'manana' ? 'selected' : '' ?>>Mañana</option>
          <option value="tarde" <?= $alumno['turno'] === 'tarde' ? 'selected' : '' ?>>Tarde</option>
          <option value="noche" <?= $alumno['turno'] === 'noche' ? 'selected' : '' ?>>Noche</option>
        </select>
      </label>

      <label>Observaciones
        <textarea name="observaciones" rows="4"><?= htmlspecialchars($alumno['observaciones']) ?></textarea>
      </label>

      <div class="acciones">
        <button type="submit">Guardar cambios</button>
        <a href="ver_alumnos.php">Cancelar</a>
      </div>
    </form>
  </div>
</body>
</html>
