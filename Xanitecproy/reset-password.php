<?php
require 'db.php';
$message = "";
$showForm = false;

// Obtener token de la URL
$token = $_GET['token'] ?? '';

if (!$token) {
    die("Token no válido.");
}

// Verificar token en la base de datos
$stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expiry > GETDATE()");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    die("Token inválido o expirado.");
}

$showForm = true;

// Procesar formulario al enviar nueva contraseña
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$password || !$confirm) {
        $message = "⚠ Por favor completa ambos campos.";
    } elseif ($password !== $confirm) {
        $message = "❌ Las contraseñas no coinciden.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
        $stmt->execute([$hash, $user['id']]);

        $message = "✅ Contraseña actualizada con éxito.";
        $showForm = false;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Restablecer contraseña</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    #particles-js {
      position: fixed;
      width: 100%;
      height: 100%;
      z-index: -1;
      background: linear-gradient(to right, #0d1b2a, #1b263b);
    }
  </style>
</head>
<body class="font-sans text-white">
  <div id="particles-js"></div>

  <div class="flex items-center justify-center min-h-screen px-4">
    <div class="bg-gray-900 bg-opacity-90 rounded-2xl shadow-2xl p-8 w-full max-w-md">
      <h2 class="text-2xl font-bold text-center text-blue-400 mb-6">🔑 Restablecer Contraseña</h2>

      <?php if ($message): ?>
        <div class="bg-blue-800 text-white px-4 py-2 rounded mb-4 text-sm text-center">
          <?php echo $message; ?>
        </div>
      <?php endif; ?>

      <?php if ($showForm): ?>
      <form method="POST" class="space-y-4">
        <div>
          <label class="block text-sm font-medium">Nueva contraseña</label>
          <input type="password" name="password" class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white" required>
        </div>
        <div>
          <label class="block text-sm font-medium">Confirmar contraseña</label>
          <input type="password" name="confirm_password" class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white" required>
        </div>
        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 py-2 rounded text-lg">Actualizar Contraseña</button>
      </form>
      <?php endif; ?>

      <!-- Botón para volver siempre -->
      <div class="mt-6 text-center">
        <a href="index.php" class="inline-block bg-gray-700 hover:bg-gray-800 px-4 py-2 rounded text-white text-sm transition">← Volver al inicio de sesión</a>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/particles.js"></script>
  <script>
    particlesJS("particles-js", {
      particles: {
        number: { value: 60, density: { enable: true, value_area: 800 } },
        color: { value: "#ffffff" },
        shape: { type: "circle" },
        opacity: { value: 0.3, random: true },
        size: { value: 4, random: true },
        line_linked: {
          enable: true,
          distance: 120,
          color: "#ffffff",
          opacity: 0.4,
          width: 1
        },
        move: { enable: true, speed: 1.2, direction: "none", out_mode: "out" }
      },
      interactivity: {
        detect_on: "canvas",
        events: {
          onhover: { enable: true, mode: "repulse" },
          onclick: { enable: true, mode: "push" }
        },
        modes: {
          repulse: { distance: 80 },
          push: { particles_nb: 4 }
        }
      },
      retina_detect: true
    });
  </script>
</body>
</html>
