<?php
require_once __DIR__ . '/../../Data/conector.php';

class AuthController
{
    private $conn;
    private $error;
    private $loginAttempts = 0;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->error = '';
        $this->loginAttempts = $_SESSION['login_attempts'] ?? 0;
    }
    
    public function verificar()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return "Método inválido.";
        }

        if ($this->loginAttempts >= 5) {
            $this->error = "Muitas tentativas. Espere 5 minutos.";
            $_SESSION['login_attempts'] = $this->loginAttempts;
            return $this->error;
        }

        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $senha = $_POST['senha'] ?? '';

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error = "Email inválido.";
            $this->loginAttempts++;
            $_SESSION['login_attempts'] = $this->loginAttempts;
            return $this->error;
        }

        if (empty($senha)) {
            $this->error = "Senha obrigatória.";
            $this->loginAttempts++;
            $_SESSION['login_attempts'] = $this->loginAttempts;
            return $this->error;
        }

        $sql = "SELECT * FROM Usuario WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado && $resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();
            
            if (password_verify($senha, $usuario['senha_hash'])) {
                
                // ✅ Gerar OTP
                $otp = random_int(100000, 999999);
                $expira = date("Y-m-d H:i:s", time() + 300); // 5 minutos

                $sqlOtp = "INSERT INTO user_otps (user_id, otp_code, expires_at, created_at) 
                           VALUES (?, ?, ?, NOW())";
                $stmtOtp = $this->conn->prepare($sqlOtp);

                if (!$stmtOtp) {
                    error_log("Erro prepare OTP: " . $this->conn->error);
                    return "Erro ao gerar código de verificação.";
                }

                $stmtOtp->bind_param("iis", $usuario['id_usuario'], $otp, $expira);

                if (!$stmtOtp->execute()) {
                    error_log("Erro execute OTP: " . $stmtOtp->error);
                    $stmtOtp->close();
                    return "Erro ao gerar código de verificação.";
                }
                
                $stmtOtp->close();

                // ✅ Enviar email (se o Python estiver configurado)
                $escapedEmail = escapeshellarg($email);
                $escapedOtp = escapeshellarg($otp);
                $pythonPath = __DIR__ . '/AuthMailSender.py';
                
                if (file_exists($pythonPath)) {
                    $command = "python $pythonPath $escapedEmail $escapedOtp 2>&1";
                    $output = shell_exec($command);
                    
                    if (strpos($output ?? '', 'Erro') !== false) {
                        error_log("Falha email OTP: $output");
                    }
                }

                // ✅ CORREÇÃO: Salvar ID do usuário (SEM criptografia)
                $_SESSION['pending_user_id'] = $usuario['id_usuario'];
                $_SESSION['pending_email'] = $usuario['email'];
                $_SESSION['pending_tipo'] = $usuario['tipo'];
                
                // ✅ Resetar tentativas de login
                $_SESSION['login_attempts'] = 0;

                header("Location: /GDC/src/View/Auth/validar.php");
                exit();
                
            } else {
                // Senha incorreta
                $this->loginAttempts++;
                $_SESSION['login_attempts'] = $this->loginAttempts;
                return "Senha incorreta.";
            }
            
        } else {
            // Email não encontrado
            $this->loginAttempts++;
            $_SESSION['login_attempts'] = $this->loginAttempts;
            return "Email não encontrado.";
        }
    }
}

// ✅ EXECUÇÃO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = new AuthController();
    $erros = $login->verificar();
    
    if ($erros) {
        header("Location: /GDC/src/View/Auth/login.php?erro=" . urlencode($erros));
        exit();
    }
}