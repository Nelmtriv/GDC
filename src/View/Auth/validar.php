<?php
session_start();

// ✅ Verificar se há sessão pendente
if (!isset($_SESSION['pending_user_id'])) {
    header("Location: /GDC/src/View/Auth/login.php?erro=" . urlencode("Faça login primeiro"));
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validar - Condomínio Digital</title>
    <link rel="stylesheet" href="../../../assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-box">
        <div class="logo">
            <h1>Condomínio Digital</h1>
            <p>Verificação de Segurança</p>
        </div>

        <?php if (isset($_GET['erro'])): ?>
            <div class="error-message" style="color:#b71c1c;background:#fdecea;padding:12px;border-radius:4px;margin:15px 0;">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($_GET['erro']); ?>
            </div>
        <?php endif; ?>

        <div style="background:#e3f2fd;padding:12px;border-radius:4px;margin:15px 0;color:#1976d2;">
            <i class="fas fa-info-circle"></i>
            Enviamos um código de 6 dígitos para <strong><?php echo htmlspecialchars($_SESSION['pending_email'] ?? ''); ?></strong>
        </div>

        <form method="POST" action="../../Controller/Auth/authConfirmation.php">
            <div class="form-group">
                <label for="codigo">Código de Verificação</label>
                <div class="input-group">
                    <i class="fas fa-shield-alt"></i>
                    <input type="text" 
                           id="codigo" 
                           name="codigo" 
                           maxlength="6" 
                           pattern="\d{6}" 
                           placeholder="000000"
                           required 
                           autofocus>
                </div>
            </div>

            <button type="submit" class="login-btn">
                <i class="fas fa-check-circle"></i> Validar Código
            </button>
            
            <button type="button" 
                    onclick="window.location.href='login.php';" 
                    class="login-btn" 
                    style="background:#757575;margin-top:10px;">
                <i class="fas fa-arrow-left"></i> Voltar ao Login
            </button>
        </form>

        <div class="footer">
            <p>© <?= date('Y') ?> Condomínio Digital</p>
            <p>Suporte: +258 85 711 7699</p>
        </div>
    </div>

    <script>
        // Auto-focus no código
        document.getElementById('codigo').focus();

        // Permitir apenas números
        document.getElementById('codigo').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>