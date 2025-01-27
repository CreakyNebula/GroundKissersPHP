<?php
// Conectar a la base de datos
$conn = new mysqli("localhost", "root", "", "playergroundkisser");

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Error de conexión a la base de datos."]));
}

// Obtener los datos enviados desde Unity
$username = $_POST['username'] ?? null;
$password = $_POST['password'] ?? null;

// Verificar que se enviaron los datos requeridos
if (!$username || !$password) {
    echo json_encode(["status" => "error", "message" => "Faltan datos."]);
    exit();
}

// Verificar si el nombre de usuario ya existe
$sql_check = "SELECT * FROM accounts WHERE username = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $username);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    // Usuario duplicado
    echo json_encode(["status" => "error", "message" => "duplicate"]);
    exit();
}

// Insertar el nuevo usuario en la tabla accounts
$hashed_password = password_hash($password, PASSWORD_BCRYPT);
$sql_insert = "INSERT INTO accounts (username, password) VALUES (?, ?)";
$stmt_insert = $conn->prepare($sql_insert);
$stmt_insert->bind_param("ss", $username, $hashed_password);

if ($stmt_insert->execute()) {
    $new_player_id = $stmt_insert->insert_id;

    // Crear una entrada en account_stats para el nuevo usuario
    $sql_stats = "INSERT INTO account_stats (player_id, muertes, parrys, zancadillas, ganadas, perdidas) VALUES (?, 0, 0, 0, 0, 0)";
    $stmt_stats = $conn->prepare($sql_stats);
    $stmt_stats->bind_param("i", $new_player_id);
    $stmt_stats->execute();

    echo json_encode(["status" => "success", "message" => "Usuario registrado exitosamente."]);
} else {
    echo json_encode(["status" => "error", "message" => "Error al registrar usuario."]);
}

// Cerrar conexión
$conn->close();
?>
