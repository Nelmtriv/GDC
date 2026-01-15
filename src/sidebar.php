<?php
// Dados do usuário (simulados para front-end)
$nome_usuario = "Nelma Bila";
$primeiro_nome = "Nelma";
$iniciais = "NB";
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="user-avatar-sidebar"><?php echo $iniciais; ?></div>
        <div class="user-info-sidebar">
            <div class="user-name"><?php echo $primeiro_nome; ?></div>
            <div class="user-role">Morador</div>
        </div>
    </div>
    
    <nav class="sidebar-menu">
        <a href="morador_dashboard.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'morador_dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        <a href="visitas.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'visitas.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-plus"></i>
            <span>Agendar Visitas</span>
        </a>
        <a href="reservas.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'reservas.php' ? 'active' : ''; ?>">
            <i class="fas fa-glass-cheers"></i>
            <span>Reservar Áreas</span>
        </a>
        <a href="encomendas.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'encomendas.php' ? 'active' : ''; ?>">
            <i class="fas fa-box"></i>
            <span>Encomendas</span>
        </a>
        <a href="avisos.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'avisos.php' ? 'active' : ''; ?>">
            <i class="fas fa-bullhorn"></i>
            <span>Avisos</span>
            <span class="badge">3</span>
        </a>
        <a href="ocorrencias.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'ocorrencias.php' ? 'active' : ''; ?>">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Ocorrências</span>
        </a>
        <a href="perfil.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'perfil.php' ? 'active' : ''; ?>">
            <i class="fas fa-user"></i>
            <span>Meu Perfil</span>
        </a>
    </nav>
    
    <div class="sidebar-footer">
        <a href="../index.php?action=logout" class="logout-btn-sidebar">
            <i class="fas fa-sign-out-alt"></i>
            <span>Sair do Sistema</span>
        </a>
    </div>
</aside>