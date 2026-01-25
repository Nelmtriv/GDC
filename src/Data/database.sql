CREATE DATABASE CondominioDigital2;
USE CondominioDigital2;

CREATE TABLE Usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome NVARCHAR(100),
    telefone NVARCHAR(20),
    email VARCHAR(100) NOT NULL UNIQUE,
	senha_hash VARCHAR(255) NOT NULL,
    nacionalidade NVARCHAR(100),
	documento VARCHAR(50) NOT NULL,
    tipo_documento VARCHAR(50) NOT NULL,
    tipo ENUM('Morador', 'Sindico', 'Porteiro', 'Admin') NOT NULL
);

CREATE TABLE Unidade (
    id_unidade INT AUTO_INCREMENT PRIMARY KEY,
    numero VARCHAR(20) NOT NULL,
    rua VARCHAR(100),
    tipo_unidade NVARCHAR(100)
);

CREATE TABLE Visitante (
    id_visitante INT AUTO_INCREMENT PRIMARY KEY
);

CREATE TABLE Porteiro (
    id_porteiro INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario)
);

CREATE TABLE Morador (
    id_morador INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    id_unidade INT,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario),
    FOREIGN KEY (id_unidade) REFERENCES Unidade(id_unidade)
);

ALTER TABLE Morador
MODIFY COLUMN id_unidade INT NULL;

CREATE TABLE Sindico (
    id_sindico INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    id_unidade INT NULL, -- O sindico pode residir em um dos alojamentos, entao, 
    -- para não se ocuparem o(s) alojamento(s) do sindico deve ser registrado tmb
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario),
    FOREIGN KEY (id_unidade) REFERENCES Unidade(id_unidade)
);

CREATE TABLE Veiculo (
    id_veiculo INT AUTO_INCREMENT PRIMARY KEY,
    id_morador INT NULL, -- id_usuario INT NULL, //Melhor opção para pôr no 
    id_visitante INT NULL,
    matricula VARCHAR(20) NOT NULL,
    cor NVARCHAR(50),
    modelo NVARCHAR(100),
    FOREIGN KEY (id_morador) REFERENCES Morador(id_morador),
    FOREIGN KEY (id_visitante) REFERENCES Visitante(id_visitante)
);

CREATE TABLE Agendamento (
    id_agendamento INT AUTO_INCREMENT PRIMARY KEY,
    id_morador INT,
    id_visitante INT,
    data DATE NOT NULL,
    hora TIME NOT NULL,
    numero_documento VARCHAR(30) NOT NULL,
    tipo_documento VARCHAR(30) NOT NULL,
    motivo VARCHAR(100) NOT NULL,
    FOREIGN KEY (id_morador) REFERENCES Morador(id_morador),
    FOREIGN KEY (id_visitante) REFERENCES Visitante(id_visitante)
);

CREATE TABLE Registro (
    id_registro INT AUTO_INCREMENT PRIMARY KEY,
    id_agendamento INT,
    id_porteiro INT,
    entrada DATETIME,
    saida DATETIME,
    FOREIGN KEY (id_agendamento) REFERENCES Agendamento(id_agendamento),
    FOREIGN KEY (id_porteiro) REFERENCES Porteiro(id_porteiro)
);

CREATE TABLE Aviso (
    id_aviso INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    conteudo TEXT NOT NULL,
    prioridade ENUM('Baixa', 'Média', 'Alta') DEFAULT 'Baixa',
    criado_por INT, -- Referência ao ID do Síndico ou Usuário Admin
    FOREIGN KEY (criado_por) REFERENCES Usuario(id_usuario)
);

CREATE TABLE Leitura_Aviso (
    id_aviso INT,
    id_usuario INT, -- Pode ser morador, porteiro ou síndico
    data_leitura DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_aviso, id_usuario),
    FOREIGN KEY (id_aviso) REFERENCES Aviso(id_aviso),
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario)
);

CREATE TABLE Reserva (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY,
    id_morador INT,
    area_comum VARCHAR(100) NOT NULL,
    data DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fim TIME NOT NULL,
    FOREIGN KEY (id_morador) REFERENCES Morador(id_morador)
);

CREATE TABLE Entrega (
    id_entrega INT AUTO_INCREMENT PRIMARY KEY,  -- Alterado de id_encomenda
    id_morador INT,
    descricao VARCHAR(255) NOT NULL,
    data_recepcao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_entrega DATETIME,                      -- Data em que o morador retirou o item
    status tinyint(1) default 0,
    FOREIGN KEY (id_morador) REFERENCES Morador(id_morador)
);

CREATE TABLE IF NOT EXISTS Ocorrencia  (
    id_ocorrencia INT AUTO_INCREMENT PRIMARY KEY,
    id_morador INT,
    tipo ENUM('Reclamacao', 'Manutencao', 'Sugestao', 'Outro') NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    descricao TEXT NOT NULL,
    status ENUM('Pendente', 'Em Analise', 'Resolvido') DEFAULT 'Pendente',
    data_abertura DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_resolucao DATETIME,
    resposta_sindico TEXT,
    FOREIGN KEY (id_morador) REFERENCES Morador(id_morador)
);

ALTER TABLE Usuario
ADD COLUMN docPath NVARCHAR(200);