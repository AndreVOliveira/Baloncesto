<?php
include_once(dirname(__FILE__) . "/../../conexao.php");

$id = $_GET['id'];
$sql = "DELETE FROM jogadores WHERE id_jogador=$id";
$conn->query($sql);
header("Location: jogadores.php");
exit;
?>
