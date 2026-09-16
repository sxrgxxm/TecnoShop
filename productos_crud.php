<?php

session_start();
require_once "database.php";

$mensaje = "";

/* =========================
   CREAR PRODUCTO
========================= */

if (isset($_POST["crear"])) {

    $nombre = trim($_POST["nombre"]);
    $descripcion = trim($_POST["descripcion"]);
    $precio = floatval($_POST["precio"]);
    $categoria = intval($_POST["categoria"]);
    $estado = $_POST["estado"];

    $imagen = "";

    if (!empty($_FILES["imagen"]["name"])) {

        $imagen = time() . "_" . basename($_FILES["imagen"]["name"]);

        move_uploaded_file(
            $_FILES["imagen"]["tmp_name"],
            "images/" . $imagen
        );
    }

    $usuario_id = $_SESSION["usuario_id"] ?? 1;

    $sql = "INSERT INTO productos
            (usuario_id, categoria_id, nombre, descripcion, precio, estado, imagen)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "iissdss",
        $usuario_id,
        $categoria,
        $nombre,
        $descripcion,
        $precio,
        $estado,
        $imagen
    );

    if ($stmt->execute()) {
        $mensaje = "Producto creado correctamente.";
    } else {
        $mensaje = "Error al crear el producto.";
    }
}


/* =========================
   ELIMINAR PRODUCTO
========================= */

if (isset($_GET["eliminar"])) {

    $id = intval($_GET["eliminar"]);

    $sql = "SELECT imagen FROM productos WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $producto = $resultado->fetch_assoc();

    if ($producto && !empty($producto["imagen"])) {

        $rutaImagen = "images/" . $producto["imagen"];

        if (file_exists($rutaImagen)) {
            unlink($rutaImagen);
        }
    }

    $sql = "DELETE FROM productos WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: productos_crud.php");
    exit;
}


/* =========================
   EDITAR PRODUCTO
========================= */

if (isset($_POST["editar"])) {

    $id = intval($_POST["id"]);
    $nombre = trim($_POST["nombre"]);
    $descripcion = trim($_POST["descripcion"]);
    $precio = floatval($_POST["precio"]);
    $categoria = intval($_POST["categoria"]);
    $estado = $_POST["estado"];

    if (!empty($_FILES["imagen"]["name"])) {

        /* Obtener imagen anterior */

        $sql = "SELECT imagen FROM productos WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $productoAnterior = $resultado->fetch_assoc();

        if ($productoAnterior && !empty($productoAnterior["imagen"])) {

            $rutaAnterior = "images/" . $productoAnterior["imagen"];

            if (file_exists($rutaAnterior)) {
                unlink($rutaAnterior);
            }
        }

        /* Subir nueva imagen */

        $imagen = time() . "_" . basename($_FILES["imagen"]["name"]);

        move_uploaded_file(
            $_FILES["imagen"]["tmp_name"],
            "images/" . $imagen
        );

        $sql = "UPDATE productos
                SET nombre = ?,
                    descripcion = ?,
                    precio = ?,
                    categoria_id = ?,
                    estado = ?,
                    imagen = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "ssdissi",
            $nombre,
            $descripcion,
            $precio,
            $categoria,
            $estado,
            $imagen,
            $id
        );

    } else {

        $sql = "UPDATE productos
                SET nombre = ?,
                    descripcion = ?,
                    precio = ?,
                    categoria_id = ?,
                    estado = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "ssdisi",
            $nombre,
            $descripcion,
            $precio,
            $categoria,
            $estado,
            $id
        );
    }

    $stmt->execute();

    header("Location: productos_crud.php");
    exit;
}


/* =========================
   PRODUCTO PARA EDITAR
========================= */

$editar = null;

if (isset($_GET["editar"])) {

    $id = intval($_GET["editar"]);

    $sql = "SELECT * FROM productos WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $resultado = $stmt->get_result();

    $editar = $resultado->fetch_assoc();
}


/* =========================
   CATEGORIAS
========================= */

$categorias = [];

$resultadoCategorias = $conn->query(
    "SELECT * FROM categorias ORDER BY nombre ASC"
);

while ($categoria = $resultadoCategorias->fetch_assoc()) {
    $categorias[] = $categoria;
}


/* =========================
   PRODUCTOS
========================= */

$sql = "SELECT
            productos.*,
            categorias.nombre AS categoria,
            usuarios.nombre AS vendedor
        FROM productos
        INNER JOIN categorias
            ON productos.categoria_id = categorias.id
        INNER JOIN usuarios
            ON productos.usuario_id = usuarios.id
        ORDER BY productos.id DESC";

$productos = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar productos - TecnoShop</title>
    <link rel="stylesheet" href="/TecnoShop/css/style.css?v=3">
</head>

<body>

<header>

    <h1>TecnoShop</h1>

    <nav>

        <a href="index.php">Inicio</a>

        <a href="productos.php">Productos</a>

        <a href="productos_crud.php">Administrar productos</a>

    </nav>

</header>


<main class="productos">

    <h2>Administración de productos</h2>


    <?php if (!empty($mensaje)): ?>

        <p class="exito">
            <?= htmlspecialchars($mensaje) ?>
        </p>

    <?php endif; ?>


    <!-- FORMULARIO -->

    <div class="formulario">

        <?php if ($editar): ?>

            <h3>Editar producto</h3>

            <form method="POST" enctype="multipart/form-data">

                <input
                    type="hidden"
                    name="id"
                    value="<?= $editar["id"] ?>"
                >

                <input
                    type="text"
                    name="nombre"
                    value="<?= htmlspecialchars($editar["nombre"]) ?>"
                    placeholder="Nombre del producto"
                    required
                >

                <textarea
                    name="descripcion"
                    placeholder="Descripción"
                    required
                ><?= htmlspecialchars($editar["descripcion"]) ?></textarea>

                <input
                    type="number"
                    name="precio"
                    value="<?= $editar["precio"] ?>"
                    min="0"
                    step="0.01"
                    placeholder="Precio"
                    required
                >

                <select name="categoria" required>

                    <?php foreach ($categorias as $categoria): ?>

                        <option
                            value="<?= $categoria["id"] ?>"
                            <?= $categoria["id"] == $editar["categoria_id"] ? "selected" : "" ?>
                        >
                            <?= htmlspecialchars($categoria["nombre"]) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <select name="estado" required>

                    <option
                        value="Nuevo"
                        <?= $editar["estado"] == "Nuevo" ? "selected" : "" ?>
                    >
                        Nuevo
                    </option>

                    <option
                        value="Usado"
                        <?= $editar["estado"] == "Usado" ? "selected" : "" ?>
                    >
                        Usado
                    </option>

                </select>

                <label>
                    Nueva imagen (opcional)
                </label>

                <input
                    type="file"
                    name="imagen"
                    accept="image/*"
                >

                <button type="submit" name="editar">
                    Guardar cambios
                </button>

                <a href="productos_crud.php">
                    Cancelar
                </a>

            </form>

        <?php else: ?>

            <h3>Crear producto</h3>

            <form method="POST" enctype="multipart/form-data">

                <input
                    type="text"
                    name="nombre"
                    placeholder="Nombre del producto"
                    required
                >

                <textarea
                    name="descripcion"
                    placeholder="Descripción"
                    required
                ></textarea>

                <input
                    type="number"
                    name="precio"
                    placeholder="Precio"
                    min="0"
                    step="0.01"
                    required
                >

                <select name="categoria" required>

                    <option value="">
                        Selecciona una categoría
                    </option>

                    <?php foreach ($categorias as $categoria): ?>

                        <option value="<?= $categoria["id"] ?>">
                            <?= htmlspecialchars($categoria["nombre"]) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <select name="estado" required>

                    <option value="Nuevo">
                        Nuevo
                    </option>

                    <option value="Usado">
                        Usado
                    </option>

                </select>

                <label>
                    Imagen del producto
                </label>

                <input
                    type="file"
                    name="imagen"
                    accept="image/*"
                >

                <button type="submit" name="crear">
                    Crear producto
                </button>

            </form>

        <?php endif; ?>

    </div>


    <!-- TABLA -->

    <div class="tabla-clientes">

        <h3>Productos registrados</h3>

        <table>

            <tr>

                <th>ID</th>
                <th>Imagen</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Estado</th>
                <th>Vendedor</th>
                <th>Acciones</th>

            </tr>

            <?php while ($producto = $productos->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?= $producto["id"] ?>
                    </td>

                    <td>

                        <?php if (!empty($producto["imagen"])): ?>

                            <img
                                src="images/<?= htmlspecialchars($producto["imagen"]) ?>"
                                width="70"
                                height="70"
                                style="object-fit: cover; border-radius: 6px;"
                                alt="Producto"
                            >

                        <?php else: ?>

                            Sin imagen

                        <?php endif; ?>

                    </td>

                    <td>
                        <?= htmlspecialchars($producto["nombre"]) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($producto["categoria"]) ?>
                    </td>

                    <td>
                        $<?= number_format($producto["precio"], 0, ",", ".") ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($producto["estado"]) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($producto["vendedor"]) ?>
                    </td>

                    <td>

                        <a
                            href="productos_crud.php?editar=<?= $producto["id"] ?>"
                            class="btn-editar"
                        >
                            Editar
                        </a>

                        <a
                            href="productos_crud.php?eliminar=<?= $producto["id"] ?>"
                            class="btn-eliminar"
                            onclick="return confirm('¿Seguro que deseas eliminar este producto?')"
                        >
                            Eliminar
                        </a>

                    </td>

                </tr>

            <?php endwhile; ?>

        </table>

    </div>

</main>

</body>
</html>

