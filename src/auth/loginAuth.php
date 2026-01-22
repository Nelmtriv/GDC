<?php
require_once "../data/conector.php";

session_start();

if (!isset($_SESSION['tentativas'])) {
    $_SESSION['tentativas'] = 0;
}
if ($_SESSION['tentativas'] >= 5) {
    header("Location: ../login.php?erro=" . urlencode(
        "Login bloqueado. Excedeu o número máximo de 5 tentativas. Tente mais tarde."
    ));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit();
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$senha = isset($_POST['senha']) ? $_POST['senha'] : '';

if (empty($email) || empty($senha)) {
    header("Location: ../login.php?erro=" . urlencode("Preencha email e senha."));
    exit();
}

$conector = new Conector();
$conexao = $conector->getConexao();

$stmt = $conexao->prepare("SELECT * FROM Usuario WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado && $resultado->num_rows === 1) {

    $user = $resultado->fetch_assoc();

    if (password_verify($senha, $user['senha_hash'])) {

        $_SESSION['tentativas'] = 0;

 
        $_SESSION['id'] = $user['id_usuario'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['tipo_usuario'] = $user['tipo'];

        switch ($user['tipo']) {
            case 'Morador':
                header("Location: ../view/Morador/index.php");
                break;

            case 'Sindico':
                header("Location: ../view/Sindico/index.php");
                break;

            case 'Porteiro':
                header("Location: ../view/Porteiro/index.php");
                break;

            default:
                header("Location: ../login.php?erro=" . urlencode("Tipo de usuário inválido."));
        }
        exit();

    } else {
        $_SESSION['tentativas']++;

        $restantes = 5 - $_SESSION['tentativas'];

        if ($restantes > 0) {
            header("Location: ../login.php?erro=" . urlencode(
                "Senha incorreta. Tentativas restantes: $restantes"
            ));
        } else {
            header("Location: ../login.php?erro=" . urlencode(
                "Login bloqueado após 5 tentativas inválidas."
            ));
        }
        exit();
    }

} else {
    $_SESSION['tentativas']++;

    $restantes = 5 - $_SESSION['tentativas'];

    header("Location: ../login.php?erro=" . urlencode(
        "Usuário não encontrado. Tentativas restantes: $restantes"
    ));
    exit();
}
