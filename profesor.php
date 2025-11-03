<?php
// profesor.php – Panel del profesor con cursos y alumnos desde BD
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Verificar que sea profesor logueado
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'profesor' || empty($_SESSION['id_profesor'])) {
    header('Location: login_profesor.html');
    exit;
}

require 'conexion.php';

$id_profesor = (int)$_SESSION['id_profesor'];

// 1) Traer los cursos/materias asignados a ESTE profesor
// curso_materia: id_curso_materia, id_curso, id_materia, id_escuela, id_profesor
$sqlCursos = "
    SELECT 
        cm.id_curso_materia,
        cm.id_curso,
        cm.id_escuela,
        c.nombre  AS nombre_curso,
        m.nombre  AS nombre_materia,
        e.nombre  AS nombre_escuela
    FROM curso_materia cm
    INNER JOIN cursos   c ON cm.id_curso   = c.id_curso
    INNER JOIN materias m ON cm.id_materia = m.id_materia
    INNER JOIN escuelas e ON cm.id_escuela = e.id_escuela
    WHERE cm.id_profesor = ?
    ORDER BY e.nombre, c.nombre, m.nombre
";


$stmt = $conn->prepare($sqlCursos);
$stmt->bind_param('i', $id_profesor);
$stmt->execute();
$resCursos = $stmt->get_result();

// Guardamos en array para poder reutilizar
$cursos = [];
while ($row = $resCursos->fetch_assoc()) {
    $cursos[] = $row;
}
$stmt->close();

// 2) Ver si el profesor ya eligió un curso y una fecha (GET)
$id_curso_materia_sel = isset($_GET['id_curso_materia']) ? (int)$_GET['id_curso_materia'] : 0;
$fecha = isset($_GET['fecha']) && $_GET['fecha'] !== '' ? $_GET['fecha'] : date('Y-m-d');

// Buscar info del curso seleccionado dentro del array $cursos
$cursoSeleccionado = null;
foreach ($cursos as $c) {
    if ($c['id_curso_materia'] == $id_curso_materia_sel) {
        $cursoSeleccionado = $c;
        break;
    }
}

// 3) Si hay curso seleccionado, buscar alumnos de ese curso
$alumnos = [];
if ($cursoSeleccionado) {
    $sqlAlu = "
        SELECT id_alumno, nombre, dni
        FROM alumnos
        WHERE id_curso = ? AND id_escuela = ?
        ORDER BY nombre
    ";
    $stmt2 = $conn->prepare($sqlAlu);
    $stmt2->bind_param('ii', $cursoSeleccionado['id_curso'], $cursoSeleccionado['id_escuela']);
    $stmt2->execute();
    $resAlu = $stmt2->get_result();
    while ($a = $resAlu->fetch_assoc()) {
        $alumnos[] = $a;
    }
    $stmt2->close();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>EduData – Panel del Profesor</title>

  <!-- Estilos base del sitio -->
  <link rel="stylesheet" href="styles.css" />
  <!-- Estilos específicos del panel del profesor -->
  <link rel="stylesheet" href="profesor.css" />
</head>
<body>

  <!-- Barra superior -->
  <nav class="nav">
    <div class="nav-inner">
      <div class="brand"><a href="index.html" class="brand-link">EduData</a></div>
      <ul class="menu">
        <li><a class="btn-acceso" href="logout_profesor.php" id="btn-logout">Cerrar sesión</a></li>
      </ul>
    </div>
  </nav>

  <main>
    <section class="contenedor">
      <h1 class="titulo">Panel del Profesor</h1>

      <?php if (isset($_GET['ok'])): ?>
        <p class="mensaje-exito">✅ Asistencia guardada correctamente.</p>
      <?php endif; ?>

      <p class="sub">
        Elegí el curso / materia que tenés asignado y registrá la asistencia del día.
      </p>

      <!-- Selector de curso y fecha (GET) -->
      <form class="filtros" id="form-filtros" method="get" action="profesor.php">
        <label>
          Curso / Materia
          <select name="id_curso_materia" required>
            <option value="">Seleccionar…</option>
            <?php foreach ($cursos as $c): ?>
              <?php
                $texto = $c['nombre_escuela'] . ' - ' . $c['nombre_curso'] . ' - ' . $c['nombre_materia'];
              ?>
              <option value="<?= (int)$c['id_curso_materia'] ?>"
                <?= $id_curso_materia_sel === (int)$c['id_curso_materia'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($texto) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </label>

        <label>
          Fecha
          <input type="date" name="fecha" value="<?= htmlspecialchars($fecha) ?>" required>
        </label>

        <button type="submit" class="btn-primario">Ver lista</button>
      </form>

      <!-- Tabla de asistencia (solo si hay curso seleccionado) -->
      <div class="tabla-wrap">
        <?php if ($cursoSeleccionado && !empty($alumnos)): ?>
          <form action="guardar_asistencia.php" method="POST" id="form-asistencia">
            <!-- Datos ocultos del curso y fecha -->
            <input type="hidden" name="id_curso_materia" value="<?= (int)$cursoSeleccionado['id_curso_materia'] ?>">
            <input type="hidden" name="fecha" value="<?= htmlspecialchars($fecha) ?>">

            <table class="tabla">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Alumno</th>
                  <th>DNI</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody>
                <?php $i = 1; ?>
                <?php foreach ($alumnos as $al): ?>
                  <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($al['nombre']) ?></td>
                    <td><?= htmlspecialchars($al['dni']) ?></td>
                    <td class="estado">
                      <!-- Solo P / A como pediste -->
                      <label>
                        <input type="radio"
                               name="estado[<?= (int)$al['id_alumno'] ?>]"
                               value="P"
                               checked>
                        Presente
                      </label>
                      <label>
                        <input type="radio"
                               name="estado[<?= (int)$al['id_alumno'] ?>]"
                               value="A">
                        Ausente
                      </label>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>

            <div class="guardar">
              <button type="submit" class="btn-primario">Guardar asistencia</button>
            </div>
          </form>
        <?php elseif ($cursoSeleccionado && empty($alumnos)): ?>
          <p>No hay alumnos cargados para este curso.</p>
        <?php else: ?>
          <p>Elegí un curso y una fecha y luego presioná <strong>Ver lista</strong>.</p>
        <?php endif; ?>
      </div>

    </section>
  </main>

  <footer class="footer">© 2025 EduData — Panel del Profesor</footer>

</body>
</html>
