<?php
session_start();
include 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $cedula = $_POST['cedula'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $clave = $_POST['clave'];

    // Hashear la contraseña
    $claveHash = password_hash($clave, PASSWORD_DEFAULT);

    try {
        // Preparar la consulta para insertar un nuevo usuario
        $sql = "INSERT INTO usuario (Nombre, Apellido, Cédula, Correo, Teléfono, Dirección, Clave, Tipo_Usuario) 
                VALUES (:nombre, :apellido, :cedula, :correo, :telefono, :direccion, :clave, 2)"; // Tipo_Usuario = 2 por defecto para usuarios normales
        $stmt = $pdo->prepare($sql);

        // Ejecutar la consulta
        $stmt->execute([
            'nombre' => $nombre,
            'apellido' => $apellido,
            'cedula' => $cedula,
            'correo' => $correo,
            'telefono' => $telefono,
            'direccion' => $direccion,
            'clave' => $claveHash
        ]);

        echo '<a href="http://localhost/shopsmart_v_1.0.0.0.1/html/login.html">iniciar sesión</a>';
    } catch (Exception $e) {
        echo 'Error al registrar el usuario: ' . $e->getMessage();
    }
}
?>
