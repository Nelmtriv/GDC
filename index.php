<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Condomínio Digital</title>
    <link rel="stylesheet" href="../../../assets/css/morador.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Cabeçalho do Dashboard -->
    <header class="dashboard-header">
        <div class="header-left">
            <h2><i class="fas fa-building"></i> Condomínio Digital</h2>
            <div class="header-subtitle">Dashboard do Morador</div>
        </div>

        <div class="user-info">
            
           <a href="/GDC/src/View/Auth/login.php" 
            class="login-btn" >
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
            <a href="/GDC/src/View/Auth/register.php" 
            class="register-btn" >
                <i class="fas fa-sign-un-alt"></i> Register
            </a>

        </div>
    </header>

    
    <footer class="dashboard-footer">
        <p>Sistema Condomínio Digital &copy; <?php echo date('Y'); ?></p>
        <p>Desenvolvido por Nelma Odair Bila</p>
    </footer>

</body>

</html>