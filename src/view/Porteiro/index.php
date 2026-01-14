<?php
session_start();
require_once __DIR__ . '/../../data/conector.php';

$conector = new Conector();
$conexao = $conector->getConexao();

$stmt = $conexao->prepare("Select * from porteiro Where id_usuario = ?");
$stmt->bind_param("s", $_SESSION['id']);
$stmt->execute();
$resultado = $stmt->get_result();

if($resultado->num_rows > 0) {
    $row = $resultado->fetch_assoc();
    $userName = $row['nome'];
    $iniciais = strtoupper(substr($userName, 0, 1));
}


?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Porteiro</title>
    <link rel="stylesheet" href="../../../assets/css/porteiro.css">
    
</head>
<body>
    <!-- Cabeçalho do Dashboard -->
    <header class="dashboard-header">
        <div>
            <h2><i class="fas fa-door-closed"></i> Controle de Acesso</h2>
            <div class="header-subtitle">Dashboard do Porteiro</div>
        </div>
        
        <div class="user-info">
            <div class="user-avatar">
                <?php echo $iniciais; ?>
            </div>
            <div class="user-details">
                <div class="user-name"><?php echo $userName; ?></div>
                <div class="user-role">
                    <i class="fas fa-user-shield"></i> Porteiro
                </div>
            </div>
            <a href="../../logout.php?logout=1" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </header>
    
    <!-- Conteúdo Principal -->
    <main class="dashboard-container">
        <!-- Seção de Boas-vindas -->
        <section class="welcome-section">
            <h1><i class="fas fa-door-open"></i> Bem-vindo, <?php echo $userName; ?>!</h1>
            <p>Painel de controle de acesso do condomínio. Registre visitantes, controle encomendas 
               e gerencie o fluxo de entrada e saída. Sua atenção aos detalhes garante a segurança de todos.</p>
            
            <div class="quick-actions">
                <a href="registrar_visita.php" class="action-btn">
                    <i class="fas fa-user-plus"></i> Registrar Visita
                </a>
                <a href="entregas.php" class="action-btn">
                    <i class="fas fa-box"></i> Registrar Entrega
                </a>
                <a href="controle_acesso.php" class="action-btn">
                    <i class="fas fa-clipboard-check"></i> Controle de Acesso
                </a>
                <a href="avisos_porteiro.php" class="action-btn">
                    <i class="fas fa-bullhorn"></i> Avisos do Dia
                </a>
                <a href="emergencia.php" class="action-btn">
                    <i class="fas fa-phone-alt"></i> Contatos de Emergência
                </a>
                <a href="relatorio_diario.php" class="action-btn">
                    <i class="fas fa-file-alt"></i> Relatório Diário
                </a>
            </div>
        </section>

        <section class="dashboard-grid">
            <a href="visitantes.php" class="dashboard-card-link">
                <div class="dashboard-card">
                    <div class="card-title">
                        <i class="fas fa-users"></i> Visitantes Hoje
                    </div>
                    <div class="card-content">
                        <p>24</p>
                        <p>18 registrados • 6 aguardando</p>
                        <div class="card-link">
                            Ver detalhes <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </div>
            </a>

            <a href="encomendas.php" class="dashboard-card-link">
                <div class="dashboard-card">
                    <div class="card-title">
                        <i class="fas fa-box-open"></i> Encomendas Pendentes
                    </div>
                    <div class="card-content">
                        <p>15</p>
                        <p>8 hoje • 7 anteriores</p>
                        <div class="card-link">
                            Ver encomendas <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </div>

            <a href="reservas.php" class="dashboard-card-link">
                <div class="dashboard-card">
                    <div class="card-title">
                        <i class="fas fa-calendar-check"></i> Reservas Ativas
                    </div>
                    <div class="card-content">
                        <p>5</p>
                        <p>3 para hoje • 2 para amanhã</p>
                        <div class="card-link">
                            Ver reservas <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </div>
            </a>

            <a href="emergencia.php" class="dashboard-card-link">
                <div class="dashboard-card">
                    <div class="card-title">
                        <i class="fas fa-exclamation-triangle"></i> Situações Especiais
                    </div>
                    <div class="card-content">
                        <p>2</p>
                        <p>1 manutenção • 1 entrega especial</p>
                        <div class="card-link">
                            Ver detalhes <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </div>
            </a>

            <a href="avisos.php" class="dashboard-card-link">
                <div class="dashboard-card">
                    <div class="card-title">
                        <i class="fas fa-clipboard-list"></i> Avisos do Dia
                    </div>
                    <div class="card-content">
                        <p>7</p>
                        <p>3 importantes • 4 informativos</p>
                        <div class="card-link">
                            Ver avisos <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </div>
            </a>
            
            <div class="dashboard-card">
                <div class="card-title">
                    <i class="fas fa-clock"></i> Turno Atual
                </div>
                <div class="card-content">
                    <p>Diurno</p>
                    <p>06:00 - 18:00 • João da noite às 18h</p>
                    <div class="card-link">
                        Trocar turno <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
            </div>
        </section>

        <section class="control-panel">
            <h3><i class="fas fa-tachometer-alt"></i> Controles Rápidos</h3>
            <div class="control-buttons">
                <a href="#" class="control-btn" id="btn-entrada">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Registrar Entrada</span>
                </a>
                <a href="#" class="control-btn" id="btn-saida">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Registrar Saída</span>
                </a>
                <a href="#" class="control-btn" id="btn-encomenda">
                    <i class="fas fa-shipping-fast"></i>
                    <span>Nova Encomenda</span>
                </a>
                <a href="#" class="control-btn" id="btn-visita">
                    <i class="fas fa-user-clock"></i>
                    <span>Visita Agendada</span>
                </a>
                <a href="#" class="control-btn" id="btn-prestador">
                    <i class="fas fa-tools"></i>
                    <span>Prestador Serviço</span>
                </a>
                <a href="#" class="control-btn" id="btn-emergencia">
                    <i class="fas fa-ambulance"></i>
                    <span>Chamar Emergência</span>
                </a>
            </div>
        </section>
        
>
        <section class="visitor-list">
            <h3><i class="fas fa-user-friends"></i> Visitantes Recentes</h3>
            <div class="list-container">
                <div class="list-item">
                    <div class="item-info">
                        <h4>Carlos Silva - Apt. 302</h4>
                        <p><i class="far fa-clock"></i> Entrada: 14:30 • Motivo: Entrega</p>
                    </div>
                    <div class="item-actions">
                        <a href="#" class="btn-icon btn-approve" title="Registrar saída">
                            <i class="fas fa-check"></i>
                        </a>
                    </div>
                </div>
                
                <div class="list-item">
                    <div class="item-info">
                        <h4>Maria Oliveira - Casa 12</h4>
                        <p><i class="far fa-clock"></i> Aguardando desde 15:15 • 2 pessoas</p>
                    </div>
                    <div class="item-actions">
                        <a href="#" class="btn-icon btn-approve" title="Autorizar entrada">
                            <i class="fas fa-user-check"></i>
                        </a>
                        <a href="#" class="btn-icon btn-reject" title="Recusar entrada">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
                
                <div class="list-item">
                    <div class="item-info">
                        <h4>Pedro Santos - Visita ao Apt. 405</h4>
                        <p><i class="far fa-clock"></i> Entrada: 10:45 • Saída: 12:30</p>
                    </div>
                    <div class="item-status status-delivered">
                        Finalizado
                    </div>
                </div>
            </div>
        </section>

        <section class="package-list">
            <h3><i class="fas fa-boxes"></i> Encomendas para Entrega</h3>
            <div class="list-container">
                <div class="list-item">
                    <div class="item-info">
                        <h4>Apt. 204 - João Mendes</h4>
                        <p><i class="fas fa-shipping-fast"></i> Correios • Pacote médio</p>
                    </div>
                    <div class="item-status status-pending">
                        Aguardando
                    </div>
                </div>
                
                <div class="list-item">
                    <div class="item-info">
                        <h4>Casa 8 - Ana Costa</h4>
                        <p><i class="fas fa-pizza-slice"></i> Pizza Hut • Entrega rápida</p>
                    </div>
                    <div class="item-actions">
                        <a href="#" class="btn-icon btn-approve" title="Entregue">
                            <i class="fas fa-check"></i>
                        </a>
                        <a href="#" class="btn-icon btn-view" title="Detalhes">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
                
                <div class="list-item">
                    <div class="item-info">
                        <h4>Apt. 501 - Roberto Alves</h4>
                        <p><i class="fas fa-building"></i> Farmácia • Medicamento</p>
                    </div>
                    <div class="item-status status-arrived">
                        Urgente
                    </div>
                </div>
            </div>
        </section>

        <section class="visitor-list">
            <h3><i class="fas fa-info-circle"></i> Informações do Turno</h3>
            <div class="list-container">
                <div class="list-item" style="background: #e8f5e9;">
                    <div class="item-info">
                        <h4>Turno: Diurno (06:00 - 18:00)</h4>
                        <p>Porteiro: <?php echo $nome_usuario; ?> • Início: 06:00</p>
                    </div>
                    <div class="item-actions">
                        <a href="#" class="btn-icon btn-view">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </div>
                
                <div class="list-item">
                    <div class="item-info">
                        <h4><i class="fas fa-phone-alt"></i> Contatos de Emergência</h4>
                        <p>Bombeiros: 112 • Polícia: 112 </p>
                    </div>
                    <div class="item-actions">
                        <a href="#" class="btn-icon btn-view">
                            <i class="fas fa-phone"></i>
                        </a>
                    </div>
                </div>
                
                <div class="list-item">
                    <div class="item-info">
                        <h4><i class="fas fa-user-tie"></i> Síndico de Plantão</h4>
                        <p>Maria Silva • 85 37 62 7843• Apt. 101</p>
                    </div>
                    <div class="item-actions">
                        <a href="#" class="btn-icon btn-view">
                            <i class="fas fa-comment-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="dashboard-footer">
        <p>Sistema de Gestão Condominial &copy; <?php echo date('Y'); ?> • Portaria</p>
        <p>Turno Diurno • Última atualização: <?php echo date('H:i:s'); ?></p>
    </footer>

    <script>
        document.querySelectorAll('.control-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const action = this.id.replace('btn-', '');
                
                const actions = {
                    'entrada': 'Registrando entrada...',
                    'saida': 'Registrando saída...',
                    'encomenda': 'Abrindo formulário de encomenda...',
                    'visita': 'Verificando visitas agendadas...',
                    'prestador': 'Registro de prestador de serviço...',
                    'emergencia': 'Acionando contatos de emergência...'
                };
                
                alert(actions[action] || 'Ação em desenvolvimento');
            });
        });

        function updateTime() {
            const now = new Date();
            document.querySelector('.dashboard-footer p:last-child').textContent = 
                `Turno Diurno • Última atualização: ${now.toLocaleTimeString('pt')}`;
        }
        
        setInterval(updateTime, 1000);
    </script>
</body>
</html>