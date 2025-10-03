<?php

class CarroDTO {
    private $cod_carro;
    private $marca;
    private $modelo;
    private $ano;
    private $cor;
    private $combustivel;
    private $cambio;
    private $preco_diaria;
    private $status;

    // --- Getters e Setters ---

    public function getCodCarro() {
        return $this->cod_carro;
    }
    public function setCodCarro($cod_carro) {
        $this->cod_carro = $cod_carro;
    }

    public function getMarca() {
        return $this->marca;
    }
    public function setMarca($marca) {
        $this->marca = $marca;
    }

    public function getModelo() {
        return $this->modelo;
    }
    public function setModelo($modelo) {
        $this->modelo = $modelo;
    }

    public function getAno() {
        return $this->ano;
    }
    public function setAno($ano) {
        $this->ano = $ano;
    }

    public function getCor() {
        return $this->cor;
    }
    public function setCor($cor) {
        $this->cor = $cor;
    }

    public function getCombustivel() {
        return $this->combustivel;
    }
    public function setCombustivel($combustivel) {
        $this->combustivel = $combustivel;
    }

    public function getCambio() {
        return $this->cambio;
    }
    public function setCambio($cambio) {
        $this->cambio = $cambio;
    }

    public function getPrecoDiaria() {
        return $this->preco_diaria;
    }
    public function setPrecoDiaria($preco_diaria) {
        $this->preco_diaria = $preco_diaria;
    }

    public function getStatus() {
        return $this->status;
    }
    public function setStatus($status) {
        $this->status = $status;
    }
}