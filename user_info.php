<?php
session_start();
if (isset($_SESSION['usuario_nombre'])) {
    echo "Hola, " . htmlspecialchars($_SESSION['usuario_nombre']);
} else {
    echo "Usuario";
}
?>