<?php
session_start();
require 'db.php';

$mensaje = "";
$claseAlerta = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    header("Location: success.php");
    exit;
  } else {
    $mensaje = "❌ Correo o contraseña incorrectos.";
    $claseAlerta = "danger";
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Login - XanitecProy</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
  body, html {
    height: 100%;
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    overflow: hidden;
    position: relative;
  }

  #particles-js {
    position: fixed;
    width: 100%;
    height: 100%;
    z-index: -1;
    background: linear-gradient(to right, #0d1b2a, #1b263b);
  }

  .login-card {
    position: relative;
    z-index: 2;
    background: white;
    padding: 2.5rem 2rem;
    border-radius: 1rem;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    width: 100%;
    max-width: 400px;
    margin: auto;
    top: 20vh;
  }

  .login-card h2 {
    color: #343a40;
    font-weight: 700;
    margin-bottom: 1.5rem;
    text-align: center;
    letter-spacing: 1.1px;
  }

  label {
    font-weight: 600;
    color: #495057;
  }

  .btn-primary {
    background: #2575fc;
    border: none;
    font-weight: 600;
    padding: 0.6rem;
    letter-spacing: 0.05em;
    transition: background 0.3s ease;
  }

  .btn-primary:hover {
    background: #1a5edb;
  }

  .btn-link-custom {
    display: block;
    margin-top: 1rem;
    text-align: center;
    font-weight: 600;
    color: #2575fc;
    cursor: pointer;
    text-decoration: none;
  }
  .btn-link-custom:hover {
    color: #1a5edb;
    text-decoration: underline;
  }

  .alert {
    font-weight: 600;
    font-size: 0.95rem;
  }
</style>
</head>
<body>

  <div id="particles-js"></div>

  <div class="login-card">
    <h2>Iniciar sesión</h2>

    <?php if ($mensaje): ?>
      <div class="alert alert-<?php echo htmlspecialchars($claseAlerta); ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($mensaje); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
      </div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="mb-4">
        <label for="email" class="form-label">Correo electrónico</label>
        <input
          type="email"
          class="form-control form-control-lg"
          id="email"
          name="email"
          placeholder="usuario@ejemplo.com"
          required
          autofocus
        />
      </div>

      <div class="mb-4">
        <label for="password" class="form-label">Contraseña</label>
        <input
          type="password"
          class="form-control form-control-lg"
          id="password"
          name="password"
          placeholder="••••••••"
          required
        />
      </div>

      <button type="submit" class="btn btn-primary w-100">Ingresar</button>
    </form>

    <a href="forgot_password.php" class="btn-link-custom">¿Olvidaste tu contraseña?</a>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/particles.js"></script>
<script>
particlesJS("particles-js", {
  particles: {
    number: { value: 60, density: { enable: true, value_area: 800 } },
    color: { value: "#ffffff" },
    shape: { type: "circle" },
    opacity: { value: 0.25, random: true },
    size: { value: 3.5, random: true },
    line_linked: {
      enable: true,
      distance: 120,
      color: "#ffffff",
      opacity: 0.3,
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
