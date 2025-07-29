<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
require_once 'db.php';

$accion = $_GET['accion'] ?? 'listar';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$mensaje = '';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $post_accion = $_POST['accion'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $ubicacion = $_POST['ubicacion'] ?? '';
        $responsable = $_POST['responsable'] ?? '';
        $usuario = $_SESSION['user_id'];

        if ($post_accion === 'agregar') {
            $stmt = $pdo->prepare("EXEC agregar_almacen :nombre, :ubicacion, :responsable, :usuario");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':ubicacion', $ubicacion);
            $stmt->bindParam(':responsable', $responsable);
            $stmt->bindParam(':usuario', $usuario);
            if ($stmt->execute()) {
                header("Location: almacenes.php?accion=listar&msg=agregado");
                exit;
            } else {
                $mensaje = "Error al agregar almacén.";
            }
        } elseif ($post_accion === 'editar' && $id > 0) {
            $stmt = $pdo->prepare("EXEC actualizar_almacen :id, :nombre, :ubicacion, :responsable, :usuario");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':ubicacion', $ubicacion);
            $stmt->bindParam(':responsable', $responsable);
            $stmt->bindParam(':usuario', $usuario);
            if ($stmt->execute()) {
                header("Location: almacenes.php?accion=listar&msg=actualizado");
                exit;
            } else {
                $mensaje = "Error al actualizar almacén.";
            }
        }
    }
} catch (PDOException $e) {
    $mensaje = "Error de base de datos: " . $e->getMessage();
}

if ($accion === 'eliminar' && $id > 0) {
    try {
        $usuario = $_SESSION['user_id'];
        $stmt = $pdo->prepare("EXEC eliminar_almacen :id, :usuario");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
        header("Location: almacenes.php?accion=listar&msg=eliminado");
        exit;
    } catch (PDOException $e) {
        $mensaje = "Error al eliminar almacén: " . $e->getMessage();
    }
}

$msg = $_GET['msg'] ?? '';
$msgTexto = match ($msg) {
    'agregado' => '✅ Almacén agregado exitosamente.',
    'actualizado' => '✅ Almacén actualizado exitosamente.',
    'eliminado' => '🗑️ Almacén eliminado exitosamente.',
    default => ''
};

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
  <meta charset="UTF-8">
  <title>📦 Gestión de Almacenes</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen px-4 py-6 relative overflow-x-hidden">

  <!-- Fondo de partículas -->
  <div id="particles-js" class="fixed inset-0 -z-10"></div>

  <div class="max-w-7xl mx-auto space-y-6 relative z-10">
    <div class="flex justify-between items-center">
      <h1 class="text-3xl font-bold text-blue-400">📦 Gestión de Almacenes</h1>
      <a href="success.php" class="text-blue-500 border border-blue-500 hover:bg-blue-500 hover:text-white transition px-4 py-2 rounded-lg">⬅️ Inicio</a>
    </div>

    <?php if ($mensaje): ?>
      <div class="bg-red-600 text-white font-semibold px-4 py-2 rounded-lg"><?= htmlspecialchars($mensaje) ?></div>
    <?php elseif ($msgTexto): ?>
      <div class="bg-green-600 text-white font-semibold px-4 py-2 rounded-lg"><?= htmlspecialchars($msgTexto) ?></div>
    <?php endif; ?>

    <?php if ($accion === 'listar'): ?>
      <?php $stmt = $pdo->query("SELECT * FROM almacenes ORDER BY id DESC"); $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); ?>
      <div class="flex justify-end">
        <a href="almacenes.php?accion=agregar" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg">➕ Agregar Almacén</a>
      </div>
      <div class="overflow-x-auto mt-4">
        <table class="w-full table-auto border-collapse bg-gray-800 rounded-xl overflow-hidden">
          <thead>
            <tr class="bg-blue-900 text-blue-100">
              <th class="px-4 py-2">ID</th>
              <th class="px-4 py-2">Nombre</th>
              <th class="px-4 py-2">Ubicación</th>
              <th class="px-4 py-2">Responsable</th>
              <th class="px-4 py-2">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($resultado): ?>
              <?php foreach ($resultado as $row): ?>
                <tr class="border-t border-gray-700 hover:bg-gray-700/50">
                  <td class="px-4 py-2"><?= $row['id'] ?></td>
                  <td class="px-4 py-2"><?= htmlspecialchars($row['nombre']) ?></td>
                  <td class="px-4 py-2"><?= htmlspecialchars($row['ubicacion']) ?></td>
                  <td class="px-4 py-2"><?= htmlspecialchars($row['responsable']) ?></td>
                  <td class="px-4 py-2 space-x-2">
                    <a href="almacenes.php?accion=ver&id=<?= $row['id'] ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-sm">Ver</a>
                    <a href="almacenes.php?accion=editar&id=<?= $row['id'] ?>" class="bg-yellow-500 hover:bg-yellow-600 text-black px-2 py-1 rounded text-sm">Editar</a>
                    <a href="almacenes.php?accion=eliminar&id=<?= $row['id'] ?>" onclick="return confirm('¿Eliminar almacén?')" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-sm">Eliminar</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="5" class="text-center py-4">No hay almacenes registrados.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    <?php elseif ($accion === 'ver'): ?>
      <h2 class="text-2xl font-semibold text-blue-300">Detalles del Almacén</h2>
      <ul class="bg-gray-800 p-6 rounded-xl space-y-2">
        <li><strong>ID:</strong> <?= $id ?></li>
        <li><strong>Nombre:</strong> <?= htmlspecialchars($data['nombre']) ?></li>
        <li><strong>Ubicación:</strong> <?= htmlspecialchars($data['ubicacion']) ?></li>
        <li><strong>Responsable:</strong> <?= htmlspecialchars($data['responsable']) ?></li>
      </ul>
      <div class="mt-4 flex gap-3">
        <a href="almacenes.php?accion=listar" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">⬅️ Volver</a>
        <a href="almacenes.php?accion=editar&id=<?= $id ?>" class="bg-yellow-500 hover:bg-yellow-600 text-black px-4 py-2 rounded-lg">✏️ Editar</a>
      </div>

    <?php elseif ($accion === 'agregar' || $accion === 'editar'): ?>
      <h2 class="text-2xl font-semibold text-blue-300"><?= $accion === 'agregar' ? '➕ Agregar Almacén' : '✏️ Editar Almacén' ?></h2>
      <form method="POST" class="space-y-4 mt-4">
        <input type="hidden" name="accion" value="<?= $accion ?>">
        <div>
          <label class="block mb-1">Nombre <span class="text-red-400">*</span></label>
          <input type="text" name="nombre" required value="<?= htmlspecialchars($data['nombre'] ?? '') ?>" class="w-full px-4 py-2 rounded bg-gray-700 text-white border border-gray-600 focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block mb-1">Ubicación</label>
          <input type="text" name="ubicacion" value="<?= htmlspecialchars($data['ubicacion'] ?? '') ?>" class="w-full px-4 py-2 rounded bg-gray-700 text-white border border-gray-600 focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block mb-1">Responsable</label>
          <input type="text" name="responsable" value="<?= htmlspecialchars($data['responsable'] ?? '') ?>" class="w-full px-4 py-2 rounded bg-gray-700 text-white border border-gray-600 focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex gap-3">
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
            <?= $accion === 'agregar' ? 'Guardar' : 'Actualizar' ?>
          </button>
          <a href="almacenes.php?accion=listar" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold">Cancelar</a>
        </div>
      </form>
    <?php endif; ?>
  </div>

  <!-- Scripts para particles.js -->
  <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
  <script>
    particlesJS('particles-js', {
      particles: {
        number: { value: 70, density: { enable: true, value_area: 800 } },
        color: { value: "#00bfff" },
        shape: { type: "circle" },
        opacity: { value: 0.5 },
        size: { value: 4 },
        line_linked: { enable: true, distance: 150, color: "#00bfff", opacity: 0.4, width: 1 },
        move: { enable: true, speed: 2 }
      },
      interactivity: {
        events: {
          onhover: { enable: true, mode: "grab" },
          onclick: { enable: true, mode: "push" }
        },
        modes: {
          grab: { distance: 140, line_linked: { opacity: 0.6 } },
          push: { particles_nb: 4 }
        }
      },
      retina_detect: true
    });
  </script>
</body>
</html>
