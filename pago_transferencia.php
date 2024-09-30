<?php
session_start();
include 'db.php';

if (!isset($_SESSION['usuario_id'])) {
    echo "Debe iniciar sesión para continuar con el pago.";
    exit;
}

$userId = $_SESSION['usuario_id'];

try {
    // Obtener la información del usuario
    $query = "SELECT * FROM usuario WHERE ID_Usuario = :userId";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['userId' => $userId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo "No se encontró el usuario.";
        exit;
    }
} catch (PDOException $e) {
    echo "Error al obtener la información del usuario: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago por Transferencia</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}
 /* Estilos para el encabezado */
header {
    background-color: #007bff;
    color: white;
    padding: 10px 0;
    text-align: center;
    font-size:x-large;
    font-family:cursive;
}

/* Estilos para el logo */
.logo{
    position: absolute;
    top: 10px;
    right: 1px;
}

.logo img{
    width: 50%;
    height: 60%;
}

/* Estilos para el contenido principal */
.main-content {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Estilos para el título */
h1 {
    color: #333;
    margin-bottom: 20px;
}

/* Estilos para los enlaces */
nav {
    text-align: center; /* Centra los enlaces dentro del contenedor */
    margin-top: 20px; /* Añade espacio arriba del contenedor de navegación */
}

nav a {
    display: inline-block; /* Permite aplicar padding y border a los enlaces */
    padding: 10px 20px; /* Espacio interno del "botón" */
    margin: 0 10px; /* Espacio entre los botones */
    text-decoration: none; /* Elimina el subrayado del enlace */
    color: black; /* Color del texto */
    background-color: #00aeff; /* Color de fondo del botón */
    border-radius: 5px; /* Bordes redondeados del botón */
    font-size: 20px; /* Tamaño de la fuente */
    transition: all 0.3s ease; /* Transición suave para el cambio de color y tamaño */
}

nav a:hover {
    background-color: #b300a4; /* Color de fondo al pasar el puntero del mouse */
    transform: scale(1.1); /* Aumenta el tamaño del botón */
}

/* Estilos para el texto en movimiento */
.scrolling-tex{
    white-space: nowrap;
    animation: scroll 15s linear infinite;
}

@keyframes scroll{
    0%{
        transform: translateX(80%);
    }
    100%{
        transform: translateX(-80%);
    }
}

.page-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}


.login-container {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 400px;
    width: 100%;
}

h2 {
    margin-top: 0;
    color: #333;
}

label {
    display: block;
    margin: 10px 0 5px;
    color: #666;
}

input[type="text"], input[type="password"] {
    width: 100%;
    padding: 10px;
    margin: 5px 0 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-sizing: border-box;
}

button {
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 10px;
    width: 100%;
    font-size: 1em;
    cursor: pointer;
    transition: background-color 0.2s;
}

button:hover {
    background-color: #0056b3;
}

.error {
    color: red;
    margin-top: 10px;
}

.footer {
    background-color: #333;
    color: white;
    text-align: center;
    font-size: small;
    padding: 10px;
    position: fixed;
    bottom: 0;
    width: 100%;
    font-family: sans-serif;
}
    </style>
</head>
</head>
<body>
<header>
        <h1>ShopSmart</h1>
        <div class="logo"><img src="http://localhost/shopsmart_v_1.0.0.0.1/Imagenes/Logo_Saturn.png" alt="Logo"></div>
        <nav>
            <a href="http://localhost/shopsmart_v_1.0.0.0.1/index.html">Inicio</a>
            <a href="http://localhost/shopsmart_v_1.0.0.0.1/html/register.html">Registro</a>
            <a href="http://localhost/shopsmart_v_1.0.0.0.1/html/catalogo.html">Catálogo</a>
        </nav>
        <hr>
        <div class="header"><div class="scrolling-tex">Venta de Viveres y Verduras</div></div>
    </header>
    <h1>Pago por Transferencia</h1>
    <form action="http://localhost/shopsmart_v_1.0.0.0.1/php/procesar_transferencia.php" method="POST">
        <p>Nombre: <?php echo htmlspecialchars($usuario['Nombre']); ?></p>
        <p>Correo: <?php echo htmlspecialchars($usuario['Correo']); ?></p>
        <p>Teléfono: <?php echo htmlspecialchars($usuario['Teléfono']); ?></p>
        <p>Seleccione el medio de transferencia:</p>
        <select name="medio_transferencia">
            <option value="nequi">Nequi</option>
            <option value="ahorra_a_la_mano">Ahorro a la Mano</option>
            <option value="bancolombia">Transferencia a Cuenta Bancaria</option>
        </select><br><br>
        <label for="numero_cuenta">Número de Cuenta/Nequi:</label>
        <input type="text" id="numero_cuenta" name="numero_cuenta" required><br><br>
        <button type="submit">Procesar Pago</button>
    </form>
</body>
</html>
<?php
// No es necesario cerrar la conexión PDO explícitamente, ya que se cerrará automáticamente al finalizar el script.
?>

