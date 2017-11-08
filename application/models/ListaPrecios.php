<?php

class Application_Model_ListaPrecios extends Application_Model_Base
{

	protected $_name = 'listas_precios';

	public function getList(){
    	$query = $this->select()->setIntegrityCheck(false)
            ->from($this, array('*'))
            ->join('tipo_cliente', 'tipo_cliente.id = listas_precios.tipo_cliente', array('tipo_cliente.nombre as tipo_cliente'))
            // ->join('productos', 'productos.id = precios.id_producto', array('*'))
            ->where('listas_precios.eliminado = ?', 0);

        $rows = $this->fetchAll($query);

        return $rows->toArray();
    }

    public function getListaPrecioById($id){
    	try{
            $query = $this->select()->setIntegrityCheck(false)
	            ->from($this, array('*'))
	            ->where('listas_precios.eliminado= ?', 0)
                ->where('listas_precios.id = ?', $id);

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
