const RESTORE_CONFIG = {
  ENDPOINT: "https://encuestas-graduados.unibague.edu.co/api/form-response.php",
  SECRET_TOKEN: "PEGAR_TOKEN_AQUI",
  BATCH_SIZE: 50,
  SLEEP_MS: 1500,
  PROGRESS_KEY: "RESTORE_FULL_LAST_ROW",
};

function restoreFullRowsFromSheet() {
  const sheet = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();
  const allData = sheet.getDataRange().getDisplayValues();
  const headers = allData[0].map((header) => String(header || "").trim());
  const rows = allData.slice(1);

  const props = PropertiesService.getScriptProperties();
  const startIndex = Number(props.getProperty(RESTORE_CONFIG.PROGRESS_KEY)) || 0;
  const endIndex = Math.min(startIndex + RESTORE_CONFIG.BATCH_SIZE, rows.length);

  Logger.log(`Restaurando filas ${startIndex + 2} a ${endIndex + 1}`);

  for (let i = startIndex; i < endIndex; i++) {
    const row = rows[i];
    const fila = i + 2;
    const answers = buildAnswers(headers, row);
    const documento = findDocumento(headers, row);

    if (!documento) {
      Logger.log(`Fila ${fila}: sin documento, omitida`);
      continue;
    }

    const payload = {
      restore_full_row: true,
      restore_at: new Date().toISOString(),
      answers,
    };

    try {
      const response = UrlFetchApp.fetch(RESTORE_CONFIG.ENDPOINT, {
        method: "post",
        contentType: "application/json",
        headers: { "X-API-TOKEN": RESTORE_CONFIG.SECRET_TOKEN },
        payload: JSON.stringify(payload),
        muteHttpExceptions: true,
      });

      Logger.log(
        `Fila ${fila} | ${documento} -> HTTP ${response.getResponseCode()} | ` +
          response.getContentText().substring(0, 200)
      );
    } catch (error) {
      props.setProperty(RESTORE_CONFIG.PROGRESS_KEY, String(i));
      Logger.log(`Fila ${fila} | ${documento} -> ERROR: ${error}`);
      return;
    }

    Utilities.sleep(RESTORE_CONFIG.SLEEP_MS);
  }

  if (endIndex >= rows.length) {
    props.deleteProperty(RESTORE_CONFIG.PROGRESS_KEY);
    Logger.log("Restauracion completa.");
  } else {
    props.setProperty(RESTORE_CONFIG.PROGRESS_KEY, String(endIndex));
    Logger.log(`Progreso guardado. Proxima ejecucion inicia en fila ${endIndex + 2}.`);
  }
}

function buildAnswers(headers, row) {
  const answers = {};

  headers.forEach((header, index) => {
    if (!header) return;
    answers[header] = [String(row[index] || "").trim()];
  });

  return answers;
}

function findDocumento(headers, row) {
  const docKeys = new Set([
    "numero de identificacion",
    "número de identificación",
    "documento de identidad",
    "documento",
    "cedula",
    "cédula",
  ]);

  for (let index = 0; index < headers.length; index++) {
    const key = headers[index].toLowerCase().replace(/\s+/g, " ").trim();
    if (docKeys.has(key)) {
      return String(row[index] || "").trim();
    }
  }

  return "";
}

function resetRestoreFullRows() {
  PropertiesService.getScriptProperties().deleteProperty(RESTORE_CONFIG.PROGRESS_KEY);
  Logger.log("Progreso de restauracion reiniciado.");
}
