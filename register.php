<?php
require 'db.php';

$mensaje = "";
$claseAlerta = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];
  $confirm = $_POST['confirm_password'];

  if ($password !== $confirm) {
    $mensaje = "❌ Las contraseñas no coinciden.";
    $claseAlerta = "danger";
  } else {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
      $mensaje = "⚠️ Ya existe un usuario con ese correo.";
      $claseAlerta = "warning";
    } else {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
      $stmt->execute([$email, $hash]);
      $mensaje = "✅ Registro exitoso.";
      $claseAlerta = "success";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Registro</title>
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

    .card {
      border-radius: 1rem;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      max-width: 480px;
      width: 100%;
      padding: 2rem 2.5rem;
      background: white;
      transition: transform 0.3s ease;
      position: relative;
      z-index: 2;
    }

    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 48px rgba(0, 0, 0, 0.15);
    }

    label {
      font-weight: 600;
      color: #495057;
    }

    .form-control:focus {
      border-color: #2575fc;
      box-shadow: 0 0 8px rgba(37, 117, 252, 0.3);
    }

    .btn-success {
      background: #28a745;
      border: none;
      font-weight: 600;
      padding: 0.65rem;
      letter-spacing: 0.05em;
      transition: background 0.3s ease;
    }

    .btn-success:hover {
      background: #218838;
    }

    .alert {
      font-weight: 600;
    }

    .container {
      position: relative;
      z-index: 2;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
  </style>
</head>
<body>

<div id="particles-js"></div>

<div class="container">
  <div class="card">
    <h4 class="mb-4">Crear cuenta</h4>

    <?php if ($mensaje): ?>
      <div class="alert alert-<?php echo $claseAlerta; ?> alert-dismissible fade show" role="alert">
        <?php echo $mensaje; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="mb-4">
        <label for="email" class="form-label">Correo electrónico</label>
        <input type="email" class="form-control form-control-lg" id="email" name="email" required />
      </div>
      <div class="mb-4">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" class="form-control form-control-lg" id="password" name="password" required />
      </div>
      <div class="mb-4">
        <label for="confirm_password" class="form-label">Confirmar contraseña</label>
        <input type="password" class="form-control form-control-lg" id="confirm_password" name="confirm_password" required />
      </div>
      <button type="submit" class="btn btn-success w-100">Registrarse</button>
      <a href="index.php" class="btn btn-secondary w-100 mt-3">Volver al inicio</a>
    </form>
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
