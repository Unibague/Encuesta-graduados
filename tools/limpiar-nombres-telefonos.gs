/**
 * Limpieza masiva de Nombres, Apellidos y Teléfonos en una hoja de Google Sheets.
 *
 * Qué hace:
 * - Nombres / Apellidos (y cualquier columna cuyo encabezado contenga "nombre"
 *   o "apellido"): quita tildes y la ñ (queda como N), quita cualquier caracter
 *   que no sea letra o espacio, y pasa todo a MAYÚSCULAS.
 * - Teléfonos (columnas cuyo encabezado contenga "elefono", "elular" o
 *   "whatsapp"): deja solo dígitos y les antepone "57" (sin espacios), sin
 *   duplicarlo si ya lo tienen.
 *
 * Pensado para hojas de 20 000+ filas: procesa por lotes y guarda el progreso
 * en PropertiesService (igual que restore-full-from-sheet.gs). Si Apps Script
 * corta la ejecución (límite de 6 minutos) o se cierra la pestaña, la función
 * se vuelve a lanzar sola mediante un disparador (trigger) hasta terminar.
 *
 * Cómo usarlo:
 * 1. Abre el Google Sheet.
 * 2. Extensiones > Apps Script, pega este archivo y guarda.
 * 3. Selecciona la función "iniciarLimpieza" y presiona Ejecutar (autoriza
 *    los permisos que pida la primera vez).
 * 4. Déjalo trabajando: se reanuda solo cada ~15 segundos hasta terminar
 *    todas las filas. Puedes ver el progreso en Ejecuciones (icono de reloj)
 *    o con la función "verProgreso".
 * 5. Si necesitas parar el disparador automático sin perder el progreso,
 *    ejecuta "detenerLimpieza". Para retomar, vuelve a ejecutar "iniciarLimpieza".
 * 6. Para volver a procesar todo desde cero, ejecuta "reiniciarProgreso".
 */

const LIMPIEZA_CONFIG = {
  HEADER_SEARCH_ROWS: 5,            // busca la fila de encabezados en las primeras N filas
  BATCH_SIZE: 1000,                 // filas que se leen/escriben juntas por columna
  MAX_RUNTIME_MS: 4.5 * 60 * 1000,  // margen de seguridad (el límite real de Apps Script es 6 min)
  RETRY_DELAY_MS: 15 * 1000,        // espera antes de reanudar automáticamente
  TRIGGER_FN: 'continuarLimpieza',
  PROP_LAST_ROW: 'LIMPIEZA_ULTIMA_FILA',
  PROP_HEADER_ROW: 'LIMPIEZA_FILA_ENCABEZADO',
};

function iniciarLimpieza() {
  borrarTriggersLimpieza();
  procesarLoteLimpieza();
}

function continuarLimpieza() {
  procesarLoteLimpieza();
}

function reiniciarProgreso() {
  const props = PropertiesService.getScriptProperties();
  props.deleteProperty(LIMPIEZA_CONFIG.PROP_LAST_ROW);
  props.deleteProperty(LIMPIEZA_CONFIG.PROP_HEADER_ROW);
  borrarTriggersLimpieza();
  Logger.log('Progreso reiniciado. Vuelve a ejecutar "iniciarLimpieza".');
}

function detenerLimpieza() {
  borrarTriggersLimpieza();
  Logger.log('Disparador automático eliminado. El progreso guardado se conserva; ejecuta "iniciarLimpieza" para retomar.');
}

function verProgreso() {
  const props = PropertiesService.getScriptProperties();
  const sheet = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();
  const ultimaFila = props.getProperty(LIMPIEZA_CONFIG.PROP_LAST_ROW);
  Logger.log(`Última fila procesada: ${ultimaFila || '(sin iniciar)'} de ${sheet.getLastRow()} filas totales.`);
}

function borrarTriggersLimpieza() {
  ScriptApp.getProjectTriggers().forEach(t => {
    if (t.getHandlerFunction() === LIMPIEZA_CONFIG.TRIGGER_FN) ScriptApp.deleteTrigger(t);
  });
}

// Rango Unicode de los "diacriticos combinados" (tildes, virgulilla de la ñ, etc.)
// que quedan sueltos tras normalize('NFD'). Se arma con fromCharCode para evitar
// problemas de codificacion del propio archivo fuente.
const DIACRITICOS_COMBINADOS = new RegExp(
  '[' + String.fromCharCode(0x0300) + '-' + String.fromCharCode(0x036f) + ']',
  'g'
);

function normalizarTexto(valor) {
  return String(valor || '')
    .normalize('NFD')
    .replace(DIACRITICOS_COMBINADOS, '')
    .toLowerCase();
}

function limpiarNombre(valor) {
  if (valor === '' || valor === null || valor === undefined) return valor;
  let s = String(valor);
  s = s.normalize('NFD').replace(DIACRITICOS_COMBINADOS, ''); // quita tildes y disuelve la ñ en "n"
  s = s.replace(/[^a-zA-Z\s]/g, '');                       // quita cualquier caracter que no sea letra o espacio
  s = s.replace(/\s+/g, ' ').trim();
  return s.toUpperCase();
}

function limpiarTelefono(valor) {
  if (valor === '' || valor === null || valor === undefined) return valor;
  const digitos = String(valor).replace(/\D/g, '');
  if (digitos === '') return '';
  if (digitos.startsWith('57') && digitos.length === 12) {
    return digitos; // ya tiene el indicativo (57 + 10 dígitos de celular colombiano)
  }
  return '57' + digitos;
}

function encontrarFilaEncabezadoLimpieza(sheet) {
  const props = PropertiesService.getScriptProperties();
  const guardada = props.getProperty(LIMPIEZA_CONFIG.PROP_HEADER_ROW);
  if (guardada) return parseInt(guardada, 10);

  const maxFilas = Math.min(LIMPIEZA_CONFIG.HEADER_SEARCH_ROWS, sheet.getLastRow());
  const valores = sheet.getRange(1, 1, maxFilas, sheet.getLastColumn()).getValues();
  for (let i = 0; i < valores.length; i++) {
    const fila = valores[i].map(normalizarTexto);
    if (fila.some(v => v.indexOf('nombre') !== -1)) {
      props.setProperty(LIMPIEZA_CONFIG.PROP_HEADER_ROW, String(i + 1));
      return i + 1;
    }
  }
  throw new Error('No se encontró la fila de encabezados (se buscó una columna que contenga "Nombre").');
}

function detectarColumnasLimpieza(sheet, filaEncabezado) {
  const encabezados = sheet.getRange(filaEncabezado, 1, 1, sheet.getLastColumn()).getValues()[0];
  const columnas = { nombres: [], telefonos: [] };
  encabezados.forEach((h, idx) => {
    const norm = normalizarTexto(h);
    if (!norm) return;
    if (norm.indexOf('nombre') !== -1 || norm.indexOf('apellido') !== -1) {
      columnas.nombres.push(idx + 1);
    } else if (norm.indexOf('elefono') !== -1 || norm.indexOf('elular') !== -1 || norm.indexOf('whatsapp') !== -1) {
      columnas.telefonos.push(idx + 1);
    }
  });
  return columnas;
}

function procesarLoteLimpieza() {
  const startTime = Date.now();
  const sheet = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();
  const props = PropertiesService.getScriptProperties();

  const filaEncabezado = encontrarFilaEncabezadoLimpieza(sheet);
  const columnas = detectarColumnasLimpieza(sheet, filaEncabezado);

  if (!columnas.nombres.length && !columnas.telefonos.length) {
    Logger.log('No se encontraron columnas de nombres/apellidos ni de teléfono. Revisa los encabezados.');
    return;
  }

  const lastRow = sheet.getLastRow();
  const primeraFilaDatos = filaEncabezado + 1;
  let fila = parseInt(props.getProperty(LIMPIEZA_CONFIG.PROP_LAST_ROW), 10) || primeraFilaDatos;

  if (fila > lastRow) {
    finalizarLimpieza(props);
    return;
  }

  while (fila <= lastRow) {
    const filasRestantes = lastRow - fila + 1;
    const tam = Math.min(LIMPIEZA_CONFIG.BATCH_SIZE, filasRestantes);

    columnas.nombres.forEach(col => {
      const rango = sheet.getRange(fila, col, tam, 1);
      const valores = rango.getValues();
      for (let i = 0; i < valores.length; i++) {
        valores[i][0] = limpiarNombre(valores[i][0]);
      }
      rango.setValues(valores);
    });

    columnas.telefonos.forEach(col => {
      const rango = sheet.getRange(fila, col, tam, 1);
      const valores = rango.getValues();
      for (let i = 0; i < valores.length; i++) {
        valores[i][0] = limpiarTelefono(valores[i][0]);
      }
      rango.setValues(valores);
    });

    fila += tam;
    props.setProperty(LIMPIEZA_CONFIG.PROP_LAST_ROW, String(fila));

    if (Date.now() - startTime > LIMPIEZA_CONFIG.MAX_RUNTIME_MS) {
      break;
    }
  }

  if (fila > lastRow) {
    finalizarLimpieza(props);
  } else {
    borrarTriggersLimpieza();
    ScriptApp.newTrigger(LIMPIEZA_CONFIG.TRIGGER_FN)
      .timeBased()
      .after(LIMPIEZA_CONFIG.RETRY_DELAY_MS)
      .create();
    Logger.log(`Progreso guardado en la fila ${fila} de ${lastRow}. Continuará automáticamente en ~${LIMPIEZA_CONFIG.RETRY_DELAY_MS / 1000}s.`);
  }
}

function finalizarLimpieza(props) {
  borrarTriggersLimpieza();
  props.deleteProperty(LIMPIEZA_CONFIG.PROP_LAST_ROW);
  Logger.log('Limpieza completada para todas las filas.');
}
