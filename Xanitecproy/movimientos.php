<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->query("EXEC listar_movimientos");
$movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Movimientos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #111827;
      color: #E5E7EB;
    }
    #particles-js {
      position: fixed;
      inset: 0;
      z-index: -1;
    }
  </style>
</head>
<body class="min-h-screen px-6 py-10">

  <div id="particles-js"></div>

  <div class="max-w-6xl mx-auto bg-gray-900 bg-opacity-90 rounded-2xl shadow-2xl p-10">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-3xl md:text-4xl font-bold text-blue-400">📋 Historial de Movimientos</h1>
      <a href="almacenes.php" class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2 rounded-lg font-semibold">
        ⬅️ Volver
      </a>
    </div>

    <?php if (count($movimientos) > 0): ?>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-white rounded-lg overflow-hidden">
          <thead class="bg-gray-700 text-blue-300 uppercase text-xs tracking-wider">
            <tr>
              <th class="px-6 py-3 text-left">Fecha</th>
              <th class="px-6 py-3 text-left">Usuario</th>
              <th class="px-6 py-3 text-left">Acción</th>
              <th class="px-6 py-3 text-left">Módulo</th>
              <th class="px-6 py-3 text-left">Descripción</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($movimientos as $mov): ?>
              <tr class="border-t border-gray-700 hover:bg-gray-800">
                <td class="px-6 py-3"><?= date("d/m/Y H:i", strtotime($mov['fecha_movimiento'])) ?></td>
                <td class="px-6 py-3"><?= htmlspecialchars($mov['usuario']) ?></td>
                <td class="px-6 py-3">
                  <span class="px-2 py-1 rounded-full text-xs font-semibold
                    <?= $mov['accion'] === 'crear' ? 'bg-green-500/20 text-green-400' : (
                         $mov['accion'] === 'editar' ? 'bg-yellow-400/20 text-yellow-300' : 'bg-red-500/20 text-red-400') ?>">
                    <?= ucfirst($mov['accion']) ?>
                  </span>
                </td>
                <td class="px-6 py-3"><?= ucfirst($mov['modulo']) ?></td>
                <td class="px-6 py-3"><?= htmlspecialchars($mov['descripcion']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="text-yellow-400 font-semibold mt-4">No hay movimientos registrados.</div>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/particles.js"></script>
  <script>
    particlesJS("particles-js", {
      particles: {
        number: { value: 60, density: { enable: true, value_area: 900 } },
        color: { value: "#60A5FA" },
        shape: { type: "circle" },
        opacity: { value: 0.3, random: true },
        size: { value: 3, random: true },
        line_linked: { enable: true, distance: 120, color: "#3B82F6", opacity: 0.4, width: 1 },
        move: { enable: true, speed: 1.1, direction: "none", out_mode: "out" }
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
