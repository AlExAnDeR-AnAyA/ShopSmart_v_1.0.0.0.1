<?php
session_start();
include 'db.php'; 

// Verificar si se han enviado los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "Datos recibidos.<br>"; // Depuración

    $nombre = isset($_POST['name']) ? $_POST['name'] : null;
    $correo = isset($_POST['email']) ? $_POST['email'] : null;
    $asunto = isset($_POST['subject']) ? $_POST['subject'] : null;
    $mensaje = isset($_POST['message']) ? $_POST['message'] : null;

    // Depurar: imprimir valores recibidos
    echo "Nombre: " . htmlspecialchars($nombre) . "<br>";
    echo "Correo: " . htmlspecialchars($correo) . "<br>";
    echo "Asunto: " . htmlspecialchars($asunto) . "<br>";
    echo "Mensaje: " . htmlspecialchars($mensaje) . "<br>";

    if ($nombre && $correo && $asunto && $mensaje) {
        try {
            // Preparar y ejecutar la consulta
            $stmt = $pdo->prepare("INSERT INTO contactos (nombre, correo, asunto, mensaje) VALUES (:nombre, :correo, :asunto, :mensaje)");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':asunto', $asunto);
            $stmt->bindParam(':mensaje', $mensaje);

            if ($stmt->execute()) {
                echo "Mensaje enviado correctamente.";
                header("Location: http://localhost/shopsmart_v_1.0.0.0.1/index.html");
                exit(); // Asegura que no se siga ejecutando el script después de redirigir
            } else {
                echo "Error: No se pudo enviar el mensaje.";
            }
        } catch (PDOException $e) {
            echo "Error en la consulta: " . $e->getMessage();
        }
    } else {
        echo "Todos los campos son obligatorios.";
    }
} else {
    echo "No se han enviado datos.";
}

// Cerrar la conexión
$pdo = null;
?>
