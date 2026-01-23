<?php
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['tipo_usuario'])) {
    redirecionar_por_tipo($_SESSION['tipo_usuario']);
}

$cookie_restaurada = false;
foreach ($_COOKIE as $name => $value) {
    if (strpos($name, 'gdc_session_') === 0) {
        $data = json_decode($value, true);
        if (is_array($data) && isset($data['id_usuario'], $data['email'], $data['tipo'], $data['login_time'])) {
            $tempo_decorrido = time() - $data['login_time'];
            // Verificar se cookie tem menos de 24h
            if ($tempo_decorrido < (24 * 60 * 60)) {
                $_SESSION['id'] = $data['id_usuario'];
                $_SESSION['email'] = $data['email'];
                $_SESSION['tipo_usuario'] = $data['tipo'];
                $_SESSION['login_time'] = $data['login_time'];
                $_SESSION['session_timeout'] = 24 * 60 * 60;
                $cookie_restaurada = true;
                
                redirecionar_por_tipo($data['tipo']);
                break;
            } else {
                setcookie($name, '', time() - 3600, '/');
            }
        }
    }
}

header("Location: src/login.php");
exit();

function redirecionar_por_tipo($tipo) {
    switch ($tipo) {
        case 'Morador':
            header("Location: src/view/morador/index.php");
            break;
        case 'Sindico':
            header("Location: src/view/sindico/index.php");
            break;
        case 'Porteiro':
            header("Location: src/view/porteiro/index.php");
            break;
        default:
            header("Location: src/login.php");
    }
    exit();
}
?>
