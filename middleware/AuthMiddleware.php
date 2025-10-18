<?php

class AuthMiddleware {
    
    public static function checkAuth() {
        if (empty($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
            // Guarda a URL que o usuário tentou acessar para que possamos mandá-lo de volta depois do login.
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            
            // Cria uma mensagem de erro para o usuário saber por que foi redirecionado.
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Você precisa estar logado para acessar esta página.'];
            
            // Redireciona para o login e para a execução do script.
            header("Location: " . BASE_URL . "/view/auth/login.php");
            exit;
        }
    }

    public static function checkProfile(array $perfisPermitidos) {
        // ANTES de verificar o perfil, ele sempre verifica se o usuário está logado.
        // Isso evita repetir código, pois toda página restrita por perfil também exige login.
        self::checkAuth();

        $usuarioPerfil = $_SESSION['usuario']['perfil'] ?? 'visitante';

        // Se o perfil do usuário NÃO ESTÁ na lista de perfis permitidos...
        if (!in_array($usuarioPerfil, $perfisPermitidos)) {
            // ...registra uma tentativa de acesso indevido no log de auditoria.
            $nomeUsuario = $_SESSION['usuario']['nome'] ?? 'Desconhecido';
            $detalhes = "Tentativa de acesso não autorizado pelo usuário '{$nomeUsuario}' à página '{$_SERVER['REQUEST_URI']}'. Perfil requerido: " . implode(', ', $perfisPermitidos);
            registrarLog("ACESSO_NEGADO", $detalhes);

            // Cria uma mensagem de erro e expulsa o usuário para a página inicial.
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Você não tem permissão para acessar esta página.'];
            header("Location: " . BASE_URL . "/public/index.php");
            exit;
        }
    }
}