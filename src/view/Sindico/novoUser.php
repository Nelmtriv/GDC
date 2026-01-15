<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Morador</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/colors.css">
    <style>
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../../../assets/img/coco.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            max-width: 500px;
            width: 100%;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .back-btn-header {
            background-color: var(--color-gray);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .back-btn-header:hover {
            background-color: var(--color-dark);
            transform: translateX(-3px);
        }
        
        .alert-success, .alert-error {
            animation: slideDown 0.4s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card fade-in">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="fas fa-user-plus"></i>
                    <h3>Criar Novo Morador</h3>
                </div>
                <a href="moradores.php" class="back-btn-header">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
            <div class="card-body">
                <?php
                session_start();
                $mostrou_mensagem = false;
                if (isset($_SESSION['mensagem'])) {
                    $tipo = $_SESSION['tipo_mensagem'] === 'sucesso' ? 'alert-success' : 'alert-error';
                    echo "<div class='$tipo' id='alerta'>";
                    echo $_SESSION['mensagem'];
                    echo "</div>";
                    $mostrou_mensagem = true;
                    unset($_SESSION['mensagem']);
                    unset($_SESSION['tipo_mensagem']);
                }
                ?>
                
                <form action="../../controller/Sindico/user.php" method="post" id="form-novo-morador">
                    <div class="form-group">
                        <label for="nome">Nome Completo:</label>
                        <input type="text" id="nome" name="nome" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="senha">Senha</label>
                        <input type="password" id="senha" name="senha" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="unidade">Unidade</label>
                        <select name="unidade" id="unidade" required>
                            <option value="">-- Selecione uma unidade --</option>
                            <?php
                            require_once '../../data/conector.php';
                            $conector = new Conector();
                            $conexao = $conector->getConexao();
                            
                            $resultado = $conexao->query("SELECT id_unidade, numero FROM Unidade ORDER BY numero");
                            
                            if ($resultado && $resultado->num_rows > 0) {
                                while ($unidade = $resultado->fetch_assoc()) {
                                    echo "<option value='" . $unidade['id_unidade'] . "'>" . $unidade['numero'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="telefone">Telefone</label>
                        <input type="tel" id="telefone" name="telefone">
                    </div>
                    
                    <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">
                        <i class="fas fa-save"></i> Criar Morador
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Se houve sucesso, limpar mensagem após 3s e resetar formulário
        <?php if ($mostrou_mensagem && $_SESSION['tipo_mensagem'] ?? null === 'sucesso'): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const alerta = document.getElementById('alerta');
            if (alerta) {
                setTimeout(function() {
                    alerta.style.opacity = '0';
                    alerta.style.transition = 'opacity 0.4s ease-out';
                    
                    setTimeout(function() {
                        alerta.style.display = 'none';
                        document.getElementById('form-novo-morador').reset();
                    }, 400);
                }, 3000);
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>