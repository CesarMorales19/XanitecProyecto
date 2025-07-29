
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>productos.php</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">
  <div class="container mx-auto px-4 py-10">
    <div class="bg-white shadow-xl rounded-xl p-8">
<pre class='whitespace-pre-wrap text-sm text-gray-800'>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'db.php';

// Función para registrar movimiento (puedes mantenerla igual)
function registrarMovimiento($pdo, $usuario, $accion, $modulo, $descripcion) {
    $sql = "INSERT INTO movimientos (usuario, accion, modulo, descripcion) VALUES (:usuario, :accion, :modulo, :descripcion)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'usuario' => $usuario,
        'accion' => $accion,
        'modulo' => $modulo,
        'descripcion' => $descripcion
    ]);
}

$usuario = $_SESSION['user_id'];

$almacen_id = isset($_GET['almacen_id']) ? (int)$_GET['almacen_id'] : 0;
if ($almacen_id <= 0) {
    die("ID de almacén no válido.");
}

// Crear carpeta uploads si no existe
$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Obtener info del almacén usando procedimiento almacenado
$stmt = $pdo->prepare("EXEC obtener_almacen_por_id :id");
$stmt->bindParam(':id', $almacen_id, PDO::PARAM_INT);
$stmt->execute();
$almacen = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$almacen) {
    die("Almacén no encontrado.");
}

$accion = $_GET['accion'] ?? 'listar';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$mensaje = '';

// Manejo POST para agregar o editar producto con imagen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $precio = $_POST['precio'] ?? 0;
    $cantidad = $_POST['cantidad'] ?? 0;
    $post_accion = $_POST['accion'] ?? '';
    $imagen_nombre = $_POST['imagen_actual'] ?? '';

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['imagen']['tmp_name'];
        $fileName = $_FILES['imagen']['name'];
        $fileSize = $_FILES['imagen']['size'];
        $fileType = $_FILES['imagen']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                if ($imagen_nombre && file_exists($uploadDir . $imagen_nombre) && $imagen_nombre !== $newFileName) {
                    unlink($uploadDir . $imagen_nombre);
                }
                $imagen_nombre = $newFileName;
            } else {
                $mensaje = 'Error al mover la imagen subida.';
            }
        } else {
            $mensaje = 'Solo se permiten archivos JPG, JPEG, PNG y GIF.';
        }
    }

    if (!$mensaje) {
        if ($post_accion === 'agregar') {
            // Usar procedimiento almacenado agregar_producto
            $stmt = $pdo->prepare("EXEC agregar_producto :nombre, :descripcion, :precio, :cantidad, :almacen_id, :imagen");
            $stmt->execute([
                ':nombre' => $nombre,
                ':descripcion' => $descripcion,
                ':precio' => $precio,
                ':cantidad' => $cantidad,
                ':almacen_id' => $almacen_id,
                ':imagen' => $imagen_nombre
            ]);

            $desc = "Se agregó el producto '$nombre' al almacén '{$almacen['nombre']}' (ID $almacen_id)";
            registrarMovimiento($pdo, $usuario, 'crear', 'productos', $desc);

            header("Location: productos.php?almacen_id=$almacen_id&msg=agregado");
            exit;
        } elseif ($post_accion === 'editar' && $id > 0) {
            // Usar procedimiento almacenado editar_producto
            $stmt = $pdo->prepare("EXEC editar_producto :id, :nombre, :descripcion, :precio, :cantidad, :almacen_id, :imagen");
            $stmt->execute([
                ':id' => $id,
                ':nombre' => $nombre,
                ':descripcion' => $descripcion,
                ':precio' => $precio,
                ':cantidad' => $cantidad,
                ':almacen_id' => $almacen_id,
                ':imagen' => $imagen_nombre
            ]);

            $desc = "Se editó el producto ID $id en el almacén '{$almacen['nombre']}' (ID $almacen_id): nuevo nombre '$nombre', precio $precio, cantidad $cantidad";
            registrarMovimiento($pdo, $usuario, 'editar', 'productos', $desc);

            header("Location: productos.php?almacen_id=$almacen_id&msg=actualizado");
            exit;
        }
    }
}

// GET: eliminar producto
if ($accion === 'eliminar' && $id > 0) {
    // Obtener producto antes de eliminar (con procedimiento)
    $stmt = $pdo->prepare("EXEC obtener_producto_por_id :id, :almacen_id");
    $stmt->execute([':id' => $id, ':almacen_id' => $almacen_id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    $prodImagen = $producto['imagen'] ?? null;
    $nombreProducto = $producto['nombre'] ?? 'Desconocido';

    if ($prodImagen && file_exists($uploadDir . $prodImagen)) {
        unlink($uploadDir . $prodImagen);
    }

    $desc = "Se eliminó el producto '$nombreProducto' (ID $id) del almacén '{$almacen['nombre']}' (ID $almacen_id)";
    registrarMovimiento($pdo, $usuario, 'eliminar', 'productos', $desc);

    // Eliminar producto con procedimiento
    $stmt = $pdo->prepare("EXEC eliminar_producto :id, :almacen_id");
    $stmt->execute([':id' => $id, ':almacen_id' => $almacen_id]);

    header("Location: productos.php?almacen_id=$almacen_id&msg=eliminado");
    exit;
}


// Mensajes
$msg = $_GET['msg'] ?? '';
$msgTexto = '';
if ($msg === 'agregado') $msgTexto = "Producto agregado exitosamente.";
if ($msg === 'actualizado') $msgTexto = "Producto actualizado exitosamente.";
if ($msg === 'eliminado') $msgTexto = "Producto eliminado exitosamente.";

$data = ['nombre' => '', 'descripcion' => '', 'precio' => '', 'cantidad' => '', 'imagen' => ''];
if (($accion === 'editar' || $accion === 'ver') && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = :id AND almacen_id = :almacen_id");
    $stmt->execute(['id' => $id, 'almacen_id' => $almacen_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$data) {
        $mensaje = "Producto no encontrado.";
        $accion = 'listar';
    }
}

if ($accion === 'listar') {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE almacen_id = :almacen_id ORDER BY id DESC");
    $stmt->execute(['almacen_id' => $almacen_id]);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Productos del Almacén <?= htmlspecialchars($almacen['nombre']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
    #previewImagen {
        max-width: 150px;
        max-height: 150px;
        margin-top: 10px;
    }
</style>
</head>
<body>
<div class="container mt-4">
    <h1>Productos en almacén: <?= htmlspecialchars($almacen['nombre']) ?></h1>
    <a href="almacenes.php" class="btn btn-secondary mb-3">⬅️ Volver a Almacenes</a>

    <?php if ($mensaje): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($mensaje) ?></div>
    <?php elseif ($msgTexto): ?>
        <div class="alert alert-success"><?= htmlspecialchars($msgTexto) ?></div>
    <?php endif; ?>

    <?php if ($accion === 'listar'): ?>
        <a href="productos.php?almacen_id=<?= $almacen_id ?>&accion=agregar" class="btn btn-primary mb-3">➕ Agregar Producto</a>

        <?php if ($productos): ?>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Imagen</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $prod): ?>
                <tr>
                    <td><?= $prod['id'] ?></td>
                    <td><?= htmlspecialchars($prod['nombre']) ?></td>
                    <td><?= htmlspecialchars($prod['descripcion']) ?></td>
                    <td><?= number_format($prod['precio'], 2) ?></td>
                    <td><?= (int)$prod['cantidad'] ?></td>
                    <td>
                        <?php if ($prod['imagen'] && file_exists($uploadDir . $prod['imagen'])): ?>
                            <img src="uploads/<?= htmlspecialchars($prod['imagen']) ?>" alt="Imagen" style="max-width: 80px; max-height: 80px;">
                        <?php else: ?>
                            Sin imagen
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="productos.php?almacen_id=<?= $almacen_id ?>&accion=editar&id=<?= $prod['id'] ?>" class="btn btn-warning btn-sm">✏️ Editar</a>
                        <a href="productos.php?almacen_id=<?= $almacen_id ?>&accion=eliminar&id=<?= $prod['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar producto?')">🗑️ Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>No hay productos registrados en este almacén.</p>
        <?php endif; ?>

    <?php elseif ($accion === 'agregar' || $accion === 'editar'): ?>

        <h2><?= $accion === 'agregar' ? '➕ Agregar Producto' : '✏️ Editar Producto' ?></h2>

        <form method="POST" enctype="multipart/form-data" class="mt-3" novalidate>
            <input type="hidden" name="accion" value="<?= $accion ?>">
            <?php if ($accion === 'editar'): ?>
                <input type="hidden" name="imagen_actual" value="<?= htmlspecialchars($data['imagen']) ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label>Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($data['nombre'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label>Descripción</label>
                <textarea name="descripcion" class="form-control"><?= htmlspecialchars($data['descripcion'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label>Precio</label>
                <input type="number" step="0.01" min="0" name="precio" class="form-control" value="<?= htmlspecialchars($data['precio'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label>Cantidad</label>
                <input type="number" min="0" name="cantidad" class="form-control" value="<?= htmlspecialchars($data['cantidad'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label>Imagen</label>
                <input type="file" name="imagen" accept="image/*" class="form-control" id="inputImagen">
                <?php if ($accion === 'editar' && $data['imagen'] && file_exists($uploadDir . $data['imagen'])): ?>
                    <img src="uploads/<?= htmlspecialchars($data['imagen']) ?>" id="previewImagen" alt="Imagen actual">
                <?php else: ?>
                    <img id="previewImagen" style="display:none;" alt="Vista previa">
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary"><?= $accion === 'agregar' ? 'Guardar' : 'Actualizar' ?></button>
            <a href="productos.php?almacen_id=<?= $almacen_id ?>" class="btn btn-danger ms-2">Cancelar</a>
        </form>

    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Vista previa de imagen antes de enviar
    document.getElementById('inputImagen').addEventListener('change', function(event) {
        const preview = document.getElementById('previewImagen');
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            preview.src = '';
            preview.style.display = 'none';
        }
    });
</script>

</body>
</html>

</pre>
    </div>
  </div>
</body>
</html>
