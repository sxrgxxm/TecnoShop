<?php

require_once "database.php";

$categorias = $conn->query(
    "SELECT * FROM categorias ORDER BY nombre ASC"
);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías - TecnoShop</title>
    <link rel="stylesheet" href="/TecnoShop/css/style.css?v=3">
</head>

<body>

<header>
    <h1>TecnoShop</h1>

    <nav>
        <a href="index.php">Inicio</a>
        <a href="productos.php">Productos</a>
        <a href="categorias.php">Categorías</a>
    </nav>
</header>

<main class="productos">

    <h2>Categorías disponibles</h2>

    <div class="categorias-grid">

        <?php while ($categoria = $categorias->fetch_assoc()): ?>

            <a
                href="productos.php?categoria=<?= $categoria['id'] ?>"
                class="categoria-card"
            >

                <h3>
                    <?= htmlspecialchars($categoria['nombre']) ?>
                </h3>

                <span>
                    Ver productos
                </span>

            </a>

        <?php endwhile; ?>

    </div>

</main>

</body>
</html>