<?php

function loadEnvVars()
{
    $env_file = dirname(__FILE__) . '/.env';

    if (file_exists($env_file)) {
        $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Saltar comentarios y líneas vacías
            if (strpos($line, '#') === 0 || trim($line) === '') {
                continue;
            }

            // Parsear conjuntos clave=valor
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Quitar comillas
            $value = trim($value, '"\'');

            // Establecer variable de entorno
            if (!isset($_ENV[$key]) && !getenv($key)) {
                $_ENV['YG_'.$key] = $value;
                putenv("YG_$key=$value");
            }
        }
    }
}

// Cargar variables del archivo
loadEnvVars();
