/**
 * Generador de QR Code para el Encuentro de Graduados
 *
 * Uso:
 *   node tools/gen-qr.js                                              → localhost por defecto
 *   node tools/gen-qr.js https://mi-dominio.com/encuentro.php         → URL personalizada
 *
 * El script actualiza automáticamente qr-encuentro.html con el nuevo QR.
 */
'use strict';

const { execSync } = require('child_process');
const fs   = require('fs');
const path = require('path');

// Instalar qrcode si no está disponible
try { require('qrcode'); }
catch (_) {
  console.log('[QR] Instalando dependencia qrcode...');
  execSync('npm install qrcode', { stdio: 'inherit', cwd: path.join(__dirname, '..') });
}

const QRCode  = require('qrcode');
const url     = process.argv[2] || 'http://localhost:8000/encuentro.php';
const htmlOut = path.join(__dirname, '..', 'qr-encuentro.html');

console.log('[QR] Generando código para:', url);

// SVG para visualización (vectorial, escala perfecta)
QRCode.toString(url, {
  type: 'svg',
  width: 300,
  margin: 2,
  color: { dark: '#1a1a2e', light: '#ffffff' },
  errorCorrectionLevel: 'H',
}, (err, svg) => {
  if (err) { console.error('[QR] Error SVG:', err); process.exit(1); }

  // PNG alta resolución para descarga
  QRCode.toDataURL(url, {
    width: 600,
    margin: 2,
    scale: 8,
    color: { dark: '#1a1a2e', light: '#ffffff' },
    errorCorrectionLevel: 'H',
  }, (err2, pngDataUrl) => {
    if (err2) { console.error('[QR] Error PNG:', err2); process.exit(1); }

    let html = fs.readFileSync(htmlOut, 'utf8');

    // Reemplazar el bloque SVG inline
    html = html.replace(/<svg[\s\S]*?<\/svg>/, svg);

    // Reemplazar el PNG para descarga
    html = html.replace(
      /const PNG_DATA = '[^']+'/,
      `const PNG_DATA = '${pngDataUrl}'`
    );

    // Actualizar la etiqueta de URL
    html = html.replace(
      /<div class="url-text">[^<]+<\/div>/,
      `<div class="url-text">${url}</div>`
    );

    fs.writeFileSync(htmlOut, html, 'utf8');
    const sizeKB = (fs.statSync(htmlOut).size / 1024).toFixed(1);
    console.log(`[QR] Listo (${sizeKB} KB). Abre qr-encuentro.html en tu navegador.`);
    console.log('[QR] O visita: http://localhost:8000/qr-encuentro.html');
  });
});
