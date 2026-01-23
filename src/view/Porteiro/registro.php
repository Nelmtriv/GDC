<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['tipo_usuario'] !== 'Porteiro') {
    header('Location: ../../login.php');
    exit();
}

require_once __DIR__ . '/../../data/conector.php';
$conexao = (new Conector())->getConexao();

/* BASE URL */
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
$base_url = $protocol . $_SERVER['HTTP_HOST'] . '/GDC';

/* VISITAS */
$stmt = $conexao->prepare("
    SELECT 
        a.id_agendamento,
        a.motivo,
        DATE(a.data) AS data,
        a.hora AS hora_agendada,
        r.entrada,
        r.saida,
        v.nome AS visitante_nome,
        v.documento,
        u.numero AS casa_numero
    FROM Agendamento a
    JOIN Visitante v ON v.id_visitante = a.id_visitante
    JOIN Morador m ON m.id_morador = a.id_morador
    JOIN Unidade u ON u.id_unidade = m.id_unidade
    LEFT JOIN Registro r ON r.id_agendamento = a.id_agendamento
    WHERE DATE(a.data) >= CURDATE()
    ORDER BY a.data, a.hora
");
$stmt->execute();
$visitas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Registro de Visitas</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
*{box-sizing:border-box;font-family:'Poppins',sans-serif}
body{margin:0;background:#f4f6f9;color:#1f2937}

.header{
    background:#fff;
    padding:20px 32px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:4px solid #4a148c;
    box-shadow:0 4px 12px rgba(0,0,0,.08)
}
.header h2{display:flex;gap:10px;align-items:center}
.header i{color:#4a148c}

.container{
    max-width:1200px;
    margin:30px auto;
    background:#fff;
    padding:30px;
    border-radius:14px;
    box-shadow:0 10px 25px rgba(0,0,0,.08)
}

/* FILTROS */
.filters{
    display:flex;
    gap:15px;
    margin-bottom:25px
}
.filters input,.filters select{
    padding:12px;
    border-radius:8px;
    border:1px solid #d1d5db
}

/* TABELA */
table{width:100%;border-collapse:collapse}
th{background:#f3f4f6;padding:14px;text-align:left}
td{padding:14px;border-bottom:1px solid #e5e7eb}
tr:hover{background:#fafafa}

/* STATUS */
.status{
    padding:6px 14px;
    border-radius:999px;
    font-size:12px;
    font-weight:600
}
.pendente{background:#fef3c7;color:#92400e}
.em{background:#ede9fe;color:#4a148c}
.done{background:#dcfce7;color:#166534}

/* BOTÕES */
.btn{padding:8px 14px;border-radius:8px;border:none;color:#fff;cursor:pointer}
.btn-in{background:#10b981}
.btn-out{background:#4a148c}
.btn-off{background:#9ca3af;cursor:not-allowed}
</style>
</head>

<body>

<header class="header">
    <h2><i class="fas fa-user-check"></i> Registro de Visitas</h2>
    <a href="index.php" style="text-decoration:none;color:#4a148c">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</header>

<div class="container">

    <!-- FILTROS -->
    <div class="filters">
        <input type="date" id="filtroData">
        <select id="filtroStatus">
            <option value="">Todos</option>
            <option value="pendente">Pendente</option>
            <option value="em">Em visita</option>
            <option value="done">Concluído</option>
        </select>
    </div>

    <table id="tabela">
        <thead>
            <tr>
                <th>Visitante</th>
                <th>Documento</th>
                <th>Motivo</th>
                <th>Casa</th>
                <th>Entrada</th>
                <th>Saída</th>
                <th>Status</th>
                <th>Ação</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($visitas as $v):

            if (!$v['entrada']) {
                $status = 'Pendente';
                $cls = 'pendente';
            } elseif (!$v['saida']) {
                $status = 'Em visita';
                $cls = 'em';
            } else {
                $status = 'Concluído';
                $cls = 'done';
            }
        ?>
        <tr data-data="<?= $v['data'] ?>" data-status="<?= $cls ?>">
            <td><?= htmlspecialchars($v['visitante_nome']) ?></td>
            <td><strong><?= htmlspecialchars($v['documento']) ?></strong></td>
            <td><?= htmlspecialchars($v['motivo']) ?></td>
            <td>Casa <?= $v['casa_numero'] ?></td>

            <td><?= $v['entrada'] ? date('H:i:s', strtotime($v['entrada'])) : '--' ?></td>
            <td><?= $v['saida'] ? date('H:i:s', strtotime($v['saida'])) : '--' ?></td>

            <td><span class="status <?= $cls ?>"><?= $status ?></span></td>

            <td>
                <?php if (!$v['entrada']): ?>
                    <button class="btn btn-in"
                        onclick="acao('entrada', <?= $v['id_agendamento'] ?>)">
                        Entrada
                    </button>
                <?php elseif (!$v['saida']): ?>
                    <button class="btn btn-out"
                        onclick="acao('saida', <?= $v['id_agendamento'] ?>)">
                        Saída
                    </button>
                <?php else: ?>
                    <button class="btn btn-off" disabled>Finalizado</button>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p id="semResultados" style="display:none;text-align:center;color:#6b7280;margin-top:20px">
        Nenhuma visita agendada para os filtros selecionados.
    </p>

</div>

<script>
function acao(tipo, id) {
    fetch('<?= $base_url ?>/src/controller/Porteiro/registro.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'acao=' + tipo + '&id_agendamento=' + id
    })
    .then(r => r.json())
    .then(d => d.success ? location.reload() : alert(d.message));
}

/* FILTROS */
document.querySelectorAll('#filtroData,#filtroStatus').forEach(el => {
    el.addEventListener('change', () => {
        const data = document.getElementById('filtroData').value;
        const status = document.getElementById('filtroStatus').value;
        let visiveis = 0;

        document.querySelectorAll('#tabela tbody tr').forEach(tr => {
            const okData = !data || tr.dataset.data === data;
            const okStatus = !status || tr.dataset.status === status;
            tr.style.display = (okData && okStatus) ? '' : 'none';
            if (okData && okStatus) visiveis++;
        });

        document.getElementById('semResultados').style.display =
            visiveis === 0 ? 'block' : 'none';
    });
});

</script>

</body>
</html>
