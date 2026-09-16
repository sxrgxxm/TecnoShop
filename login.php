<?php

session_start();
require_once "database.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $correo = trim($_POST["correo"]);
    $password = $_POST["password"];

    $sql = "SELECT * FROM usuarios WHERE correo = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows == 1) {

        $usuario = $resultado->fetch_assoc();

        if (password_verify($password, $usuario["password"])) {

            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION["nombre"] = $usuario["nombre"];

            header("Location: index.php");
            exit;
        }
    }

    $mensaje = "Correo o contraseña incorrectos.";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - TecnoShop</title>
    <link rel="stylesheet" href="/TecnoShop/css/style.css?v=3">
</head>
<body>

<div class="formulario">

    <h1>Iniciar sesión</h1>

    <?php if ($mensaje): ?>
        <p class="error"><?= $mensaje ?></p>
    <?php endif; ?>

    <form method="POST">

        <input type="email"
               name="correo"
               placeholder="Correo"
               required>

        <input type="password"
               name="password"
               placeholder="Contraseña"
               required>

        <button type="submit">
            Ingresar
        </button>

    </form>

    <a href="registro.php">Crear cuenta</a>

</div>

</body>
</html>