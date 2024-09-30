<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $medio_pago = $_POST['medio_pago'];

    if ($medio_pago === 'efectivo') {
        // Generar la factura para pago en efectivo
        header("Location: http://localhost/shopsmart_v_1.0.0.0.1/php/generar_factura.php?medio=efectivo");
        exit;
    } elseif ($medio_pago === 'tarjeta') {
        // Redirigir a la página de pago con tarjeta
        header("Location: http://localhost/shopsmart_v_1.0.0.0.1/php/pago_tarjeta.php");
        exit;
    } elseif ($medio_pago === 'transferencia') {
        // Redirigir a la página de pago por transferencia
        header("Location: http://localhost/shopsmart_v_1.0.0.0.1/php/pago_transferencia.php");
        exit;
    } else {
        echo "<p>Medio de pago no reconocido.</p>";
    }
}
?>
