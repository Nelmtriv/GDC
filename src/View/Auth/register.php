<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Condomínio Digital</title>
    <link rel="stylesheet" href="../../../assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<body>
    <div class="login-box">
        <div class="logo">
            <h1>Condomínio Digital</h1>
            <p>Plataforma Digital de Automatizacao das Actividades de Gestao Condominial</p>
        </div>

        <?php if (isset($_GET['erro'])): ?>
            <div class="error-message" style="color:#b71c1c;background:#fdecea;padding:8px;border-radius:4px;margin:10px 0;">
                <?php echo htmlspecialchars($_GET['erro']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="../../Controller/Auth/registerAuth.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nome">nome</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="text" id="nome" name="nome" required>
                </div>
            </div>

            <div class="form-group">
                <label for="telefone">telefone</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="tel" id="telefone" name="telefone" required>
                </div>
            </div>

            <div class="form-group">
                <label for="nacionalidade">nacionalidade</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="text" id="nacionalidade" name="nacionalidade" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="tipo_documento">tipo_documento</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <select name="tipo_documento" id="tipo_documento" required>
                        <option value="" selected>Selecione uma opção</option>
                        <option value="BI">BI</option>
                        <option value="Passaporte">Passaporte</option>
                    </select>
                </div>
            </div>

            
            <div class="form-group">
                <label for="documento">Número documento</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="text" id="documento" name="documento" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="imagem_doc_path">Bilhete de Identidade:</label>
                <input type="file" name="imagem_doc_path" class="input-group" id="imagem_doc_path" accept="image/jpeg,image/png,image/gif,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" required>
                </div>
            </div>

            <div class="form-group">
                <label for="senha">Senha</label>
                <div class="input-group password-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="senha" name="senha" required>
                    <button type="button" class="toggle-password" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="tipo">tipo</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <select name="tipo" id="tipo" required>
                        <option value="" selected>Selecione uma opção</option>
                        <option value="Sindico">Sindico</option>
                        <option value="Porteiro">Porteiro</option>
                        <option value="Morador">Morador</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="login-btn">
                <i class="fas fa-sign-in-alt"></i>Registrar
            </button>
            <button type="button" onclick="window.location.href='login.php';" class="login-btn">
                <i class="fas fa-sign-un-alt"></i>Entrar
            </button>
        </form>

        <div class="footer">
            <p>&copy; <?= date('Y') ?> Condomínio Digital</p>
            <p>Suporte: +258 85 711 7699</p>
        </div>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const senhaInput = document.getElementById('senha');
            const icon = this.querySelector('i');

            if (senhaInput.type === 'password') {
                senhaInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                senhaInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Auto-focus no email
        document.getElementById('email').focus();

        // TESTE: Marcar visualmente os elementos para debug
        setTimeout(function() {
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.style.border = '2px solid rgba(151, 67, 215, 0.5)';
                setTimeout(() => {
                    input.style.border = '2px solid rgba(224, 224, 224, 0.7)';
                }, 1000);
            });
        }, 500);
    </script>
</body>

</html>