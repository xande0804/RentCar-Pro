<?php
class EnderecoDTO {
    private $cod_endereco;
    private $cod_usuario;
    private $cep;
    private $logradouro;
    private $numero;
    private $complemento;
    private $bairro;
    private $cidade;
    private $estado;

    // --- Getters e Setters ---
    public function getCodEndereco() {
        return $this->cod_endereco;
    }
    public function setCodEndereco($cod_endereco) {
        $this->cod_endereco = $cod_endereco;
    }

    public function getCodUsuario() {
        return $this->cod_usuario;
    }
    public function setCodUsuario($cod_usuario) {
        $this->cod_usuario = $cod_usuario;
    }

    public function getCep() {
        return $this->cep;
    }
    public function setCep($cep) {
        $this->cep = $cep;
    }

    public function getLogradouro() {
        return $this->logradouro;
    }
    public function setLogradouro($logradouro) {
        $this->logradouro = $logradouro;
    }

    public function getNumero() {
        return $this->numero;
    }
    public function setNumero($numero) {
        $this->numero = $numero;
    }

    public function getComplemento() {
        return $this->complemento;
    }
    public function setComplemento($complemento) {
        $this->complemento = $complemento;
    }

    public function getBairro() {
        return $this->bairro;
    }
    public function setBairro($bairro) {
        $this->bairro = $bairro;
    }

    public function getCidade() {
        return $this->cidade;
    }
    public function setCidade($cidade) {
        $this->cidade = $cidade;
    }

    public function getEstado() {
        return $this->estado;
    }
    public function setEstado($estado) {
        $this->estado = $estado;
    }
}