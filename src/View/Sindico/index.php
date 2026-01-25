<?php
require_once __DIR__ . '/../../Data/conector.php';
require_once __DIR__ . '/../../Controller/Auth/session.php';

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'Sindico') {
    header("Location: /GDC/src/View/Auth/login.php?erro=" . urlencode("Acesso negado"));
    exit();
}

$conector = new Conector();
$conexao = $conector->getConexao();

$stmt = $conexao->prepare("
    SELECT s.id_sindico, u.nome
    FROM Sindico s
    INNER JOIN Usuario u ON s.id_usuario = u.id_usuario
    WHERE s.id_usuario = ?
");
$stmt->bind_param("i", $_SESSION['usuario_id']); // ✅ usar 'i' para INT
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $row = $resultado->fetch_assoc();
    $userName = $row['nome'];
    $iniciais = strtoupper(substr($userName, 0, 1));
} else {
    header("Location: /GDC/src/View/Auth/login.php?erro=" . urlencode("Síndico não encontrado"));
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Síndico</title>
    <link rel="stylesheet" href="../../../assets/css/sindico.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

</head>

<body>
    <?php include_once 'sidebar.php'; ?>
    <header class="dashboard-header">
        <div>
            <h2><i class="fas fa-building"></i> Gestão Condominial</h2>
            <div class="header-subtitle">Dashboard do Síndico</div>
        </div>

        <div class="user-info">
            <div class="user-avatar">
                <?php echo $iniciais; ?>
            </div>
            <div class="user-details">
                <div class="user-name"><?php echo $userName; ?></div>
                <div class="user-role"><i class="fas fa-user-shield"></i> Síndico</div>
            </div>
<a href="../../View/Auth/logout.php?logout=1" 
   class="logout-btn" 
   onclick="return confirmarSaida();">
    <i class="fas fa-sign-out-alt"></i> Sair
</a>

        </div>
    </header>

    <main class="dashboard-container">
        <section class="welcome-section">
            <h1><i class="fas fa-user-shield"></i> Bem-vindo, <?php echo $userName; ?>!</h1>
            <p>Painel de administração para gerenciamento completo do condomínio.</p>
<div class="quick-actions">
    <a href="moradores.php" class="action-btn"><i class="fas fa-users"></i> Gerir Moradores</a>
    <a href="reservas.php" class="action-btn"><i class="fas fa-calendar-check"></i> Gerir Reservas</a>
    <a href="ocorrencias.php" class="action-btn"><i class="fas fa-exclamation-triangle"></i> Gerir Ocorrências</a>
    <a href="avisos.php" class="action-btn"><i class="fas fa-bullhorn"></i> Publicar Avisos</a>
    <a href="novoVeiculo.php" class="action-btn"><i class="fas fa-car"></i> Gerir Veículos</a>
    <a href="porteiros.php" class="action-btn"><i class="fas fa-user-tie"></i> Gerir Porteiros</a>
    <a href="unidade.php" class="action-btn"><i class="fas fa-home"></i> Gerir Unidades</a> <!-- NOVO CARD -->
</div>
        </section>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-title"><i class="fas fa-users"></i> Total de Moradores</div>
                <div class="card-content">

                </div>
            </div>
            <div class="dashboard-card">
                
                <div class="card-title"><i class="fas fa-calendar-alt"></i> Reservas Pendentes</div>
                <div class="card-content">

                </div>
            </div>
            <div class="dashboard-card">
                <div class="card-title"><i class="fas fa-exclamation-circle"></i> Ocorrências Ativas</div>
                <div class="card-content">

                </div>
            </div>
            <div class="dashboard-card">
                <div class="card-title"><i class="fas fa-bullhorn"></i> Avisos Publicados</div>
                <div class="card-content">

                </div>
            </div>
        </div>

        <section class="info-section">
            <h3><i class="fas fa-info-circle"></i> Informações da Sessão</h3>
            <table class="info-table">
                <tr>
                    <td><i class="fas fa-user-shield"></i> Perfil</td>
                    <td>Síndico Administrador</td>
                </tr>
                <tr>
                    <td><i class="fas fa-envelope"></i> Email</td>
                    <td><?php echo $_SESSION['email']; ?></td>
                </tr>
                <tr>
                    <td><i class="fas fa-calendar-alt"></i> Último Acesso</td>
                    <td><?php echo date('d/m/Y H:i:s'); ?></td>
                </tr>
                <tr>
                    <td><i class="fas fa-building"></i> Condomínio</td>
                    <td>Residencial das Flores</td>
                </tr>
                <tr>
                    <td><i class="fas fa-tasks"></i> Tarefas Pendentes</td>
                    <td>19 itens</td>
                </tr>
            </table>
        </section>
    </main>

    <footer class="dashboard-footer">
        <p>Sistema Condomínio Digital &copy; <?php echo date('Y'); ?></p>
        <p>Desenvolvido por Nelma Odair Bila</p>
    </footer>
    <script>
        function confirmarSaida() {
    return confirm("Tem a certeza que deseja sair?");
}
    </script>
</body>
</html>