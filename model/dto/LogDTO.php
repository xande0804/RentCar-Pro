<?php
class LogDTO {
    private $cod_log;
    private $cod_usuario;
    private $acao_realizada;
    private $detalhes;
    private $data_hora;

    // --- Getters e Setters ---
    public function getCodUsuario() { return $this->cod_usuario; }
    public function setCodUsuario($cod_usuario) { $this->cod_usuario = $cod_usuario; }
    public function getAcaoRealizada() { return $this->acao_realizada; }
    public function setAcaoRealizada($acao_realizada) { $this->acao_realizada = $acao_realizada; }
    public function getDetalhes() { return $this->detalhes; }
    public function setDetalhes($detalhes) { $this->detalhes = $detalhes; }
}