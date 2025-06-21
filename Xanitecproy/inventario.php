<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require 'db.php';

$filtrar_por_almacen = isset($_GET['filtrar']) && $_GET['filtrar'] === 'almacen';

try {
    if ($filtrar_por_almacen) {
        // Obtener lista de almacenes para el select
        $stmt = $pdo->query("SELECT id, nombre FROM almacenes");
        $almacenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $id_almacen = $_GET['id_almacen'] ?? null;

        if ($id_almacen) {
            $stmt = $pdo->prepare("
                SELECT i.id, i.producto, i.cantidad, a.nombre AS almacen_nombre
                FROM inventario i
                JOIN almacenes a ON i.almacen_id = a.id
                WHERE i.almacen_id = ?
            ");
            $stmt->execute([$id_almacen]);
            $inventario = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $inventario = [];
        }
    } else {
        // Inventario general
        $stmt = $pdo->query("
            SELECT i.id, i.producto, i.cantidad, a.nombre AS almacen_nombre
            FROM inventario i
            JOIN almacenes a ON i.almacen_id = a.id
        ");
        $inventario = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Inventario<?php echo $filtrar_por_almacen ? " por Almacén" : ""; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="p-4">

  <?php if ($filtrar_por_almacen): ?>
    <form method="GET" action="inventario.php" class="mb-3">
      <input type="hidden" name="filtrar" value="almacen" />
      <label for="id_almacen" class="form-label">Selecciona un Almacén:</label>
      <select name="id_almacen" id="id_almacen" onchange="this.form.submit()" class="form-select w-auto">
        <option value="">-- Seleccione --</option>
        <?php foreach ($almacenes as $almacen): ?>
          <option value="<?= $almacen['id'] ?>" <?= ($id_almacen == $almacen['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($almacen['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>
  <?php endif; ?>

  <h1>Inventario <?php echo $filtrar_por_almacen ? "del almacén " . ($id_almacen ? htmlspecialchars($id_almacen) : "") : "(General)"; ?></h1>

  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>Almacén</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($inventario): ?>
        <?php foreach ($inventario as $item): ?>
          <tr>
            <td><?= htmlspecialchars($item['id']) ?></td>
            <td><?= htmlspecialchars($item['producto']) ?></td>
            <td><?= htmlspecialchars($item['cantidad']) ?></td>
            <td><?= htmlspecialchars($item['almacen_nombre']) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="4" class="text-center">No hay datos para mostrar.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

</body>
</html>
