
<?php
require_once __DIR__ . '/../../Controller/Auth/session.php';
require_once __DIR__ . '/../../Data/conector.php';
/* =========================
   PROTEÇÃO POR TIPO DE USUÁRIO
========================= */
if (
    !isset($_SESSION['tipo']) ||
    $_SESSION['tipo'] !== 'Morador'
) {
    header('Location: ../../Auth/login.php');
    exit;
}

// $conexao = (new Conector())->getConexao();
$conn = new Conector();
$conexao = $conn->getConexao();


/* =========================
   BUSCAR MORADOR
========================= */
$stmt = $conexao->prepare("
    SELECT m.id_morador, m.id_unidade, u.nome
    FROM Morador m
    INNER JOIN Usuario u ON m.id_usuario = u.id_usuario
    WHERE m.id_usuario = ?");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$morador = $stmt->get_result()->fetch_assoc();

// if (!$morador) {
//     session_destroy();
//     header("Location: ../../login.php");
//     exit;
// }

$idMorador   = $morador['id_morador'];
$nomeMorador = $morador['nome'];
$iniciais    = strtoupper(substr($nomeMorador, 0, 1));

/* =========================
   BUSCAR VISITAS
========================= */
$stmt = $conexao->prepare("
    SELECT 
        a.data,
        a.hora,
        a.motivo,
        -- v.nome AS visitante,
        -- v.documento,
        r.entrada,
        r.saida
    FROM Agendamento a
    JOIN Visitante v ON v.id_visitante = a.id_visitante
    LEFT JOIN Registro r ON r.id_agendamento = a.id_agendamento
    WHERE a.id_morador = ?
    ORDER BY a.data DESC, a.hora DESC
");
$stmt->bind_param("i", $idMorador);
$stmt->execute();
$visitas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Minhas Visitas</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
* {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f4f6f9;
            margin: 0;
        }

        /* CONTAINER */
        .container {
            max-width: 1100px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
        }

        /* HEADER */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .header h2 {
            color: #333;
            display: flex;
            gap: 10px;
        }

        .btn-add {
            background: #9743d7;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: .3s;
        }

        .btn-add:hover {
            background: #8639c2;
            transform: translateY(-2px);
        }

        /* GRID DE VISITAS */
        .visitas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }

        /* CARD */
        .visita-card {
            background: #fafafa;
            border-radius: 12px;
            padding: 20px;
            border-left: 5px solid #9743d7;
            box-shadow: 0 6px 15px rgba(0, 0, 0, .06);
            transition: .3s;
        }

        .visita-card:hover {
            transform: translateY(-4px);
        }

        .visita-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .visita-header h4 {
            margin: 0;
            color: #333;
        }

        .status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pendente {
            background: #fff3cd;
            color: #856404;
        }

        /* INFO */
        .visita-info {
            font-size: 14px;
            color: #555;
        }

        .visita-info div {
            margin-bottom: 6px;
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .visita-info i {
            color: #9743d7;
        }

        /* MODAL */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .55);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal {
            background: #ffffff;
            width: 500px;
            padding: 30px;
            border-radius: 14px;
        }

        .modal h3 {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-size: 14px;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        input:focus {
            outline: none;
            border-color: #9743d7;
        }

        .modal button {
            width: 100%;
            padding: 14px;
            background: #9743d7;
            color: #fff;
            border: none;
            border-radius: 8px;
            margin-top: 10px;
        }

        .close {
            background: #6c757d;
        }

        /* ===== LAYOUT ===== */
        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 240px;
            background: #9743d7;
            color: #ffffff;
            padding: 25px 20px;
            display: flex;
            flex-direction: column;
        }

        /* TÍTULO */
        .sidebar h2 {
            font-size: 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }

        /* NAV */
        .sidebar nav {
            display: flex;
            flex-direction: column;
        }

        /* LINKS */
        .sidebar nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            color: #ffffff;
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 10px;
            font-size: 15px;
            transition: background 0.2s ease, color 0.2s ease;
            background: transparent;
            /* IMPORTANTE */
        }

        /* ÍCONES */
        .sidebar nav a i {
            color: #ffffff;
        }

        /* HOVER (somente quando NÃO ativo) */
        .sidebar nav a:hover:not(.active) {
            background: rgba(255, 255, 255, 0.18);
        }

        /* ===== ITEM ATIVO — BRANCO REAL ===== */
        .sidebar nav a.active {
            background: #ffffff !important;
            color: #9743d7 !important;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* ÍCONE DO ATIVO */
        .sidebar nav a.active i {
            color: #9743d7 !important;
        }

        /* LOGOUT */
        .sidebar .logout {
            margin-top: auto;
            background: rgba(0, 0, 0, 0.25);
        }

        /* ===== CONTEÚDO ===== */
        .content {
            flex: 1;
            padding: 40px;
            background: #f4f6f9;
        }

        /* ===== LAYOUT ===== */
        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 240px;
            background: #9743d7;
            color: #ffffff;
            padding: 25px 20px;
            display: flex;
            flex-direction: column;
        }

        /* TÍTULO */
        .sidebar h2 {
            font-size: 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }

        /* NAV */
        .sidebar nav {
            display: flex;
            flex-direction: column;
        }

        /* LINKS */
        .sidebar nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            color: #ffffff;
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 10px;
            font-size: 15px;
            transition: background 0.2s ease, color 0.2s ease;
            background: transparent;
            /* IMPORTANTE */
        }

        /* ÍCONES */
        .sidebar nav a i {
            color: #ffffff;
        }

        /* HOVER (somente quando NÃO ativo) */
        .sidebar nav a:hover:not(.active) {
            background: rgba(255, 255, 255, 0.18);
        }

        /* ===== ITEM ATIVO — BRANCO REAL ===== */
        .sidebar nav a.active {
            background: #ffffff !important;
            color: #9743d7 !important;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* ÍCONE DO ATIVO */
        .sidebar nav a.active i {
            color: #9743d7 !important;
        }

        /* LOGOUT */
        .sidebar .logout {
            margin-top: auto;
            background: rgba(0, 0, 0, 0.25);
        }

        /* ===== CONTEÚDO ===== */
        .content {
            flex: 1;
            padding: 40px;
            background: #f4f6f9;
        }


        .dashboard-header {
            background: rgba(255, 255, 255, 0.5);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-bottom: 3px solid #9743d7;
        }

        .header-left h2 {
            color: #333;
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-left h2 i {
            color: #9743d7;
        }

        .header-subtitle {
            color: #222;
            font-size: 14px;
            margin-top: 5px;
        }
        /* BOTÃO */
        .btn-primary {
            background: #9743d7;
            color: #ffffff;
            border: none;
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;

            display: flex;
            align-items: center;
            gap: 8px;

            transition: 0.25s ease;
        }

        .btn-primary:hover {
            background: #7e22ce;
            transform: translateY(-2px);
        }

        /* USER */
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #9743d7;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .page-content {
            padding: 0 25px 40px;
        }

        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
                margin: 15px;
            }

            .header-actions {
                width: 100%;
                justify-content: space-between;
            }
        }
        .page-actions {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 25px;
}

/* STATUS */
.status{padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600}
.status-pendente{background:#fff3cd;color:#856404}
.status-em{background:#ede9fe;color:#4a148c}
.status-done{background:#dcfce7;color:#166534}

/* BOTÃO */
.btn-primary{
    background:#9743d7;color:#fff;border:none;
    padding:12px 18px;border-radius:10px;
    cursor:pointer;display:flex;gap:8px;align-items:center;

}
/* SELECT DOCUMENTO */
.select-doc {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    background: #f9fafb;
    font-size: 14px;
    cursor: pointer;
}

.select-doc:focus {
    outline: none;
    border-color: #9743d7;
    background: #ffffff;
}

/* INPUT DOCUMENTO */
#numero_documento {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    font-size: 14px;
}

#numero_documento:focus {
    outline: none;
    border-color: #9743d7;
}

/* ESCONDER */
.hidden {
    display: none;
}

</style>
</head>

<body>

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <h2><i class="fas fa-home"></i> Morador</h2>
        <nav>
            <a href="index.php"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a href="agendar_visita.php" class="active"><i class="fas fa-users"></i> Visitas</a>
            <a href="reservas.php"><i class="fas fa-calendar-check"></i> Reservas</a>
            <a href="encomendas.php"><i class="fas fa-box"></i> Encomendas</a>
            <a href="avisos.php"><i class="fas fa-bullhorn"></i> Avisos</a>
            <a href="ocorrencias.php"><i class="fas fa-exclamation-triangle"></i> Ocorrências</a>
<a href="../../logout.php?logout=1" 
   class="logout" 
   onclick="return confirmarSaida();">
    <i class="fas fa-sign-out-alt"></i> Sair
</a>

        </nav>
    </aside>

    <!-- CONTEÚDO -->
    <main class="content">

        <header class="dashboard-header">
            <div class="header-left">
                <h2><i class="fas fa-users"></i> Minhas Visitas</h2>
                <div class="header-subtitle">Consulte e agende visitas</div>
            </div>
            <div class="user-info">
                <div class="user-avatar"><?= $iniciais ?></div>
                <strong><?= htmlspecialchars($nomeMorador) ?></strong>
            </div>
        </header>

        <div class="container">

            <div class="page-actions">
                <button class="btn-primary" onclick="abrirModal()">
                    <i class="fas fa-calendar-plus"></i> Agendar Visita
                </button>
            </div>

            <?php if (empty($visitas)): ?>
                <p style="color:#777">Nenhuma visita agendada.</p>
            <?php else: ?>

            <table style="width:100%;border-collapse:collapse;background:#fff">
                <thead>
                    <tr style="background:#f3f4f6">
                        <th style="padding:14px">Visitante</th>
                        <th>Documento</th>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Motivo</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>

                <?php foreach ($visitas as $v): ?>
                    <?php
                    if (!$v['entrada']) {
                        $status = 'Agendada';
                        $cls = 'status-pendente';
                    } elseif (!$v['saida']) {
                        $status = 'Em visita';
                        $cls = 'status-em';
                    } else {
                        $status = 'Concluída';
                        $cls = 'status-done';
                    }
                    ?>

                    <tr style="border-bottom:1px solid #e5e7eb">
                        <td style="padding:14px"><?= htmlspecialchars($v['visitante']) ?></td>
                        <td><?= htmlspecialchars($v['documento']) ?></td>
                        <td><?= date('d/m/Y', strtotime($v['data'])) ?></td>
                        <td><?= date('H:i', strtotime($v['hora'])) ?></td>
                        <td><?= htmlspecialchars($v['motivo']) ?></td>
                        <td><span class="status <?= $cls ?>"><?= $status ?></span></td>
                    </tr>
                <?php endforeach; ?>

                </tbody>
            </table>

            <?php endif; ?>

        </div>
    </main>
</div>

<!-- MODAL -->
<div class="modal-overlay" id="modal">
    <div class="modal">
        <h3>
            <i class="fas fa-calendar-plus"></i>
            Agendar Visita
        </h3>

        <form id="formVisita" action="../../controller/Morador/visitas.php" method="POST">

            <!-- NOME -->
            <div class="form-group">
                <label>Nome do Visitante</label>
                <input type="text" name="nome_visitante" required>
            </div>

            <!-- TIPO DOCUMENTO -->
            <div class="form-group">
                <label>Tipo de Documento</label>
                <select id="tipo_documento" class="select-doc" required>
                    <option value="">Selecione o tipo</option>
                    <option value="BI">Bilhete de Identidade</option>
                    <option value="PASSAPORTE">Passaporte</option>
                    <option value="CARTA">Carta de Condução</option>
                </select>
            </div>

            <div class="form-group hidden" id="grupo_numero">
                <label>Número do Documento</label>
                <input type="text" id="numero_documento">
            </div>

            <!-- CAMPO REAL ENVIADO -->
            <input type="hidden" name="documento" id="documento_final">

            <!-- MOTIVO -->
            <div class="form-group">
                <label>Motivo da Visita</label>
                <input type="text" name="motivo" required>
            </div>

            <!-- DATA -->
            <div class="form-group">
                <label>Data</label>
                <input type="date" name="data" required min="<?= date('Y-m-d') ?>">
            </div>

            <!-- HORA -->
            <div class="form-group">
                <label>Hora</label>
                <input type="time" name="hora" required>
            </div>

            <!-- BOTÕES -->
            <button type="submit" class="btn-primary">
                <i class="fas fa-check"></i> Agendar
            </button>

            <button type="button" class="close" onclick="fecharModal()">
                Cancelar
            </button>

        </form>
    </div>
</div>


<script>
function abrirModal() {
    document.getElementById('modal').style.display = 'flex';
}

function fecharModal() {
    document.getElementById('modal').style.display = 'none';
}

const tipoDoc = document.getElementById('tipo_documento');
const grupoNumero = document.getElementById('grupo_numero');
const numeroDoc = document.getElementById('numero_documento');
const finalDoc = document.getElementById('documento_final');
const form = document.getElementById('formVisita');

tipoDoc.addEventListener('change', () => {

    if (!tipoDoc.value) {
        grupoNumero.classList.add('hidden');
        numeroDoc.value = '';
        return;
    }

    grupoNumero.classList.remove('hidden');

    const placeholders = {
        BI: 'Ex: 110102345678A',
        PASSAPORTE: 'Ex: M1234567',
        CARTA: 'Ex: C-123456'
    };

    numeroDoc.placeholder = placeholders[tipoDoc.value];
});

form.addEventListener('submit', (e) => {

    if (!tipoDoc.value || !numeroDoc.value.trim()) {
        e.preventDefault();
        alert('Selecione o tipo e informe o número do documento');
        return;
    }

    finalDoc.value = tipoDoc.value + ': ' + numeroDoc.value.trim();
});
</script>



</body>
</html>
