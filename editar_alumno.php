<?php
// editar_alumno.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.html');
    exit;
}
require 'conexion.php';

$id_usuario = (int)$_SESSION['id_usuario'];
$id_alumno  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id_alumno <= 0) {
    header('Location: ver_alumnos.php');
    exit;
}

// 1) Verificar que el alumno existe y pertenece al usuario
$sql = "SELECT * FROM alumnos WHERE id_alumno = ? AND id_usuario = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $id_alumno, $id_usuario);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    $stmt->close();
    die("No tenés permiso para editar este alumno.");
}
$alumno = $res->fetch_assoc();
$stmt->close();

// 2) Si viene POST, procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre              = trim($_POST['nombre'] ?? '');
    $dni                 = trim($_POST['dni'] ?? '');
    $email               = trim($_POST['email'] ?? '');
    $fecha_nacimiento    = trim($_POST['fecha_nacimiento'] ?? '');

    $id_escuela          = (int)($_POST['id_escuela'] ?? 0);
    $id_curso            = (int)($_POST['id_curso'] ?? 0);

    $telefono_emergencia = trim($_POST['telefono_emergencia'] ?? '');
    $nacionalidad        = trim($_POST['nacionalidad'] ?? '');
    $direccion           = trim($_POST['direccion'] ?? '');
    $materia_dificultosa = trim($_POST['materia_dificultosa'] ?? '');
    $obra_social         = trim($_POST['obra_social'] ?? '');
    $turno               = trim($_POST['turno'] ?? '');
    $observaciones       = trim($_POST['observaciones'] ?? '');

    // Validaciones mínimas
    if ($nombre==='' || $dni==='' || $email==='' || $fecha_nacimiento==='' || !$id_escuela || !$id_curso) {
        die('Faltan campos obligatorios.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('Correo inválido.');
    }

    // Coherencia curso ↔ escuela
    $chk = $conn->prepare("SELECT 1 FROM cursos WHERE id_curso = ? AND id_escuela = ? LIMIT 1");
    $chk->bind_param('ii', $id_curso, $id_escuela);
    $chk->execute(); $chk->store_result();
    if ($chk->num_rows === 0) {
        $chk->close();
        die('El curso no pertenece a la escuela seleccionada.');
    }
    $chk->close();

    // UPDATE
    $upd = $conn->prepare("UPDATE alumnos SET
        nombre=?, dni=?, email=?, fecha_nacimiento=?,
        id_curso=?, id_escuela=?,
        telefono_emergencia=?, nacionalidad=?, direccion=?, materia_dificultosa=?,
        obra_social=?, turno=?, observaciones=?
        WHERE id_alumno=? AND id_usuario=?");

    $upd->bind_param(
        "ssssiisssssssii",
        $nombre, $dni, $email, $fecha_nacimiento,
        $id_curso, $id_escuela,
        $telefono_emergencia, $nacionalidad, $direccion, $materia_dificultosa,
        $obra_social, $turno, $observaciones,
        $id_alumno, $id_usuario
    );

    if ($upd->execute()) {
        $upd->close();
        header("Location: ver_alumnos.php?edit=ok");
        exit;
    } else {
        echo "Error al actualizar: " . $upd->error;
        $upd->close();
    }
}

// 3) Si es GET o falló algo, mostrar formulario con valores actuales
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Editar alumno</title>
  <link rel="stylesheet" href="alumnos.css" />
</head>
<body>

  <header class="header-alumnos">
    <h1 class="titulo-principal">Editar Alumno</h1>
    <a href="ver_alumnos.php" class="btn-logout">← Volver</a>
  </header>

  <section class="alumnos-section">
    <form method="POST" class="formulario">

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
        <input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($alumno['fecha_nacimiento']) ?>" required />
      </label>

      <!-- 🔽 Escuelas (IDs REALES) -->
      <label>Escuela
        <select name="id_escuela" required>
          <option value="" disabled>Selecciona una escuela</option>

          <!-- ⚠️ Reemplazá los values con tus IDs reales -->
          <option value="1" <?= (int)$alumno['id_escuela']===1 ? 'selected':''; ?>>Escuela Técnica 4-022 “Enrique Mosconi”</option>
          <option value="2" <?= (int)$alumno['id_escuela']===2 ? 'selected':''; ?>>Escuela Ing. Gabriel del Mazo</option>
          <option value="3" <?= (int)$alumno['id_escuela']===3 ? 'selected':''; ?>>Escuela Nº XXX</option>
        </select>
      </label>

      <!-- 🔽 Cursos (IDs REALES) -->
      <label>Curso
        <select name="id_curso" required>
          <option value="" disabled>Selecciona un curso</option>

          <!-- ⚠️ Reemplazá los values con tus IDs reales -->
          <optgroup label="Escuela 1">
            <option value="10" <?= (int)$alumno['id_curso']===10 ? 'selected':''; ?>>1ºA</option>
            <option value="11" <?= (int)$alumno['id_curso']===11 ? 'selected':''; ?>>1ºB</option>
            <option value="12" <?= (int)$alumno['id_curso']===12 ? 'selected':''; ?>>2ºA</option>
          </optgroup>

          <optgroup label="Escuela 2">
            <option value="20" <?= (int)$alumno['id_curso']===20 ? 'selected':''; ?>>1ºA</option>
            <option value="21" <?= (int)$alumno['id_curso']===21 ? 'selected':''; ?>>2ºB</option>
          </optgroup>

          <optgroup label="Escuela 3">
            <option value="30" <?= (int)$alumno['id_curso']===30 ? 'selected':''; ?>>3ºA</option>
          </optgroup>
        </select>
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
          <option value=""     <?= $alumno['turno']===''        ? 'selected':''; ?>>Selecciona…</option>
          <option value="mañana" <?= $alumno['turno']==='mañana' ? 'selected':''; ?>>Mañana</option>
          <option value="tarde"  <?= $alumno['turno']==='tarde'  ? 'selected':''; ?>>Tarde</option>
          <option value="noche"  <?= $alumno['turno']==='noche'  ? 'selected':''; ?>>Noche</option>
        </select>
      </label>

      <label>Observaciones
        <textarea name="observaciones" rows="3"><?= htmlspecialchars($alumno['observaciones']) ?></textarea>
      </label>

      <div class="acciones">
        <button type="submit">Guardar cambios</button>
        <a href="ver_alumnos.php">Cancelar</a>
      </div>
    </form>
  </section>

</body>
</html>
