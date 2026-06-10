<?php
require_once __DIR__ . '/includes/functions.php';
requerirRol(['estudiante']);
refrescarSesionDesdeUsuario();

$usuario = usuarioActual();
$mensaje = $_GET['mensaje'] ?? '';
$solicitudes = solicitudesDelEstudiante((string)$usuario['ci']);
$flujos = leerFlujos((string)$usuario['ci']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Solicitudes</title>
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

<main class="container wide">
    <section class="section-title left">
        <h1>Mis solicitudes</h1>
        <p>Seguimiento personal de trámites registrados y pasos realizados.</p>
    </section>

    <?php if ($mensaje): ?>
        <div class="alert success"><?= e($mensaje) ?></div>
    <?php endif; ?>

    <section class="profile-strip">
        <div>
            <h2><?= e((string)$usuario['nombre']) ?></h2>
            <p>CI: <?= e((string)$usuario['ci']) ?> | Carrera: <?= e((string)$usuario['carrera']) ?></p>
        </div>
        <span class="estado <?= $usuario['matriculado'] ? 'aprobado' : 'rechazado' ?>">
            <?= $usuario['matriculado'] ? 'Matriculado' : 'No matriculado' ?>
        </span>
    </section>

    <section class="table-card spacing-bottom">
        <?php if (empty($solicitudes)): ?>
            <div class="empty">
                <h3>No registró solicitudes todavía.</h3>
                <a class="btn primary" href="index.php">Registrar un trámite</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Trámite</th>
                            <th>Detalle</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($solicitudes as $s): ?>
                            <?php $tipoFila = $s['_tipo_key'] ?? $s['tipo'] ?? ''; ?>
                            <tr>
                                <td>#<?= e((string)$s['id']) ?></td>
                                <td><?= e((string)$s['tramite']) ?></td>
                                <td>
                                    <?php if (($tipoFila ?? '') === 'inscripcion'): ?>
                                        Materia: <?= e((string)($s['materia'] ?? '')) ?> - Grupo: <?= e((string)($s['grupo'] ?? '')) ?>
                                    <?php else: ?>
                                        <?= e((string)($s['tipo_certificado'] ?? '')) ?> - Gestión <?= e((string)($s['gestion'] ?? '')) ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= e((string)($s['fecha_registro'] ?? '')) ?></td>
                                <td><span class="<?= e(claseEstado((string)$s['estado'])) ?>"><?= e((string)$s['estado']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="section-title left compact-title">
        <h2>Mi flujo registrado</h2>
        <p>Estos son los pasos que Kardex también puede revisar.</p>
    </section>

    <section class="timeline">
        <?php foreach (array_slice($flujos, 0, 10) as $flujo): ?>
            <article>
                <span><?= e((string)$flujo['fecha']) ?></span>
                <h3><?= e((string)$flujo['accion']) ?></h3>
                <p><strong><?= e((string)$flujo['tramite']) ?>:</strong> <?= e((string)$flujo['detalle']) ?></p>
            </article>
        <?php endforeach; ?>
    </section>
</main>
</body>
</html>
