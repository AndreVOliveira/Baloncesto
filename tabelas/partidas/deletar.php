<?php

include_once(dirname(__FILE__) . "/../../conexao.php");

$id = $_GET['id'];
$sql = "DELETE FROM partidas WHERE id_partida=$id";
$conn->query($sql);
header("Location: partidas.php");
?>
