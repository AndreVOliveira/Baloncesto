<?php
include_once("../conexao.php");

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

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partidas de Basquete</title>
</head>
<body>
    <h1>Partidas de Basquete</h1>
    <table border="1">
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
                    <td><?php echo $row['id_partida']; ?></td>
                    <td><?php echo $row['data_partida']; ?></td>
                    <td><?php echo $row['equipe_casa']; ?></td>
                    <td><?php echo $row['equipe_visitante']; ?></td>
                    <td><?php echo $row['placar_casa']; ?></td>
                    <td><?php echo $row['placar_visitante']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
