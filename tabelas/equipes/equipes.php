<?php
include_once(dirname(__FILE__) . "/../../conexao.php");

$resultado_jogadores = $conn->query("
    SELECT j.id_jogador,
           j.primeiro_nome,
           j.ultimo_nome,
           j.posicao,
           e.nome_equipe
    FROM jogadores j
    JOIN equipes e ON j.equipe_id = e.equipe_id
");

if (!$resultado_jogadores) {
    die("Erro na consulta de jogadores: " . $conn->error);
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jogadores de Basquete</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Jogadores de Basquete</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Equipe</th>
                <th>Primeiro Nome</th>
                <th>Último Nome</th>
                <th>Posição</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultado_jogadores->fetch_assoc()): ?>
                <tr>
                    <td data-label="ID"><?php echo htmlspecialchars($row['id_jogador']); ?></td>
                    <td data-label="EQUIPE"><?php echo htmlspecialchars($row['nome_equipe']); ?></td>
                    <td data-label="PRIMEIRO NOME"><?php echo htmlspecialchars($row['primeiro_nome']); ?></td>
                    <td data-label="ÚLTIMO NOME"><?php echo htmlspecialchars($row['ultimo_nome']); ?></td>
                    <td data-label="POSIÇÃO"><?php echo htmlspecialchars($row['posicao']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
