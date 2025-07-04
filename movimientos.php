<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}


// Ejecutar el procedimiento almacenado
$stmt = $pdo->query("EXEC listar_movimientos");
$movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Movimientos del sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1 class="mb-4">📋 Historial de Movimientos</h1>
    <a href="almacenes.php" class="btn btn-secondary mb-3">⬅️ Volver</a>

    <?php if (count($movimientos) > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Módulo</th>
                    <th>Descripción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movimientos as $mov): ?>
                    <tr>
                        <td><?= date("d/m/Y H:i", strtotime($mov['fecha_movimiento'])) ?></td>
                        <td><?= htmlspecialchars($mov['usuario']) ?></td>
                        <td><span class="badge bg-<?= $mov['accion'] === 'crear' ? 'success' : ($mov['accion'] === 'editar' ? 'warning text-dark' : 'danger') ?>">
                            <?= ucfirst($mov['accion']) ?>
                        </span></td>
                        <td><?= ucfirst($mov['modulo']) ?></td>
                        <td><?= htmlspecialchars($mov['descripcion']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No hay movimientos registrados.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

