<?php
require 'db.php';

$filtrar_por_almacen = isset($_GET['filtrar']) && $_GET['filtrar'] === 'almacen';

if ($filtrar_por_almacen) {
    // Obtener todos los almacenes
    $stmt = $pdo->query("SELECT id, nombre FROM almacenes");
    $almacenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $id_almacen = $_GET['id_almacen'] ?? null;

    if ($id_almacen) {
        $stmt = $pdo->prepare("
            SELECT p.id, p.nombre, p.descripcion, p.imagen, p.precio, p.cantidad, a.nombre AS almacen_nombre
            FROM productos p
            JOIN almacenes a ON p.almacen_id = a.id
            WHERE p.almacen_id = ?
        ");
        $stmt->execute([$id_almacen]);
        $inventario = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $inventario = [];
    }
} else {
    $stmt = $pdo->query("
        SELECT p.id, p.nombre, p.descripcion, p.imagen, p.precio, p.cantidad, a.nombre AS almacen_nombre
        FROM productos p
        JOIN almacenes a ON p.almacen_id = a.id
    ");
    $inventario = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h1 class="mb-4">Inventario de Productos</h1>

    <form method="GET" action="inventario.php" class="mb-4">
        <input type="hidden" name="filtrar" value="almacen" />
        <label for="id_almacen" class="form-label">Filtrar por almacén:</label>
        <select name="id_almacen" id="id_almacen" onchange="this.form.submit()" class="form-select">
            <option value="">-- Selecciona un almacén --</option>
            <?php foreach ($almacenes as $almacen): ?>
                <option value="<?= $almacen['id'] ?>" <?= isset($id_almacen) && $id_almacen == $almacen['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($almacen['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if (!empty($inventario)): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Imagen</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Almacén</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inventario as $producto): ?>
                    <tr>
                        <td><?= htmlspecialchars($producto['nombre']) ?></td>
                        <td><?= htmlspecialchars($producto['descripcion']) ?></td>
                        <td>
                            <?php
                            $rutaImagen = 'imagenes/' . $producto['imagen']; // Ajusta la carpeta si es distinta
                            if (!empty($producto['imagen']) && file_exists($rutaImagen)):
                            ?>
                                <img src="<?= $rutaImagen ?>" width="80" alt="Imagen del producto" />
                            <?php else: ?>
                                <span class="text-muted">Sin imagen</span>
                            <?php endif; ?>
                        </td>
                        <td>$<?= number_format($producto['precio'], 2) ?></td>
                        <td><?= $producto['cantidad'] ?></td>
                        <td><?= htmlspecialchars($producto['almacen_nombre']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">No hay inventario para mostrar.</div>
    <?php endif; ?>
</body>
</html>
