<?php

class Application_Model_Cliente extends Application_Model_Base
{

    protected $_name = 'clientes';

    public function getList(){
        $query = $this->select()->setIntegrityCheck(false)
        ->from($this, array('*'))
        ->join('estados', 'estados.id = clientes.estado', array('estados.nombre AS nom_estado'))
        ->join('tipo_cliente', 'tipo_cliente.id = clientes.id_tipo_cliente', array('tipo_cliente.nombre AS nom_tipo'))
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
            ->where('clientes.eliminado= ?', 0)
            ->where('clientes.id= ?', $id);

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

    public function getListFiltered($search,$paginate=NULL){
        $days = 15;
        $fecha = date('Y-m-j');
        $nuevafecha = strtotime ( "-$days day" , strtotime ( $fecha ) ) ;
        $nuevafecha = date ( 'Y-m-j' , $nuevafecha );

        $search['search'] = $search['search'];
        $query = $this->select()->setIntegrityCheck(false)
            ->from($this, array('*'))
            ->join('estados', 'estados.id = clientes.estado', array('estados.nombre as nom_estado'))
            ->join('tipo_cliente', 'tipo_cliente.id = clientes.id_tipo_cliente', array('tipo_cliente.nombre as tipo_cliente', 'tipo_cliente.descuento'))
            ->where('clientes.eliminado = 0')
            ->where("(clientes.id LIKE '%{$search['search']}%' OR clientes.nombre LIKE '%{$search['search']}%' OR apellido LIKE '%{$search['search']}%' OR email LIKE '%{$search['search']}%' OR cuit LIKE '%{$search['search']}%' OR CONCAT(clientes.nombre,' ', apellido) LIKE '%{$search['search']}%')")
            ->group('clientes.id');

            if ($paginate)
                $query->limit($paginate['per_page'],$paginate['start_from']);
           
        $rows = $this->fetchAll($query);

        return $rows->toArray();
    }
}
