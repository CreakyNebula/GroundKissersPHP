<?php
include 'db_connection.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica si se enviaron todos los datos necesarios
    if (isset($_POST['partida_id'], $_POST['player_id'], $_POST['muertes'], $_POST['parrys'], $_POST['zancadillas'], $_POST['ganadas'], $_POST['perdidas'], $_POST['partidas_totales'])) {
        $partida_id = $_POST['partida_id'];
        $player_id = (int) $_POST['player_id'];
        $muertes = (int) $_POST['muertes'];
        $parrys = (int) $_POST['parrys'];
        $zancadillas = (int) $_POST['zancadillas'];
        $ganadas = (int) $_POST['ganadas'];
        $perdidas = (int) $_POST['perdidas'];
        $partidas_totales = (int) $_POST['partidas_totales'];

        // Inserta o actualiza estadísticas específicas de la partida
        $sql = "
            INSERT INTO stats_partida_player (partida_id, player_id, muertes, parrys, zancadillas)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                muertes = muertes + VALUES(muertes),
                parrys = parrys + VALUES(parrys),
                zancadillas = zancadillas + VALUES(zancadillas)
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siiii", $partida_id, $player_id, $muertes, $parrys, $zancadillas);

        if ($stmt->execute()) {
            // Inserta o actualiza estadísticas acumuladas del jugador
            $sql_accum = "
                INSERT INTO account_stats (player_id, Muertes, parrys, zancadillas, Ganadas, Perdidas, Partidas_totales)
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    Muertes = Muertes + VALUES(Muertes),
                    parrys = parrys + VALUES(parrys),
                    zancadillas = zancadillas + VALUES(zancadillas),
                    Ganadas = Ganadas + VALUES(Ganadas),
                    Perdidas = Perdidas + VALUES(Perdidas),
                    Partidas_totales = Partidas_totales + VALUES(Partidas_totales)
            ";

            $stmt_accum = $conn->prepare($sql_accum);
            $stmt_accum->bind_param("iiiiiii", $player_id, $muertes, $parrys, $zancadillas, $ganadas, $perdidas, $partidas_totales);

            if ($stmt_accum->execute()) {
                echo json_encode(["status" => "success", "message" => "Estadísticas guardadas correctamente."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error al actualizar estadísticas acumuladas: " . $stmt_accum->error]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Error al guardar estadísticas: " . $stmt->error]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Faltan datos."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método no permitido."]);
}
?>