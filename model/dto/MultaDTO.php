<?php
class MultaDTO {
    private $cod_multa;
    private $cod_reserva;
    private $descricao;
    private $valor;
    private $status;
    private $data_vencimento;
    private $data_registro;
    private $data_resolucao;
    private $cod_usuario_registro;
    private $observacoes;

    // --- Getters e Setters ---

    public function getCodMulta() { return $this->cod_multa; }
    public function setCodMulta($cod_multa) { $this->cod_multa = $cod_multa; }

    public function getCodReserva() { return $this->cod_reserva; }
    public function setCodReserva($cod_reserva) { $this->cod_reserva = $cod_reserva; }

    public function getDescricao() { return $this->descricao; }
    public function setDescricao($descricao) { $this->descricao = $descricao; }

    public function getValor() { return $this->valor; }
    public function setValor($valor) { $this->valor = $valor; }

    public function getStatus() { return $this->status; }
    public function setStatus($status) { $this->status = $status; }

    public function getDataVencimento() { return $this->data_vencimento; }
    public function setDataVencimento($data_vencimento) { $this->data_vencimento = $data_vencimento; }

    public function getDataRegistro() { return $this->data_registro; }
    public function setDataRegistro($data_registro) { $this->data_registro = $data_registro; }

    public function getDataResolucao() { return $this->data_resolucao; }
    public function setDataResolucao($data_resolucao) { $this->data_resolucao = $data_resolucao; }

    public function getCodUsuarioRegistro() { return $this->cod_usuario_registro; }
    public function setCodUsuarioRegistro($cod_usuario_registro) { $this->cod_usuario_registro = $cod_usuario_registro; }

    public function getObservacoes() { return $this->observacoes; }
    public function setObservacoes($observacoes) { $this->observacoes = $observacoes; }
}