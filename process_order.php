<?php
session_start();
include 'db.php';


if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$idUsuario = $_SESSION['usuario_id'];

try {
    $pdo->beginTransaction();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['total']) || !is_numeric($data['total'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid or missing total']);
            exit;
        }

        $cart = $data['cart'];
        $total = $data['total'];
        $paymentMethod = $data['paymentMethod'];
        $fecha = date('Y-m-d H:i:s');  // Definir la fecha actual

        // Insertar en la tabla pedido
        $sql = "INSERT INTO pedido (ID_Producto, Costo_Total, ID_Usuario, Fecha) VALUES (:idProducto, :costoTotal, :idUsuario, :fecha)";
        
        //Recorrer cada producto en el carrito y hacer una inserción por cada uno si es necesario
        foreach ($cart as $item) {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'idProducto' => $item['id'], // Usar el ID de producto del carrito
                'costoTotal' => $item['price'] * $item['quantity'], // Calcular el costo total para ese producto
                'idUsuario' => $idUsuario,
                'fecha' => $fecha
            ]);
        }

        $orderId = $pdo->lastInsertId();

        foreach ($cart as $item) {
            // Verificar si el producto existe
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM producto WHERE ID_Producto = ?");
            $stmt->execute([$item['id']]);
            if ($stmt->fetchColumn() == 0) {
                throw new Exception("El producto con ID {$item['id']} no existe.");
            }

            // Insertar detalle del pedido
            $stmt = $pdo->prepare("INSERT INTO detalle_pedido (ID_Pedido, ID_Producto, Cantidad_Producto) VALUES (?, ?, ?)");
            $stmt->execute([$orderId, $item['id'], $item['quantity']]);
        }

        // Insertar en la tabla factura
        $sqlFactura = "INSERT INTO factura (ID_Pedido, fecha, Total) VALUES (:idPedido, :fecha, :total)";
        $stmt = $pdo->prepare($sqlFactura);
        $stmt->execute([
            'idPedido' => $orderId,
            'fecha' => $fecha,
            'total' => $total
        ]);


        $pdo->commit();
        //generar la factura en PDF o mostrarla en pantalla según lo que necesites
        generatePDF( $pdo, $orderId, $paymentMethod);
        echo json_encode(['success' => true, 'message' => 'Pedido y factura generados con éxito.']);
    }

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function generatePDF($pdo, $orderId, $paymentMethod) {
    require __DIR__ . '/includes/fpdf.php';


    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);

    // Obtener detalles del pedido desde la base de datos
    $stmt = $pdo->prepare("SELECT * FROM detalle_pedido dp JOIN producto p ON dp.ID_Producto = p.ID_Producto WHERE dp.ID_Pedido = ?");
    $stmt->execute([$orderId]);
    $pedido = $stmt->fetchAll();

    // Encabezado de la factura
    $pdf->Cell(0, 10, "Factura", 0, 1, 'C');
    $pdf->Cell(0, 10, "Fecha: " . date('Y-m-d'), 0, 1, 'C');

    // Detalles del pedido
    $pdf->Ln(10);
    $pdf->Cell(0, 10, "Detalle del Pedido", 0, 1);
    foreach ($pedido as $item) {
        $pdf->Cell(0, 10, "Producto: " . $item['Nombre'], 0, 1);
        $pdf->Cell(0, 10, "Cantidad: " . $item['Cantidad_Producto'], 0, 1);
        $pdf->Cell(0, 10, "Precio: $" . $item['Precio'], 0, 1);
    }

    // Total
    $pdf->Ln(10);
    $pdf->Cell(0, 10, "Total: $" . array_sum(array_column($pedido, 'Precio')), 0, 1);

    // Método de pago
    $pdf->Ln(10);
    switch($paymentMethod) {
        case 'efectivo':
            $pdf->Cell(0, 10, "Pago en efectivo. Por favor, pague en caja.", 0, 1);
            break;
        case 'tarjeta':
            $pdf->Cell(0, 10, "Pago con tarjeta. Transacción realizada exitosamente.", 0, 1);
            break;
        case 'transferencia':
            $pdf->Cell(0, 10, "Pago por transferencia bancaria. Transacción realizada exitosamente.", 0, 1);
            break;
        default:
            $pdf->Cell(0, 10, "Método de pago no reconocido.", 0, 1);
    }

    $pdf->Output('F', __DIR__ . '/../facturas/factura_' . $orderId . '.pdf'); // Guardar el PDF en la carpeta correcta
}

?>





