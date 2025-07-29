<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Xanitec | Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/particles.js"></script>
  <style>
    body {
      background-color: #0f172a; /* gray-900 */
      color: white;
    }
    .bg-panel {
      background-color: #1e293b; /* gray-800 */
    }
    .text-primary {
      color: #3b82f6; /* blue-500 */
    }
  </style>
</head>
<body class="h-screen overflow-hidden relative">

  <!-- Partículas de fondo -->
  <div id="particles-js" class="absolute inset-0 -z-10"></div>

  <!-- Contenido principal -->
  <div class="flex h-full items-center justify-center">
    <div class="bg-panel rounded-xl shadow-2xl w-full max-w-5xl flex flex-col md:flex-row overflow-hidden border border-gray-700">

      <!-- Ilustración lateral -->
      <div class="w-full md:w-1/2 bg-gray-900 p-10 hidden md:flex items-center justify-center">
        <img src="imagenes/undraw_logistics_xpdj.svg" alt="Logística" class="w-80" />
      </div>

      <!-- Formulario de autenticación -->
      <div class="w-full md:w-1/2 p-8 md:p-10">
        <h2 class="text-2xl font-bold text-center text-primary mb-6">Bienvenido a Xanitec Almacenes</h2>
        <div class="flex justify-center mb-6">
          <button id="loginTab" class="px-6 py-2 font-semibold text-white bg-blue-600 rounded-l-full hover:bg-blue-700 transition">Iniciar Sesión</button>
          <button id="registerTab" class="px-6 py-2 font-semibold text-blue-600 bg-white border border-blue-600 rounded-r-full hover:bg-blue-50 transition">Registrarse</button>
        </div>

        <!-- Formulario de login -->
        <div id="loginForm">
          <form action="login.php" method="POST" class="space-y-4">
            <div>
              <label class="block font-medium text-white">Correo electrónico</label>
              <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-600 rounded-md bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
              <label class="block font-medium text-white">Contraseña</label>
              <input type="password" name="password" required class="w-full px-4 py-2 border border-gray-600 rounded-md bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex justify-end">
              <a href="forgot_password.php" class="text-sm text-blue-400 hover:underline">¿Olvidaste tu contraseña?</a>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">Iniciar Sesión</button>
          </form>
        </div>

        <!-- Formulario de registro -->
        <div id="registerForm" class="hidden">
          <form action="register.php" method="POST" class="space-y-4">
            <div>
              <label class="block font-medium text-white">Correo electrónico</label>
              <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-600 rounded-md bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
              <label class="block font-medium text-white">Contraseña</label>
              <input type="password" name="password" required class="w-full px-4 py-2 border border-gray-600 rounded-md bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
              <label class="block font-medium text-white">Confirmar Contraseña</label>
              <input type="password" name="confirm_password" required class="w-full px-4 py-2 border border-gray-600 rounded-md bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition">Registrarse</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script>
    // Partículas
    particlesJS("particles-js", {
      particles: {
        number: { value: 60, density: { enable: true, value_area: 800 } },
        color: { value: "#3b82f6" },
        shape: { type: "circle" },
        opacity: { value: 0.3, random: true },
        size: { value: 4, random: true },
        line_linked: { enable: true, distance: 120, color: "#3b82f6", opacity: 0.4, width: 1 },
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

    // Tabs lógica
    const loginTab = document.getElementById("loginTab");
    const registerTab = document.getElementById("registerTab");
    const loginForm = document.getElementById("loginForm");
    const registerForm = document.getElementById("registerForm");

    loginTab.addEventListener("click", () => {
      loginForm.classList.remove("hidden");
      registerForm.classList.add("hidden");
      loginTab.classList.add("bg-blue-600", "text-white");
      loginTab.classList.remove("bg-white", "text-blue-600");
      registerTab.classList.remove("bg-blue-600", "text-white");
      registerTab.classList.add("bg-white", "text-blue-600");
    });

    registerTab.addEventListener("click", () => {
      loginForm.classList.add("hidden");
      registerForm.classList.remove("hidden");
      registerTab.classList.add("bg-blue-600", "text-white");
      registerTab.classList.remove("bg-white", "text-blue-600");
      loginTab.classList.remove("bg-blue-600", "text-white");
      loginTab.classList.add("bg-white", "text-blue-600");
    });
  </script>
</body>
</html>
