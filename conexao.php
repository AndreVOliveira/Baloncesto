<?php
$servername = "localhost";
$username = "root";
$password = "Home@spSENAI2025!"; // sua senha do MySQL
$dbname = "basquete";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>
