<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Login / Registro</title>
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

    .nav-tabs {
      border-bottom: none;
      justify-content: center;
      margin-bottom: 2rem;
    }

    .nav-tabs .nav-link {
      border: none;
      font-weight: 600;
      font-size: 1.1rem;
      color: #495057;
      padding: 0.75rem 1.5rem;
      border-radius: 50px;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    .nav-tabs .nav-link.active {
      background-color: #2575fc;
      color: white !important;
      box-shadow: 0 6px 12px rgba(37, 117, 252, 0.5);
    }

    label {
      font-weight: 600;
      color: #495057;
    }

    .form-control:focus {
      border-color: #2575fc;
      box-shadow: 0 0 8px rgba(37, 117, 252, 0.3);
    }

    .btn-primary {
      background: #2575fc;
      border: none;
      font-weight: 600;
      padding: 0.65rem;
      letter-spacing: 0.05em;
      transition: background 0.3s ease;
    }

    .btn-primary:hover {
      background: #1a5edb;
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

    a {
      color: #2575fc;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.2s ease;
    }

    a:hover {
      color: #1a5edb;
      text-decoration: underline;
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
      <ul class="nav nav-tabs" id="formTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab" aria-controls="login" aria-selected="true">Iniciar Sesión</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab" aria-controls="register" aria-selected="false">Registrarse</button>
        </li>
      </ul>

      <div class="tab-content" id="formTabsContent">
        <!-- LOGIN -->
        <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab" tabindex="0">
          <form action="login.php" method="POST" novalidate>
            <div class="mb-4">
              <label for="loginEmail" class="form-label">Correo electrónico</label>
              <input type="email" class="form-control form-control-lg" id="loginEmail" name="email" placeholder="usuario@ejemplo.com" required autofocus />
            </div>
            <div class="mb-4">
              <label for="loginPassword" class="form-label">Contraseña</label>
              <input type="password" class="form-control form-control-lg" id="loginPassword" name="password" placeholder="••••••••" required />
            </div>
            <div class="d-flex justify-content-end mb-3">
              <a href="forgot_password.php">¿Olvidaste tu contraseña?</a>
            </div>
            <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
          </form>
        </div>

        <!-- REGISTRO -->
        <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab" tabindex="0">
          <form action="register.php" method="POST" novalidate>
            <div class="mb-4">
              <label for="registerEmail" class="form-label">Correo electrónico</label>
              <input type="email" class="form-control form-control-lg" id="registerEmail" name="email" placeholder="usuario@ejemplo.com" required />
            </div>
            <div class="mb-4">
              <label for="registerPassword" class="form-label">Contraseña</label>
              <input type="password" class="form-control form-control-lg" id="registerPassword" name="password" placeholder="••••••••" required />
            </div>
            <div class="mb-4">
              <label for="registerConfirm" class="form-label">Confirmar Contraseña</label>
              <input type="password" class="form-control form-control-lg" id="registerConfirm" name="confirm_password" placeholder="••••••••" required />
            </div>
            <button type="submit" class="btn btn-success w-100">Registrarse</button>
          </form>
        </div>
      </div>
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
