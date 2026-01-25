<?php
require_once __DIR__ . '/../../Data/conector.php';
require_once __DIR__ . '/../../Model/User.php';
require_once __DIR__ . '/../../Model/Sindico.php';
require_once __DIR__ . "/../../Model/Morador.php";

class RegisterAuthController{
    private $conn;

    public function __construct() {
        $Connection = new Conector();
        $this->conn = $Connection->getConexao();
    }

    public function registerAuthController(){

        if($_SERVER['REQUEST_METHOD'] != 'POST'){
            return 'FALHA NA REQUISIÇÂO';
        }

        $user = new User();
        $morador = new Morador();
        $sindico = new Sindico();

        $nome = $_POST['nome'];
        $senha = $_POST['senha'];
        $email = $_POST['email'];
        $tipo = $_POST['tipo'];
        $tel = $_POST['telefone'];
        $documento = $_POST['documento'];
        $tipo_documento = $_POST['tipo_documento'];
        $nacionalidade = $_POST['nacionalidade'];

        $erros = '';

        if (empty($nome) || empty($email) || empty($senha)) {
            return "Preencha todos os campos obrigatórios.<br>";
        }

        $tipoPermitido = ['Admin', 'Morador', 'Sindico', 'Porteiro'];
        if (!in_array($tipo, $tipoPermitido)) {
            return "Tipo de utilizador inválido.";
        }

        if (strlen($senha) < 6) {
            return "A senha deve ter no mínimo 6 caracteres.";
        }

        if (strlen($tel) < 9) {
            return "O número deve ter no mínimo 9 digitos.";
        }

        $user->setEmail($email);


        try {
            $user->VerifyUser($this->conn);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // $uploadDirDoc = "../../../uploads/BI/" . $email . "/";
        $uploadDirDoc = "../../../uploads/$email/BI/";
        if (!file_exists($uploadDirDoc)) {
            if (!mkdir($uploadDirDoc, 0777, true)){
                throw new Exception("Erro ao criar diretório de upload.");
            }
        }

        $docFilePath = null;
        if (isset($_FILES['imagem_doc_path']) && $_FILES['imagem_doc_path']['error'] == UPLOAD_ERR_OK) {
            $docFileName = basename($_FILES['imagem_doc_path']['name']);
            $docFileExt = strtolower(pathinfo($docFileName, PATHINFO_EXTENSION));
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!in_array($_FILES['imagem_doc_path']['type'], $allowedTypes)) {
                throw new Exception("Tipo de arquivo do relatório não permitido.");
            }
            $newdocFileName = uniqid() . '.' . $docFileExt;
            $targetFileAlvarDoc = $uploadDirDoc . $newdocFileName;
            if (move_uploaded_file($_FILES['imagem_doc_path']['tmp_name'], $targetFileAlvarDoc)) {
                $docFilePath = "/GDC/uploads/$email/BI/" . $newdocFileName;
                $user->setDocPath($docFilePath);
            } else {
                throw new Exception("Erro ao fazer upload do documento.");
            }
        }

        $hashedPassword = password_hash($senha, PASSWORD_DEFAULT);

        $user->setNome($nome);
        $user->setEmail($email);
        $user->setSenha_hash($hashedPassword);
        $user->setTipo($tipo);
        $user->setTelefone($tel);
        $user->setDocumento($documento);
        $user->setNacionalidade($nacionalidade);
        $user->setTipo_documento($tipo_documento);
        
        if($user->salvar($this->conn)){
            $id = $user->LastIdInsert($this->conn);

            session_start();
            $_SESSION['email'] = $email;
            $_SESSION['tipo'] = $tipo;
            $_SESSION['usuario_id'] = $id;

            setcookie('user_email', $email, time() + 3600, "/");

            switch ($tipo) {
                case 'Morador':
                    $morador->setUsuario($id);
                    if(!$morador->salvarMorador($this->conn)){
                        header("Location: /GDC/src/View/Auth/register.php?erro=" . urlencode("Erro ao Salvar Morador."));
                        exit();
                    }
                    header("Location: /GDC/src/View/Morador/index.php");
                    break;

                case 'Sindico':
                    $sindico->setUsuario($id);
                    if(!$sindico->salvarSindico($this->conn)){
                        header("Location: /GDC/src/View/Auth/register.php?erro=" . urlencode("Erro ao Salvar sindico."));
                        exit();
                    }
                    header("Location: /GDC/src/View/Sindico/index.php");
                    break;

                case 'Porteiro':
                    header("Location: /GDC/src/View/Porteiro/index.php");
                    break;

                default:
                    header("Location: /GDC/src/View/Auth/register.php?erro=" . urlencode("Tipo de usuário inválido."));
            }

            exit();
        }
    }
}

$erros = '';
if ($erros) {
    header("Location: /GDC/src/View/Auth/register.php?erro=" . urlencode($erros));
    exit();
}
$registro = new RegisterAuthController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $erros = $registro->registerAuthController();
}