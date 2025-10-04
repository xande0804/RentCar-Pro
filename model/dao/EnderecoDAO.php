<?php
require_once __DIR__ . "/Conexao.php";
require_once __DIR__ . "/../dto/EnderecoDTO.php";

class EnderecoDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexao::getInstance();
    }

    public function createOrUpdate(EnderecoDTO $endereco) {
        try {
            // Verifica se já existe um endereço para este usuário
            $sql_find = "SELECT cod_endereco FROM tbl_enderecos WHERE cod_usuario = ?";
            $stmt_find = $this->pdo->prepare($sql_find);
            $stmt_find->execute([$endereco->getCodUsuario()]);

            if ($stmt_find->fetch()) {
                // Se existe, atualiza (UPDATE)
                $sql = "UPDATE tbl_enderecos SET cep = ?, logradouro = ?, numero = ?, complemento = ?, bairro = ?, cidade = ?, estado = ? WHERE cod_usuario = ?";
            } else {
                // Se não existe, insere (INSERT)
                $sql = "INSERT INTO tbl_enderecos (cep, logradouro, numero, complemento, bairro, cidade, estado, cod_usuario) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            }
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $endereco->getCep(), $endereco->getLogradouro(), $endereco->getNumero(),
                $endereco->getComplemento(), $endereco->getBairro(), $endereco->getCidade(),
                $endereco->getEstado(), $endereco->getCodUsuario()
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao salvar endereço: " . $e->getMessage());
            return false;
        }
    }
}