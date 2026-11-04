<?php

require_once __DIR__ . '/SigaGraduado.php';
require_once __DIR__ . '/EndpointAlternoGraduado.php';
require_once __DIR__ . '/ListaManualGraduados.php';

// Hoja (primera pestana) con las cedulas habilitadas a mano para la
// actualizacion de datos. Debe estar compartida con la cuenta de servicio.
const LISTA_MANUAL_ACTUALIZACION_SPREADSHEET_ID = '13XhASU-81uOb5mD2KyCHPMdaT7uxk8OvlURxAcptB1U';

/**
 * Verificacion de graduados/egresados para la actualizacion de datos, con los
 * mismos niveles del registro al Encuentro, en orden:
 *   1. SIGA (graduado/egresado)
 *   2. Endpoint alterno (ver_graduados_bd.php)
 *   3. Lista manual de la actualizacion de datos en Google Sheets, donde el
 *      administrador agrega la cedula de quienes valido como graduados.
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

    $lista = consultarListaManualGraduados($cedula, LISTA_MANUAL_ACTUALIZACION_SPREADSHEET_ID);
    if ($lista->eligible) {
        return (object) [
            'available' => true,
            'eligible' => true,
            'graduated' => false,
            'status' => 'lista_manual',
            // La hoja de actualizacion solo tiene la cedula; no se precarga nada.
            'data' => [],
        ];
    }

    return (object) [
        'available' => $siga->available || $alterno->available || $lista->available,
        'eligible' => false,
        'graduated' => false,
        'status' => 'no_encontrado',
        'data' => [],
    ];
}
