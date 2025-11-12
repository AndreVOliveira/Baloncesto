create database if not exists basquete
default character set utf8
default collate utf8_general_ci;

use basquete;

Create table cadastro(
id_cadastro int not null auto_increment primary key,
nome varchar(75) not null,
email varchar(50) not null unique,
senha varchar(50) not null,
telefone varchar(20) not null,
data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

    Create table equipes (
    equipe_id int not null auto_increment primary key,
    nome_equipe varchar (75) not null,
    cidade varchar (75) not null,
    conferencia varchar(50),
    abreviacao varchar(10)
    )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

    Create table jogadores (
    id_jogador int not null auto_increment primary key,
    equipe_id int not null,
    primeiro_nome varchar(50) not null ,
    ultimo_nome varchar(50) not null,
    posicao varchar(10) not null,
    idade int,
    numero_camisa varchar(10),
    FOREIGN KEY (equipe_id) REFERENCES equipes (equipe_id)
    )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
    
   CREATE TABLE partidas (
    id_partida INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    data_partida DATE NOT NULL,
    equipe_casa_id INT NOT NULL,
    equipe_visitante_id INT NOT NULL,
    placar_casa INT DEFAULT 0,
    placar_visitante INT DEFAULT 0,
    FOREIGN KEY (equipe_casa_id) REFERENCES equipes(equipe_id),
    FOREIGN KEY (equipe_visitante_id) REFERENCES equipes(equipe_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;