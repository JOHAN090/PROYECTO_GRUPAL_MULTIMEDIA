<?php
require_once __DIR__ . '/includes/functions.php';
requerirRol(['estudiante']);
refrescarSesionDesdeUsuario();

$usuario = usuarioActual();
$tipo = $_GET['tipo'] ?? '';
$tramite = obtenerTramite($tipo);

if (!$tramite) {
    header('Location: index.php?mensaje=' . urlencode('Seleccione un trámite válido.'));
    exit;
}

if (!empty($tramite['requiere_matricula']) && !estudianteEstaMatriculado((string)$usuario['ci'])) {
    registrarFlujo('Validación de matrícula', 'El sistema detectó que el estudiante no está matriculado y lo derivó al subproceso de matrícula.', $tramite['titulo'], 'Derivado');
    header('Location: matriculacion.php?mensaje=' . urlencode('Antes de inscribirse debe completar su matrícula.'));
    exit;
}

registrarFlujo('Ingreso a formulario', 'El estudiante abrió el formulario del trámite.', $tramite['titulo'], 'En proceso');

$errores = $_SESSION['errores'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errores'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar - <?= e($tramite['titulo']) ?></title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header class="topbar">
    <div class="brand">UMSA Digital</div>
    <div class="navlinks">
        <a href="index.php">Inicio</a>
        <a href="mis_solicitudes.php">Mis solicitudes</a>
        <a href="workflow.php">Workflow</a>
            <a href="diagramas.php">Diagramas</a>
        <a href="logout.php">Salir</a>
    </div>
</header>

<main class="container narrow">
    <section class="panel">
        <a class="back" href="index.php">← Volver</a>
        <h1><?= e($tramite['titulo']) ?></h1>
        <p class="muted"><?= e($tramite['descripcion']) ?></p>

        <div class="student-box">
            <h3>Datos del estudiante</h3>
            <p><strong>Nombre:</strong> <?= e((string)$usuario['nombre']) ?></p>
            <p><strong>CI:</strong> <?= e((string)$usuario['ci']) ?></p>
            <p><strong>Carrera:</strong> <?= e((string)$usuario['carrera']) ?></p>
            <p><strong>Estado de matrícula:</strong>
                <span class="estado <?= $usuario['matriculado'] ? 'aprobado' : 'rechazado' ?>">
                    <?= $usuario['matriculado'] ? 'Matriculado' : 'No matriculado' ?>
                </span>
            </p>
        </div>

        <?php if (!empty($errores)): ?>
            <div class="alert error">
                <strong>Revise los siguientes errores:</strong>
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form class="form" method="POST" action="guardar.php">
            <input type="hidden" name="tipo" value="<?= e($tipo) ?>">

            <?php foreach ($tramite['campos'] as $campo): ?>
                <?php
                $name = $campo['name'];
                $label = $campo['label'];
                $type = $campo['type'];
                $required = !empty($campo['required']);
                $value = $old[$name] ?? '';
                ?>
                <div class="form-group">
                    <label for="<?= e($name) ?>">
                        <?= e($label) ?> <?= $required ? '<span>*</span>' : '' ?>
                    </label>

                    <?php if ($type === 'textarea'): ?>
                        <textarea id="<?= e($name) ?>" name="<?= e($name) ?>" rows="4" <?= $required ? 'required' : '' ?>><?= e($value) ?></textarea>
                    <?php elseif ($type === 'select'): ?>
                        <select id="<?= e($name) ?>" name="<?= e($name) ?>" <?= $required ? 'required' : '' ?>>
                            <option value="">Seleccione una opción</option>
                            <?php foreach (($campo['opciones'] ?? []) as $opcion): ?>
                                <option value="<?= e($opcion) ?>" <?= $value === $opcion ? 'selected' : '' ?>>
                                    <?= e($opcion) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input
                            id="<?= e($name) ?>"
                            type="<?= e($type) ?>"
                            name="<?= e($name) ?>"
                            value="<?= e($value) ?>"
                            <?= $required ? 'required' : '' ?>
                        >
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <button class="btn primary full" type="submit">Registrar solicitud</button>
        </form>
    </section>
</main>
</body>
</html>
