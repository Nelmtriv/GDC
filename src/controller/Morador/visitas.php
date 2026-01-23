<?php
session_start();
require_once __DIR__ . '/../../data/conector.php';

if (
    !isset($_SESSION['id']) ||
    $_SESSION['tipo_usuario'] !== 'Morador'
) {
    header('Location: ../../login.php');
    exit;
}

$conexao = (new Conector())->getConexao();

/* Buscar morador */
$stmt = $conexao->prepare(
    "SELECT id_morador FROM Morador WHERE id_usuario = ?"
);
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$morador = $stmt->get_result()->fetch_assoc();

if (!$morador) {
    header('Location: ../../view/morador/agendar_visita.php');
    exit;
}

$idMorador = $morador['id_morador'];

/* Dados do formulário */
$nomeVisitante = trim($_POST['nome_visitante'] ?? '');
$documento     = trim($_POST['documento'] ?? '');
$data          = $_POST['data'] ?? '';
$hora          = $_POST['hora'] ?? '';
$motivo        = trim($_POST['motivo'] ?? '');

if (!$nomeVisitante || !$documento || !$data || !$hora || !$motivo) {
    header('Location: ../../view/morador/agendar_visita.php');
    exit;
}

/* Inserir visitante */
$stmt = $conexao->prepare(
    "INSERT INTO Visitante (nome, documento) VALUES (?, ?)"
);
$stmt->bind_param("ss", $nomeVisitante, $documento);
$stmt->execute();
$idVisitante = $stmt->insert_id;

/* Inserir agendamento (✅ COM MOTIVO) */
$stmt = $conexao->prepare(
    "INSERT INTO Agendamento 
     (id_morador, id_visitante, data, hora, motivo)
     VALUES (?, ?, ?, ?, ?)"
);

/* ✅ AQUI ESTÁ A CORREÇÃO */
$stmt->bind_param(
    "iisss",
    $idMorador,
    $idVisitante,
    $data,
    $hora,
    $motivo
);

$stmt->execute();

header('Location: ../../view/morador/agendar_visita.php');
exit;
