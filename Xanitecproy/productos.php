<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'db.php';

$almacen_id = isset($_GET['almacen_id']) ? (int)$_GET['almacen_id'] : 0;
if ($almacen_id <= 0) {
    die("ID de almacén no válido.");
}

// Crear carpeta uploads si no existe
$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Obtener info del almacén
$stmt = $pdo->prepare("SELECT * FROM almacenes WHERE id = :id");
$stmt->execute(['id' => $almacen_id]);
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

    // Imagen previa si estamos editando
    $imagen_nombre = $_POST['imagen_actual'] ?? '';

    // Manejar subida de imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['imagen']['tmp_name'];
        $fileName = $_FILES['imagen']['name'];
        $fileSize = $_FILES['imagen']['size'];
        $fileType = $_FILES['imagen']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Extensiones permitidas
        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Crear nombre único
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Eliminar imagen anterior si existe y es diferente
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
            $sql = "INSERT INTO productos (nombre, descripcion, precio, cantidad, almacen_id, imagen) VALUES (:nombre, :descripcion, :precio, :cantidad, :almacen_id, :imagen)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'cantidad' => $cantidad,
                'almacen_id' => $almacen_id,
                'imagen' => $imagen_nombre
            ])) {
                header("Location: productos.php?almacen_id=$almacen_id&msg=agregado");
                exit;
            } else {
                $mensaje = "Error al agregar producto.";
            }
        } elseif ($post_accion === 'editar' && $id > 0) {
            $sql = "UPDATE productos SET nombre=:nombre, descripcion=:descripcion, precio=:precio, cantidad=:cantidad, imagen=:imagen WHERE id=:id AND almacen_id=:almacen_id";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'cantidad' => $cantidad,
                'imagen' => $imagen_nombre,
                'id' => $id,
                'almacen_id' => $almacen_id
            ])) {
                header("Location: productos.php?almacen_id=$almacen_id&msg=actualizado");
                exit;
            } else {
                $mensaje = "Error al actualizar producto.";
            }
        }
    }
}

// GET: eliminar producto
if ($accion === 'eliminar' && $id > 0) {
    // Antes eliminar imagen si existe
    $stmt = $pdo->prepare("SELECT imagen FROM productos WHERE id=:id AND almacen_id=:almacen_id");
    $stmt->execute(['id' => $id, 'almacen_id' => $almacen_id]);
    $prodImagen = $stmt->fetchColumn();
    if ($prodImagen && file_exists($uploadDir . $prodImagen)) {
        unlink($uploadDir . $prodImagen);
    }

    $stmt = $pdo->prepare("DELETE FROM productos WHERE id=:id AND almacen_id=:almacen_id");
    $stmt->execute(['id' => $id, 'almacen_id' => $almacen_id]);
    header("Location: productos.php?almacen_id=$almacen_id&msg=eliminado");
    exit;
}

// Mensajes de confirmación
$msg = $_GET['msg'] ?? '';
$msgTexto = '';
if ($msg === 'agregado') $msgTexto = "Producto agregado exitosamente.";
if ($msg === 'actualizado') $msgTexto = "Producto actualizado exitosamente.";
if ($msg === 'eliminado') $msgTexto = "Producto eliminado exitosamente.";

// Para editar/ver producto
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

// Obtener productos para listar (cuando no se está agregando o editando)
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
