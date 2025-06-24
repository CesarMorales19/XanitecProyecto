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
        $mail->Subject = 'Recupera tu contrase\u00f1a';
        $mail->Body = "
            <p>Hola,</p>
            <p>Haz clic en el siguiente enlace para restablecer tu contrase\u00f1a:</p>
            <p><a href='$reset_link'>$reset_link</a></p>
            <p>Este enlace expirar\u00e1 en 1 hora.</p>
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

            if (enviarEmailRecuperacion($email, $reset_link)) {
                $message = "✅ Revisa tu correo para recuperar tu contraseña.";
            } else {
                $message = "⚠️ Error al enviar el correo. Intenta m\u00e1s tarde.";
            }
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body, html {
      height: 100%;
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #0d1b2a;
      overflow: hidden;
      position: relative;
    }

    .particles {
      position: fixed;
      width: 100%;
      height: 100%;
      z-index: 0;
      top: 0;
      left: 0;
      pointer-events: none;
    }

    .particle {
      position: absolute;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.3);
      opacity: 0.8;
      animation-timing-function: linear;
      animation-iteration-count: infinite;
    }

    @keyframes float1 {
      0% { transform: translate(0, 0) scale(1);}
      50% { transform: translate(30px, -20px) scale(1.2);}
      100% { transform: translate(0, 0) scale(1);}
    }

    @keyframes float2 {
      0% { transform: translate(0, 0) scale(1);}
      50% { transform: translate(-20px, 25px) scale(0.8);}
      100% { transform: translate(0, 0) scale(1);}
    }

    @keyframes float3 {
      0% { transform: translate(0, 0) scale(1);}
      50% { transform: translate(25px, 20px) scale(1.1);}
      100% { transform: translate(0, 0) scale(1);}
    }

    .particle:nth-child(1) {
      width: 12px; height: 12px;
      top: 20%; left: 10%;
      animation: float1 7s infinite;
    }
    .particle:nth-child(2) {
      width: 8px; height: 8px;
      top: 40%; left: 25%;
      animation: float2 6s infinite;
    }
    .particle:nth-child(3) {
      width: 10px; height: 10px;
      top: 65%; left: 15%;
      animation: float3 8s infinite;
    }
    .particle:nth-child(4) {
      width: 14px; height: 14px;
      top: 80%; left: 35%;
      animation: float1 9s infinite;
    }
    .particle:nth-child(5) {
      width: 9px; height: 9px;
      top: 30%; left: 50%;
      animation: float2 7.5s infinite;
    }
    .particle:nth-child(6) {
      width: 7px; height: 7px;
      top: 55%; left: 70%;
      animation: float3 6.5s infinite;
    }
    .particle:nth-child(7) {
      width: 11px; height: 11px;
      top: 75%; left: 80%;
      animation: float1 8.5s infinite;
    }
    .particle:nth-child(8) {
      width: 10px; height: 10px;
      top: 45%; left: 85%;
      animation: float2 7.8s infinite;
    }

    .card {
      max-width: 420px;
      width: 100%;
      background: white;
      border-radius: 1rem;
      padding: 2.5rem 2rem;
      box-shadow: 0 10px 30px rgba(37, 117, 252, 0.15);
      text-align: center;
      margin: auto;
      z-index: 10;
      position: relative;
      top: 20vh;
    }

    h4 {
      margin-bottom: 1.8rem;
      color: #2575fc;
      font-weight: 700;
    }
    label {
      font-weight: 600;
      color: #495057;
    }
    .form-control {
      border-radius: 0.5rem;
      padding: 0.75rem 1rem;
      border: 1.5px solid #ccc;
    }
    .form-control:focus {
      border-color: #2575fc;
      box-shadow: 0 0 8px rgba(37, 117, 252, 0.4);
      outline: none;
    }
    .btn-primary {
      background: #2575fc;
      border: none;
      font-weight: 600;
      padding: 0.75rem;
      border-radius: 0.7rem;
      width: 100%;
      letter-spacing: 0.05em;
      margin-top: 1rem;
    }
    .btn-primary:hover {
      background: #1a5edb;
    }
    .alert {
      font-weight: 600;
      border-radius: 0.6rem;
    }
    .btn-outline-secondary {
      margin-top: 0.5rem;
      border-radius: 0.7rem;
      font-weight: 600;
      padding: 0.75rem;
      letter-spacing: 0.05em;
    }
  </style>
</head>
<body>
  <div class="particles" aria-hidden="true">
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
  </div>

  <div class="card shadow-sm">
    <h4>¿Olvidaste tu contraseña?</h4>

    <?php if ($message): ?>
      <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="mb-4 text-start">
        <label for="email" class="form-label">Correo electrónico</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="usuario@ejemplo.com" required autofocus />
      </div>
      <button type="submit" class="btn btn-primary">Enviar enlace de recuperación</button>
    </form>

    <a href="index.php" class="btn btn-outline-secondary">← Volver al inicio</a>
  </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
