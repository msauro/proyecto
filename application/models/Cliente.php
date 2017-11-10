<?php

class Application_Model_Cliente extends Application_Model_Base
{

    protected $_name = 'clientes';

    public function getList(){
        $query = $this->select()->setIntegrityCheck(false)
            ->from($this, array('*'))
            ->where('clientes.eliminado = ?', 0);

        $rows = $this->fetchAll($query);

        return $rows->toArray();
    }

    public function getClienteByEmail($email){
        try{
            $query = $this->select()
                ->from($this, array('*'))
                ->where('email = ?', $email)
                ->where('eliminado = ?', 0);

            $row = $this->fetchRow($query);

            if(!$row) {
                return null;
            }

            return $row->toArray();
            
        }
        catch(Exception $e){
            return $e;
        }
    }

    public function getClienteByCuit($cuit){
        try{
            $query = $this->select()
                ->from($this, array('*'))
                ->where('cuit = ?', $cuit)
                ->where('eliminado = ?', 0);

            $row = $this->fetchRow($query);

            if(!$row) {
                return null;
            }

            return $row->toArray();
            
        }
        catch(Exception $e){
            return $e;
        }
    }

    public function getClienteById($id){
        try{
            $query = $this->select()->setIntegrityCheck(false)
                ->from($this, array('*'))
                ->where('clientes.eliminado= ?', 0);

            $row = $this->fetchRow($query);

            if(!$row) {
                return null;
            }

            return $row->toArray();
        }
        catch(Exception $e){
            return $e;
        }
    }

    public function getListType(){
        $query = $this->select()->setIntegrityCheck(false)
            ->from($this, array('*'))
            ->join('tipo_cliente', 'tipo_cliente.id = clientes.id', array('tipo_cliente.id as tipo_cliente'))
            ->where('clientes.eliminado = ?', 0);

        $rows = $this->fetchAll($query);
        return $rows->toArray();
    }
   
}
