/**
 * Elimina filas duplicadas en las hojas del Encuentro de Graduados 2026,
 * dejando únicamente la última fila (la más reciente) de cada persona.
 *
 * Cómo usarlo:
 * 1. Abre el Google Sheet "Lista asistencia graduados".
 * 2. Ve a Extensiones > Apps Script.
 * 3. Pega este archivo (o impórtalo) y guarda.
 * 4. Para revisar ANTES de borrar (recomendado): elige "previsualizarDuplicadosTodo"
 *    y presiona Ejecutar. Esto NO borra nada, solo lista qué se borraría.
 * 5. Para revisar el resultado: ícono de reloj ⏱ "Ejecuciones" en el menú izquierdo,
 *    clic en la ejecución más reciente para ver el registro (Logger.log).
 * 6. Cuando confirmes que la lista tiene sentido, elige "eliminarDuplicadosTodo"
 *    y presiona Ejecutar para borrar de verdad.
 */

const DEDUPE_CONFIG = {
  ASISTENTES_GID: 1419700379, // hoja principal (Nombre, Celular, Correo, Acompañantes, ¿Asistió?, Total)
  NO_ASISTENTES_TITLE: 'No asistentes', // hoja de quienes marcaron "No podré asistir"
  FIRST_DATA_ROW: 4, // los datos empiezan en la fila 4 (A4)
};

function previsualizarDuplicadosTodo() {
  procesarTodo(true);
}

function eliminarDuplicadosTodo() {
  procesarTodo(false);
}

function procesarTodo(dryRun) {
  const ss = SpreadsheetApp.getActiveSpreadsheet();

  const sheetAsistentes = ss.getSheets().find(
    s => s.getSheetId() === DEDUPE_CONFIG.ASISTENTES_GID
  );
  if (sheetAsistentes) {
    eliminarDuplicadosEnHoja(sheetAsistentes, 5, dryRun); // A:Nombre B:Celular C:Correo D:Acomp E:¿Asistió?
  } else {
    Logger.log('No se encontró la hoja de asistentes (gid ' + DEDUPE_CONFIG.ASISTENTES_GID + ').');
  }

  const sheetNoAsistentes = ss.getSheets().find(
    s => s.getName().trim().toLowerCase() === DEDUPE_CONFIG.NO_ASISTENTES_TITLE.toLowerCase()
  );
  if (sheetNoAsistentes) {
    eliminarDuplicadosEnHoja(sheetNoAsistentes, 3, dryRun); // A:Nombre B:Celular C:Correo
  } else {
    Logger.log('No se encontró la hoja "' + DEDUPE_CONFIG.NO_ASISTENTES_TITLE + '".');
  }
}

/**
 * Revisa una hoja específica y determina qué filas son duplicadas, dejando la fila
 * más abajo (la más reciente) de cada persona. La clave de "misma persona" es el
 * correo; si una fila no tiene correo, se usa el nombre como respaldo.
 * Si dryRun es true, solo lista lo que borraría, sin tocar la hoja.
 */
function eliminarDuplicadosEnHoja(sheet, numColumnas, dryRun) {
  const primeraFila = DEDUPE_CONFIG.FIRST_DATA_ROW;
  const lastRow = sheet.getLastRow();

  if (lastRow < primeraFila) {
    Logger.log(`"${sheet.getName()}": no hay datos para revisar.`);
    return;
  }

  const range = sheet.getRange(primeraFila, 1, lastRow - primeraFila + 1, numColumnas);
  const values = range.getDisplayValues();

  // 1) Encontrar la ÚLTIMA fila (más abajo) de cada clave
  const ultimaFilaPorClave = {};
  values.forEach((fila, i) => {
    const numeroFila = primeraFila + i;
    const clave = normalizarClave(fila);
    if (!clave) return;
    ultimaFilaPorClave[clave] = numeroFila; // se sobreescribe -> queda la última aparición
  });

  // 2) Marcar para borrar todo lo que NO sea la última fila de su clave
  const filasABorrar = [];
  values.forEach((fila, i) => {
    const numeroFila = primeraFila + i;
    const clave = normalizarClave(fila);
    if (!clave) return;
    if (ultimaFilaPorClave[clave] !== numeroFila) {
      filasABorrar.push({ fila: numeroFila, nombre: fila[0] || '', correo: fila[2] || '' });
    }
  });

  if (!filasABorrar.length) {
    Logger.log(`"${sheet.getName()}": no se encontraron duplicados.`);
    return;
  }

  const etiqueta = dryRun ? '(vista previa, no se borró nada)' : 'eliminada(s)';
  Logger.log(`"${sheet.getName()}": ${filasABorrar.length} fila(s) duplicada(s) ${etiqueta}:`);
  filasABorrar.forEach(f => Logger.log(`  - Fila ${f.fila}: ${f.nombre} | ${f.correo}`));

  if (dryRun) return;

  // 3) Borrar de abajo hacia arriba para no desfasar los números de fila
  filasABorrar
    .map(f => f.fila)
    .sort((a, b) => b - a)
    .forEach(fila => sheet.deleteRow(fila));
}

function normalizarClave(fila) {
  const correo = String(fila[2] || '').trim().toLowerCase();
  if (correo) return 'correo:' + correo;

  const nombre = String(fila[0] || '').trim().toLowerCase().replace(/\s+/g, ' ');
  return nombre ? 'nombre:' + nombre : '';
}
