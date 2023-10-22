<?php
// Establecer la conexión utilizando PDO
function conectar() {
    $servidor = "127.0.0.1";
    $usuario = "mo201801";
    $password = "mo201801";
    $database = "chat";
    
    try {
        $conexion = new PDO("mysql:host=$servidor;dbname=$database", $usuario, $password);
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conexion;
    } catch (PDOException $e) {
        die("Error en la conexión a la base de datos: " . $e->getMessage());
    }
}

// Función para ejecutar consultas SQL
function ejecutar($sql) {
    $conexion = conectar();
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
}

// Función para consultar datos
function consultar($sql, $cols_num) {
    $conexion = conectar();
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $matriz = $stmt->fetchAll(PDO::FETCH_NUM);

    return $matriz;
}

// Función de retorno de datos AJAX
function AJAX($nombre, $mensaje) {
    if (!empty($nombre) && !empty($mensaje)) {
        $nombre = filter_var($nombre, FILTER_SANITIZE_STRING);
        $mensaje = filter_var($mensaje, FILTER_SANITIZE_STRING);

        $sql = "CALL insertar(:nombre, :mensaje)";
        $conexion = conectar();
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':mensaje', $mensaje);
        $stmt->execute();
    }

    $chat = consultar("CALL mostrar();", 2);
    header("Content-Type: text/plain");
    foreach ($chat as $dato) {
        echo $dato[0] . ": " . $dato[1] . "\n\n";
    }
}

// Comprobar si se recibe la variable nombre y mensaje a través de AJAX
if (isset($_REQUEST["nombre"]) && isset($_REQUEST["mensaje"])) {
    $nombre = $_REQUEST["nombre"];
    $mensaje = $_REQUEST["mensaje"];
    AJAX($nombre, $mensaje);
} else {
    echo "Solo Personal Autorizado";
}
?>
