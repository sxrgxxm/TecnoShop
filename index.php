<?php

session_start();
require_once "config/database.php";

$categorias = $conn->query(
    "SELECT * FROM categorias ORDER BY nombre ASC"
);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>TecnoShop</title>

    <link rel="stylesheet" href="/TecnoShop/css/style.css?v=3">

    <style>

        .categorias {
            width: 90%;
            max-width: 1000px;
            margin: 60px auto;
            text-align: center;
        }

        .categorias h2 {
            font-size: 32px;
            margin-bottom: 30px;
        }

        .categorias-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        .categoria-card {
            display: block;
            background: white;
            padding: 35px 20px;
            border-radius: 15px;
            text-decoration: none;
            color: #222;
            box-shadow: 0 5px 15px rgba(0,0,0,0.10);
            border: 1px solid #e5e7eb;
            transition: 0.3s;
        }

        .categoria-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .categoria-card h3 {
            font-size: 25px;
            color: #2563eb;
            margin-bottom: 12px;
        }

        .categoria-card p {
            color: #666;
            margin: 0;
        }

        @media (max-width: 700px) {

            .categorias-grid {
                grid-template-columns: 1fr;
            }

        }

    </style>

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
            <a href="categorias_crud.php">Editar categorías</a>
            <a href="logout.php">Cerrar sesión</a>

        <?php else: ?>

            <a href="login.php">Iniciar sesión</a>
            <a href="registro.php">Registrarse</a>

        <?php endif; ?>

    </nav>

</header>


<main>

    <!-- PRESENTACIÓN -->

    <section class="inicio">

        <h2>Compra y vende tecnología</h2>

        <p>
            Encuentra CPU, computadores, celulares y consolas
            nuevas y usadas.
        </p>

        <a href="productos.php" class="boton">
            Ver todos los productos
        </a>

    </section>


    <!-- CATEGORÍAS -->

    <section class="categorias">

        <h2>Categorías</h2>

        <div class="categorias-grid">

            <?php while ($categoria = $categorias->fetch_assoc()): ?>

            <a href="productos.php?categoria=<?= $categoria["id"] ?>"
                class="categoria-card">

            <h3>
                <?= htmlspecialchars($categoria["nombre"]) ?>
            </h3>

            <p>
                Ver productos →
            </p>

</a>

            <?php endwhile; ?>

        </div>

    </section>

</main>

</body>

</html>