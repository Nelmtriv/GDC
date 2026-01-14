<?php
session_start();
require_once '../../controller/AuthController.php';

$auth = new AuthController();
$auth->checkAccess(['Sindico']);

$userName = $auth->getUserName();
$primeiro_nome = explode(' ', $userName)[0];
$iniciais = strtoupper(substr($primeiro_nome, 0, 1) . 
                      (isset(explode(' ', $userName)[1]) ? substr(explode(' ', $userName)[1], 0, 1) : ''));
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Ocorrências - Síndico</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Estilos gerais (iguais às páginas anteriores) */
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
        body { background: linear-gradient(135deg,#f3f4f6 0%,#e5e7eb 100%); color:#1f2937; min-height:100vh; }

        .dashboard-header {
            background:white;
            color:#1f2937;
            padding:1.5rem 2rem;
            display:flex;
            justify-content:space-between;
            align-items:center;
            box-shadow:0 4px 6px rgba(0,0,0,0.1);
            border-bottom:3px solid #7e22ce;
            position:sticky;
            top:0;
            z-index:100;
        }
        .dashboard-header h2 { font-size:1.5rem; font-weight:600; display:flex; align-items:center; gap:.75rem; }
        .dashboard-header h2 i { color:#7e22ce; }
        .header-subtitle { font-size:.875rem; color:#6b7280; margin-top:.25rem; }

        .user-info { display:flex; align-items:center; gap:1.25rem; }
        .user-avatar {
            width:50px; height:50px;
            background:#7e22ce;
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            color:white;
            font-weight:600;
            font-size:1rem;
            box-shadow:0 1px 3px rgba(0,0,0,0.1);
        }
        .user-details { text-align:right; }
        .user-name { font-weight:500; font-size:1rem; color:#1f2937; }
        .user-role { font-size:.75rem; color:#6b7280; display:flex; align-items:center; gap:.25rem; margin-top:.125rem; }
        
        .logout-btn, .back-btn {
            background: #ff4757;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .logout-btn:hover, .back-btn:hover {
            background: #ff3742;
            transform: translateY(-2px);
        }
        .back-btn {
            background: #6c757d;
        }
        .back-btn:hover {
            background: #5a6268;
        }

        /* Main container */
        .dashboard-container { max-width:1400px; margin:2rem auto; padding:0 1.25rem; }

        /* Page header */
        .page-header {
            background: white;
            border-radius: .75rem;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .page-header h1 {
            color: #1f2937;
            font-size: 1.75rem;
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .page-header h1 i { color: #7e22ce; }
        .page-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        .btn-primary {
            background: #7e22ce;
            color: white;
        }
        .btn-primary:hover {
            background: #5b21b6;
            transform: translateY(-2px);
        }
        .btn-success {
            background: #10b981;
            color: white;
        }
        .btn-success:hover {
            background: #059669;
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 0;
            background: white;
            border-radius: .75rem .75rem 0 0;
            overflow: hidden;
            margin-bottom: 0;
        }
        .tab {
            padding: 1rem 2rem;
            background: #f8fafc;
            border: none;
            cursor: pointer;
            font-weight: 500;
            color: #6b7280;
            transition: all 0.3s ease;
            border-bottom: 2px solid transparent;
        }
        .tab.active {
            background: white;
            color: #7e22ce;
            border-bottom: 2px solid #7e22ce;
        }
        .tab:hover:not(.active) {
            background: #f1f5f9;
        }

        /* Content */
        .tab-content {
            background: white;
            border-radius: 0 .75rem .75rem .75rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .tab-pane {
            display: none;
        }
        .tab-pane.active {
            display: block;
        }

        /* Ocorrência card */
        .ocorrencia-card {
            background: white;
            border-radius: .75rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: 1px solid #e5e7eb;
            border-left: 4px solid #f59e0b;
            transition: all 0.3s ease;
        }
        .ocorrencia-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .ocorrencia-card.urgente {
            border-left-color: #ef4444;
        }
        .ocorrencia-card.andamento {
            border-left-color: #3b82f6;
        }
        .ocorrencia-card.resolvida {
            border-left-color: #10b981;
        }
        .ocorrencia-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .ocorrencia-title {
            font-weight: 600;
            color: #1f2937;
            font-size: 1.1rem;
        }
        .ocorrencia-badges {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
        }
        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .badge-urgente { background: #fee2e2; color: #991b1b; }
        .badge-normal { background: #dbeafe; color: #1e40af; }
        .badge-andamento { background: #fef3c7; color: #92400e; }
        .badge-resolvida { background: #d1fae5; color: #065f46; }
        .ocorrencia-info {
            margin-bottom: 1rem;
        }
        .info-row {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            margin-bottom: .5rem;
        }
        .info-label {
            color: #6b7280;
            min-width: 120px;
        }
        .info-value {
            color: #1f2937;
            font-weight: 500;
        }
        .ocorrencia-desc {
            color: #4b5563;
            line-height: 1.5;
            margin-bottom: 1rem;
            padding: 1rem;
            background: #f9fafb;
            border-radius: .5rem;
        }
        .ocorrencia-actions {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
        }

        /* Footer */
        .dashboard-footer { 
            background:white; 
            color:#6b7280; 
            text-align:center; 
            padding:1.5rem; 
            margin-top:3rem; 
            border-top:1px solid #e5e7eb; 
        }

        @media (max-width:768px) {
            .dashboard-header { flex-direction:column; padding:1.25rem; text-align:center; gap:1rem; }
            .user-info { flex-direction:column; gap:1rem; }
            .user-details { text-align:center; }
            .page-header { flex-direction: column; align-items: flex-start; }
            .page-actions { width: 100%; justify-content: center; }
            .tabs { overflow-x: auto; flex-wrap: nowrap; }
            .tab { white-space: nowrap; }
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <header class="dashboard-header">
        <div>
            <h2><i class="fas fa-building"></i> Gestão Condominial</h2>
            <div class="header-subtitle">Gerenciamento de Ocorrências</div>
        </div>

        <div class="user-info">
            <div class="user-avatar"><?php echo $iniciais; ?></div>
            <div class="user-details">
                <div class="user-name"><?php echo $userName; ?></div>
                <div class="user-role"><i class="fas fa-user-shield"></i> Síndico</div>
            </div>
            <a href="sindico_dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <a href="../../controller/AuthController.php?action=logout" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </header>

    <!-- Conteúdo Principal -->
    <main class="dashboard-container">
        <!-- Cabeçalho da Página -->
        <div class="page-header">
            <h1><i class="fas fa-exclamation-triangle"></i> Ocorrências</h1>
            <div class="page-actions">
                <a href="nova_ocorrencia.php" class="btn btn-success">
                    <i class="fas fa-plus-circle"></i> Nova Ocorrência
                </a>
                <a href="#" class="btn btn-primary">
                    <i class="fas fa-file-export"></i> Relatório
                </a>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab active" onclick="openTab('pendentes')">Pendentes (8)</button>
            <button class="tab" onclick="openTab('andamento')">Em Andamento (4)</button>
            <button class="tab" onclick="openTab('resolvidas')">Resolvidas (24)</button>
            <button class="tab" onclick="openTab('todas')">Todas (36)</button>
        </div>

        <!-- Conteúdo das Tabs -->
        <div class="tab-content">
            <!-- Tab Pendentes -->
            <div id="pendentes" class="tab-pane active">
                <!-- Ocorrência 1 -->
                <div class="ocorrencia-card urgente">
                    <div class="ocorrencia-header">
                        <div class="ocorrencia-title">Vazamento de Água - Bloco A</div>
                        <div class="ocorrencia-badges">
                            <span class="badge badge-urgente">Urgente</span>
                            <span class="badge badge-andamento">Pendente</span>
                        </div>
                    </div>
                    <div class="ocorrencia-info">
                        <div class="info-row">
                            <span class="info-label">Reportado por:</span>
                            <span class="info-value">João Silva (Apto 101)</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Data:</span>
                            <span class="info-value">14/03/2024 08:30</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Local:</span>
                            <span class="info-value">Hall do 3º andar - Bloco A</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Categoria:</span>
                            <span class="info-value">Infraestrutura</span>
                        </div>
                    </div>
                    <div class="ocorrencia-desc">
                        Vazamento significativo de água no teto do hall do 3º andar, formando poça no chão. Risco de danos elétricos e acidentes.
                    </div>
                    <div class="ocorrencia-actions">
                        <button class="btn btn-success btn-sm" onclick="atribuirOcorrencia(1)">
                            <i class="fas fa-user-check"></i> Atribuir
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="verDetalhesOcorrencia(1)">
                            <i class="fas fa-eye"></i> Detalhes
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="marcarUrgente(1)">
                            <i class="fas fa-exclamation-circle"></i> Urgente
                        </button>
                    </div>
                </div>

                <!-- Ocorrência 2 -->
                <div class="ocorrencia-card">
                    <div class="ocorrencia-header">
                        <div class="ocorrencia-title">Elevador Parado</div>
                        <div class="ocorrencia-badges">
                            <span class="badge badge-normal">Normal</span>
                            <span class="badge badge-andamento">Pendente</span>
                        </div>
                    </div>
                    <div class="ocorrencia-info">
                        <div class="info-row">
                            <span class="info-label">Reportado por:</span>
                            <span class="info-value">Maria Santos (Apto 205)</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Data:</span>
                            <span class="info-value">13/03/2024 14:15</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Local:</span>
                            <span class="info-value">Elevador Social - Bloco B</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Categoria:</span>
                            <span class="info-value">Manutenção</span>
                        </div>
                    </div>
                    <div class="ocorrencia-desc">
                        Elevador social do bloco B parado no 5º andar desde às 14h. Moradores idosos estão com dificuldade de acesso.
                    </div>
                    <div class="ocorrencia-actions">
                        <button class="btn btn-success btn-sm" onclick="atribuirOcorrencia(2)">
                            <i class="fas fa-user-check"></i> Atribuir
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="verDetalhesOcorrencia(2)">
                            <i class="fas fa-eye"></i> Detalhes
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="marcarUrgente(2)">
                            <i class="fas fa-exclamation-circle"></i> Urgente
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tab Em Andamento -->
            <div id="andamento" class="tab-pane">
                <!-- Ocorrência em andamento -->
                <div class="ocorrencia-card andamento">
                    <div class="ocorrencia-header">
                        <div class="ocorrencia-title">Reparo na Iluminação</div>
                        <div class="ocorrencia-badges">
                            <span class="badge badge-andamento">Em Andamento</span>
                            <span class="badge badge-normal">Normal</span>
                        </div>
                    </div>
                    <div class="ocorrencia-info">
                        <div class="info-row">
                            <span class="info-label">Responsável:</span>
                            <span class="info-value">José - Zelador</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Data Início:</span>
                            <span class="info-value">12/03/2024</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Previsão:</span>
                            <span class="info-value">15/03/2024</span>
                        </div>
                    </div>
                    <div class="ocorrencia-desc">
                        Substituição de lâmpadas queimadas nas áreas comuns e revisão do sistema de iluminação.
                    </div>
                    <div class="ocorrencia-actions">
                        <button class="btn btn-success btn-sm" onclick="marcarResolvida(3)">
                            <i class="fas fa-check-circle"></i> Resolver
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="verDetalhesOcorrencia(3)">
                            <i class="fas fa-eye"></i> Detalhes
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tab Resolvidas -->
            <div id="resolvidas" class="tab-pane">
                <!-- Ocorrência resolvida -->
                <div class="ocorrencia-card resolvida">
                    <div class="ocorrencia-header">
                        <div class="ocorrencia-title">Limpeza da Piscina</div>
                        <div class="ocorrencia-badges">
                            <span class="badge badge-resolvida">Resolvida</span>
                        </div>
                    </div>
                    <div class="ocorrencia-info">
                        <div class="info-row">
                            <span class="info-label">Resolvida por:</span>
                            <span class="info-value">Empresa AquaClean</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Data Resolução:</span>
                            <span class="info-value">10/03/2024</span>
                        </div>
                    </div>
                    <div class="ocorrencia-desc">
                        Limpeza completa da piscina realizada, ajuste do pH e cloração conforme normas.
                    </div>
                </div>
            </div>

            <!-- Tab Todas -->
            <div id="todas" class="tab-pane">
                <p>Lista completa de todas as ocorrências.</p>
            </div>
        </div>

        <!-- Estatísticas -->
        <div style="background: white; border-radius: .75rem; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h3><i class="fas fa-chart-pie"></i> Estatísticas</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
                <div style="text-align: center; padding: 1.5rem; background: #f8fafc; border-radius: .5rem;">
                    <div style="font-size: 2rem; color: #ef4444; font-weight: 700;">12</div>
                    <div style="color: #6b7280;">Ativas</div>
                </div>
                <div style="text-align: center; padding: 1.5rem; background: #f8fafc; border-radius: .5rem;">
                    <div style="font-size: 2rem; color: #f59e0b; font-weight: 700;">8</div>
                    <div style="color: #6b7280;">Pendentes</div>
                </div>
                <div style="text-align: center; padding: 1.5rem; background: #f8fafc; border-radius: .5rem;">
                    <div style="font-size: 2rem; color: #3b82f6; font-weight: 700;">4</div>
                    <div style="color: #6b7280;">Em Andamento</div>
                </div>
                <div style="text-align: center; padding: 1.5rem; background: #f8fafc; border-radius: .5rem;">
                    <div style="font-size: 2rem; color: #10b981; font-weight: 700;">24</div>
                    <div style="color: #6b7280;">Resolvidas</div>
                </div>
            </div>
        </div>
    </main>

    <footer class="dashboard-footer">
        <p>Sistema Condomínio Digital &copy; <?php echo date('Y'); ?></p>
        <p>Desenvolvido por Nelma Odair Bila</p>
    </footer>

    <script>
        function openTab(tabName) {
            // Esconder todas as tabs
            document.querySelectorAll('.tab-pane').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remover active de todos os botões
            document.querySelectorAll('.tab').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Mostrar a tab selecionada
            document.getElementById(tabName).classList.add('active');
            
            // Ativar o botão da tab
            event.currentTarget.classList.add('active');
        }

        function atribuirOcorrencia(id) {
            const responsavel = prompt('Atribuir para qual responsável?');
            if(responsavel) {
                alert('Ocorrência ' + id + ' atribuída para: ' + responsavel);
            }
        }

        function marcarUrgente(id) {
            if(confirm('Marcar esta ocorrência como URGENTE?')) {
                alert('Ocorrência ' + id + ' marcada como urgente!');
            }
        }

        function marcarResolvida(id) {
            const solucao = prompt('Descreva a solução aplicada:');
            if(solucao) {
                alert('Ocorrência ' + id + ' marcada como resolvida!');
            }
        }

        function verDetalhesOcorrencia(id) {
            alert('Abrindo detalhes da ocorrência ' + id);
            // Redirecionar para página de detalhes
        }
    </script>
</body>
</html>