<?php

class Application_Model_PedidoDetalle extends Application_Model_Base
{
	protected $_name = 'pedidos_detalles';

	public function getDetallePedidoById($id){
		try{
            $query = $this->select()->setIntegrityCheck(false)
	            ->from($this, array('*'))
            	->join('pedidos', 'pedidos_detalles.id_pedido = pedidos.id', array('*'))
            	->join('productos', 'pedidos_detalles.id_producto = productos.id', array('*'))
            	->join('producto_proveedor', 'producto_proveedor.codigo_producto = productos.codigo', array('codigo_producto_proveedor', 'codigo_producto'))
	            ->where('pedidos_detalles.id_pedido = ?', $id)
	            ->where('producto_proveedor.id_proveedor = pedidos.id_proveedor');
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