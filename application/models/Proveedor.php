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

   
}
