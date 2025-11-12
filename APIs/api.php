<?php
include "../conexao.php";

function chamarAPI($endpoint) {
    $apiKey = "c0f7ac0f-5c5d-4713-847d-7b2afbe9fbe9";
    $url = "https://api.balldontlie.io/v1/$endpoint";

    $options = [
        "http" => [
            "header" => "Authorization: Bearer $apiKey\r\n",
            "method" => "GET"
        ],
        "ssl" => [
            "verify_peer" => false,
            "verify_peer_name" => false
        ]
    ];

    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        echo "❌ Erro ao acessar: $url<br>";
        return null;
    }

    return json_decode($response, true);
}


// ✅ Cria pasta JSON se não existir antes de salvar
function salvarJSON($nomeArquivo, $dados) {
    if (!is_dir("json")) {
        mkdir("json");
    }
    file_put_contents("json/$nomeArquivo.json", json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/*
-----------------------------------------------------------
-------------------------EQUIPES---------------------------
-----------------------------------------------------------
*/
$dadosEquipes = chamarAPI("teams");
if ($dadosEquipes) {
    salvarJSON("equipes", $dadosEquipes);

    foreach ($dadosEquipes['data'] as $team) {
        $nome = addslashes($team['full_name']);
        $cidade = addslashes($team['city']);
        $conferencia = addslashes($team['conference']);
        $divisao = addslashes($team['division']);
        $abreviacao = addslashes($team['abbreviation']);

        $sql = "INSERT IGNORE INTO equipes (nome_equipe, cidade, conferencia, divisao, abreviacao)
                VALUES ('$nome', '$cidade', '$conferencia', '$divisao', '$abreviacao')";
        $conn->query($sql);
    }
}

/*
-----------------------------------------------------------
------------------------JOGADORES--------------------------
-----------------------------------------------------------
*/
$page = 1;
$allPlayers = [];

do {
    $dadosJogadores = chamarAPI("players?page=$page&per_page=100");
    if (!$dadosJogadores || empty($dadosJogadores['data'])) break;

    salvarJSON("jogadores_page_$page", $dadosJogadores);
    $allPlayers = array_merge($allPlayers, $dadosJogadores["data"]);

    foreach ($dadosJogadores['data'] as $jogador) {
        $primeiro = addslashes($jogador["first_name"]);
        $ultimo = addslashes($jogador["last_name"]);
        $posicao = addslashes($jogador["position"]);
        $teamABV = $jogador["team"]["abbreviation"];

        $res = $conn->query("SELECT equipe_id FROM equipes WHERE abreviacao='$teamABV' LIMIT 1");
        if ($res && $res->num_rows > 0) {
            $equipe_id = $res->fetch_assoc()["equipe_id"];
            $sql = "INSERT IGNORE INTO jogadores (equipe_id, primeiro_nome, ultimo_nome, posicao)
                    VALUES ('$equipe_id', '$primeiro', '$ultimo', '$posicao')";
            $conn->query($sql);
        }
    }

    $page++;
    sleep(1); // evita limite da API
} while (!empty($dadosJogadores["meta"]["next_page"]));

salvarJSON("jogadores", ["data" => $allPlayers]);

/*
-----------------------------------------------------------
-------------------------PARTIDAS--------------------------
-----------------------------------------------------------
*/
$dadosPartidas = chamarAPI("games?seasons[]=2023&per_page=100");
if ($dadosPartidas) {
    salvarJSON("partidas", $dadosPartidas);

    foreach ($dadosPartidas["data"] as $game) {
        $data = substr($game["date"], 0, 10);
        $home_abv = $game["home_team"]["abbreviation"];
        $visit_abv = $game["visitor_team"]["abbreviation"];
        $placar_casa = $game["home_team_score"];
        $placar_visit = $game["visitor_team_score"];

        $resHome = $conn->query("SELECT equipe_id FROM equipes WHERE abreviacao='$home_abv' LIMIT 1");
        $resVisit = $conn->query("SELECT equipe_id FROM equipes WHERE abreviacao='$visit_abv' LIMIT 1");

        if ($resHome && $resVisit && $resHome->num_rows > 0 && $resVisit->num_rows > 0) {
            $id_casa = $resHome->fetch_assoc()["equipe_id"];
            $id_visit = $resVisit->fetch_assoc()["equipe_id"];

            $sql = "INSERT IGNORE INTO partidas (data_partida, equipe_casa_id, equipe_visitante_id, placar_casa, placar_visitante)
                    VALUES ('$data', $id_casa, $id_visit, $placar_casa, $placar_visit)";
            $conn->query($sql);
        }
    }
}

echo "✅ Dados importados e salvos com sucesso!";
?>
