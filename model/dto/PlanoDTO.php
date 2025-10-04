<?php
class PlanoDTO {
    private $cod_plano;
    private $nome;
    private $dias_minimos;
    private $multiplicador_valor;

    // --- Getters e Setters ---
    public function getCodPlano() {
        return $this->cod_plano;
    }
    public function setCodPlano($cod_plano) {
        $this->cod_plano = $cod_plano;
    }

    public function getNome() {
        return $this->nome;
    }
    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function getDiasMinimos() {
        return $this->dias_minimos;
    }
    public function setDiasMinimos($dias_minimos) {
        $this->dias_minimos = $dias_minimos;
    }

    public function getMultiplicadorValor() {
        return $this->multiplicador_valor;
    }
    public function setMultiplicadorValor($multiplicador_valor) {
        $this->multiplicador_valor = $multiplicador_valor;
    }
}
