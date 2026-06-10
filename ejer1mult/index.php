<?php
require_once __DIR__ . '/includes/functions.php';
requerirLogin();
refrescarSesionDesdeUsuario();

$usuario = usuarioActual();
$mensaje = $_GET['mensaje'] ?? '';
$tramites = tramites();
$esKardex = esRol('kardex');
$misSolicitudes = esRol('estudiante') ? solicitudesDelEstudiante((string)$usuario['ci']) : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Digitalización de Trámites UMSA</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header class="hero">
    <nav class="navbar">
        <div class="brand">UMSA Digital</div>
        <div class="navlinks">
            <a href="index.php">Inicio</a>
            <?php if ($esKardex): ?>
                <a href="listar.php">Solicitudes</a>
                <a href="kardex.php">Panel Kardex</a>
            <?php else: ?>
                <a href="mis_solicitudes.php">Mis solicitudes</a>
            <?php endif; ?>
            <a href="workflow.php">Workflow</a>
            <a href="diagramas.php">Diagramas</a>
            <a href="logout.php">Salir</a>
        </div>
    </nav>

    <section class="hero-content">
        <span class="badge">Actividad Grupal 1 - Punto A</span>
        <h1>Digitalización de Trámites Universitarios</h1>
        <p>
            Plataforma web con login por roles, validación de matrícula, almacenamiento en JSON
            y registro de flujos para seguimiento desde Kardex.
        </p>
        <?php if ($esKardex): ?>
            <a class="btn primary" href="kardex.php">Entrar al panel Kardex</a>
        <?php else: ?>
            <a class="btn primary" href="#tramites">Iniciar trámite</a>
        <?php endif; ?>
    </section>
</header>

<main class="container">
    <?php if ($mensaje): ?>
        <div class="alert success"><?= e($mensaje) ?></div>
    <?php endif; ?>

    <section class="profile-strip">
        <div>
            <h2>Usuario activo</h2>
            <p><?= e((string)$usuario['nombre']) ?> - <?= e(strtoupper((string)$usuario['rol'])) ?></p>
        </div>
        <?php if (esRol('estudiante')): ?>
            <div>
                <span class="estado <?= $usuario['matriculado'] ? 'aprobado' : 'rechazado' ?>">
                    <?= $usuario['matriculado'] ? 'Matriculado' : 'No matriculado' ?>
                </span>
            </div>
        <?php endif; ?>
    </section>

    <?php if ($esKardex): ?>
        <section class="cards three">
            <article class="card">
                <div class="icon">📋</div>
                <h3>Revisar solicitudes</h3>
                <p>Permite consultar todos los trámites enviados por los estudiantes y cambiar su estado.</p>
                <a class="btn primary" href="listar.php">Ver solicitudes</a>
            </article>
            <article class="card">
                <div class="icon">🧾</div>
                <h3>Registro de flujos</h3>
                <p>Muestra los pasos que realizó cada estudiante dentro del sistema.</p>
                <a class="btn primary" href="kardex.php">Ver flujos</a>
            </article>
            <article class="card">
                <div class="icon">🔎</div>
                <h3>Seguimiento por CI</h3>
                <p>Permite buscar a un estudiante y revisar sus trámites y movimientos.</p>
                <a class="btn ghost" href="kardex.php">Buscar estudiante</a>
            </article>
            <article class="card">
                <div class="icon">🔄</div>
                <h3>Modelo BPM / Workflow</h3>
                <p>Visualiza el flujo normal, detallado y la bitácora real de procesos.</p>
                <a class="btn primary" href="workflow.php">Ver workflow</a>
            </article>
        </section>
    <?php else: ?>
        <section id="tramites" class="section-title">
            <h2>Trámites disponibles</h2>
            <p>Seleccione el trámite que desea iniciar.</p>
        </section>

        <section class="cards">
            <?php foreach ($tramites as $tipo => $tramite): ?>
                <article class="card">
                    <div class="icon"><?= $tipo === 'inscripcion' ? '📝' : '📄' ?></div>
                    <h3><?= e($tramite['titulo']) ?></h3>
                    <p><?= e($tramite['descripcion']) ?></p>
                    <?php if ($tipo === 'inscripcion' && !$usuario['matriculado']): ?>
                        <div class="alert warning">Debe realizar primero el subproceso de matrícula.</div>
                        <a class="btn primary" href="matriculacion.php">Ir a matriculación</a>
                    <?php else: ?>
                        <a class="btn primary" href="registrar.php?tipo=<?= e($tipo) ?>">Registrar solicitud</a>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </section>

        <section class="info-grid">
            <article>
                <h3>Validación</h3>
                <p>Para inscripción, el sistema verifica si el estudiante está matriculado.</p>
            </article>
            <article>
                <h3>Subproceso</h3>
                <p>Si no está matriculado, se deriva a un formulario de matrícula.</p>
            </article>
            <article>
                <h3>Seguimiento</h3>
                <p>Kardex puede revisar el historial de pasos realizados por cada estudiante.</p>
            </article>
        </section>

        <?php if (!empty($misSolicitudes)): ?>
            <section class="section-title left compact-title">
                <h2>Últimas solicitudes</h2>
            </section>
            <section class="mini-list">
                <?php foreach (array_slice($misSolicitudes, 0, 3) as $s): ?>
                    <article>
                        <strong><?= e((string)$s['tramite']) ?></strong>
                        <span class="<?= e(claseEstado((string)$s['estado'])) ?>"><?= e((string)$s['estado']) ?></span>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    <?php endif; ?>
</main>

<footer>
    <p>Proyecto académico - Multimedia - UMSA</p>
</footer>
</body>
</html>
