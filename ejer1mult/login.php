<?php
require_once __DIR__ . '/includes/functions.php';

if (usuarioActual()) {
    header('Location: index.php');
    exit;
}

$mensaje = $_GET['mensaje'] ?? '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = limpiar((string)($_POST['correo'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($correo === '' || $password === '') {
        $error = 'Ingrese correo y contraseña.';
    } elseif (loginUsuario($correo, $password)) {
        header('Location: index.php?mensaje=' . urlencode('Bienvenido al sistema.'));
        exit;
    } else {
        $error = 'Correo o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - UMSA Digital</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<main class="login-page">
    <section class="login-card">
        <span class="badge">Sistema con roles</span>
        <h1>UMSA Digital</h1>
        <p class="muted">Ingrese como estudiante o como Kardex para acceder a las funciones del sistema.</p>

        <?php if ($mensaje): ?>
            <div class="alert success"><?= e($mensaje) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert error"><?= e($error) ?></div>
        <?php endif; ?>

        <form class="form" method="POST">
            <div class="form-group">
                <label>Correo</label>
                <input type="email" name="correo" required placeholder="ejemplo@umsa.bo">
            </div>

            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" required placeholder="123456">
            </div>

            <button class="btn primary full" type="submit">Ingresar</button>
        </form>

        <div class="demo-users">
            <h3>Usuarios de prueba</h3>
            <p><strong>Estudiante matriculado:</strong> estudiante@umsa.bo / 123456</p>
            <p><strong>Estudiante no matriculado:</strong> no.matriculado@umsa.bo / 123456</p>
            <p><strong>Kardex:</strong> kardex@umsa.bo / 123456</p>
        </div>
    </section>
</main>
</body>
</html>
