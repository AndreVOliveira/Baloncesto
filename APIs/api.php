<?php
include "../conexao.php";
$conn->set_charset("utf8");

/**
 * Chama a API usando cURL, retorna array decodificado ou null em caso de erro.
 * Mostra diagnóstico do HTTP code e do corpo quando falha.
 */
function chamarAPI($endpoint) {
    $base = "https://api.balldontlie.io/v1/";
    $url = $base . $endpoint;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

    // troque SUA_CHAVE_AQUI pelo token real
    $headers = [
        "Authorization: Bearer c0f7ac0f-5c5d-4713-847d-7b2afbe9fbe9",
        "Accept: application/json"
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($errno) {
        echo "Erro cURL ($errno): $error\n";
        return null;
    }
    if ($httpCode !== 200) {
        echo "HTTP $httpCode ao acessar $url\n";
        echo substr($response, 0, 500) . "\n";
        return null;
    }
    return json_decode($response, true);
}


/**
 * Salva JSON em pasta json (cria se não existir)
 */
function salvarJSON($nomeArquivo, $dados) {
    if (!is_dir(__DIR__ . "/json")) {
        mkdir(__DIR__ . "/json", 0777, true);
    }
    file_put_contents(__DIR__ . "/json/$nomeArquivo.json", json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/* ===================== EQUIPES ===================== */
echo "Importando equipes...\n";
$dadosEquipes = chamarAPI("teams");
if ($dadosEquipes && isset($dadosEquipes['data']) && is_array($dadosEquipes['data'])) {
    salvarJSON("equipes", $dadosEquipes);
    foreach ($dadosEquipes['data'] as $team) {
        $nome = $conn->real_escape_string($team['full_name'] ?? '');
        $cidade = $conn->real_escape_string($team['city'] ?? '');
        $conferencia = $conn->real_escape_string($team['conference'] ?? '');
        $divisao = $conn->real_escape_string($team['division'] ?? '');
        $abreviacao = $conn->real_escape_string($team['abbreviation'] ?? '');

        if ($nome === '') continue;
        $sql = "INSERT IGNORE INTO equipes (nome_equipe, cidade, conferencia, divisao, abreviacao)
                VALUES ('$nome', '$cidade', '$conferencia', '$divisao', '$abreviacao')";
        $conn->query($sql);
    }
    echo "Equipes importadas: " . count($dadosEquipes['data']) . "\n";
} else {
    echo "Falha ao obter dados de equipes. Verifique mensagens acima.\n";
}

/* ===================== JOGADORES ===================== */
echo "Importando jogadores...\n";
$page = 1;
$totalJogadores = 0;
$allPlayers = [];

do {
    $endpoint = "players?page=$page&per_page=100";
    $dadosJogadores = chamarAPI($endpoint);
    if (!$dadosJogadores || empty($dadosJogadores['data'])) break;

    salvarJSON("jogadores_page_$page", $dadosJogadores);
    if (isset($dadosJogadores['data']) && is_array($dadosJogadores['data'])) {
        foreach ($dadosJogadores['data'] as $jogador) {
            $primeiro = $conn->real_escape_string($jogador["first_name"] ?? '');
            $ultimo = $conn->real_escape_string($jogador["last_name"] ?? '');
            $posicao = $conn->real_escape_string($jogador["position"] ?? '');
            $teamABV = $conn->real_escape_string($jogador["team"]["abbreviation"] ?? '');

            if ($teamABV === '') continue;

            $res = $conn->query("SELECT equipe_id FROM equipes WHERE abreviacao='$teamABV' LIMIT 1");
            if ($res && $res->num_rows > 0) {
                $equipe_id = intval($res->fetch_assoc()["equipe_id"]);
                $sql = "INSERT IGNORE INTO jogadores (equipe_id, primeiro_nome, ultimo_nome, posicao)
                        VALUES ($equipe_id, '$primeiro', '$ultimo', '$posicao')";
                $conn->query($sql);
                $totalJogadores++;
            }
        }
    }

    echo "Página $page importada: " . count($dadosJogadores['data']) . " jogadores\n";
    $page++;
    sleep(1);
} while (!empty($dadosJogadores["meta"]["next_page"]));

salvarJSON("jogadores_completo", ["data" => $allPlayers]);
echo "Total de jogadores importados: $totalJogadores\n";

/* ===================== PARTIDAS ===================== */
echo "Importando partidas (temporada 2023)...\n";
$page = 1;
$totalPartidas = 0;

do {
    $endpoint = "games?seasons[]=2023&page=$page&per_page=100";
    $dadosPartidas = chamarAPI($endpoint);
    if (!$dadosPartidas || empty($dadosPartidas['data'])) break;

    salvarJSON("partidas_page_$page", $dadosPartidas);

    foreach ($dadosPartidas['data'] as $game) {
        $data = substr($game['date'] ?? '', 0, 10);
        $home_abv = $game['home_team']['abbreviation'] ?? '';
        $visit_abv = $game['visitor_team']['abbreviation'] ?? '';
        $placar_casa = isset($game['home_team_score']) ? intval($game['home_team_score']) : 0;
        $placar_visit = isset($game['visitor_team_score']) ? intval($game['visitor_team_score']) : 0;

        if ($home_abv === '' || $visit_abv === '') continue;

        $resHome = $conn->query("SELECT equipe_id FROM equipes WHERE abreviacao='$home_abv' LIMIT 1");
        $resVisit = $conn->query("SELECT equipe_id FROM equipes WHERE abreviacao='$visit_abv' LIMIT 1");

        if ($resHome && $resVisit && $resHome->num_rows > 0 && $resVisit->num_rows > 0) {
            $id_casa = intval($resHome->fetch_assoc()["equipe_id"]);
            $id_visit = intval($resVisit->fetch_assoc()["equipe_id"]);
            $sql = "INSERT IGNORE INTO partidas (data_partida, equipe_casa_id, equipe_visitante_id, placar_casa, placar_visitante)
                    VALUES ('$data', $id_casa, $id_visit, $placar_casa, $placar_visit)";
            $conn->query($sql);
            $totalPartidas++;
        }
    }

    echo "Página $page de partidas importada: " . count($dadosPartidas['data']) . " jogos\n";
    $page++;
    sleep(1);
} while (!empty($dadosPartidas["meta"]["next_page"]));

echo "Total de partidas importadas: $totalPartidas\n";
$conn->close();
?>
