<?php


include_once(dirname(__FILE__) . "/../../conexao.php");

$id = (int) $_GET['id'];  // Cast to integer for safety

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_equipe = $_POST['nome_equipe'];
    $cidade = $_POST['cidade'];
    $conferencia = $_POST['conferencia'];
    $divisao = $_POST['divisao'];
    $abreviacao = $_POST['abreviacao'];
    // Update jogadores table and use correct variable names
    $sql = "UPDATE equipes SET nome_equipe='$nome_equipe', cidade='$cidade', conferencia='$conferencia', divisao='$divisao', abreviacao='$abreviacao' WHERE equipe_id=$id";
    if ($conn->query($sql)) {
        header("Location: equipes.php");  // Redirect to jogadores.php, not equipes.php
        exit;
    } else {
        echo "Erro: " . $conn->error;
    }
}

$sql = "SELECT * FROM equipes WHERE equipe_id=$id";
$result = $conn->query($sql);
$partidas= $result->fetch_assoc();
?>

<h2>Editar equipe</h2>
<form method="post">
    Nome: <input type="varchar" name="nome_equipe" value="<?= htmlspecialchars($partidas['nome_equipe']) ?>"><br><br>
    Cidade: <input type="int" name="cidade" value="<?= htmlspecialchars($partidas['cidade']) ?>"><br><br>
    Conferência: <input type="int" name="conferencia" value="<?= htmlspecialchars($partidas['conferencia']) ?>"><br><br>
    Divisão: <input type="int" name="divisao" value="<?= htmlspecialchars($partidas['divisao']) ?>"><br><br>
    Abreviação: <input type="int" name="abreviacao" value="<?= htmlspecialchars($partidas['abreviacao']) ?>"><br><br>
    <input type="submit" value="Atualizar">
</form>
<a href="equipes.php">Voltar</a>