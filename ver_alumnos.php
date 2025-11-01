<?php
// ver_alumnos.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.html');
    exit;
}

require 'conexion.php';

$id_usuario = (int)$_SESSION['id_usuario'];

// Traer alumnos del usuario logueado
$sql = "SELECT 
            a.id_alumno,
            a.nombre,
            a.dni,
            a.email,
            a.fecha_nacimiento,
            e.nombre AS escuela,
            c.nombre AS curso,
            a.turno,
            a.fecha_registro
        FROM alumnos a
        INNER JOIN escuelas e ON a.id_escuela = e.id_escuela
        INNER JOIN cursos c   ON a.id_curso   = c.id_curso
        WHERE a.id_usuario = ?
        ORDER BY a.id_alumno DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Listado de Alumnos</title>
  <link rel="stylesheet" href="ver_alumnos.css">
</head>
<body>

  <header>
    <h1>📋 Alumnos Registrados</h1>
    <a href="alumnos.php" class="btn-volver">➕ Nuevo alumno</a>
    <a href="logout.php" class="btn-logout" onclick="return confirm('¿Cerrar sesión?')">🚪 Salir</a>
  </header>

  <?php if (isset($_GET['ok'])): ?>
    <p class="mensaje-exito">✅ Alumno registrado correctamente.</p>
  <?php endif; ?>

  <section class="tabla-alumnos">
    <table border="1" cellspacing="0" cellpadding="8">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>DNI</th>
          <th>Email</th>
          <th>Fecha Nac.</th>
          <th>Escuela</th>
          <th>Curso</th>
          <th>Turno</th>
          <th>Fecha Registro</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['id_alumno']) ?></td>
              <td><?= htmlspecialchars($row['nombre']) ?></td>
              <td><?= htmlspecialchars($row['dni']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td><?= htmlspecialchars($row['fecha_nacimiento']) ?></td>
              <td><?= htmlspecialchars($row['escuela']) ?></td>
              <td><?= htmlspecialchars($row['curso']) ?></td>
              <td><?= htmlspecialchars($row['turno']) ?></td>
              <td><?= htmlspecialchars($row['fecha_registro']) ?></td>
              <td>
                <a href="editar_alumno.php?id=<?= $row['id_alumno'] ?>" class="btn-editar">✏️ Editar</a>
                <a href="borrar_alumno.php?id=<?= $row['id_alumno'] ?>" class="btn-borrar" onclick="return confirm('¿Eliminar este alumno?')">🗑️ Borrar</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="10" style="text-align:center;">No hay alumnos registrados todavía.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </section>

</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
