<?php
require_once __DIR__ . '/includes/functions.php';
requerirRol(['kardex']);

$tipo = $_GET['tipo'] ?? 'todos';
$buscar = strtolower(trim((string)($_GET['buscar'] ?? '')));
$mensaje = $_GET['mensaje'] ?? '';
$tramites = tramites();

if ($tipo !== 'todos' && !obtenerTramite($tipo)) {
    $tipo = 'todos';
}

$solicitudes = $tipo === 'todos' ? todasLasSolicitudes() : array_map(function ($s) use ($tipo) {
    $s['_tipo_key'] = $tipo;
    return $s;
}, leerSolicitudes($tipo));

if ($buscar !== '') {
    $solicitudes = array_filter($solicitudes, function ($s) use ($buscar) {
        $texto = strtolower(
            ($s['nombre'] ?? '') . ' ' .
            ($s['ci'] ?? '') . ' ' .
            ($s['correo'] ?? '') . ' ' .
            ($s['carrera'] ?? '') . ' ' .
            ($s['tramite'] ?? '') . ' ' .
            ($s['estado'] ?? '')
        );
        return str_contains($texto, $buscar);
    });
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitudes registradas</title>
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
        <h1>Solicitudes registradas</h1>
        <p>Kardex puede consultar solicitudes y actualizar el estado de cada trámite.</p>
    </section>

    <?php if ($mensaje): ?>
        <div class="alert success"><?= e($mensaje) ?></div>
    <?php endif; ?>

    <section class="filters">
        <div>
            <a class="chip <?= $tipo === 'todos' ? 'active' : '' ?>" href="listar.php">Todos</a>
            <?php foreach ($tramites as $key => $item): ?>
                <a class="chip <?= $tipo === $key ? 'active' : '' ?>" href="listar.php?tipo=<?= e($key) ?>">
                    <?= e($item['titulo']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <form method="GET" class="search">
            <input type="hidden" name="tipo" value="<?= e($tipo) ?>">
            <input type="text" name="buscar" placeholder="Buscar por nombre, CI, correo..." value="<?= e($buscar) ?>">
            <button class="btn ghost" type="submit">Buscar</button>
        </form>
    </section>

    <section class="table-card">
        <?php if (empty($solicitudes)): ?>
            <div class="empty">
                <h3>No existen solicitudes registradas.</h3>
                <p>Cuando los estudiantes registren trámites, aparecerán en esta tabla.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Trámite</th>
                            <th>Estudiante</th>
                            <th>CI</th>
                            <th>Carrera</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Actualizar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($solicitudes as $s): ?>
                            <?php $tipoFila = $s['_tipo_key'] ?? $s['tipo'] ?? ''; ?>
                            <tr>
                                <td>#<?= e((string)$s['id']) ?></td>
                                <td><?= e((string)$s['tramite']) ?></td>
                                <td>
                                    <strong><?= e((string)($s['nombre'] ?? '')) ?></strong>
                                    <small><?= e((string)($s['correo'] ?? '')) ?></small>
                                </td>
                                <td><?= e((string)($s['ci'] ?? '')) ?></td>
                                <td><?= e((string)($s['carrera'] ?? '')) ?></td>
                                <td><?= e((string)($s['fecha_registro'] ?? '')) ?></td>
                                <td>
                                    <span class="<?= e(claseEstado((string)($s['estado'] ?? 'Pendiente'))) ?>">
                                        <?= e((string)($s['estado'] ?? 'Pendiente')) ?>
                                    </span>
                                </td>
                                <td>
                                    <form class="inline-form" method="POST" action="actualizar_estado.php">
                                        <input type="hidden" name="tipo" value="<?= e((string)$tipoFila) ?>">
                                        <input type="hidden" name="id" value="<?= e((string)$s['id']) ?>">
                                        <select name="estado">
                                            <?php foreach (estadosPermitidos() as $estado): ?>
                                                <option value="<?= e($estado) ?>" <?= (($s['estado'] ?? '') === $estado) ? 'selected' : '' ?>>
                                                    <?= e($estado) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button class="btn mini" type="submit">Guardar</button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="details-row">
                                <td></td>
                                <td colspan="7">
                                    <?php if (($tipoFila ?? '') === 'inscripcion'): ?>
                                        <strong>Materia:</strong> <?= e((string)($s['materia'] ?? '')) ?> |
                                        <strong>Grupo:</strong> <?= e((string)($s['grupo'] ?? '')) ?> |
                                    <?php endif; ?>
                                    <?php if (($tipoFila ?? '') === 'certificado'): ?>
                                        <strong>Tipo:</strong> <?= e((string)($s['tipo_certificado'] ?? '')) ?> |
                                        <strong>Gestión:</strong> <?= e((string)($s['gestion'] ?? '')) ?> |
                                    <?php endif; ?>
                                    <strong>Motivo:</strong> <?= e((string)($s['motivo'] ?? 'Sin observación')) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
