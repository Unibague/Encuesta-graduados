<?php
/**
 * Genera una copia local del paquete "country-state-city" como módulos ES
 * nativos (con extensión .js en cada import y los JSON convertidos a
 * módulos JS), para poder servirlos desde el propio dominio en vez de
 * depender de un CDN externo (jsdelivr) al cargar registrograduados.php.
 *
 * El paquete original usa imports sin extensión (`./country`) y JSON
 * importado directamente (`./assets/country.json`), algo que solo
 * funciona porque jsdelivr transforma/empaqueta el código; un navegador
 * cargando el archivo tal cual no lo resuelve. Aquí se reescribe a algo
 * que un <script type="module"> entiende sin necesidad de bundler.
 *
 * Uso: php tools/build-country-state-city-esm.php
 */

$src = __DIR__ . '/country-state-city/lib';
$dest = dirname(__DIR__) . '/assets/js/country-state-city';

if (!is_dir($src)) {
    fwrite(STDERR, "No se encontró {$src}\n");
    exit(1);
}

if (!is_dir($dest)) {
    mkdir($dest, 0777, true);
}

function writeFile(string $path, string $content): void
{
    file_put_contents($path, $content);
    echo "  -> " . basename($path) . ' (' . number_format(strlen($content)) . " bytes)\n";
}

echo "Generando datos (JSON -> módulo JS)...\n";
foreach (['country', 'state', 'city'] as $name) {
    $json = file_get_contents("{$src}/assets/{$name}.json");
    writeFile("{$dest}/{$name}-data.js", 'export default ' . $json . ';' . PHP_EOL);
}

echo "Copiando utils...\n";
writeFile("{$dest}/utils.js", file_get_contents("{$src}/utils/index.js"));

echo "Reescribiendo módulos...\n";

$country = file_get_contents("{$src}/country.js");
$country = str_replace("from './assets/country.json'", "from './country-data.js'", $country);
$country = str_replace("from './utils'", "from './utils.js'", $country);
writeFile("{$dest}/country.js", $country);

$state = file_get_contents("{$src}/state.js");
$state = str_replace("from './assets/state.json'", "from './state-data.js'", $state);
$state = str_replace("from './utils'", "from './utils.js'", $state);
writeFile("{$dest}/state.js", $state);

$city = file_get_contents("{$src}/city.js");
$city = str_replace("from './assets/city.json'", "from './city-data.js'", $city);
$city = str_replace("from './utils'", "from './utils.js'", $city);
writeFile("{$dest}/city.js", $city);

$index = file_get_contents("{$src}/index.js");
$index = str_replace("from './country'", "from './country.js'", $index);
$index = str_replace("from './state'", "from './state.js'", $index);
$index = str_replace("from './city'", "from './city.js'", $index);
writeFile("{$dest}/index.js", $index);

echo "Listo: {$dest}\n";
