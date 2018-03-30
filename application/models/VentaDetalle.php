<?php

class Application_Model_VentaDetalle extends Application_Model_Base
{
	protected $_name = 'ventas_detalles';

	public function getDetalleVentaById($id){
		try{
            $query = $this->select()->setIntegrityCheck(false)
	            ->from($this, array('*'))
            	->join('productos', 'ventas_detalles.id_producto = productos.id', array('*'))
	            ->where('id_venta = ?', $id);

            $row = $this->fetchAll($query);

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