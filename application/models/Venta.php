<?php

class Application_Model_Venta extends Application_Model_Base
{

	protected $_name = 'ventas';

	public function getList(){
    	$query = $this->select()
            ->from($this, array('*'))
            ->where('eliminado = ?', 0);

        $rows = $this->fetchAll($query);

        return $rows->toArray();
    }

	public function getVentaById($id){
		try{
            $query = $this->select()
	            ->from($this, array('*'))
	            ->where('id = ?', $id);

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

	public function getVentaByEmail($email){		
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


    public function isEmailAvailable($email){
    	
    	try {
			$query = $this->select()
				->from($this, array('email'))
				->where('email = ?', $email)
				->where('eliminado = ?', 0);

			$row = $this->fetchRow($query);

			if (!$row) {
				return true;
			}
			
			return false;
		}
		catch(Exception $e){
			return $e;
		}
	}

	public function getFullVenta($id){
		try{
            $query = $this->select()->setIntegrityCheck(false)
	            ->from($this, array('*'))
            	->join('ventas_detalles', 'ventas_detalles.id_venta = ventas.id', array('*'))
            	->join('clientes', 'clientes.id = ventas.id_cliente', array('*'))
	            ->where('ventas.id = ?', $id);

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
