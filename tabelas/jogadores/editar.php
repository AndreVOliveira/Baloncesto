<?php


include_once(dirname(__FILE__) . "/../../conexao.php");

$id = (int) $_GET['id'];  // Cast to integer for safety

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $primeiro_nome = $_POST['primeiro_nome'];
    $ultimo_nome = $_POST['ultimo_nome'];
    $posicao = $_POST['posicao'];
    // Update jogadores table and use correct variable names
    $sql = "UPDATE jogadores SET primeiro_nome='$primeiro_nome', ultimo_nome='$ultimo_nome', posicao='$posicao' WHERE id_jogador=$id";
    if ($conn->query($sql)) {
        header("Location: jogadores.php");  // Redirect to jogadores.php, not equipes.php
        exit;
    } else {
        echo "Erro: " . $conn->error;
    }
}

$sql = "SELECT * FROM jogadores WHERE id_jogador=$id";
$result = $conn->query($sql);
$jogadores= $result->fetch_assoc();
?>

<h2>Editar equipe</h2>
<form method="post">
    Primeiro Nome: <input type="text" name="primeiro_nome" value="<?= htmlspecialchars($jogadores['primeiro_nome']) ?>"><br><br>
    Ultimo Nome: <input type="text" name="ultimo_nome" value="<?= htmlspecialchars($jogadores['ultimo_nome']) ?>"><br><br>
    Posição: <input type="text" name="posicao" value="<?= htmlspecialchars($jogadores['posicao']) ?>"><br><br>
    <input type="submit" value="Atualizar">
</form>
<a href="jogadores.php">Voltar</a>
