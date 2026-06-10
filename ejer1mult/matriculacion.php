<?php
require_once __DIR__ . '/includes/functions.php';
requerirRol(['estudiante']);
refrescarSesionDesdeUsuario();

$usuario = usuarioActual();
$mensaje = $_GET['mensaje'] ?? '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gestion = limpiar((string)($_POST['gestion'] ?? ''));
    $comprobante = limpiar((string)($_POST['comprobante'] ?? ''));
    $observacion = limpiar((string)($_POST['observacion'] ?? ''));

    if ($gestion === '' || $comprobante === '') {
        $error = 'Complete gestión y número de comprobante.';
        registrarFlujo('Subproceso de matricula', 'El formulario de matrícula tenía campos incompletos.', 'Matriculacion', 'Observado', null, null, 'F1', 'P2');
    } else {
        $matriculaciones = matriculaciones();
        $matriculaciones[] = [
            'id' => generarId($matriculaciones),
            'fecha' => date('Y-m-d H:i:s'),
            'ci' => (string)$usuario['ci'],
            'nombre' => (string)$usuario['nombre'],
            'correo' => (string)$usuario['correo'],
            'carrera' => (string)$usuario['carrera'],
            'gestion' => $gestion,
            'comprobante' => $comprobante,
            'observacion' => $observacion,
            'estado' => 'Matriculado'
        ];

        guardarMatriculaciones($matriculaciones);
        actualizarUsuarioPorCi((string)$usuario['ci'], ['matriculado' => true]);
        refrescarSesionDesdeUsuario();

        registrarFlujo('Subproceso de matricula', 'El estudiante completó la matrícula con comprobante ' . $comprobante . '.', 'Matriculacion', 'Completado', null, null, 'F1', 'P2');

        header('Location: registrar.php?tipo=inscripcion&mensaje=' . urlencode('Matrícula completada. Ahora puede inscribirse a materias.'));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Subproceso de Matrícula</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header class="topbar">
    <div class="brand">UMSA Digital</div>
    <div class="navlinks">
        <a href="index.php">Inicio</a>
        <a href="mis_solicitudes.php">Mis solicitudes</a>
        <a href="workflow.php">Workflow</a>
        <a href="logout.php">Salir</a>
    </div>
</header>

<main class="container narrow">
    <section class="panel">
        <a class="back" href="index.php">← Volver</a>
        <h1>Subproceso de matrícula</h1>
        <p class="muted">Este paso se activa cuando el estudiante intenta inscribirse a materias pero no figura como matriculado.</p>

        <?php if ($mensaje): ?>
            <div class="alert warning"><?= e($mensaje) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert error"><?= e($error) ?></div>
        <?php endif; ?>

        <div class="student-box">
            <h3>Datos del estudiante</h3>
            <p><strong>Nombre:</strong> <?= e((string)$usuario['nombre']) ?></p>
            <p><strong>CI:</strong> <?= e((string)$usuario['ci']) ?></p>
            <p><strong>Carrera:</strong> <?= e((string)$usuario['carrera']) ?></p>
        </div>

        <form class="form" method="POST">
            <div class="form-group">
                <label>Gestión académica <span>*</span></label>
                <input type="text" name="gestion" required placeholder="Ejemplo: 2026">
            </div>

            <div class="form-group">
                <label>Número de comprobante <span>*</span></label>
                <input type="text" name="comprobante" required placeholder="Ejemplo: MAT-2026-001">
            </div>

            <div class="form-group">
                <label>Observación</label>
                <textarea name="observacion" rows="4" placeholder="Detalle opcional de la matrícula"></textarea>
            </div>

            <button class="btn primary full" type="submit">Completar matrícula</button>
        </form>
    </section>
</main>
</body>
</html>
