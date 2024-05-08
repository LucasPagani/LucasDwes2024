<?php
// Ruta al archivo de texto con la lista de palabras
$ruta_archivo_txt = 'palabras.txt';

// Ruta al archivo JSON de salida
$ruta_archivo_json = 'palabras.json';

// Lee el contenido del archivo de texto
$contenido_txt = file_get_contents($ruta_archivo_txt);

// Divide el contenido en líneas y elimina los espacios en blanco alrededor
$lineas = array_map('trim', explode("\n", $contenido_txt));

// Crea un array asociativo con las palabras
$datos_json = ['palabras' => $lineas];

// Convierte el array en formato JSON
$json = json_encode($datos_json, JSON_PRETTY_PRINT);

// Escribe el JSON en el archivo
file_put_contents($ruta_archivo_json, $json);

echo "El archivo JSON se ha creado correctamente.";
?>
