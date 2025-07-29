<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit;
}
require "db.php";

try {
    $stmt = $pdo->query("EXEC listar_almacenes");
    $almacenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $inventario = [];
    $filtrar_por_almacen = isset($_GET['filtrar']) && $_GET['filtrar'] === 'almacen';
    $id_almacen = $_GET['id_almacen'] ?? null;

    if ($filtrar_por_almacen && is_numeric($id_almacen)) {
        $stmt = $pdo->prepare("EXEC listar_inventario :id_almacen");
        $stmt->bindParam(':id_almacen', $id_almacen, PDO::PARAM_INT);
    } else {
        $null = null;
        $stmt = $pdo->prepare("EXEC listar_inventario :id_almacen");
        $stmt->bindParam(':id_almacen', $null, PDO::PARAM_NULL);
    }
    $stmt->execute();
    $inventario = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Inventario</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #121212;
      color: #E0E0E0;
    }
    #particles-js {
      position: fixed;
      inset: 0;
      z-index: -1;
      background: linear-gradient(135deg, #1F2937, #111827);
    }
  </style>
</head>
<body class="min-h-screen px-6 py-10">

  <div id="particles-js"></div>

  <div class="max-w-7xl mx-auto bg-gray-900 bg-opacity-90 rounded-2xl shadow-2xl p-10">

    <div class="flex flex-col md:flex-row items-center justify-between mb-8">
      <h1 class="text-4xl font-bold text-blue-400">Inventario de Productos</h1>
      <a href="success.php" class="mt-4 md:mt-0 bg-blue-700 hover:bg-blue-800 text-white px-6 py-2 rounded-lg font-semibold shadow">
        ⬅️ Volver al inicio
      </a>
    </div>

    <form method="GET" action="inventario.php" class="mb-8">
      <input type="hidden" name="filtrar" value="almacen" />
      <label for="id_almacen" class="block mb-2 font-medium text-gray-300">Filtrar por almacén:</label>
      <select name="id_almacen" id="id_almacen" onchange="this.form.submit()" class="w-full md:w-1/2 bg-gray-800 border border-gray-700 text-white rounded-lg p-2">
        <option value="">-- Selecciona un almacén --</option>
        <?php foreach ($almacenes as $almacen): ?>
          <option value="<?= htmlspecialchars($almacen['id']) ?>" <?= ($id_almacen == $almacen['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($almacen['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>

    <?php if (!empty($inventario)): ?>
      <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm bg-gray-800 text-white rounded-lg overflow-hidden">
          <thead class="bg-gray-700 text-blue-300 text-sm uppercase tracking-wider">
            <tr>
              <th class="px-6 py-3">Nombre</th>
              <th class="px-6 py-3">Descripción</th>
              <th class="px-6 py-3">Imagen</th>
              <th class="px-6 py-3">Precio</th>
              <th class="px-6 py-3">Cantidad</th>
              <th class="px-6 py-3">Almacén</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($inventario as $producto): ?>
              <tr class="border-t border-gray-700 hover:bg-gray-700">
                <td class="px-6 py-4"><?= htmlspecialchars($producto['nombre']) ?></td>
                <td class="px-6 py-4"><?= htmlspecialchars($producto['descripcion']) ?></td>
                <td class="px-6 py-4">
                  <?php
                    $rutaImagen = 'imagenes/' . $producto['imagen'];
                    if (!empty($producto['imagen']) && file_exists($rutaImagen)):
                      $ext = pathinfo($producto['imagen'], PATHINFO_EXTENSION);
                      if ($ext === 'svg'):
                  ?>
                    <object data="<?= htmlspecialchars($rutaImagen) ?>" type="image/svg+xml" class="w-16 h-16"></object>
                  <?php else: ?>
                    <img src="<?= htmlspecialchars($rutaImagen) ?>" alt="Imagen del producto" class="w-16 h-16 object-contain" />
                  <?php endif; else: ?>
                    <span class="text-gray-400">Sin imagen</span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4">$<?= number_format($producto['precio'], 2) ?></td>
                <td class="px-6 py-4"><?= (int)$producto['cantidad'] ?></td>
                <td class="px-6 py-4"><?= htmlspecialchars($producto['almacen_nombre']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="text-yellow-300 font-semibold mt-6">No hay inventario para mostrar.</div>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/particles.js"></script>
  <script>
    particlesJS("particles-js", {
      particles: {
        number: { value: 70, density: { enable: true, value_area: 900 } },
        color: { value: "#60A5FA" },
        shape: { type: "circle" },
        opacity: { value: 0.3, random: true },
        size: { value: 3, random: true },
        line_linked: { enable: true, distance: 120, color: "#3B82F6", opacity: 0.4, width: 1 },
        move: { enable: true, speed: 1.2, direction: "none", out_mode: "out" }
      },
      interactivity: {
        detect_on: "canvas",
        events: {
          onhover: { enable: true, mode: "repulse" },
          onclick: { enable: true, mode: "push" }
        },
        modes: {
          repulse: { distance: 100 },
          push: { particles_nb: 4 }
        }
      },
      retina_detect: true
    });
  </script>

</body>
</html>
