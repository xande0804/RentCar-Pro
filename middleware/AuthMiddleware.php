<?php

class AuthMiddleware {

    public static function checkAuth() {
        if (empty($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {

            // Preserva uma redirect_url apenas se ainda não existir
            if (empty($_SESSION['redirect_url'])) {
                $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
                // Preserva também a query string se houver
                $query = $_SERVER['QUERY_STRING'] ?? '';
                if (!empty($query) && strpos($requestUri, '?') === false) {
                    $requestUri .= '?' . $query;
                }
                $_SESSION['redirect_url'] = $requestUri;
            }

            // Mensagem para o usuário
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Você precisa estar logado para acessar esta página.'];

            header("Location: " . BASE_URL . "/view/auth/login.php");
            exit;
        }
    }

    public static function checkProfile(array $perfisPermitidos) {
        // Primeiro, precisa estar logado
        self::checkAuth();

        $usuarioPerfil = $_SESSION['usuario']['perfil'] ?? 'visitante';

        if (!in_array($usuarioPerfil, $perfisPermitidos)) {
            // Loga tentativa de acesso negado (se sua config.php tiver registrarLog)
            $nomeUsuario = $_SESSION['usuario']['nome'] ?? 'Desconhecido';
            $detalhes = "Tentativa de acesso não autorizado pelo usuário '{$nomeUsuario}' à página '{$_SERVER['REQUEST_URI']}'. Perfil requerido: " . implode(', ', $perfisPermitidos);
            registrarLog("ACESSO_NEGADO", $detalhes);

            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Você não tem permissão para acessar esta página.'];
            header("Location: " . BASE_URL . "/public/index.php");
            exit;
        }
    }
}
