<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit;
}
if (isset($_POST['logout'])) {
  session_destroy();
  header("Location: index.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Bienvenido a Xanitec</title>
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
<body class="min-h-screen flex items-center justify-center px-4 md:px-12">

  <div id="particles-js"></div>

  <main class="relative z-10 w-full bg-gray-900 bg-opacity-90 rounded-2xl shadow-2xl p-10 grid grid-cols-1 md:grid-cols-2 gap-10 items-center max-w-7xl">

    <!-- Ilustración -->
    <div class="flex justify-center">
      <img src="imagenes/undraw_data-reports_l2u3.svg" alt="Bienvenido" class="w-full max-w-md">
    </div>

    <!-- Contenido -->
    <div>
      <h1 class="text-5xl font-extrabold text-green-400 mb-6">¡Bienvenido a Xanitec!</h1>
      <p class="text-gray-300 text-lg mb-8">
        Gestiona tus almacenes, inventarios y movimientos de forma ágil, clara y profesional.
      </p>

      <div class="grid gap-4 sm:grid-cols-2">
        <a href="almacenes.php" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-xl text-center transition">
          Almacenes
        </a>
        <a href="inventario.php" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-xl text-center transition">
          Inventario
        </a>
        <a href="movimientos.php" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-xl text-center transition">
          Movimientos
        </a>
        <form method="POST">
          <button
            type="submit"
            name="logout"
            class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-xl transition"
          >
            Cerrar sesión
          </button>
        </form>
      </div>
    </div>

  </main>

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
