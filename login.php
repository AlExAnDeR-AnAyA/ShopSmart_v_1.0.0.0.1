<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula = $_POST['cedula'];
    $clave = $_POST['clave'];

    // Preparar y ejecutar la consulta
    $sql = "SELECT * FROM usuario WHERE Cédula = :cedula";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['cedula' => $cedula]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($clave, $usuario['Clave'])) {
        // Autenticación exitosa
        $_SESSION['usuario_id'] = $usuario['ID_Usuario'];
        $_SESSION['usuario_nombre'] = $usuario['Nombre'];
        header("Location: http://localhost/shopsmart_v_1.0.0.0.1/html/catalogo.html"); // Redirige a la página del catálogo
        exit();
    } else {
        // Autenticación fallida
        echo '<script>
                document.getElementById("error").textContent = "Número de cédula o contraseña incorrectos.";
              </script>';
    }
}
?>
