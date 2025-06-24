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
        $message = "Por favor completa ambos campos.";
    } elseif ($password !== $confirm) {
        $message = "Las contraseñas no coinciden.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
        $stmt->execute([$hash, $user['id']]);

        $message = "✅ Contraseña actualizada con éxito. Puedes <a href='login.php' class='text-success fw-bold'>iniciar sesión</a> ahora.";
        $showForm = false;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Resetear contraseña</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      overflow: hidden;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    #particles-js {
      position: fixed;
      width: 100%;
      height: 100%;
      z-index: -1;
      background: linear-gradient(to right, #0d1b2a, #1b263b);
    }

    .card {
      position: relative;
      z-index: 2;
      border-radius: 1rem;
      min-width: 350px;
      padding: 30px;
      background-color: #212529;
      color: white;
      box-shadow: 0 0 20px rgba(0,0,0,0.3);
      margin: auto;
      top: 20vh;
    }

    label {
      font-weight: 600;
    }

    .btn-primary {
      background: #2575fc;
      border: none;
      font-weight: 600;
    }

    .btn-primary:hover {
      background: #1a5edb;
    }

    .alert {
      font-size: 0.95rem;
    }

    .container {
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
      z-index: 2;
    }
  </style>
</head>
<body>

  <div id="particles-js"></div>

  <div class="container">
    <div class="card">
      <h4 class="mb-3">Restablecer contraseña</h4>

      <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
      <?php endif; ?>

      <?php if ($showForm): ?>
      <form method="POST">
        <div class="mb-3">
          <label for="password" class="form-label">Nueva contraseña</label>
          <input type="password" class="form-control" id="password" name="password" required />
        </div>
        <div class="mb-3">
          <label for="confirm_password" class="form-label">Confirmar contraseña</label>
          <input type="password" class="form-control" id="confirm_password" name="confirm_password" required />
        </div>
        <button type="submit" class="btn btn-primary w-100">Actualizar contraseña</button>
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
