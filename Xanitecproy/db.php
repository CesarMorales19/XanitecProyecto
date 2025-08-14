<?php
$host = 'localhost\\sqlexpress';
$dbname = 'basexanitec';
$username = 'csr';
$password = '1234';

try {
    $pdo = new PDO("sqlsrv:Server=$host;Database=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
echo "Conexión establecida correctamente."; // <-- para prueba
}
?>