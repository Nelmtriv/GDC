<?php
session_start();

// Incluir a classe de conexão
require_once '../../data/conector.php';

// Verificar se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../view/Porteiro/visita.php?erro=Método não permitido');
    exit;
}

// Obter dados do formulário
$nome_visitante = trim($_POST['nome_visitante'] ?? '');
$tipo_documento = trim($_POST['tipo_documento'] ?? '');
$documento = trim($_POST['documento'] ?? '');
$id_morador = intval($_POST['id_morador'] ?? 0);
$data = $_POST['data'] ?? '';
$hora = $_POST['hora'] ?? '';
$motivo = trim($_POST['motivo'] ?? '');

// Array de erros
$erros = [];

// Validações
if (empty($nome_visitante)) {
    $erros[] = 'Nome do visitante é obrigatório';
} elseif (strlen($nome_visitante) < 3) {
    $erros[] = 'Nome deve ter no mínimo 3 caracteres';
}

if (empty($tipo_documento)) {
    $erros[] = 'Tipo de documento é obrigatório';
}

if (empty($documento)) {
    $erros[] = 'Número do documento é obrigatório';
} elseif (strlen($documento) < 3) {
    $erros[] = 'Documento inválido';
}

if ($id_morador <= 0) {
    $erros[] = 'Selecione um morador válido';
}

if (empty($data)) {
    $erros[] = 'Data é obrigatória';
} else {
    // Validar formato de data
    $data_obj = DateTime::createFromFormat('Y-m-d', $data);
    if (!$data_obj || $data_obj->format('Y-m-d') !== $data) {
        $erros[] = 'Data inválida';
    } else {
        // Verificar se data não é no passado
        $hoje = new DateTime();
        $hoje->setTime(0, 0, 0);
        if ($data_obj < $hoje) {
            $erros[] = 'A data não pode ser no passado';
        }
    }
}

if (empty($hora)) {
    $erros[] = 'Hora é obrigatória';
} else {
    // Validar formato de hora
    if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $hora)) {
        $erros[] = 'Hora inválida';
    }
}

// Se houver erros, redirecionar com mensagens
if (!empty($erros)) {
    $erro_msg = urlencode(implode(', ', $erros));
    header("Location: ../../view/Porteiro/visita.php?erro=$erro_msg");
    exit;
}

try {
    // Conectar ao banco de dados
    $conector = new Conector();
    $conexao = $conector->getConexao();

    // Verificar se o morador existe
    $stmt = $conexao->prepare("SELECT id_morador FROM Morador WHERE id_morador = ?");
    $stmt->bind_param("i", $id_morador);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header('Location: ../../view/Porteiro/visita.php?erro=' . urlencode('Morador não encontrado'));
        $stmt->close();
        exit;
    }

    // Iniciar transação
    $conexao->begin_transaction();

    // 1. Verificar se o visitante com esse documento já existe
    $stmt = $conexao->prepare("SELECT id_visitante FROM Visitante WHERE documento = ?");
    $stmt->bind_param("s", $documento);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Visitante já existe, pegar o ID
        $row = $result->fetch_assoc();
        $id_visitante = $row['id_visitante'];
    } else {
        // Criar novo visitante
        $stmt = $conexao->prepare("INSERT INTO Visitante (nome, documento) VALUES (?, ?)");
        $stmt->bind_param("ss", $nome_visitante, $documento);
        $stmt->execute();
        $id_visitante = $conexao->insert_id;
        $stmt->close();
    }

    // 2. Inserir agendamento
    $stmt = $conexao->prepare("INSERT INTO Agendamento (id_morador, id_visitante, data, hora) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $id_morador, $id_visitante, $data, $hora);
    $stmt->execute();
    $id_agendamento = $conexao->insert_id;
    $stmt->close();

    // Confirmar transação
    $conexao->commit();

    // Redirecionar com mensagem de sucesso
    $sucesso_msg = urlencode('Visita registrada com sucesso!');
    header("Location: ../../view/Porteiro/visita.php?success=$sucesso_msg");
    exit;

} catch (mysqli_sql_exception $e) {
    // Se houver erro, fazer rollback
    if (isset($conexao)) {
        $conexao->rollback();
    }
    
    $erro_msg = urlencode('Erro ao registrar visita: ' . $e->getMessage());
    header("Location: ../../view/Porteiro/visita.php?erro=$erro_msg");
    exit;
} catch (Exception $e) {
    if (isset($conexao)) {
        $conexao->rollback();
    }
    
    $erro_msg = urlencode('Erro inesperado: ' . $e->getMessage());
    header("Location: ../../view/Porteiro/visita.php?erro=$erro_msg");
    exit;
}
?>
