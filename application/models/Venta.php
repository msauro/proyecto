<?php

class Application_Model_Venta extends Application_Model_Base
{

	protected $_name = 'ventas';

	public function getList(){
    	$query = $this->select()->setIntegrityCheck(false)
            ->from($this, array('*'))
           	->join('clientes', 'clientes.id = ventas.id_cliente', array('nombre','apellido','telefono','cuit','direccion','razon_social'))
            ->where('ventas.eliminado = ?', 0);

        $rows = $this->fetchAll($query);

        return $rows->toArray();
    }

    public function getCantVentasRango($desde,$hasta){
    	$query = "SELECT COUNT(id)
				FROM ventas
				WHERE  ventas.fecha > '$desde' AND ventas.fecha < '$hasta'";

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $stmt = $db->query($query);
        $cantVentas =  $stmt->fetch();
        
        return $cantVentas;
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
            	// ->join('ventas_detalles', 'ventas_detalles.id_venta = ventas.id', array('*'))
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
