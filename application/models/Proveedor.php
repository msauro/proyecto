<?php

class Application_Model_Proveedor extends Application_Model_Base
{

	protected $_name = 'proveedores';

	public function getList(){
    	$query = $this->select()->setIntegrityCheck(false)
            ->from($this, array('*'))
            ->where('proveedores.eliminado = ?', 0);

        $rows = $this->fetchAll($query);

        return $rows->toArray();
    }

    public function getProveedorById($id){
    	try{
            $query = $this->select()->setIntegrityCheck(false)
	            ->from($this, array('*'))
                ->where('proveedores.id = ?', $id)
	            ->where('proveedores.eliminado= ?', 0);

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

    public function getProveedorByCuit($cuit){
        try{
            $query = $this->select()
                ->from($this, array('*'))
                ->where('cuit = ?', $cuit);

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

    public function getProveedorByEmail($email){
        try{
            $query = $this->select()
                ->from($this, array('*'))
                ->where('email = ?', $email);

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

    public function getListFiltered($search,$paginate=NULL){
        $search['search'] = $search['search'];
        $query = $this->select()->setIntegrityCheck(false)
            ->from($this, array('*'))
            // ->join('estados', 'estados.id = clientes.estado', array('estados.nombre as nom_estado'))
            // ->join('tipo_cliente', 'tipo_cliente.id = clientes.id_tipo_cliente', array('tipo_cliente.nombre as tipo_cliente', 'tipo_cliente.descuento'))
            ->where('proveedores.eliminado = 0')
            ->where("(proveedores.id LIKE '%{$search['search']}%' OR proveedores.razon_social LIKE '%{$search['search']}%' OR cuit LIKE '%{$search['search']}%')")
            ->group('proveedores.id');

            if ($paginate)
                $query->limit($paginate['per_page'],$paginate['start_from']);
           
        $rows = $this->fetchAll($query);

        return $rows->toArray();
    }

   
}
