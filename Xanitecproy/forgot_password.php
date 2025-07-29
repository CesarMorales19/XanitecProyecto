<?php
require 'db.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarEmailRecuperacion($email, $reset_link) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Username = '197f3332fc71f8';
        $mail->Password = 'b912c5a3a5023a';
        $mail->Port = 2525;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        $mail->setFrom('al222311022@gmail.com', 'XanitecProy');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Recupera tu contraseña';
        $mail->Body = "
            <p>Hola,</p>
            <p>Haz clic en el siguiente enlace para restablecer tu contraseña:</p>
            <p><a href='$reset_link'>$reset_link</a></p>
            <p>Este enlace expirará en 1 hora.</p>
            <p>Si no solicitaste este cambio, ignora este correo.</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error al enviar correo: " . $mail->ErrorInfo);
        return false;
    }
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';

    if (!$email) {
        $message = "Por favor ingresa tu correo.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $token = bin2hex(random_bytes(16));
            $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE email = ?");
            $stmt->execute([$token, $expiry, $email]);
            $reset_link = "http://localhost/XanitecProy/reset-password.php?token=$token";

            $message = enviarEmailRecuperacion($email, $reset_link)
                ? "✅ Revisa tu correo para recuperar tu contraseña."
                : "⚠️ Error al enviar el correo. Intenta más tarde.";
        } else {
            $message = "❌ No existe una cuenta con ese correo.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Recuperar contraseña</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/particles.js"></script>
  <style>
    body {
      background-color: #0d1b2a;
    }
    #particles-js {
      position: absolute;
      width: 100%;
      height: 100%;
      z-index: -1;
    }
  </style>
</head>
<body class="text-white relative min-h-screen overflow-hidden">
  <div id="particles-js"></div>

  <div class="flex flex-col md:flex-row justify-center items-center min-h-screen px-6 py-10 relative z-10">
    <!-- Formulario -->
    <div class="bg-white text-gray-800 rounded-2xl shadow-2xl p-8 w-full max-w-md md:mr-12">
      <h2 class="text-2xl font-bold text-blue-600 mb-4 text-center">¿Olvidaste tu contraseña?</h2>

      <?php if ($message): ?>
        <div class="bg-blue-100 text-blue-700 px-4 py-2 rounded mb-4 text-sm font-semibold shadow">
          <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>

      <form method="POST" class="space-y-4">
        <div>
          <label for="email" class="block font-medium text-sm text-gray-700">Correo electrónico</label>
          <input type="email" id="email" name="email" required
            class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="usuario@ejemplo.com">
        </div>
        <button type="submit"
          class="w-full bg-blue-600 hover:bg-blue-700 transition text-white font-semibold py-2 rounded-md">
          Enviar enlace de recuperación
        </button>
        <a href="index.php" class="block text-center text-sm text-gray-600 hover:underline">← Volver al inicio</a>
      </form>
    </div>

    <!-- Imagen SVG -->
    <div class="hidden md:block max-w-md w-full mt-10 md:mt-0">
      <img src="imagenes/undraw_forgot-password_nttj.svg" alt="Recuperar contraseña" class="w-full h-auto">
    </div>
  </div>

  <script>
    particlesJS("particles-js", {
      particles: {
        number: { value: 50, density: { enable: true, value_area: 800 } },
        color: { value: "#ffffff" },
        shape: { type: "circle" },
        opacity: { value: 0.15, random: true },
        size: { value: 4, random: true },
        move: { enable: true, speed: 1, direction: "none", out_mode: "out" }
      },
      interactivity: {
        detect_on: "canvas",
        events: { onhover: { enable: false }, onclick: { enable: false } }
      },
      retina_detect: true
    });
  </script>
</body>
</html>
