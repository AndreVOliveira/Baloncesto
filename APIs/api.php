<?php
include "../conexao.php";
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

        $nome = $conn -> real_escape_string($equipes['name']);
        $cidade = $conn -> real_escape_string($equipes['city']);
        $conferencia = $conn -> real_escape_string($equipes["leagues"]["standard"]["conference"]);
        $divisao = $conn -> real_escape_string($equipes["leagues"]["standard"]["division"]);
        $abreviacao = $conn -> real_escape_string($equipes["code"]);

        $sqlequipe = "INSERT IGNORE INTO equipes (nome_equipe, cidade, conferencia, divisao, abreviacao)
                VALUES ('$nome', '$cidade', '$conferencia', '$divisao', '$abreviacao')";
        $conn->query($sqlequipe);
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

            $primeiro = $conn -> real_escape_string($jogador['firstname']);
            $ultimo = $conn -> real_escape_string($jogador['lastname']);
            $posicao = $conn -> real_escape_string($jogador["leagues"]["standard"]["pos"]);
            $idade = $conn -> real_escape_string($jogador["birth"]["age"]);
            $num = $conn -> real_escape_string($jogador["leagues"]["standard"]["jersey"]);

            $sqljog = "INSERT IGNORE INTO jogadores
                    (equipe_id, primeiro_nome, ultimo_nome, posicao, idade, numero_camisa)
                    VALUES ('$equipe', '$primeiro', '$ultimo', '$posicao', '$idade', '$num')";
            $conn->query($sqljog);
        }
    }
    echo("jogadores importados");
}
?>