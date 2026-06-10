<?php
require_once __DIR__ . '/includes/functions.php';
requerirRol(['kardex']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listar.php');
    exit;
}

$tipo = $_POST['tipo'] ?? '';
$id = (int)($_POST['id'] ?? 0);
$estado = $_POST['estado'] ?? '';
$tramite = obtenerTramite($tipo);

if (!$tramite || $id <= 0 || !in_array($estado, estadosPermitidos(), true)) {
    header('Location: listar.php?mensaje=' . urlencode('No se pudo actualizar la solicitud.'));
    exit;
}

$solicitudes = leerSolicitudes($tipo);
$actualizado = false;
$ciEstudiante = '';
$nombreEstudiante = '';

foreach ($solicitudes as &$solicitud) {
    if ((int)($solicitud['id'] ?? 0) === $id) {
        $solicitud['estado'] = $estado;
        $solicitud['fecha_actualizacion'] = date('Y-m-d H:i:s');
        $ciEstudiante = (string)($solicitud['ci'] ?? '');
        $nombreEstudiante = (string)($solicitud['nombre'] ?? '');
        $actualizado = true;
        break;
    }
}
unset($solicitud);

if ($actualizado) {
    guardarSolicitudes($tipo, $solicitudes);
    $codFlujo = workflowCodigoPorTipo((string)$tipo);
    $codProceso = $tipo === 'inscripcion' ? 'P5' : 'P4';
    registrarFlujo('Cambio de estado por Kardex', 'Kardex cambió la solicitud #' . $id . ' al estado: ' . $estado . '.', $tramite['titulo'], $estado, $ciEstudiante, $nombreEstudiante, $codFlujo, $codProceso);
    header('Location: listar.php?tipo=' . urlencode($tipo) . '&mensaje=' . urlencode('Estado actualizado correctamente.'));
    exit;
}

header('Location: listar.php?tipo=' . urlencode($tipo) . '&mensaje=' . urlencode('Solicitud no encontrada.'));
exit;
