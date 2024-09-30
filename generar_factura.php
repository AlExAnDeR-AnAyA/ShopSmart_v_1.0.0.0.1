<?php
session_start();
include 'db.php';

if (!isset($_SESSION['usuario_id'])) {
    echo "Debe iniciar sesión para ver la factura.";
    exit;
}

$userId = $_SESSION['usuario_id'];

try {
    // Obtener el último pedido del usuario
    $query = "SELECT * FROM pedido WHERE ID_Usuario = :userId ORDER BY Fecha DESC LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['userId' => $userId]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        echo "No se encontró ningún pedido para este usuario.";
        exit;
    }

    $pedidoId = $pedido['ID_Pedido'];

    // Obtener los detalles del pedido
    $queryDetalles = "
        SELECT p.Nombre, dp.Cantidad_Producto, p.Precio
        FROM detalle_pedido dp
        JOIN producto p ON dp.ID_Producto = p.ID_Producto
        WHERE dp.ID_Pedido = :pedidoId";
    $stmt = $pdo->prepare($queryDetalles);
    $stmt->execute(['pedidoId' => $pedidoId]);
    $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($detalles)) {
        echo "No se encontraron detalles para este pedido.";
        exit;
    }

    // Calcular el total
    $total = 0;
    foreach ($detalles as $detalle) {
        $total += $detalle['Cantidad_Producto'] * $detalle['Precio'];
    }

    // Mostrar la factura en HTML
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Factura</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            h1, h2, p { margin: 10px 0; }
            .detalle-producto { border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-bottom: 10px; }
            .total { font-weight: bold; font-size: 1.2em; }
        </style>
    </head>
    <body>
        <h1>Factura</h1>
        <p>Fecha: ' . htmlspecialchars($pedido['Fecha']) . '</p>
        <h2>Detalle del Pedido</h2>';

    foreach ($detalles as $detalle) {
        echo '<div class="detalle-producto">';
        echo '<p>Producto: ' . htmlspecialchars($detalle['Nombre']) . '</p>';
        echo '<p>Cantidad: ' . htmlspecialchars($detalle['Cantidad_Producto']) . '</p>';
        echo '<p>Precio: $' . number_format($detalle['Precio'], 2) . '</p>';
        echo '</div>';
    }

    echo '<p class="total">Total: $' . number_format($total, 2) . '</p>';
    echo '<p>Pago en efectivo. Por favor, pague en caja.</p>';
    echo '</body></html>';

} catch (PDOException $e) {
    echo "Error al generar la factura: " . $e->getMessage();
}
?>

