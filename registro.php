<?php

session_start();
require_once "database.php";

$mensaje = "";
$exito = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = trim($_POST["nombre"]);
    $correo = trim($_POST["correo"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombre, correo, password)
            VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nombre, $correo, $password);

    if ($stmt->execute()) {
        $mensaje = "¡Tu cuenta ha sido creada exitosamente!";
        $exito = true;
    } else {
        $mensaje = "No se pudo crear la cuenta. El correo puede estar registrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - TecnoShop</title>
    <link rel="stylesheet" href="/TecnoShop/css/style.css?v=3">
</head>

<body>

<div class="formulario">

    <h1>Crear cuenta</h1>

    <?php if ($mensaje): ?>
        <p class="<?= $exito ? 'exito' : 'error' ?>">
            <?= htmlspecialchars($mensaje) ?>
        </p>
    <?php endif; ?>

    <?php if (!$exito): ?>

        <form method="POST">

            <input
                type="text"
                name="nombre"
                placeholder="Nombre"
                required
            >

            <input
                type="email"
                name="correo"
                placeholder="Correo"
                required
            >

            <input
                type="password"
                name="password"
                placeholder="Contraseña"
                required
            >

            <button type="submit">
                Registrarse
            </button>

        </form>

    <?php else: ?>

        <a href="login.php" class="boton">
            Iniciar sesión
        </a>

    <?php endif; ?>

</div>

</body>
</html>