<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['game_id'])) {
        $game_id = $_POST['game_id'];
        $muertes = $_POST['total_muertes'];
        $zancadillas = $_POST['total_zancadillas'];
        $parrys = $_POST['total_parrys'];


        $sql = "
            INSERT INTO stats_partida_total (game_id, total_muertes, total_zancadillas, total_parry)
            VALUES ('$game_id', '$muertes', '$zancadillas', '$parrys')
            ON DUPLICATE KEY UPDATE game_id = game_id
        ";

        $stmt = $conn->query($sql);
;
        //$stmt->bind_param("s", $game_id);

        if ($stmt) {
            echo json_encode(["status" => "success", "message" => "Game ID registrado correctamente."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al registrar Game ID: " . $stmt->error]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Faltan datos."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método no permitido."]);
}
?>