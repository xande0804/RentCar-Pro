<?php

class CarroDTO {
    private $cod_carro;
    private $marca;
    private $modelo;
    private $categoria;      
    private $ano;
    private $cor;
    private $combustivel;
    private $cambio;
    private $ar_condicionado;
    private $preco_diaria;
    private $status;
    private $km_total;
    private $descricao;
    private $imagem_url;

    public function getCodCarro() {
        return $this->cod_carro;
    }
    public function setCodCarro($cod_carro) {
        $this->cod_carro = (int)$cod_carro;
    }

    public function getMarca() {
        return $this->marca;
    }
    public function setMarca($marca) {
        $this->marca = trim($marca);
    }

    public function getModelo() {
        return $this->modelo;
    }
    public function setModelo($modelo) {
        $this->modelo = trim($modelo);
    }

    public function getCategoria() {
        return $this->categoria;
    }
    public function setCategoria($categoria) {
        $categoria = trim((string)$categoria);
        $this->categoria = ($categoria === '') ? null : $categoria;
    }

    public function getAno() {
        return $this->ano;
    }
    public function setAno($ano) {
        $this->ano = $ano; // year(4) no banco
    }

    public function getCor() {
        return $this->cor;
    }
    public function setCor($cor) {
        $this->cor = trim($cor);
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

    public function getArCondicionado() {
        return $this->ar_condicionado;
    }
    public function setArCondicionado($ar) {
        $this->ar_condicionado = (int)$ar;
    }

    public function getPrecoDiaria() {
        return $this->preco_diaria;
    }
    public function setPrecoDiaria($preco) {
        $this->preco_diaria = (float)str_replace(',', '.', $preco);
    }

    public function getStatus() {
        return $this->status;
    }
    public function setStatus($status) {
        $this->status = $status;
    }

    public function getKmTotal() {
        return $this->km_total;
    }
    public function setKmTotal($km) {
        $this->km_total = (int)$km;
    }

    public function getDescricao() {
        return $this->descricao;
    }
    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    public function getImagemUrl() { 
        return $this->imagem_url; 
    }
    public function setImagemUrl($imagem_url) { 
        $this->imagem_url = $imagem_url; 
    }

}
