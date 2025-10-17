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

            if ($stmt->execute()) {
                return $this->pdo->lastInsertId();
            }
            return false;

        } catch (PDOException $e) {
            error_log("Erro ao criar usuário: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retorna todos os usuários do banco.
     */
    public function getAll($filtros = []) {
        try {
            $sql = "SELECT * FROM tbl_usuarios";
            $params = [];
            $whereClauses = [];

            // Filtro por termo de busca (nome ou e-mail)
            if (!empty($filtros['busca'])) {
                $whereClauses[] = "(nome LIKE :busca OR email LIKE :busca)";
                $params[':busca'] = '%' . $filtros['busca'] . '%';
            }

            // Filtro por perfil
            if (!empty($filtros['perfil'])) {
                $whereClauses[] = "perfil = :perfil";
                $params[':perfil'] = $filtros['perfil'];
            }

            // Filtro por status
            if (!empty($filtros['status'])) {
                $whereClauses[] = "status = :status";
                $params[':status'] = $filtros['status'];
            }

            // Se houver alguma cláusula, une todas com "AND"
            if (!empty($whereClauses)) {
                $sql .= " WHERE " . implode(' AND ', $whereClauses);
            }

            $sql .= " ORDER BY nome ASC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Erro ao listar usuários: " . $e->getMessage());
            return [];
        }
    }

    /**
     * NOVO MÉTODO: Altera o status de um usuário (para 'inativo' ou 'ativo').
     */
    public function changeStatus($id, $status) {
        try {
            $sql = "UPDATE tbl_usuarios SET status = ? WHERE cod_usuario = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$status, $id]);
        } catch (PDOException $e) {
            error_log("Erro ao alterar status do usuário: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        return $this->changeStatus($id, 'inativo');
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
            $sql = "SELECT * FROM tbl_usuarios WHERE email = :email AND status = 'ativo' LIMIT 1";
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

    public function updateCadastroCompleto($id, $cpf, $telefone) {
        try {
            $sql = "UPDATE tbl_usuarios SET 
                        cpf = :cpf, 
                        telefone = :telefone, 
                        cadastro_completo = 1
                    WHERE cod_usuario = :id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":cpf", $cpf);
            $stmt->bindValue(":telefone", $telefone);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao completar cadastro do usuário: " . $e->getMessage());
            return false;
        }
    }

    public function updateProfile($id, $novoPerfil) {
        try {
            $sql = "UPDATE tbl_usuarios SET perfil = :perfil WHERE cod_usuario = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":perfil", $novoPerfil);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar perfil do usuário: " . $e->getMessage());
            return false;
        }
    }

    public function getAllClientesEUsuarios() {
        try {
            $sql = "SELECT cod_usuario, nome, email, perfil, cadastro_completo FROM tbl_usuarios WHERE perfil IN ('cliente', 'usuario') ORDER BY nome ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar clientes e usuários: " . $e->getMessage());
            return [];
        }
    }

    public function createAndReturnId(UsuarioDTO $usuario) {
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
            
            // Estes podem ser nulos no cadastro inicial
            $stmt->bindValue(":cpf", $usuario->getCpf());
            $stmt->bindValue(":telefone", $usuario->getTelefone());

            // Executa a inserção
            if ($stmt->execute()) {
                // Se a execução foi bem-sucedida, retorna o último ID inserido
                return $this->pdo->lastInsertId();
            } else {
                // Se a execução falhar por outro motivo
                return false;
            }

        } catch (PDOException $e) {
            // Se ocorrer uma exceção (como e-mail duplicado), registra o erro
            error_log("Erro ao criar usuário e retornar ID: " . $e->getMessage());
            return false;
        }
    }
}