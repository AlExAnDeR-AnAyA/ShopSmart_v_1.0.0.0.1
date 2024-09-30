<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['usuario_id'])) {
        echo "Debe iniciar sesión para continuar con el pago.";
        exit;
    }

    $userId = $_SESSION['usuario_id'];
    $medioTransferencia = $_POST['medio_transferencia'];
    $numeroCuenta = $_POST['numero_cuenta'];

    // Validar la información de la transferencia
    if (empty($medioTransferencia) || empty($numeroCuenta)) {
        echo "<p>Error: Todos los campos son obligatorios.</p>";
        exit;
    }

    // Validar el número de cuenta (esto depende de las reglas de formato que estés usando)
    if (!preg_match('/^\d{10,20}$/', $numeroCuenta)) {
        echo "<p>Error: El número de cuenta debe tener entre 10 y 20 dígitos.</p>";
        exit;
    }

    // Simular la lógica para procesar el pago por transferencia
    // Aquí se añade la lógica de conexión con la plataforma bancaria para recibir la transferencia.
    $pagoExitoso = true; // Simulación: siempre exitoso.

    if ($pagoExitoso) {
        try {
            // Registrar el pago en la base de datos
            $stmt = $pdo->prepare("INSERT INTO factura (ID_Pedido, Fecha, Total) VALUES (?, NOW(), ?)");
            $stmt->execute([$pedidoId, $total]);

            // Mostrar mensaje de éxito
            echo "<p>Pago por transferencia procesado exitosamente.</p>";
            echo "<p>Medio de transferencia: $medioTransferencia</p>";
            echo "<p>Número de cuenta: $numeroCuenta</p>";

            // Aquí se redirigir a una página de confirmación de pedido o agradecer la compra
            // header("Location: confirmacion_pedido.php");
        } catch (PDOException $e) {
            echo "<p>Error: No se pudo procesar la transferencia. Por favor, inténtelo de nuevo.</p>";
            error_log("Error en la base de datos: " . $e->getMessage());
        }
    } else {
        echo "<p>Error: No se pudo procesar la transferencia. Por favor, inténtelo de nuevo.</p>";
    }
}
?>

