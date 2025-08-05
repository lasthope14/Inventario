<?php

// Archivo de redirección para análisis de documentos duplicados
// Este archivo simplifica el acceso desde cPanel

// Detectar si estamos en un subdominio
$baseUrl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$baseUrl .= $_SERVER['HTTP_HOST'];

// Si estamos en un subdirectorio, agregarlo
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
if ($scriptPath !== '/') {
    $baseUrl .= $scriptPath;
}

// Redireccionar a la ruta Laravel
$redirectUrl = $baseUrl . '/duplicados';

// Si ya estamos en la ruta correcta, incluir el bootstrap de Laravel
if (parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) === '/duplicados.php') {
    header('Location: ' . $redirectUrl, true, 301);
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análisis de Documentos Duplicados - Hidroobras</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚨 Análisis de Documentos Duplicados</h1>
        <h2>Sistema de Inventario Hidroobras</h2>
        
        <div class="alert">
            <strong>Problema Crítico Detectado:</strong><br>
            Se identificaron documentos duplicados que pueden estar sobrescribiendo archivos importantes.
        </div>
        
        <p>Para acceder al sistema completo de análisis de documentos, haga clic en el siguiente enlace:</p>
        
        <a href="<?php echo $redirectUrl; ?>" class="btn">
            🔍 Acceder al Análisis Completo
        </a>
        
        <p><small>Este sistema le permitirá:</small></p>
        <ul style="text-align: left; max-width: 400px; margin: 0 auto;">
            <li>Identificar documentos duplicados</li>
            <li>Detectar archivos faltantes</li>
            <li>Limpiar registros huérfanos</li>
            <li>Generar reportes completos</li>
            <li>Implementar soluciones automáticas</li>
        </ul>
        
        <hr style="margin: 30px 0;">
        
        <h3>¿Qué pasó?</h3>
        <p style="text-align: left;">
            Los documentos se guardaban con nombres idénticos (como "hoja de vida.pdf") sin considerar 
            a qué elemento pertenecían. Esto causó que:
        </p>
        <ul style="text-align: left; max-width: 500px; margin: 0 auto;">
            <li>El archivo de "hoja de vida.pdf" del arnés se sobrescribiera con el de la eslinga</li>
            <li>Se perdieran documentos importantes</li>
            <li>Múltiples elementos quedaran sin sus documentos originales</li>
        </ul>
        
        <h3>✅ Solución Implementada</h3>
        <p style="text-align: left;">
            Ahora los archivos se guardan con formato: <strong>CODIGO_ELEMENTO_nombre_documento.extension</strong><br>
            Ejemplo: <code>ARN001_hoja de vida.pdf</code>, <code>ESL002_hoja de vida.pdf</code>
        </p>
    </div>
</body>
</html> 