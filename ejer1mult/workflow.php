<?php
require_once __DIR__ . '/includes/functions.php';
requerirLogin();

$vista = limpiar((string)($_GET['vista'] ?? 'normal'));
if (!in_array($vista, ['normal', 'detallado', 'bitacora'], true)) {
    $vista = 'normal';
}

$usuario = usuarioActual();
$esKardex = esRol('kardex');
$procesos = workflowTramites();
$agrupados = procesosPorFlujo();
$flujos = leerFlujos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Workflow BPM de Trámites</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header class="topbar">
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
</header>

<main class="container wide">
    <section class="section-title left">
        <h1>Workflow BPM de Trámites</h1>
        <p>
            Esta sección representa el flujo como el ejemplo del licenciado: código de flujo,
            proceso actual, siguiente proceso, tipo, rol responsable y pantalla asociada.
        </p>
    </section>

    <section class="workflow-tabs">
        <a class="chip <?= $vista === 'normal' ? 'active' : '' ?>" href="workflow.php?vista=normal">Vista normal</a>
        <a class="chip <?= $vista === 'detallado' ? 'active' : '' ?>" href="workflow.php?vista=detallado">Vista detallada</a>
        <a class="chip <?= $vista === 'bitacora' ? 'active' : '' ?>" href="workflow.php?vista=bitacora">Bitácora real</a>
    </section>

    <?php if ($vista === 'normal'): ?>
        <section class="section-title left compact-title">
            <h2>Vista normal del workflow</h2>
            <p>Equivale a la tabla de procesos: flujo, proceso, siguiente proceso, tipo, rol y pantalla.</p>
        </section>

        <section class="table-card spacing-bottom">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>codFlujo</th>
                            <th>Trámite</th>
                            <th>codProceso</th>
                            <th>codProcesoSiguiente</th>
                            <th>Tipo</th>
                            <th>Rol</th>
                            <th>Pantalla</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($procesos as $p): ?>
                            <tr>
                                <td><strong><?= e((string)$p['codFlujo']) ?></strong></td>
                                <td><?= e((string)$p['nombreFlujo']) ?></td>
                                <td><?= e((string)$p['codProceso']) ?></td>
                                <td><?= e((string)($p['codProcesoSiguiente'] ?? 'NULL')) ?></td>
                                <td><?= e((string)$p['tipo']) ?> - <?= e(nombreTipoProceso((string)$p['tipo'])) ?></td>
                                <td><?= e((string)$p['codRol']) ?> - <?= e(nombreRolWorkflow((string)$p['codRol'])) ?></td>
                                <td><code><?= e((string)$p['pantalla']) ?></code></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($vista === 'detallado'): ?>
        <section class="section-title left compact-title">
            <h2>Vista detallada por trámite</h2>
            <p>Muestra cada flujo completo con su descripción y orden de avance.</p>
        </section>

        <?php foreach ($agrupados as $codFlujo => $items): ?>
            <section class="workflow-flow-card">
                <div class="workflow-header">
                    <div>
                        <span class="badge"><?= e((string)$codFlujo) ?></span>
                        <h2><?= e((string)($items[0]['nombreFlujo'] ?? 'Flujo')) ?></h2>
                    </div>
                    <p><?= count($items) ?> procesos</p>
                </div>

                <div class="workflow-steps">
                    <?php foreach ($items as $p): ?>
                        <article class="workflow-step <?= strtolower((string)$p['tipo']) ?>">
                            <div class="step-code"><?= e((string)$p['codProceso']) ?></div>
                            <div class="step-body">
                                <h3><?= e(nombreTipoProceso((string)$p['tipo'])) ?> - <?= e(nombreRolWorkflow((string)$p['codRol'])) ?></h3>
                                <p><?= e((string)$p['descripcion']) ?></p>
                                <small>
                                    Pantalla: <code><?= e((string)$p['pantalla']) ?></code> |
                                    Siguiente: <strong><?= e((string)($p['codProcesoSiguiente'] ?? 'Fin')) ?></strong>
                                </small>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if ($vista === 'bitacora'): ?>
        <section class="section-title left compact-title">
            <h2>Bitácora real del sistema</h2>
            <p>Esta vista usa <code>data/flujos.json</code> y muestra lo que realmente hizo cada usuario.</p>
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
                            <strong>Usuario:</strong> <?= e((string)$flujo['nombre']) ?> |
                            <strong>CI:</strong> <?= e((string)$flujo['ci']) ?> |
                            <strong>Rol:</strong> <?= e((string)$flujo['rol']) ?>
                        </p>
                        <p>
                            <strong>Trámite:</strong> <?= e((string)$flujo['tramite']) ?> |
                            <strong>Flujo:</strong> <?= e((string)($flujo['codFlujo'] ?? '')) ?> |
                            <strong>Proceso:</strong> <?= e((string)($flujo['codProceso'] ?? '')) ?>
                        </p>
                        <p><?= e((string)$flujo['detalle']) ?></p>
                        <span class="<?= e(claseEstado((string)$flujo['estado'])) ?>"><?= e((string)$flujo['estado']) ?></span>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</main>
</body>
</html>
