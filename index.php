<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// The project is mounted below the domain document root at /pilotfb.
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/pilotfb/index.php';
$mountPath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

if ($mountPath !== '' && $mountPath !== '/' && str_starts_with($requestUri, $mountPath)) {
    $path = substr($requestUri, strlen($mountPath)) ?: '/';
    $_SERVER['REQUEST_URI'] = $path;
}

require __DIR__.'/public/index.php';
