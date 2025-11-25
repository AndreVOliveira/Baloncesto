<?php
include_once(dirname(__FILE__) . "/../../conexao.php");

$resultado_partidas = $conn->query("
    SELECT p.id_partida,
           p.data_partida,
           e1.nome_equipe AS equipe_casa,
           e2.nome_equipe AS equipe_visitante,
           p.placar_casa,
           p.placar_visitante
    FROM partidas p
    JOIN equipes e1 ON p.equipe_casa_id = e1.equipe_id
    JOIN equipes e2 ON p.equipe_visitante_id = e2.equipe_id
");

if (!$resultado_partidas) {
    die("Erro na consulta de partidas: " . $conn->error);
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partidas de Basquete</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Partidas de Basquete</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Time Casa</th>
                <th>Time Visitante</th>
                <th>Pontos Casa</th>
                <th>Pontos Visitante</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultado_partidas->fetch_assoc()): ?>
                <tr>
                    <td data-label="ID"><?php echo htmlspecialchars($row['id_partida']); ?></td>
                    <td data-label="Data"><?php echo htmlspecialchars(date('d/m/Y', strtotime($row['data_partida']))); ?></td>
                    <td data-label="Time Casa"><?php echo htmlspecialchars($row['equipe_casa']); ?></td>
                    <td data-label="Time Visitante"><?php echo htmlspecialchars($row['equipe_visitante']); ?></td>
                    <td data-label="Pontos Casa"><?php echo htmlspecialchars($row['placar_casa']); ?></td>
                    <td data-label="Pontos Visitante"><?php echo htmlspecialchars($row['placar_visitante']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
