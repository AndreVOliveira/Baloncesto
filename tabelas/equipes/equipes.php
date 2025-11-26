<?php
include_once(dirname(__FILE__) . "/../../conexao.php");

$equipes = $conn->query("
    SELECT e.equipe_id,
           e.nome_equipe,
           e.cidade,
           e.conferencia,
           e.divisao,
           e.abreviacao
    FROM equipes e
");

if (!$equipes) {
    die("Erro na consulta de equipes: " . $conn->error);
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipes de Basquete</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Partidas de Basquete</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Cidade</th>
                <th>Conferencia</th>
                <th>Divisão</th>
                <th>Abrevição</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $equipes->fetch_assoc()): ?>
                <tr>
                    <td data-label="ID"><?php echo htmlspecialchars($row['equipe_id']); ?></td>
                    <td data-label="Nome"><?php echo htmlspecialchars( $row['nome_equipe']); ?></td>
                    <td data-label="Cidade"><?php echo htmlspecialchars($row['cidade']); ?></td>
                    <td data-label="Conferência"><?php echo htmlspecialchars($row['conferencia']); ?></td>
                    <td data-label="Divisão"><?php echo htmlspecialchars($row['divisao']); ?></td>
                     <td data-label="Abreviação"><?php echo htmlspecialchars($row['abreviacao']); ?></td>
                <td><a href="editar.php?id=<?php echo htmlspecialchars($row['equipe_id']); ?>">Editar</a> | 
                    <a href="deletar.php?id=<?= $row['equipe_id'] ?>">Excluir</a></td>

                    </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
