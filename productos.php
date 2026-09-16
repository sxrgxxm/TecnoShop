<?php

require_once "database.php";

$categoria_id = isset($_GET["categoria"])
    ? intval($_GET["categoria"])
    : 0;


/* =========================
   PRODUCTOS POR CATEGORÍA
========================= */

if ($categoria_id > 0) {

    $sql = "SELECT
                productos.*,
                categorias.nombre AS categoria,
                usuarios.nombre AS vendedor
            FROM productos
            INNER JOIN categorias
                ON productos.categoria_id = categorias.id
            INNER JOIN usuarios
                ON productos.usuario_id = usuarios.id
            WHERE productos.categoria_id = ?
            ORDER BY productos.id DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $categoria_id);
    $stmt->execute();

    $resultado = $stmt->get_result();

    /* Nombre de la categoría */

    $sqlCategoria = "SELECT nombre FROM categorias WHERE id = ?";

    $stmtCategoria = $conn->prepare($sqlCategoria);
    $stmtCategoria->bind_param("i", $categoria_id);
    $stmtCategoria->execute();

    $categoriaResultado = $stmtCategoria->get_result();
    $categoriaActual = $categoriaResultado->fetch_assoc();

} else {

    /* Todos los productos */

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

    $resultado = $conn->query($sql);

    $categoriaActual = null;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - TecnoShop</title>
    <link rel="stylesheet" href="/TecnoShop/css/style.css?v=3">
</head>
</head>

<body>

<header>

    <h1>TecnoShop</h1>

    <nav>

        <a href="index.php">Inicio</a>
        <a href="productos.php">Productos</a>

        <?php if (isset($_SESSION["usuario_id"])): ?>

            <a href="publicar.php">Publicar producto</a>
            <a href="productos_crud.php">Editar productos</a>

        <?php else: ?>

            <a href="login.php">Iniciar sesión</a>
            <a href="registro.php">Registrarse</a>

        <?php endif; ?>

    </nav>

</header>


<main class="productos">

    <?php if ($categoriaActual): ?>

        <h2>
            Productos de <?= htmlspecialchars($categoriaActual["nombre"]) ?>
        </h2>

        <a href="productos.php" class="boton">
            Ver todos
        </a>

    <?php else: ?>

        <h2>
            Todos los productos
        </h2>

    <?php endif; ?>


    <?php if ($resultado->num_rows > 0): ?>

        <div class="grid">

            <?php while ($producto = $resultado->fetch_assoc()): ?>

                <div class="tarjeta">

                    <?php if (!empty($producto["imagen"])): ?>

                        <img
                            src="images/<?= htmlspecialchars($producto["imagen"]) ?>"
                            alt="<?= htmlspecialchars($producto["nombre"]) ?>"
                        >

                    <?php else: ?>

                        <div class="sin-imagen">
                            Sin imagen
                        </div>

                    <?php endif; ?>


                    <span class="categoria">

                        <?= htmlspecialchars($producto["categoria"]) ?>

                    </span>


                    <h3>

                        <?= htmlspecialchars($producto["nombre"]) ?>

                    </h3>


                    <p>

                        <?= htmlspecialchars($producto["descripcion"]) ?>

                    </p>


                    <strong>

                        $<?= number_format(
                            $producto["precio"],
                            0,
                            ",",
                            "."
                        ) ?>

                    </strong>


                    <p>

                        Estado:
                        <?= htmlspecialchars($producto["estado"]) ?>

                    </p>


                    <p>

                        Vendedor:
                        <?= htmlspecialchars($producto["vendedor"]) ?>

                    </p>

                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="sin-productos">

            <h3>No hay productos en esta categoría.</h3>

            <a href="productos.php" class="boton">
                Ver todos los productos
            </a>

        </div>

    <?php endif; ?>

</main>

</body>
</html>