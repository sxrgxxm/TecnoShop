<?php

session_start();
require_once "config/database.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = trim($_POST["nombre"]);
    $descripcion = trim($_POST["descripcion"]);
    $precio = $_POST["precio"];
    $categoria = $_POST["categoria"];
    $estado = $_POST["estado"];

    $imagen = "";

    if (!empty($_FILES["imagen"]["name"])) {

        $imagen = time() . "_" . basename($_FILES["imagen"]["name"]);

        move_uploaded_file(
            $_FILES["imagen"]["tmp_name"],
            "images/" . $imagen
        );
    }

    $sql = "INSERT INTO productos
            (usuario_id, categoria_id, nombre, descripcion, precio, estado, imagen)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "iissdss",
        $_SESSION["usuario_id"],
        $categoria,
        $nombre,
        $descripcion,
        $precio,
        $estado,
        $imagen
    );

    if ($stmt->execute()) {
        header("Location: productos.php");
        exit;
    }

    $mensaje = "No se pudo publicar el producto.";
}

$categorias = $conn->query("SELECT * FROM categorias");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar producto - TecnoShop</title>
    <link rel="stylesheet" href="/TecnoShop/css/style.css?v=3">
</head>

<body>

<div class="formulario">

    <h1>Publicar producto</h1>

    <?php if ($mensaje): ?>
        <p class="error"><?= $mensaje ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <input type="text"
               name="nombre"
               placeholder="Nombre del producto"
               required>

        <textarea
            name="descripcion"
            placeholder="Descripción"
            required></textarea>

        <input type="number"
               name="precio"
               placeholder="Precio"
               min="0"
               required>

        <select name="categoria" required>

            <option value="">
                Selecciona una categoría
            </option>

            <?php while ($categoria = $categorias->fetch_assoc()): ?>

                <option value="<?= $categoria["id"] ?>">
                    <?= htmlspecialchars($categoria["nombre"]) ?>
                </option>

            <?php endwhile; ?>

        </select>

        <select name="estado" required>

            <option value="Nuevo">Nuevo</option>
            <option value="Usado">Usado</option>

        </select>

        <input type="file"
               name="imagen"
               accept="image/*">

        <button type="submit">
            Publicar
        </button>

    </form>

</div>

</body>
</html>