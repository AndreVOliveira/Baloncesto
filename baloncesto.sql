create database if not exists basquete
default character set utf8
default collate utf8_general_ci;

use basquete;

CREATE TABLE equipes (
    equipe_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome_equipe VARCHAR(75) NOT NULL,
    cidade VARCHAR(75) NOT NULL,
    conferencia VARCHAR(50),
    divisao VARCHAR(50),
    abreviacao VARCHAR(10)
) ENGINE=InnoDB;

CREATE TABLE jogadores (
    id_jogador INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    equipe_id INT NOT NULL,
    primeiro_nome VARCHAR(50) NOT NULL,
    ultimo_nome VARCHAR(50) NOT NULL,
    posicao VARCHAR(10),
    FOREIGN KEY (equipe_id) REFERENCES equipes(equipe_id)
) ENGINE=InnoDB;

CREATE TABLE partidas (
    id_partida INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    data_partida DATE NOT NULL,
    equipe_casa_id INT NOT NULL,
    equipe_visitante_id INT NOT NULL,
    placar_casa INT DEFAULT 0,
    placar_visitante INT DEFAULT 0,
    FOREIGN KEY (equipe_casa_id) REFERENCES equipes(equipe_id),
    FOREIGN KEY (equipe_visitante_id) REFERENCES equipes(equipe_id)
) ENGINE=InnoDB;
