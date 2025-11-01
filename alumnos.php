<?php
// alumnos.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>EduData · Registrar Alumno</title>
  <link rel="stylesheet" href="alumnos.css" />
</head>
<body>

  <header class="header-alumnos">
    <h1 class="titulo-principal">Sección de Alumnos</h1>
    <a href="logout.php" class="btn-logout" onclick="return confirm('¿Cerrar sesión?')">🚪 Cerrar sesión</a>
  </header>

  <p class="introduccion">
    Completá el formulario. Elegí <strong>Escuela</strong> y <strong>Curso</strong> por su <em>ID</em> (valores reales de tu base).
  </p>

  <h2 class="subtitulo-izquierda">Ingresar un alumno</h2>

  <section class="alumnos-section">
    <form action="procesar_formulario.php" method="POST" class="formulario">

      <label>Nombre completo
        <input type="text" name="nombre" placeholder="Ej: Juan Pérez" required />
      </label>

      <label>DNI
        <input type="text" name="dni" placeholder="Ej: 40123456" required />
      </label>

      <label>Correo electrónico
        <input type="email" name="email" placeholder="Ej: alumno@escuela.com" required />
      </label>

      <label>Fecha de nacimiento
        <input type="date" name="fecha_nacimiento" required />
      </label>

      <!-- 🔽 Escuelas (IDs REALES) -->
      <label>Escuela
        <select name="id_escuela" required>
          <option value="" disabled selected>Selecciona una escuela</option>

          <!-- ⚠️ REEMPLAZÁ estos values con tus IDs REALES de la tabla `escuelas` -->
          <option value="1">Escuela Técnica 4-022 “Enrique Mosconi”</option>
          <option value="2">Escuela Ing. Gabriel del Mazo</option>
          <option value="3">Escuela Nº XXX</option>
        </select>
      </label>

      <!-- 🔽 Cursos (IDs REALES) -->
      <label>Curso
        <select name="id_curso" required>
          <option value="" disabled selected>Selecciona un curso</option>

          <!-- ⚠️ REEMPLAZÁ estos values con tus IDs REALES de la tabla `cursos` -->
          <optgroup label="Escuela 1">
            <option value="10">1ºA</option>
            <option value="11">1ºB</option>
            <option value="12">2ºA</option>
          </optgroup>

          <optgroup label="Escuela 2">
            <option value="20">1ºA</option>
            <option value="21">2ºB</option>
          </optgroup>

          <optgroup label="Escuela 3">
            <option value="30">3ºA</option>
          </optgroup>
        </select>
      </label>

      <label>Teléfono de emergencia
        <input type="tel" name="telefono_emergencia" placeholder="Ej: +54 9 261 555-1234" />
      </label>

      <label>Nacionalidad
        <input type="text" name="nacionalidad" placeholder="Ej: Argentina" />
      </label>

      <label>Dirección
        <input type="text" name="direccion" placeholder="Calle, número, localidad" />
      </label>

      <label>Materia más dificultosa
        <input type="text" name="materia_dificultosa" placeholder="Ej: Matemáticas" />
      </label>

      <label>Obra social
        <input type="text" name="obra_social" placeholder="Ej: OSDE" />
      </label>

      <label>Turno
        <select name="turno">
          <option value="">Selecciona…</option>
          <option value="mañana">Mañana</option>
          <option value="tarde">Tarde</option>
          <option value="noche">Noche</option>
        </select>
      </label>

      <label>Observaciones
        <textarea name="observaciones" rows="3" placeholder="Información adicional (opcional)"></textarea>
      </label>

      <button type="submit">Registrar Alumno</button>

      <div class="ver-base">
        <a href="ver_alumnos.php" class="btn-ver">📋 Ver base de alumnos</a>
      </div>
    </form>
  </section>

</body>
</html>
