<?php
require_once __DIR__ . '/../../Data/conector.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class AuthConfirmationController {
    private $conn;
    private $error;

    public function __construct() {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->error = '';
    }

    public function verificar() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return "Método inválido.";
        }

        // ✅ Validar código
        $codigo = trim($_POST['codigo'] ?? '');
        
        if (!preg_match('/^\d{6}$/', $codigo)) {
            return "Código deve ter exatamente 6 dígitos.";
        }

        // ✅ CORREÇÃO: Verificar sessão pendente (SEM criptografia)
        $user_id = $_SESSION['pending_user_id'] ?? null;
        
        if (!$user_id) {
            return "Sessão expirada. Faça login novamente.";
        }

        $user_id = intval($user_id);
        
        if ($user_id <= 0) {
            return "ID de usuário inválido.";
        }
        
        // ✅ Buscar OTP mais recente
        $sql = "SELECT * FROM user_otps 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log("Erro prepare OTP: " . $this->conn->error);
            return "Erro interno do sistema.";
        }

        $stmt->bind_param("i", $user_id);

        if (!$stmt->execute()) {
            error_log("Erro execute OTP: " . $stmt->error);
            $stmt->close();
            return "Erro ao verificar código.";
        }

        $otp = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$otp) {
            return "Nenhum código encontrado. Faça login novamente.";
        }

        // ✅ Verificações de segurança
        if ($otp['is_used']) {
            return "Código já foi usado.";
        }

        if (strtotime($otp['expires_at']) < time()) {
            return "Código expirado. Faça login novamente.";
        }

        // ✅ CORREÇÃO: Comparar código (convertendo para string)
        if ($otp['otp_code'] != $codigo) {
            return "Código inválido.";
        }

        // ✅ Marcar OTP como usado
        $sqlUpdate = "UPDATE user_otps SET is_used = 1 WHERE id = ?";
        $stmtUpdate = $this->conn->prepare($sqlUpdate);

        if (!$stmtUpdate) {
            error_log("Erro prepare update OTP: " . $this->conn->error);
            return "Erro interno do sistema.";
        }

        $stmtUpdate->bind_param("i", $otp['id']);
        
        if (!$stmtUpdate->execute()) {
            error_log("Erro execute update OTP: " . $stmtUpdate->error);
            $stmtUpdate->close();
            return "Erro ao processar código.";
        }
        
        $stmtUpdate->close();

        // ✅ CORREÇÃO: Criar sessão completa
        $_SESSION['usuario_id'] = $user_id;
        $_SESSION['email'] = $_SESSION['pending_email'];
        $_SESSION['tipo'] = $_SESSION['pending_tipo'];

        // ✅ Limpar variáveis temporárias
        unset($_SESSION['pending_user_id']);
        unset($_SESSION['pending_email']);
        unset($_SESSION['pending_tipo']);

        // ✅ Regenerar ID da sessão para segurança
        session_regenerate_id(true);

        // ✅ CORREÇÃO: Redirecionar por tipo (SEM strtolower)
        switch ($_SESSION['tipo']) {
            case 'Morador':
                header("Location: /GDC/src/View/Morador/index.php");
                exit();
                
            case 'Sindico':
                header("Location: /GDC/src/View/Sindico/index.php");
                exit();
                
            case 'Porteiro':
                header("Location: /GDC/src/View/Porteiro/index.php");
                exit();
                
            default:
                return "Tipo de usuário inválido.";
        }
    }
}

// ✅ EXECUÇÃO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $confirm = new AuthConfirmationController();
    $error = $confirm->verificar();
    
    if ($error) {
        header("Location: /GDC/src/View/Auth/validar.php?erro=" . urlencode($error));
        exit();
    }
}