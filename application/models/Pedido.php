<?php

class Application_Model_Pedido extends Application_Model_Base
{

	protected $_name = 'pedidos';

	public function getList(){
    	$query = $this->select()->setIntegrityCheck(false)
            ->distinct()
            ->from($this, array('pedidos.id as id_ped', '*'))
            // ->join('pedidos_detalles', 'pedidos_detalles.id_pedido = pedidos.id', array('*'))
            // ->join('productos', 'productos.id = pedidos_detalles.id_producto', array('*'))
            ->join('proveedores', 'proveedores.id = pedidos.id_proveedor', array('*'))
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

    public function getFullPedido($id){
        try{
            $query = $this->select()->setIntegrityCheck(false)
                ->from($this, array('pedidos.id as id_ped','*'))
                ->join('proveedores', 'proveedores.id = pedidos.id_proveedor', array('*'))
                // ->join('tipo_empresa', 'tipo_empresa.id = proveedores.id_tipo_empresa', array('tipo_empresa.nombre'))
                ->where('pedidos.id = ?', $id);

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
