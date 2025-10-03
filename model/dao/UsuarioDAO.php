<?php
require_once __DIR__ . "/Conexao.php";
require_once __DIR__ . "/../dto/UsuarioDTO.php";

class UsuarioDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexao::getInstance();
    }

    /**
     * Cadastra um novo usuário no banco de dados.
     * A senha é criptografada aqui.
     */
    public function create(UsuarioDTO $usuario) {
        try {
            $sql = "INSERT INTO tbl_usuarios (nome, email, senha, perfil, cpf, telefone) 
                    VALUES (:nome, :email, :senha, :perfil, :cpf, :telefone)";
            $stmt = $this->pdo->prepare($sql);
            
            // Criptografa a senha antes de salvar
            $senhaHash = password_hash($usuario->getSenha(), PASSWORD_DEFAULT);

            $stmt->bindValue(":nome", $usuario->getNome());
            $stmt->bindValue(":email", $usuario->getEmail());
            $stmt->bindValue(":senha", $senhaHash);
            $stmt->bindValue(":perfil", $usuario->getPerfil());
            $stmt->bindValue(":cpf", $usuario->getCpf());
            $stmt->bindValue(":telefone", $usuario->getTelefone());

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao criar usuário: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retorna todos os usuários do banco.
     */
    public function getAll() {
        try {
            $sql = "SELECT * FROM tbl_usuarios ORDER BY nome ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar usuários: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca um usuário específico pelo seu ID (cod_usuario).
     */
    public function findById($id) {
        try {
            $sql = "SELECT * FROM tbl_usuarios WHERE cod_usuario = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuário por ID: " . $e->getMessage());
            return null;
        }
    }

    public function findByEmail($email) {
        try {
            $sql = "SELECT * FROM tbl_usuarios WHERE email = :email LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':email', $email);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuário por e-mail: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Atualiza os dados de um usuário no banco.
     * A senha só é atualizada se for fornecida.
     */
    public function update(UsuarioDTO $usuario) {
        try {
            $sql = "UPDATE tbl_usuarios SET nome = :nome, email = :email, perfil = :perfil, cpf = :cpf, telefone = :telefone";
            
            if (!empty($usuario->getSenha())) {
                $sql .= ", senha = :senha";
            }
            
            $sql .= " WHERE cod_usuario = :cod_usuario";
            
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(":nome", $usuario->getNome());
            $stmt->bindValue(":email", $usuario->getEmail());
            $stmt->bindValue(":perfil", $usuario->getPerfil());
            $stmt->bindValue(":cpf", $usuario->getCpf());
            $stmt->bindValue(":telefone", $usuario->getTelefone());
            $stmt->bindValue(":cod_usuario", $usuario->getCodUsuario(), PDO::PARAM_INT);

            if (!empty($usuario->getSenha())) {
                $senhaHash = password_hash($usuario->getSenha(), PASSWORD_DEFAULT);
                $stmt->bindValue(":senha", $senhaHash);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar usuário: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deleta um usuário do banco pelo seu ID.
     */
    public function delete($id) {
        try {
            $sql = "DELETE FROM tbl_usuarios WHERE cod_usuario = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao deletar usuário: " . $e->getMessage());
            return false;
        }
    }
}