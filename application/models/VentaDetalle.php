<?php

class Application_Model_VentaDetalle extends Application_Model_Base
{
	protected $_name = 'ventas_detalles';

	public function getDetalleVentaById($id){
		try{
            $query = $this->select()
	            ->from($this, array('*'))
	            ->where('id_venta = ?', $id);

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