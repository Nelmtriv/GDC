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
    <title>Gerenciar Moradores - Síndico</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        color: #1f2937;
        min-height: 100vh;
    }

    .dashboard-header {
        background: white;
        color: #1f2937;
        padding: 1.5rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-bottom: 3px solid #7e22ce;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .dashboard-header h2 {
        font-size: 1.5rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: .75rem;
    }

    .dashboard-header h2 i {
        color: #7e22ce;
    }

    .header-subtitle {
        font-size: .875rem;
        color: #6b7280;
        margin-top: .25rem;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .user-avatar {
        width: 50px;
        height: 50px;
        background: #7e22ce;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .user-details {
        text-align: right;
    }

    .user-name {
        font-weight: 500;
        font-size: 1rem;
        color: #1f2937;
    }

    .user-role {
        font-size: .75rem;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: .25rem;
        margin-top: .125rem;
    }

    .logout-btn,
    .back-btn {
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

    .logout-btn:hover,
    .back-btn:hover {
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
    .dashboard-container {
        max-width: 1400px;
        margin: 2rem auto;
        padding: 0 1.25rem;
    }

    /* Page header */
    .page-header {
        background: white;
        border-radius: .75rem;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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

    .page-header h1 i {
        color: #7e22ce;
    }

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

    .btn-secondary {
        background: #6b7280;
        color: white;
    }

    .btn-secondary:hover {
        background: #4b5563;
    }

    .btn-success {
        background: #10b981;
        color: white;
    }

    .btn-success:hover {
        background: #059669;
    }

    /* Table */
    .content-card {
        background: white;
        border-radius: .75rem;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .table-responsive {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .data-table th {
        background: #f8fafc;
        color: #1f2937;
        font-weight: 600;
        padding: 1rem;
        text-align: left;
        border-bottom: 2px solid #e5e7eb;
    }

    .data-table td {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    .data-table tr:hover {
        background: #f9fafb;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .status-active {
        background: #d1fae5;
        color: #065f46;
    }

    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-icon {
        width: 36px;
        height: 36px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-edit {
        background: #dbeafe;
        color: #1e40af;
    }

    .btn-edit:hover {
        background: #bfdbfe;
    }

    .btn-delete {
        background: #fee2e2;
        color: #dc2626;
    }

    .btn-delete:hover {
        background: #fecaca;
    }

    .btn-view {
        background: #dcfce7;
        color: #166534;
    }

    .btn-view:hover {
        background: #bbf7d0;
    }

    /* Filters */
    .filters {
        background: white;
        border-radius: .75rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .filter-row {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-group label {
        display: block;
        margin-bottom: .5rem;
        color: #1f2937;
        font-weight: 500;
    }

    .filter-group select,
    .filter-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 1rem;
    }

    /* Footer */
    .dashboard-footer {
        background: white;
        color: #6b7280;
        text-align: center;
        padding: 1.5rem;
        margin-top: 3rem;
        border-top: 1px solid #e5e7eb;
    }

    @media (max-width:768px) {
        .dashboard-header {
            flex-direction: column;
            padding: 1.25rem;
            text-align: center;
            gap: 1rem;
        }

        .user-info {
            flex-direction: column;
            gap: 1rem;
        }

        .user-details {
            text-align: center;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .page-actions {
            width: 100%;
            justify-content: center;
        }
    }
    </style>
</head>

<body>
    <!-- Cabeçalho -->
    <header class="dashboard-header">
        <div>
            <h2><i class="fas fa-building"></i> Gestão Condominial</h2>
            <div class="header-subtitle">Gerenciamento de Moradores</div>
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
            <h1><i class="fas fa-users"></i> Gerenciar Moradores</h1>
            <div class="page-actions">
                <a href="novoUser.php" class="btn btn-success">
                    <i class="fas fa-user-plus"></i> Novo Morador
                </a>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filters">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="search"><i class="fas fa-search"></i> Pesquisar</label>
                    <input type="text" id="search" placeholder="Nome, unidade... ">
                </div>
                <div class="filter-group">
                    <label for="status"><i class="fas fa-filter"></i> Status</label>
                    <select id="status">
                        <option value="">Todos</option>
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                        <option value="pendente">Pendente</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="bloco"><i class="fas fa-building"></i> Bloco</label>
                    <select id="bloco">
                        <option value="">Todos</option>
                        <option value="A">Bloco A</option>
                        <option value="B">Bloco B</option>
                        <option value="C">Bloco C</option>
                    </select>
                </div>
            </div>
            <button class="btn btn-primary">
                <i class="fas fa-filter"></i> Aplicar Filtros
            </button>
        </div>

        <!-- Lista de Moradores -->
        <div class="content-card">
            <h3><i class="fas fa-list"></i> Lista de Moradores</h3>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Unidade</th>
                            <th>Telefone</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Buscar todos os moradores reais do banco de dados
                        $query = "SELECT m.id_morador, m.nome, m.telefone, u.email, un.numero as unidade 
                                  FROM Morador m
                                  INNER JOIN Usuario u ON m.id_usuario = u.id_usuario
                                  LEFT JOIN Unidade un ON m.id_unidade = un.id_unidade
                                  ORDER BY m.nome ASC";
                        
                        $resultado = $conexao->query($query);
                        
                        if ($resultado && $resultado->num_rows > 0) {
                            while ($morador = $resultado->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $morador['id_morador'] . "</td>";
                                echo "<td>" . htmlspecialchars($morador['nome']) . "</td>";
                                echo "<td>" . htmlspecialchars($morador['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($morador['unidade']) . "</td>";
                                echo "<td>" . htmlspecialchars($morador['telefone'] ?? 'N/A') . "</td>";
                                echo "<td>";
                                echo "<div class='action-buttons'>";
                                echo "<a href='#' class='btn-icon btn-view' title='Visualizar'>";
                                echo "<i class='fas fa-eye'></i>";
                                echo "</a>";
                                echo "<a href='#' class='btn-icon btn-edit' title='Editar'>";
                                echo "<i class='fas fa-edit'></i>";
                                echo "</a>";
                                echo "<a href='#' class='btn-icon btn-delete' title='Excluir' onclick='return confirm(\"Tem certeza?\")'>";
                                echo "<i class='fas fa-trash'></i>";
                                echo "</a>";
                                echo "</div>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align:center; color:#6b7280;'>Nenhum morador cadastrado</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>


        </td>
        </tr>
        </tbody>
        </table>
        </div>
        </div>
    </main>

    <footer class="dashboard-footer">
        <p>Sistema Condomínio Digital &copy; <?php echo date('Y'); ?></p>
        <p>Desenvolvido por Nelma Odair Bila</p>
    </footer>

    <script>
    document.getElementById('search').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.data-table tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    document.getElementById('status').addEventListener('change', function(e) {
        const status = e.target.value;
        const rows = document.querySelectorAll('.data-table tbody tr');

        rows.forEach(row => {
            if (!status) {
                row.style.display = '';
                return;
            }

            const statusCell = row.querySelector('.status-badge');
            if (statusCell) {
                const rowStatus = statusCell.textContent.toLowerCase();
                row.style.display = rowStatus.includes(status) ? '' : 'none';
            }
        });
    });
    </script>
</body>

</html>