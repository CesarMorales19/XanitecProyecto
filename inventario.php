<?php
require "db.php";

try {
    // Llamar al procedimiento para obtener almacenes
    $stmt = $pdo->query("EXEC listar_almacenes");
    $almacenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $inventario = [];
    $filtrar_por_almacen = isset($_GET['filtrar']) && $_GET['filtrar'] === 'almacen';
    $id_almacen = $_GET['id_almacen'] ?? null;

    // Preparar llamada al procedimiento listar_inventario con parámetro
    if ($filtrar_por_almacen && is_numeric($id_almacen)) {
        $stmt = $pdo->prepare("EXEC listar_inventario :id_almacen");
        $stmt->bindParam(':id_almacen', $id_almacen, PDO::PARAM_INT);
    } else {
        $null = null;
        $stmt = $pdo->prepare("EXEC listar_inventario :id_almacen");
        $stmt->bindParam(':id_almacen', $null, PDO::PARAM_NULL);
    }
    $stmt->execute();

    $inventario = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Inventario de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="container py-5">
    <h1 class="mb-4">Inventario de Productos</h1>

    <!-- Botón Volver al inicio -->
    <a href="success.php" class="btn btn-secondary mb-3">⬅️ Volver al inicio</a>

    <!-- Formulario para filtrar por almacén -->
    <form method="GET" action="inventario.php" class="mb-4">
        <input type="hidden" name="filtrar" value="almacen" />
        <label for="id_almacen" class="form-label">Filtrar por almacén:</label>
        <select name="id_almacen" id="id_almacen" onchange="this.form.submit()" class="form-select">
            <option value="">-- Selecciona un almacén --</option>
            <?php foreach ($almacenes as $almacen): ?>
                <option value="<?= htmlspecialchars($almacen['id']) ?>" <?= ($id_almacen == $almacen['id']) ? 'selected' : '' ?>>
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
                            $rutaImagen = 'imagenes/' . $producto['imagen'];
                            if (!empty($producto['imagen']) && file_exists($rutaImagen)):
                            ?>
                                <img src="<?= htmlspecialchars($rutaImagen) ?>" width="80" alt="Imagen del producto" />
                            <?php else: ?>
                                <span class="text-muted">Sin imagen</span>
                            <?php endif; ?>
                        </td>
                        <td>$<?= number_format($producto['precio'], 2) ?></td>
                        <td><?= (int)$producto['cantidad'] ?></td>
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
