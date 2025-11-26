<?php
include_once(dirname(__FILE__) . "/../../conexao.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: equipes.php");
    exit;
}

$id = intval($_GET['id']);

$conn->query("DELETE FROM jogadores WHERE equipe_id = $id");
$conn->query("DELETE FROM equipes WHERE equipe_id = $id");

header("Location: equipes.php");
exit;
?>
