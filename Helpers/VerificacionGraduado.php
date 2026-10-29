<?php

require_once __DIR__ . '/SigaGraduado.php';
require_once __DIR__ . '/EndpointAlternoGraduado.php';

/**
 * Verificacion unica de graduados/egresados, compartida por el registro al
 * Encuentro y la actualizacion de datos. Solo consulta los endpoints de
 * academia, en orden:
 *   1. SIGA (graduado/egresado)
 *   2. Endpoint alterno (ver_graduados_bd.php)
 *   3. Existencia en SIGA (consulta_estudiante.php), sin exigir grado
 * y se detiene en la primera fuente que reconozca el documento.
 */
function verificarElegibilidadGraduado(string $cedula): object
{
    $siga = consultarElegibilidadGraduadoSiga($cedula);
    if ($siga->eligible) {
        return (object) [
            'available' => true,
            'eligible' => true,
            'graduated' => $siga->graduated,
            'status' => 'graduado',
            'data' => $siga->data,
        ];
    }

    $alterno = consultarGraduadoEndpointAlterno($cedula);
    if ($alterno->eligible) {
        return (object) [
            'available' => true,
            'eligible' => true,
            'graduated' => false,
            'status' => 'alterno',
            'data' => $alterno->data,
        ];
    }

    // No se exige estar graduado: basta con que el documento exista en SIGA.
    $existencia = consultarExistenciaSiga($cedula);
    if ($existencia->exists) {
        return (object) [
            'available' => true,
            'eligible' => true,
            'graduated' => false,
            'status' => 'registrado_siga',
            'data' => $existencia->data,
        ];
    }

    return (object) [
        'available' => $siga->available || $alterno->available || $existencia->available,
        'eligible' => false,
        'graduated' => false,
        'status' => 'no_encontrado',
        'data' => [],
    ];
}
