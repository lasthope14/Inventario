<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Definir la ruta base
$basePath = __DIR__ . '/storage/app/public/inventario_imagenes';

$files = [
    'f04c44171419cc33de096a6fb76332a6.jpg',
    'f2b6dc3fa6a10001df85410b6bebd54e.jpg'
];

echo "<h2>Verificación de archivos</h2>";

// Verificar directorio
echo "Verificando directorio base: $basePath<br>";
echo "Directorio existe: " . (is_dir($basePath) ? 'Sí' : 'No') . "<br>";
if (is_dir($basePath)) {
    echo "Permisos del directorio: " . substr(sprintf('%o', fileperms($basePath)), -4) . "<br>";
    echo "Directorio escribible: " . (is_writable($basePath) ? 'Sí' : 'No') . "<br>";
    
    // Listar todos los archivos en el directorio
    echo "<br>Archivos en el directorio:<br>";
    $allFiles = scandir($basePath);
    foreach ($allFiles as $file) {
        if ($file != '.' && $file != '..') {
            echo "- $file<br>";
        }
    }
}

echo "<br><h3>Verificando archivos específicos:</h3>";
foreach ($files as $file) {
    $path = $basePath . '/' . $file;
    echo "<strong>Verificando: $file</strong><br>";
    echo "Ruta completa: $path<br>";
    echo "Existe: " . (file_exists($path) ? 'Sí' : 'No') . "<br>";
    if (file_exists($path)) {
        echo "Tamaño: " . filesize($path) . " bytes<br>";
        echo "Permisos: " . substr(sprintf('%o', fileperms($path)), -4) . "<br>";
        echo "Tipo MIME: " . mime_content_type($path) . "<br>";
    }
    echo "<br>";
}

// Verificar registro en la base de datos
try {
    require_once 'vendor/autoload.php';
    
    // Cargar variables de entorno
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_DATABASE']}",
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD']
    );
    
    echo "<h3>Registros en la base de datos:</h3>";
    foreach ($files as $file) {
        $stmt = $pdo->prepare("SELECT * FROM media WHERE file_name = ?");
        $stmt->execute([$file]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<strong>Registro para $file:</strong><br>";
        if ($result) {
            echo "ID: {$result['id']}<br>";
            echo "Modelo: {$result['model_type']}<br>";
            echo "Modelo ID: {$result['model_id']}<br>";
            echo "Colección: {$result['collection_name']}<br>";
            echo "Nombre archivo: {$result['file_name']}<br>";
            echo "Tamaño: {$result['size']}<br>";
            echo "Creado: {$result['created_at']}<br>";
        } else {
            echo "No se encontró registro en la base de datos<br>";
        }
        echo "<br>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}