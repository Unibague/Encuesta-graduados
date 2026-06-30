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

QRCode.toDataURL(url, {
  width: 400,
  margin: 2,
  color: { dark: '#1a1a2e', light: '#ffffff' },
  errorCorrectionLevel: 'H',
}, (err, dataUrl) => {
  if (err) { console.error('[QR] Error:', err); process.exit(1); }

  // Leer y actualizar el HTML
  let html = fs.readFileSync(htmlOut, 'utf8');

  // Actualizar la data URI del QR
  html = html.replace(
    /const QR_B64 = 'data:image\/png;base64,[^']+'/,
    `const QR_B64 = '${dataUrl}'`
  );

  // Actualizar la etiqueta de URL
  html = html.replace(
    /<div class="url-text"[^>]*>.*?<\/div>/,
    `<div class="url-text" id="urlLabel">${url}</div>`
  );

  fs.writeFileSync(htmlOut, html, 'utf8');
  console.log('[QR] Listo. Abre qr-encuentro.html en tu navegador para imprimir.');
  console.log('[QR] O visita: http://localhost:8000/qr-encuentro.html');
});
