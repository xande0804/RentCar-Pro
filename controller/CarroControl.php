<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . "/../model/dto/CarroDTO.php";
require_once __DIR__ . "/../model/dao/CarroDAO.php";

$redirectURL = BASE_URL . "/view/admin/carros.php";

// para logs
$nomeUsuarioLogado = $_SESSION['usuario']['nome'] ?? 'Sistema';

define('UPLOADS_DIR_ABSOLUTO', __DIR__ . '/../public/uploads/carros/');
define('UPLOADS_DIR_RELATIVO', 'public/uploads/carros/');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $carroDAO = new CarroDAO();

    

    if ($acao === 'cadastrar') {
        $carroDTO = new CarroDTO();
        $carroDTO->setMarca(trim($_POST['marca'] ?? ''));
        $carroDTO->setModelo(trim($_POST['modelo'] ?? ''));
        $carroDTO->setCategoria($_POST['categoria'] ?? null);             
        $carroDTO->setAno(filter_input(INPUT_POST, 'ano', FILTER_VALIDATE_INT));
        $carroDTO->setCor(trim($_POST['cor'] ?? ''));
        $carroDTO->setCombustivel($_POST['combustivel'] ?? 'flex');
        $carroDTO->setCambio($_POST['cambio'] ?? 'manual');
        $carroDTO->setArCondicionado(isset($_POST['ar_condicionado']) ? 1 : 0);
        $carroDTO->setPrecoDiaria($_POST['preco_diaria'] ?? 0);
        $carroDTO->setStatus($_POST['status'] ?? 'disponivel');
        $carroDTO->setKmTotal((int)($_POST['km_total'] ?? 0));            
        $carroDTO->setDescricao(trim($_POST['descricao'] ?? null));        

        if ($carroDAO->create($carroDTO)) {
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Carro adicionado com sucesso!'];
            registrarLog("CADASTRO_CARRO", "Usuário '{$nomeUsuarioLogado}' adicionou o novo carro: {$carroDTO->getMarca()} {$carroDTO->getModelo()}.");
        } else {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao adicionar carro.'];
        }
    }
    else if ($acao === 'atualizar') {
        $cod_carro = filter_input(INPUT_POST, 'cod_carro', FILTER_VALIDATE_INT);
        if (!$cod_carro) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'ID do carro é inválido.'];
        } else {
            $carroDTO = new CarroDTO();
            $carroDTO->setCodCarro($cod_carro);
            $carroDTO->setMarca(trim($_POST['marca'] ?? ''));
            $carroDTO->setModelo(trim($_POST['modelo'] ?? ''));
            $carroDTO->setCategoria($_POST['categoria'] ?? null);           
            $carroDTO->setAno(filter_input(INPUT_POST, 'ano', FILTER_VALIDATE_INT));
            $carroDTO->setCor(trim($_POST['cor'] ?? ''));
            $carroDTO->setCombustivel($_POST['combustivel'] ?? 'flex');
            $carroDTO->setCambio($_POST['cambio'] ?? 'manual');
            $carroDTO->setArCondicionado(isset($_POST['ar_condicionado']) ? 1 : 0);
            $carroDTO->setPrecoDiaria($_POST['preco_diaria'] ?? 0);
            $carroDTO->setStatus($_POST['status'] ?? 'disponivel');
            $carroDTO->setKmTotal((int)($_POST['km_total'] ?? 0));          
            $carroDTO->setDescricao(trim($_POST['descricao'] ?? null));     

            if ($carroDAO->update($carroDTO)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Carro atualizado com sucesso!'];
                registrarLog("ATUALIZACAO_CARRO", "Usuário '{$nomeUsuarioLogado}' atualizou os dados do carro '{$carroDTO->getMarca()} {$carroDTO->getModelo()}' (ID: {$cod_carro}).");
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao atualizar carro.'];
            }
        }
    }
    else if ($acao === 'excluir') {
        $cod_carro = filter_input(INPUT_POST, 'cod_carro', FILTER_VALIDATE_INT);
        if (!$cod_carro) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'ID do carro é inválido.'];
        } else {
            $carroParaExcluir = $carroDAO->findById($cod_carro);
            if ($carroDAO->delete($cod_carro)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Carro excluído com sucesso!'];
                $nomeCarro = $carroParaExcluir ? "{$carroParaExcluir['marca']} {$carroParaExcluir['modelo']}" : "ID {$cod_carro}";
                registrarLog("EXCLUSAO_CARRO", "Usuário '{$nomeUsuarioLogado}' excluiu o carro '{$nomeCarro}'.");
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao excluir carro. Verifique se ele não está associado a uma reserva ativa.'];
            }
        }
    }
}

function processarImagemUpload($fileInputName, &$errorMessage) {
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $arquivo = $_FILES[$fileInputName];

    // 1) Tamanho (máx. 2MB)
    $maxSize = 2 * 1024 * 1024;
    if ($arquivo['size'] > $maxSize) {
        $errorMessage = 'Erro: O arquivo é muito grande (máx. 2MB).';
        return null;
    }

    // 2) MIME real
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $arquivo['tmp_name']);
    finfo_close($finfo);

    $allowed = [
        'image/jpeg' => '.jpg',
        'image/png'  => '.png',
        'image/webp' => '.webp',
    ];
    if (!isset($allowed[$mime])) {
        $errorMessage = 'Erro: Tipo de arquivo inválido (apenas JPG, PNG, WEBP).';
        return null;
    }

    // 3) Garante que é imagem e limita dimensão
    $infoImg = @getimagesize($arquivo['tmp_name']);
    if (!$infoImg) {
        $errorMessage = 'Erro: Arquivo não é uma imagem válida.';
        return null;
    }
    [$w, $h] = $infoImg;
    if ($w > 4096 || $h > 4096) {
        $errorMessage = 'Erro: Dimensão da imagem excede o limite (4096×4096).';
        return null;
    }

    // 4) Pasta
    if (!file_exists(UPLOADS_DIR_ABSOLUTO)) {
        if (!@mkdir(UPLOADS_DIR_ABSOLUTO, 0777, true)) {
            $errorMessage = 'Erro: não foi possível criar o diretório de upload.';
            return null;
        }
    }

    // 5) Nome único e move
    $nomeArquivo = uniqid('carro_', true) . $allowed[$mime];
    $abs = rtrim(UPLOADS_DIR_ABSOLUTO, '/\\') . DIRECTORY_SEPARATOR . $nomeArquivo;
    $rel = rtrim(UPLOADS_DIR_RELATIVO, '/\\') . '/' . $nomeArquivo;

    if (!move_uploaded_file($arquivo['tmp_name'], $abs)) {
        $errorMessage = 'Erro ao salvar o arquivo no servidor.';
        return null;
    }

    return $rel;
}

// --- Helper: deletar imagem antiga local (não apaga URL externa) ---
function deletarImagemAntiga($caminhoRelativo) {
    if (empty($caminhoRelativo) || filter_var($caminhoRelativo, FILTER_VALIDATE_URL)) {
        return;
    }
    $abs = __DIR__ . '/../' . ltrim($caminhoRelativo, '/\\');
    if (file_exists($abs)) {
        @unlink($abs);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $carroDAO = new CarroDAO();
    $errorMessage = '';
    $imagemFinalUrl = null;

    if ($acao === 'cadastrar') {
        $metodoImagem = $_POST['metodo_imagem'] ?? 'url';

        if ($metodoImagem === 'upload') {
            $imagemFinalUrl = processarImagemUpload('imagem_upload', $errorMessage);
            if ($errorMessage) {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => $errorMessage];
                header("Location: " . $redirectURL);
                exit;
            }
        } else {
            $urlInput = filter_input(INPUT_POST, 'imagem_url', FILTER_VALIDATE_URL);
            if (!empty($_POST['imagem_url']) && !$urlInput) {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'A URL da imagem fornecida é inválida.'];
                header("Location: " . $redirectURL);
                exit;
            }
            $imagemFinalUrl = $urlInput;
        }

        $carroDTO = new CarroDTO();
        $carroDTO->setMarca(trim($_POST['marca'] ?? ''));
        $carroDTO->setModelo(trim($_POST['modelo'] ?? ''));
        $carroDTO->setCategoria($_POST['categoria'] ?? null);
        $carroDTO->setAno(filter_input(INPUT_POST, 'ano', FILTER_VALIDATE_INT));
        $carroDTO->setCor(trim($_POST['cor'] ?? ''));
        $carroDTO->setCombustivel($_POST['combustivel'] ?? 'flex');
        $carroDTO->setCambio($_POST['cambio'] ?? 'manual');
        $carroDTO->setArCondicionado(isset($_POST['ar_condicionado']) ? 1 : 0);
        $carroDTO->setPrecoDiaria($_POST['preco_diaria'] ?? 0);
        $carroDTO->setStatus($_POST['status'] ?? 'disponivel');
        $carroDTO->setKmTotal((int)($_POST['km_total'] ?? 0));
        $carroDTO->setDescricao(trim($_POST['descricao'] ?? null));
        $carroDTO->setImagemUrl($imagemFinalUrl);

        if ($carroDAO->create($carroDTO)) {
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Carro adicionado com sucesso!'];
            registrarLog("CADASTRO_CARRO", "Usuário '{$nomeUsuarioLogado}' adicionou o novo carro: {$carroDTO->getMarca()} {$carroDTO->getModelo()}.");
        } else {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao adicionar carro.'];
        }
    }
    else if ($acao === 'atualizar') {
        $cod_carro = filter_input(INPUT_POST, 'cod_carro', FILTER_VALIDATE_INT);
        if (!$cod_carro) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'ID do carro é inválido.'];
        } else {
            $carroAtual    = $carroDAO->findById($cod_carro);
            $imagemFinalUrl = $carroAtual['imagem_url'] ?? null;
            $imagemAntiga   = $imagemFinalUrl;

            if (isset($_POST['remover_imagem']) && $_POST['remover_imagem'] == '1') {
                deletarImagemAntiga($imagemAntiga);
                $imagemFinalUrl = null;
            } else {
                $metodoImagem = $_POST['metodo_imagem'] ?? 'url';
                if ($metodoImagem === 'upload') {
                    $novaImagem = processarImagemUpload('imagem_upload', $errorMessage);
                    if ($errorMessage) {
                        $_SESSION['flash_message'] = ['type' => 'danger', 'message' => $errorMessage];
                        header("Location: " . $redirectURL);
                        exit;
                    }
                    if ($novaImagem) {
                        deletarImagemAntiga($imagemAntiga);
                        $imagemFinalUrl = $novaImagem;
                    }
                } else {
                    $urlInput = filter_input(INPUT_POST, 'imagem_url', FILTER_VALIDATE_URL);
                    if ($urlInput && $urlInput !== $imagemAntiga) {
                        deletarImagemAntiga($imagemAntiga);
                        $imagemFinalUrl = $urlInput;
                    }
                }
            }

            $carroDTO = new CarroDTO();
            $carroDTO->setCodCarro($cod_carro);
            $carroDTO->setMarca(trim($_POST['marca'] ?? ''));
            $carroDTO->setModelo(trim($_POST['modelo'] ?? ''));
            $carroDTO->setCategoria($_POST['categoria'] ?? null);
            $carroDTO->setAno(filter_input(INPUT_POST, 'ano', FILTER_VALIDATE_INT));
            $carroDTO->setCor(trim($_POST['cor'] ?? ''));
            $carroDTO->setCombustivel($_POST['combustivel'] ?? 'flex');
            $carroDTO->setCambio($_POST['cambio'] ?? 'manual');
            $carroDTO->setArCondicionado(isset($_POST['ar_condicionado']) ? 1 : 0);
            $carroDTO->setPrecoDiaria($_POST['preco_diaria'] ?? 0);
            $carroDTO->setStatus($_POST['status'] ?? 'disponivel');
            $carroDTO->setKmTotal((int)($_POST['km_total'] ?? 0));
            $carroDTO->setDescricao(trim($_POST['descricao'] ?? null));
            $carroDTO->setImagemUrl($imagemFinalUrl);

            if ($carroDAO->update($carroDTO)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Carro atualizado com sucesso!'];
                registrarLog("ATUALIZACAO_CARRO", "Usuário '{$nomeUsuarioLogado}' atualizou o carro '{$carroDTO->getMarca()} {$carroDTO->getModelo()}' (ID: {$cod_carro}).");
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao atualizar carro.'];
            }
        }
    }
    else if ($acao === 'excluir') {
        $cod_carro = filter_input(INPUT_POST, 'cod_carro', FILTER_VALIDATE_INT);
        if (!$cod_carro) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'ID do carro é inválido.'];
        } else {
            $carroParaExcluir = $carroDAO->findById($cod_carro);
            if ($carroDAO->delete($cod_carro)) {
                if ($carroParaExcluir && !empty($carroParaExcluir['imagem_url'])) {
                    deletarImagemAntiga($carroParaExcluir['imagem_url']);
                }
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Carro excluído com sucesso!'];
                $nomeCarro = $carroParaExcluir ? "{$carroParaExcluir['marca']} {$carroParaExcluir['modelo']}" : "ID {$cod_carro}";
                registrarLog("EXCLUSAO_CARRO", "Usuário '{$nomeUsuarioLogado}' excluiu o carro '{$nomeCarro}'.");
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro ao excluir carro. Verifique se ele não está associado a uma reserva ativa.'];
            }
        }
    }
}

header("Location: " . $redirectURL);
exit;
