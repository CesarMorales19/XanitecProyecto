<?php
$host = 'localhost\\sqlexpress';
$dbname = 'xanitec';
$username = '';
$password = '';

try {
    $pdo = new PDO("sqlsrv:Server=$host;Database=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Conexión exitosa a SQL Server.";
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
echo "Conexión establecida correctamente."; // <-- para prueba
}
?>
