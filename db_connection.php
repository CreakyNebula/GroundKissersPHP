<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "playergroundkisser";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {
    file_put_contents("php_log.txt", "Conexión a la base de datos exitosa\n", FILE_APPEND);
}
?>
