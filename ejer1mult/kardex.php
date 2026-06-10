<?php
require_once __DIR__ . '/includes/functions.php';
requerirRol(['kardex']);

$buscar = limpiar((string)($_GET['buscar'] ?? ''));
$flujos = leerFlujos($buscar !== '' ? $buscar : null);
$solicitudes = todasLasSolicitudes();

if ($buscar !== '') {
    $solicitudes = array_values(array_filter($solicitudes, function ($s) use ($buscar) {
        return str_contains((string)($s['ci'] ?? ''), $buscar) || str_contains(strtolower((string)($s['nombre'] ?? '')), strtolower($buscar));
    }));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Kardex</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header class="topbar">
    <div class="brand">UMSA Digital</div>
    <div class="navlinks">
        <a href="index.php">Inicio</a>
        <a href="listar.php">Solicitudes</a>
        <a href="kardex.php">Panel Kardex</a>
        <a href="workflow.php">Workflow</a>
            <a href="diagramas.php">Diagramas</a>
        <a href="logout.php">Salir</a>
    </div>
</header>

<main class="container wide">
    <section class="section-title left">
        <h1>Panel de Kardex</h1>
        <p>Registro de procesos y flujos realizados por los estudiantes.</p>
    </section>

    <section class="filters">
        <form method="GET" class="search long-search">
            <input type="text" name="buscar" placeholder="Buscar por CI o nombre del estudiante" value="<?= e($buscar) ?>">
            <button class="btn primary" type="submit">Buscar</button>
            <a class="btn ghost" href="kardex.php">Limpiar</a>
        </form>
    </section>

    <section class="stats-grid">
        <article>
            <h3><?= count(todasLasSolicitudes()) ?></h3>
            <p>Solicitudes totales</p>
        </article>
        <article>
            <h3><?= count(leerFlujos()) ?></h3>
            <p>Movimientos registrados</p>
        </article>
        <article>
            <h3><?= count(matriculaciones()) ?></h3>
            <p>Matrículas procesadas</p>
        </article>
    </section>

    <section class="section-title left compact-title">
        <h2>Solicitudes del estudiante</h2>
    </section>

    <section class="table-card spacing-bottom">
        <?php if (empty($solicitudes)): ?>
            <div class="empty"><h3>No se encontraron solicitudes.</h3></div>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>CI</th>
                            <th>Nombre</th>
                            <th>Trámite</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($solicitudes as $s): ?>
                            <tr>
                                <td>#<?= e((string)$s['id']) ?></td>
                                <td><?= e((string)$s['ci']) ?></td>
                                <td><?= e((string)$s['nombre']) ?></td>
                                <td><?= e((string)$s['tramite']) ?></td>
                                <td><?= e((string)$s['fecha_registro']) ?></td>
                                <td><span class="<?= e(claseEstado((string)$s['estado'])) ?>"><?= e((string)$s['estado']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="section-title left compact-title">
        <h2>Flujo del proceso</h2>
    </section>

    <section class="timeline">
        <?php if (empty($flujos)): ?>
            <div class="empty"><h3>No existen movimientos registrados.</h3></div>
        <?php else: ?>
            <?php foreach ($flujos as $flujo): ?>
                <article>
                    <span><?= e((string)$flujo['fecha']) ?></span>
                    <h3><?= e((string)$flujo['accion']) ?></h3>
                    <p>
                        <strong>Estudiante:</strong> <?= e((string)$flujo['nombre']) ?> | 
                        <strong>CI:</strong> <?= e((string)$flujo['ci']) ?> | 
                        <strong>Trámite:</strong> <?= e((string)$flujo['tramite']) ?>
                    </p>
                    <p><?= e((string)$flujo['detalle']) ?></p>
                    <span class="estado revision"><?= e((string)$flujo['estado']) ?></span>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
