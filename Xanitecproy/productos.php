<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
require "db.php";

// Obtener lista de almacenes
$stmt = $pdo->query("EXEC listar_almacenes");
$almacenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesar formulario para agregar producto
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion']) && $_POST['accion'] === "agregar_producto") {
    $nombre = $_POST["nombre"] ?? '';
    $descripcion = $_POST["descripcion"] ?? '';
    $precio = $_POST["precio"] ?? 0;
    $imagen = $_POST["imagen"] ?? '';
    $almacen_id = $_POST["almacen_id_producto"] ?? null;

    if (!empty($nombre) && !empty($precio) && !empty($almacen_id)) {
        $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, imagen, almacen_id) VALUES (:nombre, :descripcion, :precio, :imagen, :almacen_id)");
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":precio", $precio);
        $stmt->bindParam(":imagen", $imagen);
        $stmt->bindParam(":almacen_id", $almacen_id, PDO::PARAM_INT);
        $stmt->execute();
    }
}

// Procesar formulario para modificar inventario (sumar/restar)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion']) && $_POST['accion'] === "modificar_inventario") {
    $almacen_id = $_POST["almacen_id"] ?? null;
    $producto_id = $_POST["producto_id"] ?? null;
    $cantidad = (int)($_POST["cantidad"] ?? 0);
    $operacion = $_POST["operacion"] ?? "sumar";

    if (!empty($almacen_id) && !empty($producto_id) && $cantidad > 0) {
        // Verificar cantidad actual
        $stmt = $pdo->prepare("SELECT cantidad FROM inventario WHERE producto = :producto AND almacen_id = :almacen_id");
        $stmt->execute([":producto" => $producto_id, ":almacen_id" => $almacen_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $cantidad_actual = (int)$row['cantidad'];
            if ($operacion === "sumar") {
                $nueva_cantidad = $cantidad_actual + $cantidad;
            } else {
                $nueva_cantidad = max(0, $cantidad_actual - $cantidad);
            }
            $stmt = $pdo->prepare("UPDATE inventario SET cantidad = :cantidad WHERE producto = :producto AND almacen_id = :almacen_id");
            $stmt->execute([":cantidad" => $nueva_cantidad, ":producto" => $producto_id, ":almacen_id" => $almacen_id]);
        } else {
            if ($operacion === "sumar") {
                $stmt = $pdo->prepare("INSERT INTO inventario (producto, cantidad, almacen_id) VALUES (:producto, :cantidad, :almacen_id)");
                $stmt->execute([":producto" => $producto_id, ":cantidad" => $cantidad, ":almacen_id" => $almacen_id]);
            }
        }
        header("Location: productos.php");
        exit;
    }
}

// Eliminar producto
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    $stmt = $pdo->prepare("DELETE FROM productos WHERE id = :id");
    $stmt->execute([":id" => $id]);
    header("Location: productos.php");
    exit;
}

// Obtener lista de productos
$stmt = $pdo->query("SELECT id, nombre FROM productos");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Productos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #121212;
      color: #E0E0E0;
    }
    #particles-js {
      position: fixed;
      inset: 0;
      z-index: -1;
      background: linear-gradient(135deg, #1F2937, #111827);
    }
  </style>
</head>
<body class="min-h-screen px-6 py-10">

<div id="particles-js"></div>

<div class="max-w-7xl mx-auto bg-gray-900 bg-opacity-90 rounded-2xl shadow-2xl p-10">
    
    <h1 class="text-3xl font-bold text-blue-400 mb-6">Gestión de Productos</h1>


<a href="inventario.php" class="inline-block bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded mb-6">⬅ Volver al Inventario</a>


    <!-- Formulario para agregar producto -->
    <form method="POST" class="space-y-4 mb-10">
        <input type="hidden" name="accion" value="agregar_producto">
        <div>
            <label class="block mb-1">Nombre:</label>
            <input type="text" name="nombre" class="w-full bg-gray-700 border border-gray-600 p-2 rounded" required>
        </div>
        <div>
            <label class="block mb-1">Descripción:</label>
            <textarea name="descripcion" class="w-full bg-gray-700 border border-gray-600 p-2 rounded"></textarea>
        </div>
        <div>
            <label class="block mb-1">Precio:</label>
            <input type="number" step="0.01" name="precio" class="w-full bg-gray-700 border border-gray-600 p-2 rounded" required>
        </div>
        <div>
            <label class="block mb-1">Imagen (nombre archivo):</label>
            <input type="text" name="imagen" class="w-full bg-gray-700 border border-gray-600 p-2 rounded">
        </div>
        <div>
            <label class="block mb-1">Almacén:</label>
            <select name="almacen_id_producto" class="w-full bg-gray-700 border border-gray-600 p-2 rounded" required>
                <?php foreach ($almacenes as $a): ?>
                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="bg-green-600 hover:bg-green-700 px-6 py-2 rounded">➕ Guardar Producto</button>
    </form>

    <!-- Formulario para modificar inventario -->
    <form method="POST" class="space-y-4 mb-10">
        <input type="hidden" name="accion" value="modificar_inventario">
        <div>
            <label class="block mb-1">Almacén:</label>
            <select name="almacen_id" class="w-full bg-gray-700 border border-gray-600 p-2 rounded" required>
                <?php foreach ($almacenes as $a): ?>
                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block mb-1">Producto:</label>
            <select name="producto_id" class="w-full bg-gray-700 border border-gray-600 p-2 rounded" required>
                <?php foreach ($productos as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block mb-1">Cantidad:</label>
            <input type="number" name="cantidad" min="1" class="w-full bg-gray-700 border border-gray-600 p-2 rounded" required>
        </div>
        <div>
            <label class="block mb-1">Operación:</label>
            <select name="operacion" class="w-full bg-gray-700 border border-gray-600 p-2 rounded">
                <option value="sumar">➕ Sumar</option>
                <option value="restar">➖ Restar</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded">📦 Actualizar Inventario</button>
    </form>

    <!-- Lista de productos con opción de eliminar -->
    <h2 class="text-xl font-semibold text-yellow-300 mb-4">Lista de Productos</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm bg-gray-800 text-white rounded-lg overflow-hidden">
            <thead class="bg-gray-700 text-blue-300 text-sm uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3">Nombre</th>
                    <th class="px-6 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $p): ?>
                <tr class="border-t border-gray-700 hover:bg-gray-700">
                    <td class="px-6 py-4"><?= htmlspecialchars($p['nombre']) ?></td>
                    <td class="px-6 py-4">
                        <a href="?eliminar=<?= $p['id'] ?>" class="bg-red-600 hover:bg-red-700 px-4 py-1 rounded text-white" onclick="return confirm('¿Eliminar este producto?')">🗑 Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/particles.js"></script>
<script>
particlesJS("particles-js", {
    particles: {
        number: { value: 70, density: { enable: true, value_area: 900 } },
        color: { value: "#60A5FA" },
        shape: { type: "circle" },
        opacity: { value: 0.3, random: true },
        size: { value: 3, random: true },
        line_linked: { enable: true, distance: 120, color: "#3B82F6", opacity: 0.4, width: 1 },
        move: { enable: true, speed: 1.2, direction: "none", out_mode: "out" }
    },
    interactivity: {
        detect_on: "canvas",
        events: {
            onhover: { enable: true, mode: "repulse" },
            onclick: { enable: true, mode: "push" }
        },
        modes: {
            repulse: { distance: 100 },
            push: { particles_nb: 4 }
        }
    },
    retina_detect: true
});
</script>

</body>
</html>
