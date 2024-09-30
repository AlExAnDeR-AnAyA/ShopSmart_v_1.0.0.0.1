<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['usuario_id'])) {
        echo "Debe iniciar sesión para continuar con el pago.";
        exit;
    }

    $userId = $_SESSION['usuario_id'];
    $numeroTarjeta = $_POST['numero_tarjeta'];
    $fechaExpiracion = $_POST['fecha_expiracion'];
    $cvv = $_POST['cvv'];

    // Validar la información de la tarjeta
    if (empty($numeroTarjeta) || empty($fechaExpiracion) || empty($cvv)) {
        echo "<p>Error: Todos los campos son obligatorios.</p>";
        exit;
    }

    if (!preg_match('/^\d{16}$/', $numeroTarjeta)) {
        echo "<p>Error: El número de tarjeta debe tener 16 dígitos.</p>";
        exit;
    }

    if (!preg_match('/^\d{3}$/', $cvv)) {
        echo "<p>Error: El CVV debe tener 3 dígitos.</p>";
        exit;
    }

    // Simular la lógica para procesar el pago con tarjeta
    $pagoExitoso = true; // Simulación: siempre exitoso.

    if ($pagoExitoso) {
        try {
            // Registrar el pago en la base de datos
            $stmt = $pdo->prepare("INSERT INTO factura (ID_Pedido, Fecha, Total) VALUES (:pedidoId, NOW(), :total)");
            $stmt->execute(['pedidoId' => $pedidoId, 'total' => $total]);

            // Mostrar mensaje de éxito
            echo "<p>Pago con tarjeta procesado exitosamente.</p>";
            echo "<p>Tarjeta terminada en: " . htmlspecialchars(substr($numeroTarjeta, -4)) . "</p>";

            // Aquí se redirigir a una página de confirmación de pedido o agradecer la compra
            // header("Location: confirmacion_pedido.php");
        } catch (PDOException $e) {
            echo "<p>Error: No se pudo registrar el pago en la base de datos. Por favor, inténtelo de nuevo.</p>";
        }
    } else {
        echo "<p>Error: No se pudo procesar el pago. Por favor, inténtelo de nuevo.</p>";
    }
}
?>

