<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['player_id'])) {
        $player_id = intval($_POST['player_id']);
        error_log("Player ID recibido: " . $player_id); // Registro para depuración

        $query = $conn->prepare("
            SELECT partidas_totales, ganadas, perdidas, muertes, zancadillas, parrys 
            FROM account_stats WHERE player_id = ?
        ");
        $query->bind_param("i", $player_id);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            echo json_encode($data);
        } else {
            echo json_encode(["error" => "Player not found"]);
        }
    } else {
        echo json_encode(["error" => "Player ID not provided"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}
$conn->close();
?>
