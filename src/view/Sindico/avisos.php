<?php
require_once __DIR__ . '/../../data/conector.php';

session_start();

$conector = new Conector();
$conexao = $conector->getConexao();

$stmt = $conexao->prepare("Select * from sindico Where id_usuario = ?");
$stmt->bind_param("s", $_SESSION['id']);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $row = $resultado->fetch_assoc();
    $userName = $row['nome'];
    $iniciais = strtoupper(substr($userName, 0, 1));
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Avisos - Síndico</title>
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

        /* Formulário de Aviso */
        .form-card {
            background: white;
            border-radius: .75rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            margin-bottom: .5rem;
            color: #1f2937;
            font-weight: 500;
        }
        .form-input, .form-textarea, .form-select {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .form-input:focus, .form-textarea:focus, .form-select:focus {
            outline: none;
            border-color: #7e22ce;
        }
        .form-textarea {
            min-height: 150px;
            resize: vertical;
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }

        /* Lista de Avisos */
        .avisos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .aviso-card {
            background: white;
            border-radius: .75rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }
        .aviso-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        }
        .aviso-card.urgente {
            border-left: 4px solid #ef4444;
        }
        .aviso-card.importante {
            border-left: 4px solid #f59e0b;
        }
        .aviso-card.normal {
            border-left: 4px solid #3b82f6;
        }
        .aviso-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .aviso-title {
            font-weight: 600;
            color: #1f2937;
            font-size: 1.1rem;
            flex: 1;
        }
        .aviso-badges {
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
        .badge-importante { background: #fef3c7; color: #92400e; }
        .badge-normal { background: #dbeafe; color: #1e40af; }
        .aviso-content {
            color: #4b5563;
            line-height: 1.5;
            margin-bottom: 1rem;
            max-height: 100px;
            overflow: hidden;
            position: relative;
        }
        .aviso-content.expanded {
            max-height: none;
        }
        .read-more {
            color: #7e22ce;
            cursor: pointer;
            font-weight: 500;
            margin-top: .5rem;
            display: inline-block;
        }
        .aviso-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #6b7280;
            font-size: 0.875rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }
        .aviso-actions {
            display: flex;
            gap: .5rem;
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
            .avisos-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <header class="dashboard-header">
        <div>
            <h2><i class="fas fa-building"></i> Gestão Condominial</h2>
            <div class="header-subtitle">Publicação de Avisos</div>
        </div>

        <div class="user-info">
            <div class="user-avatar"><?php echo $iniciais; ?></div>
            <div class="user-details">
                <div class="user-name"><?php echo $userName; ?></div>
                <div class="user-role"><i class="fas fa-user-shield"></i> Síndico</div>
            </div>
            <a href="index.php" class="back-btn">
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
            <h1><i class="fas fa-bullhorn"></i> Publicar Avisos</h1>
            <div class="page-actions">
                <a href="#formulario" class="btn btn-success">
                    <i class="fas fa-plus-circle"></i> Novo Aviso
                </a>
                <a href="#" class="btn btn-primary">
                    <i class="fas fa-history"></i> Histórico
                </a>
            </div>
        </div>

        <!-- Formulário de Novo Aviso -->
        <div class="form-card" id="formulario">
            <h3><i class="fas fa-edit"></i> Novo Aviso</h3>
            <form id="avisoForm">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Título do Aviso *</label>
                        <input type="text" class="form-input" placeholder="Ex: Manutenção programada dos elevadores" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Categoria *</label>
                        <select class="form-select" required>
                            <option value="">Selecione...</option>
                            <option value="manutencao">Manutenção</option>
                            <option value="reuniao">Reunião</option>
                            <option value="pagamento">Pagamentos</option>
                            <option value="seguranca">Segurança</option>
                            <option value="outros">Outros</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Prioridade *</label>
                        <select class="form-select" required>
                            <option value="normal">Normal</option>
                            <option value="importante">Importante</option>
                            <option value="urgente">Urgente</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Data de Expiração</label>
                        <input type="date" class="form-input">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Conteúdo do Aviso *</label>
                    <textarea class="form-textarea" placeholder="Digite o conteúdo do aviso aqui..." rows="6" required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Destinatários</label>
                    <div class="form-row">
                        <div class="checkbox-group">
                            <input type="checkbox" id="todos" checked>
                            <label for="todos">Todos os moradores</label>
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="proprietarios">
                            <label for="proprietarios">Apenas proprietários</label>
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="funcionarios">
                            <label for="funcionarios">Funcionários</label>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane"></i> Publicar Aviso
                    </button>
                    <button type="button" class="btn btn-primary" onclick="salvarRascunho()">
                        <i class="fas fa-save"></i> Salvar Rascunho
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Limpar
                    </button>
                </div>
            </form>
        </div>

        <!-- Avisos Recentes -->
        <div class="form-card">
            <h3><i class="fas fa-clock"></i> Avisos Recentes</h3>
            <div class="avisos-grid">
                <!-- Aviso 1 -->
                <div class="aviso-card urgente">
                    <div class="aviso-header">
                        <div class="aviso-title">INTERRUPÇÃO DO FORNECIMENTO DE ÁGUA</div>
                        <div class="aviso-badges">
                            <span class="badge badge-urgente">Urgente</span>
                        </div>
                    </div>
                    <div class="aviso-content">
                        Informamos que haverá interrupção no fornecimento de água no dia 20/03/2024, das 08h às 17h, para manutenção preventiva no sistema hidráulico...
                        <span class="read-more" onclick="toggleReadMore(this)">Ler mais</span>
                    </div>
                    <div class="aviso-footer">
                        <div>
                            <i class="fas fa-calendar"></i> 15/03/2024
                        </div>
                        <div class="aviso-actions">
                            <button class="btn btn-primary btn-sm" onclick="editarAviso(1)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="excluirAviso(1)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Aviso 2 -->
                <div class="aviso-card importante">
                    <div class="aviso-header">
                        <div class="aviso-title">REUNIÃO DE CONDOMÍNIOS - ABRIL</div>
                        <div class="aviso-badges">
                            <span class="badge badge-importante">Importante</span>
                        </div>
                    </div>
                    <div class="aviso-content">
                        Convocamos todos os condôminos para a reunião ordinária do mês de abril, que ocorrerá no dia 25/04/2024, às 19h30, no salão de festas...
                        <span class="read-more" onclick="toggleReadMore(this)">Ler mais</span>
                    </div>
                    <div class="aviso-footer">
                        <div>
                            <i class="fas fa-calendar"></i> 10/03/2024
                        </div>
                        <div class="aviso-actions">
                            <button class="btn btn-primary btn-sm" onclick="editarAviso(2)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="excluirAviso(2)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Aviso 3 -->
                <div class="aviso-card normal">
                    <div class="aviso-header">
                        <div class="aviso-title">MANUTENÇÃO DOS ELEVADORES</div>
                        <div class="aviso-badges">
                            <span class="badge badge-normal">Normal</span>
                        </div>
                    </div>
                    <div class="aviso-content">
                        Está programada a manutenção preventiva dos elevadores do condomínio para os dias 18 e 19/03/2024. Cada elevador ficará indisponível por aproximadamente 4 horas...
                        <span class="read-more" onclick="toggleReadMore(this)">Ler mais</span>
                    </div>
                    <div class="aviso-footer">
                        <div>
                            <i class="fas fa-calendar"></i> 05/03/2024
                        </div>
                        <div class="aviso-actions">
                            <button class="btn btn-primary btn-sm" onclick="editarAviso(3)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="excluirAviso(3)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div style="background: white; border-radius: .75rem; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h3><i class="fas fa-chart-line"></i> Estatísticas de Avisos</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
                <div style="text-align: center; padding: 1.5rem; background: #f8fafc; border-radius: .5rem;">
                    <div style="font-size: 2rem; color: #7e22ce; font-weight: 700;">24</div>
                    <div style="color: #6b7280;">Total Publicados</div>
                </div>
                <div style="text-align: center; padding: 1.5rem; background: #f8fafc; border-radius: .5rem;">
                    <div style="font-size: 2rem; color: #ef4444; font-weight: 700;">5</div>
                    <div style="color: #6b7280;">Urgentes</div>
                </div>
                <div style="text-align: center; padding: 1.5rem; background: #f8fafc; border-radius: .5rem;">
                    <div style="font-size: 2rem; color: #f59e0b; font-weight: 700;">8</div>
                    <div style="color: #6b7280;">Importantes</div>
                </div>
                <div style="text-align: center; padding: 1.5rem; background: #f8fafc; border-radius: .5rem;">
                    <div style="font-size: 2rem; color: #10b981; font-weight: 700;">142</div>
                    <div style="color: #6b7280;">Visualizações</div>
                </div>
            </div>
        </div>
    </main>

    <footer class="dashboard-footer">
        <p>Sistema Condomínio Digital &copy; <?php echo date('Y'); ?></p>
        <p>Desenvolvido por Nelma Odair Bila</p>
    </footer>

    <script>
        // Formulário de aviso
        document.getElementById('avisoForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Aviso publicado com sucesso!');
            // Aqui você enviaria os dados para o servidor
        });

        function salvarRascunho() {
            alert('Rascunho salvo com sucesso!');
            // Aqui você salvaria como rascunho
        }

        function toggleReadMore(element) {
            const content = element.parentElement;
            content.classList.toggle('expanded');
            element.textContent = content.classList.contains('expanded') ? 'Ler menos' : 'Ler mais';
        }

        function editarAviso(id) {
            alert('Editando aviso ' + id);
            // Aqui você carregaria o aviso para edição
        }

        function excluirAviso(id) {
            if(confirm('Tem certeza que deseja excluir este aviso?')) {
                alert('Aviso ' + id + ' excluído!');
                // Aqui você excluiria o aviso
            }
        }

        // Validação de data
        const dataInput = document.querySelector('input[type="date"]');
        const hoje = new Date().toISOString().split('T')[0];
        dataInput.min = hoje;
    </script>
</body>
</html>