<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "playergroundkisser";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(array("status" => "failed", "error" => "Error de conexión: " . $conn->connect_error)));
}

// Verificar si se enviaron los datos necesarios
if (!isset($_POST['username']) || !isset($_POST['password'])) {
    die(json_encode(array("status" => "failed", "error" => "Faltan datos.")));
}

// Recibir datos del formulario
$inputUsername = $_POST['username'];
$inputPassword = $_POST['password'];

// Consultar base de datos
$stmt = $conn->prepare("SELECT account_id, password FROM accounts WHERE username = ?");
$stmt->bind_param("s", $inputUsername);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hashedPassword = $row['password'];
    $accountId = $row['account_id'];

    // Verificar la contraseña usando password_verify()
    if (password_verify($inputPassword, $hashedPassword)) {
        echo json_encode(array(
            "status" => "success",
            "player_id" => $accountId,
            "message" => "Inicio de sesión exitoso."
        ));
    } else {
        echo json_encode(array("status" => "failed", "error" => "Contraseña incorrecta."));
    }
} else {
    echo json_encode(array("status" => "failed", "error" => "Usuario no encontrado."));
}

$stmt->close();
$conn->close();
?>
