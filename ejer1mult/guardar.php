<?php
require_once __DIR__ . '/includes/functions.php';
requerirRol(['estudiante']);
refrescarSesionDesdeUsuario();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$usuario = usuarioActual();
$tipo = $_POST['tipo'] ?? '';
$tramite = obtenerTramite($tipo);

if (!$tramite) {
    header('Location: index.php?mensaje=' . urlencode('Tipo de trámite no válido.'));
    exit;
}

if (!empty($tramite['requiere_matricula']) && !estudianteEstaMatriculado((string)$usuario['ci'])) {
    registrarFlujo('Validacion de matricula', 'No se permitió registrar inscripción porque el estudiante no está matriculado.', $tramite['titulo'], 'Observado', null, null, 'F1', 'P1');
    header('Location: matriculacion.php?mensaje=' . urlencode('Debe matricularse antes de registrar inscripción de materias.'));
    exit;
}

$errores = validarSolicitud($tipo, $_POST);

if (!empty($errores)) {
    $_SESSION['errores'] = $errores;
    $_SESSION['old'] = $_POST;
    $codFlujo = workflowCodigoPorTipo((string)$tipo);
    $codProceso = $tipo === 'inscripcion' ? 'P3' : 'P2';
    registrarFlujo('Validacion de formulario', 'El formulario tenía campos incompletos o inválidos.', $tramite['titulo'], 'Observado', null, null, $codFlujo, $codProceso);
    header('Location: registrar.php?tipo=' . urlencode($tipo));
    exit;
}

$solicitudes = leerSolicitudes($tipo);
$id = generarId($solicitudes);
$solicitud = construirSolicitud($tipo, $_POST, $tramite, $id);
$solicitudes[] = $solicitud;

guardarSolicitudes($tipo, $solicitudes);
$codFlujo = workflowCodigoPorTipo((string)$tipo);
$codProceso = $tipo === 'inscripcion' ? 'P3' : 'P2';
registrarFlujo('Solicitud registrada', 'Se registró la solicitud #' . $id . ' y quedó en estado Pendiente.', $tramite['titulo'], 'Pendiente', null, null, $codFlujo, $codProceso);

header('Location: mis_solicitudes.php?mensaje=' . urlencode('Solicitud registrada correctamente.'));
exit;
