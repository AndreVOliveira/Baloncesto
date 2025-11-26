<?php


include_once(dirname(__FILE__) . "/../../conexao.php");

$id = (int) $_GET['id'];  // Cast to integer for safety

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data_partida = $_POST['data_partida'];
    $equipe_casa_id = $_POST['equipe_casa_id'];
    $equipe_visitante_id = $_POST['equipe_visitante_id'];
    $placar_casa = $_POST['placar_casa'];
    $placar_visitante = $_POST['placar_visitante'];
    // Update jogadores table and use correct variable names
    $sql = "UPDATE partidas SET data_partida='$data_partida', equipe_casa_id='$equipe_casa_id', equipe_visitante_id='$equipe_visitante_id', placar_casa='$placar_casa', placar_visitante='$placar_visitante' WHERE id_partida=$id";
    if ($conn->query($sql)) {
        header("Location: partidas.php");  // Redirect to jogadores.php, not equipes.php
        exit;
    } else {
        echo "Erro: " . $conn->error;
    }
}

$sql = "SELECT * FROM partidas WHERE id_partida=$id";
$result = $conn->query($sql);
$partidas= $result->fetch_assoc();
?>

<h2>Editar equipe</h2>
<form method="post">
    Data da partida: <input type=x"date" name="data_partida" value="<?= htmlspecialchars($partidas['data_partida']) ?>"><br><br>
    Equipe casa: <input type="int" name="equipe_casa_id" value="<?= htmlspecialchars($partidas['equipe_casa_id']) ?>"><br><br>
    Equipe visitante: <input type="int" name="equipe_visitante_id" value="<?= htmlspecialchars($partidas['equipe_visitante_id']) ?>"><br><br>
    Placar da casa: <input type="int" name="placar_casa" value="<?= htmlspecialchars($partidas['placar_casa']) ?>"><br><br>
    Placar do visitante: <input type="int" name="placar_visitante" value="<?= htmlspecialchars($partidas['placar_visitante']) ?>"><br><br>
    <input type="submit" value="Atualizar">
</form>
<a href="placar.php">Voltar</a>