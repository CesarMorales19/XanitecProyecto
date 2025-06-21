<?php
require 'db.php'; 

$email = $_POST['email'] ?? '';

// Verificar que el email exista en la base de datos
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user) {
    // Crear token seguro
    $token = bin2hex(random_bytes(16));
    $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

    // Guardar token y expiración en BD asociados al usuario
    $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE email = ?");
    $stmt->execute([$token, $expiry, $email]);

    // Enviar correo con enlace
    $reset_link = "https://tusitio.com/reset-password.php?token=$token";

    $subject = "Recupera tu contraseña";
    $message = "Para resetear tu contraseña, visita este enlace: $reset_link";

    mail($email, $subject, $message);

    echo "Revisa tu correo para continuar.";
} else {
    echo "No se encontró usuario con ese correo.";
}
?>
