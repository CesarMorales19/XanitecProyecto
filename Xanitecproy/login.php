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
  <title>Login - Xanitec</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center font-sans">

  <div class="bg-white shadow-2xl rounded-2xl p-10 w-full max-w-2xl">
    <h1 class="text-3xl font-bold text-center text-blue-700 mb-2">Bienvenido a Xanitec Almacenes</h1>
    <p class="text-center text-gray-600 mb-8">Por favor, inicia sesión para continuar</p>

    <?php if (!empty($mensaje)) : ?>
      <div class="mb-4 text-sm text-red-600 bg-red-100 p-3 rounded">
        <?= $mensaje ?>
      </div>
    <?php endif; ?>

    <form action="login.php" method="POST" class="space-y-6 max-w-md mx-auto">
      <div>
        <label class="block text-sm text-gray-700 mb-1">Correo electrónico</label>
        <input type="email" name="email" required
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
      </div>

      <div>
        <label class="block text-sm text-gray-700 mb-1">Contraseña</label>
        <input type="password" name="password" required
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
      </div>

      <button type="submit"
              class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
        Iniciar Sesión
      </button>
    </form>

    <div class="mt-6 text-center text-sm text-gray-600">
      ¿No tienes cuenta?
      <a href="register.php" class="text-blue-600 hover:underline">Regístrate aquí</a>
    </div>
  </div>

</body>
</html>
