<?php
session_start();
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Menú de Inventario</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      height: 100vh;
      overflow: hidden;
    }

    #particles-js {
      position: absolute;
      width: 100%;
      height: 100%;
      z-index: -1;
      background: linear-gradient(to right, #dfefff, #f7f9ff);
    }

    .container {
      position: relative;
      z-index: 2;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .card {
      border-radius: 1rem;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      max-width: 480px;
      width: 100%;
      padding: 2rem 2.5rem;
      background: white;
      transition: transform 0.3s ease;
    }

    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 48px rgba(0, 0, 0, 0.15);
    }

    .card h2 {
      text-align: center;
      font-weight: 700;
      margin-bottom: 2rem;
      color: #2575fc;
    }

    .list-group-item {
      font-weight: 600;
      font-size: 1.1rem;
      padding: 1rem 1.25rem;
      border: none;
      color: #495057;
      transition: background-color 0.3s ease;
    }

    .list-group-item:hover {
      background-color: #e9f0ff;
    }

    .btn-danger {
      background: #dc3545;
      border: none;
      font-weight: 600;
      padding: 0.65rem;
      letter-spacing: 0.05em;
      margin-top: 1.5rem;
      transition: background 0.3s ease;
    }

    .btn-danger:hover {
      background: #c82333;
    }
  </style>
</head>
<body>
  <div id="particles-js"></div>

  <div class="container">
    <div class="card">
      <h2>📦 Menú Principal</h2>
      <div class="list-group mb-3">
        <a href="almacenes.php" class="list-group-item list-group-item-action">🏬 Almacenes</a>
        <a href="inventario.php" class="list-group-item list-group-item-action">📋 Inventario</a>
        <a href="movimientos.php" class="list-group-item list-group-item-action">🔄 Movimientos</a>
      </div>
      <form method="POST">
        <button type="submit" name="logout" class="btn btn-danger w-100">Cerrar sesión</button>
      </form>
    </div>
  </div>

  <!-- Scripts -->
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
