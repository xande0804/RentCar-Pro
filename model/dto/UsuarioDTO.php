<?php

class UsuarioDTO {
    private $cod_usuario;
    private $nome;
    private $email;
    private $senha;
    private $perfil;
    private $telefone;
    private $cpf;
    private $cadastro_completo;

    // --- Getters e Setters ---

    public function getCodUsuario() {
        return $this->cod_usuario;
    }
    public function setCodUsuario($cod_usuario) {
        $this->cod_usuario = $cod_usuario;
    }

    public function getNome() {
        return $this->nome;
    }
    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function getEmail() {
        return $this->email;
    }
    public function setEmail($email) {
        $this->email = $email;
    }

    public function getSenha() {
        return $this->senha;
    }
    public function setSenha($senha) {
        $this->senha = $senha;
    }

    public function getPerfil() {
        return $this->perfil;
    }
    public function setPerfil($perfil) {
        $this->perfil = $perfil;
    }
    
    public function getTelefone() {
        return $this->telefone;
    }
    public function setTelefone($telefone) {
        $this->telefone = $telefone;
    }

    public function getCpf() {
        return $this->cpf;
    }
    public function setCpf($cpf) {
        $this->cpf = $cpf;
    }

    public function getCadastroCompleto() {
        return $this->cadastro_completo;
    }
    public function setCadastroCompleto($cadastro_completo) {
        $this->cadastro_completo = $cadastro_completo;
    }
}