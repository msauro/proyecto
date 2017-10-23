<?php

class Application_Model_Pedido extends Application_Model_Base
{

	protected $_name = 'pedidos';

	public function getList(){
    	$query = $this->select()->setIntegrityCheck(false)
            ->from($this, array('*'))
            ->join('productos', 'productos.id = pedidos.id_producto', array('productos.nombre AS nom_producto'))
            ->where('pedidos.eliminado = ?', 0);

        $rows = $this->fetchAll($query);

        return $rows->toArray();
    }

    public function getPrecioById($id){
    	try{
            $query = $this->select()->setIntegrityCheck(false)
	            ->from($this, array('*'))
                ->join('productos', 'productos.id = pedidos.id_producto', array('productos.nombre AS nom_producto'))
	            ->where('pedidos.eliminado= ?', 0);

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
