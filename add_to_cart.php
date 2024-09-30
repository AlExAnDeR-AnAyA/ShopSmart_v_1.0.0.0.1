<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idProducto = isset($_POST['id_producto']) ? $_POST['id_producto'] : null;
    $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;
    $idUsuario = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;

    // Verificar que el usuario esté autenticado y que la cantidad sea mayor que 0
    if ($idUsuario && $idProducto && $cantidad > 0) {
        // Verificar si el producto existe en la base de datos
        $stmt = $pdo->prepare("SELECT ID_Producto FROM producto WHERE ID_Producto = :idProducto");
        $stmt->execute(['idProducto' => $idProducto]);
        $producto = $stmt->fetch();

        if ($producto) {
            // Si el producto existe, agregarlo al carrito
            $sql = "INSERT INTO carrito (ID_Usuario, ID_Producto, Cantidad) VALUES (:idUsuario, :idProducto, :cantidad)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['idUsuario' => $idUsuario, 'idProducto' => $idProducto, 'cantidad' => $cantidad]);

            echo json_encode(['success' => true, 'message' => 'Producto agregado al carrito.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error: Producto no encontrado.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al agregar el producto al carrito.']);
    }
}
?>

