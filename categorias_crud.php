<?php

require_once "config/database.php";

$mensaje = "";

/* CREAR */

if (isset($_POST["crear"])) {

    $nombre = trim($_POST["nombre"]);

    if ($nombre != "") {

        $sql = "INSERT INTO categorias (nombre) VALUES (?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nombre);

        if ($stmt->execute()) {
            $mensaje = "Categoría creada correctamente.";
        } else {
            $mensaje = "La categoría ya existe.";
        }
    }
}


/* EDITAR */

if (isset($_POST["editar"])) {

    $id = intval($_POST["id"]);
    $nombre = trim($_POST["nombre"]);

    $sql = "UPDATE categorias
            SET nombre = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nombre, $id);
    $stmt->execute();

    header("Location: categorias_crud.php");
    exit;
}


/* ELIMINAR */

if (isset($_GET["eliminar"])) {

    $id = intval($_GET["eliminar"]);

    $sql = "DELETE FROM categorias WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {

        header("Location: categorias_crud.php");
        exit;

    } else {

        $mensaje = "No se puede eliminar esta categoría porque tiene productos.";
    }
}


/* OBTENER CATEGORÍA PARA EDITAR */

$editar = null;

if (isset($_GET["editar"])) {

    $id = intval($_GET["editar"]);

    $sql = "SELECT * FROM categorias WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $editar = $stmt->get_result()->fetch_assoc();
}


/* LISTAR CATEGORÍAS */

$categorias = $conn->query(
    "SELECT * FROM categorias ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar categorías - TecnoShop</title>
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

    <h2>Administrar categorías</h2>

    <?php if ($mensaje): ?>

        <p class="exito">
            <?= htmlspecialchars($mensaje) ?>
        </p>

    <?php endif; ?>


    <div class="formulario">

        <?php if ($editar): ?>

            <h3>Editar categoría</h3>

            <form method="POST">

                <input
                    type="hidden"
                    name="id"
                    value="<?= $editar['id'] ?>"
                >

                <input
                    type="text"
                    name="nombre"
                    value="<?= htmlspecialchars($editar['nombre']) ?>"
                    required
                >

                <button type="submit" name="editar">
                    Guardar cambios
                </button>

                <a href="categorias_crud.php">
                    Cancelar
                </a>

            </form>

        <?php else: ?>

            <h3>Crear categoría</h3>

            <form method="POST">

                <input
                    type="text"
                    name="nombre"
                    placeholder="Nombre de categoría"
                    required
                >

                <button type="submit" name="crear">
                    Crear categoría
                </button>

            </form>

        <?php endif; ?>

    </div>


    <div class="tabla-clientes">

        <h3>Categorías registradas</h3>

        <table>

            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>

            <?php while ($categoria = $categorias->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?= $categoria['id'] ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($categoria['nombre']) ?>
                    </td>

                    <td>

                        <a
                            href="categorias_crud.php?editar=<?= $categoria['id'] ?>"
                            class="btn-editar"
                        >
                            Editar
                        </a>

                        <a
                            href="categorias_crud.php?eliminar=<?= $categoria['id'] ?>"
                            class="btn-eliminar"
                            onclick="return confirm('¿Eliminar esta categoría?')"
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