<?php

class Application_Model_Existencia extends Application_Model_Base
{

    protected $_name = 'clientes';

    public function editarStock(){
        $query = $this->select()->setIntegrityCheck(false)
        ->from($this, array('*'))
        ->join('estados', 'estados.id = clientes.estado', array('estados.nombre AS nom_estado'))
        ->join('tipo_cliente', 'tipo_cliente.id = clientes.id_tipo_cliente', array('tipo_cliente.nombre AS nom_tipo'))
        ->where('clientes.eliminado = ?', 0);

        $rows = $this->fetchAll($query);

        return $rows->toArray();
    }
}