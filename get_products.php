<?php
include 'db.php';

$sql = "SELECT * FROM producto";
$stmt = $pdo->query($sql);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($productos);
?>
