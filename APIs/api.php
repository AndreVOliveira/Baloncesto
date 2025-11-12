<?php
include "conexao.php";
//CONFIGURAÇÃO API
//chave da api
$apiKey = "c0f7ac0f-5c5d-4713-847d-7b2afbe9fbe9";
$apiHost = "api-nba-v1.p.rapidapi.com";

function chamarAPI($endpoint, $apiKey, $apiHost){
    $url = "https://$apiHost/$endpoint";
    $headers = [
        "X-RapidAPI-Key: $apiKey",
        "X-RapidAPI-Host: $apiHost"
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

//IMPORTAR EQUIPES


echo "IMPORTANDO EQUIPES...\n";
$dadosEquipes = chamarAPI("teams", $apiKey, $apiHost);

if(isset($dadosEquipes["response"])){
    foreach ($dadosEquipes["response"] as $equipes){
        if (!$equipes["nbaFranchise"]) continue;
        
        $nome = $conn -> real_escape_string($equipe['name']);
        $cidade = $conn -> real_escape_string($equipe['city']);
        $conferencia = $conn -> real_escape_string($equipe["leagues"]["standard"]["division"]);
        $abreviacao = $conn -> real_escape_string($team["code"]);

        $sql = "INSERT IGNORE INTO equipes (nome_equipe, cidade, conferencia, divisao, abreviacao)
                VALUES ('$nome', '$cidade', '$conferencia', '$divisao', '$abreviacao')";
        $conn->query($sql);
    }
    echo " Equipes importadas!\n";
}


//IMPORTAR JOGADORES

echo "Importando jogadores...\n";

$dadosJogadores = chamarAPI("players?season=2023", $apiKey, $apiHost);

if (isset($dadosJogadores["response"])) {
    foreach ($dadosJogadores["response"] as $jogador){
        if (!isset($jogador["leagues"]["standard"])) continue;

        $timeAbrev = $jogador["leagues"]["standard"]["team"]["code"] ?? null;
        if (!$timeAbrev) continue;

        //pega ID da equipe
        $sqlBuscaEquipe = "SELECT equipe_id FROM equipes WHERE abreviacao = '$timeAbrev' LIMIT 1";
        $res = $conn -> query($sqlBuscaEquipe);
        if ($res && $res -> num_rows > 0) {
            $equipe = $res->fetch_assoc()["equipe_id"];

            $primeiro = $conn -> real_escape_string($jogador[''])
        }

    }
}
?>