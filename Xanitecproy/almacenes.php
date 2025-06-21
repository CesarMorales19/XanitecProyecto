<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'db.php'; // Definir $pdo con PDO conectado

$accion = $_GET['accion'] ?? 'listar';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$mensaje = '';

// POST: Crear o editar almacén
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $ubicacion = $_POST['ubicacion'] ?? '';
    $responsable = $_POST['responsable'] ?? '';
    $post_accion = $_POST['accion'] ?? '';

    if ($post_accion === 'agregar') {
        $sql = "INSERT INTO almacenes (nombre, ubicacion, responsable) VALUES (:nombre, :ubicacion, :responsable)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute(['nombre' => $nombre, 'ubicacion' => $ubicacion, 'responsable' => $responsable])) {
            header("Location: almacenes.php?accion=listar&msg=agregado");
            exit;
        } else {
            $mensaje = "Error al agregar almacén.";
        }
    } elseif ($post_accion === 'editar' && $id > 0) {
        $sql = "UPDATE almacenes SET nombre=:nombre, ubicacion=:ubicacion, responsable=:responsable WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute(['nombre' => $nombre, 'ubicacion' => $ubicacion, 'responsable' => $responsable, 'id' => $id])) {
            header("Location: almacenes.php?accion=listar&msg=actualizado");
            exit;
        } else {
            $mensaje = "Error al actualizar almacén.";
        }
    }
}

// GET: Eliminar almacén
if ($accion === 'eliminar' && $id > 0) {
    $sql = "DELETE FROM almacenes WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    header("Location: almacenes.php?accion=listar&msg=eliminado");
    exit;
}

// Mensajes de confirmación
$msg = $_GET['msg'] ?? '';
$msgTexto = '';
if ($msg === 'agregado') $msgTexto = "Almacén agregado exitosamente.";
if ($msg === 'actualizado') $msgTexto = "Almacén actualizado exitosamente.";
if ($msg === 'eliminado') $msgTexto = "Almacén eliminado exitosamente.";

// Datos para editar o ver detalle
$data = ['nombre' => '', 'ubicacion' => '', 'responsable' => ''];
if (($accion === 'editar' || $accion === 'ver') && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM almacenes WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        $mensaje = "Almacén no encontrado.";
        $accion = 'listar';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>📦 Gestión de Almacenes</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      height: 100vh;
      overflow: hidden;
      background: linear-gradient(to right, #dfefff, #f7f9ff);
      position: relative;
    }
    #particles-js {
      position: absolute;
      width: 100%;
      height: 100%;
      z-index: -1;
    }
    .container {
      position: relative;
      z-index: 2;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 1rem;
    }
    .card {
      border-radius: 1rem;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
      max-width: 720px;
      width: 100%;
      padding: 2rem 2.5rem;
      background: white;
      transition: transform 0.3s ease;
      overflow-x: auto;
    }
    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 48px rgba(0,0,0,0.15);
    }
    h1, h2 {
      text-align: center;
      font-weight: 700;
      margin-bottom: 1.5rem;
      color: #2575fc;
    }
    a.btn-regresar {
      display: inline-block;
      margin-bottom: 1rem;
      color: #2575fc;
      font-weight: 600;
      text-decoration: none;
      border: 2px solid #2575fc;
      padding: 0.35rem 1rem;
      border-radius: 0.5rem;
      transition: background-color 0.3s ease, color 0.3s ease;
    }
    a.btn-regresar:hover {
      background-color: #2575fc;
      color: white;
    }
    .alert {
      font-weight: 600;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    table th, table td {
      border: 1px solid #dee2e6;
      padding: 0.75rem;
      vertical-align: middle;
      text-align: center;
    }
    table th {
      background-color: #e9f0ff;
      color: #2575fc;
      font-weight: 700;
    }
    table tbody tr:hover {
      background-color: #e9f0ff;
    }
    .btn {
      font-weight: 600;
      transition: background-color 0.3s ease;
      margin: 0 2px;
    }
    .btn-primary {
      background-color: #2575fc;
      border: none;
    }
    .btn-primary:hover {
      background-color: #1a54c8;
    }
    .btn-warning {
      background-color: #f0ad4e;
      border: none;
      color: black;
    }
    .btn-warning:hover {
      background-color: #d4942b;
      color: black;
    }
    .btn-danger {
      background-color: #dc3545;
      border: none;
    }
    .btn-danger:hover {
      background-color: #c82333;
    }
    .btn-success {
      background-color: #28a745;
      border: none;
    }
    .btn-success:hover {
      background-color: #218838;
    }
    form > .mb-3 > label {
      font-weight: 600;
      text-align: left;
    }
  </style>
</head>
<body>

<div id="particles-js"></div>

<div class="container">
  <div class="card">

    <h1>📦 Gestión de Almacenes</h1>
    
    <a href="success.php" class="btn-regresar">⬅️ Regresar al Inicio</a>

    <?php if ($mensaje): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($mensaje) ?></div>
    <?php elseif ($msgTexto): ?>
      <div class="alert alert-success"><?= htmlspecialchars($msgTexto) ?></div>
    <?php endif; ?>

    <?php if ($accion === 'listar'): ?>
      <?php
      $stmt = $pdo->query("SELECT * FROM almacenes ORDER BY id DESC");
      $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
      ?>
      <a href="almacenes.php?accion=agregar" class="btn btn-primary mb-3">➕ Agregar Almacén</a>

      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Ubicación</th>
            <th>Responsable</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($resultado): ?>
            <?php foreach ($resultado as $row): ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['nombre']) ?></td>
                <td><?= htmlspecialchars($row['ubicacion']) ?></td>
                <td><?= htmlspecialchars($row['responsable']) ?></td>
                <td>
                  <a href="almacenes.php?accion=ver&id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">🔍 Ver</a>
                  <a href="almacenes.php?accion=editar&id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">✏️ Editar</a>
                  <a href="almacenes.php?accion=eliminar&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar almacén?')">🗑️ Eliminar</a>
                  <a href="productos.php?almacen_id=<?= $row['id'] ?>" class="btn btn-success btn-sm">📦 Productos</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="5" class="text-center">No hay almacenes registrados.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>

    <?php elseif ($accion === 'ver'): ?>

      <h2>Detalles del Almacén</h2>
      <table>
        <tr><th>ID</th><td><?= $id ?></td></tr>
        <tr><th>Nombre</th><td><?= htmlspecialchars($data['nombre']) ?></td></tr>
        <tr><th>Ubicación</th><td><?= htmlspecialchars($data['ubicacion']) ?></td></tr>
        <tr><th>Responsable</th><td><?= htmlspecialchars($data['responsable']) ?></td></tr>
      </table>
      <div class="mt-3 d-flex justify-content-center gap-2">
        <a href="almacenes.php?accion=listar" class="btn btn-primary">⬅️ Volver</a>
        <a href="almacenes.php?accion=editar&id=<?= $id ?>" class="btn btn-warning">✏️ Editar</a>
      </div>

    <?php elseif ($accion === 'agregar' || $accion === 'editar'): ?>

      <h2><?= $accion === 'agregar' ? '➕ Agregar Almacén' : '✏️ Editar Almacén' ?></h2>

      <form method="POST" class="mt-3" novalidate>
        <input type="hidden" name="accion" value="<?= $accion ?>">
        <div class="mb-3">
          <label>Nombre <span class="text-danger">*</span></label>
          <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($data['nombre'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label>Ubicación</label>
          <input type="text" name="ubicacion" class="form-control" value="<?= htmlspecialchars($data['ubicacion'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label>Responsable</label>
          <input type="text" name="responsable" class="form-control" value="<?= htmlspecialchars($data['responsable'] ?? '') ?>">
        </div>

        <button type="submit" class="btn btn-primary"><?= $accion === 'agregar' ? 'Guardar' : 'Actualizar' ?></button>
        <a href="almacenes.php?accion=listar" class="btn btn-danger ms-2">Cancelar</a>
      </form>

    <?php endif; ?>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/particles.js"></script>
<script>
  particlesJS("particles-js", {
    particles: {
      number: { value: 60, density: { enable: true, value_area: 800 } },
      color: { value: "#2575fc" },
      shape: { type: "circle" },
      opacity: { value: 0.3, random: true },
      size: { value: 4, random: true },
      line_linked: { enable: true, distance: 120, color: "#2575fc", opacity: 0.4, width: 1 },
      move: { enable: true, speed: 1.2, direction: "none", out_mode: "out" }
    },
    interactivity: {
      detect_on: "canvas",
      events: { onhover: { enable: true, mode: "repulse" }, onclick: { enable: true, mode: "push" } },
      modes: { repulse: { distance: 80 }, push: { particles_nb: 4 } }
    },
    retina_detect: true
  });
</script>

</body>
</html>
