<?php

class ReservaDTO {
    private $cod_reserva;
    private $cod_usuario;
    private $cod_carro;
    private $data_inicio;
    private $data_fim;
    private $valor_total;
    private $status;

    // --- Getters e Setters ---

    public function getCodReserva() {
        return $this->cod_reserva;
    }
    public function setCodReserva($cod_reserva) {
        $this->cod_reserva = $cod_reserva;
    }

    public function getCodUsuario() {
        return $this->cod_usuario;
    }
    public function setCodUsuario($cod_usuario) {
        $this->cod_usuario = $cod_usuario;
    }

    public function getCodCarro() {
        return $this->cod_carro;
    }
    public function setCodCarro($cod_carro) {
        $this->cod_carro = $cod_carro;
    }

    public function getDataInicio() {
        return $this->data_inicio;
    }
    public function setDataInicio($data_inicio) {
        $this->data_inicio = $data_inicio;
    }

    public function getDataFim() {
        return $this->data_fim;
    }
    public function setDataFim($data_fim) {
        $this->data_fim = $data_fim;
    }

    public function getValorTotal() {
        return $this->valor_total;
    }
    public function setValorTotal($valor_total) {
        $this->valor_total = $valor_total;
    }

    public function getStatus() {
        return $this->status;
    }
    public function setStatus($status) {
        $this->status = $status;
    }
}