<?php

class Application_Model_ProductoOriginal extends Application_Model_Base
{

	protected $_name = 'producto_original';

	public function getList(){
    	$query = $this->select()
            ->from($this, array('*'))
            ->where('eliminado = ?', 0);

        $rows = $this->fetchAll($query);

        return $rows->toArray();
    }


    public function getProductosByParams($codigo, $id=NULL){
        try{
        	if ($id != NULL) {
        		 $query = $this->select()
                ->from($this, array('*'))
                ->where('id_producto_original = ?', $codigo)
                ->where('id != ?', $id)
                ->where('eliminado = ?', 0);
        	}else{
        		 $query = $this->select()
                ->from($this, array('*'))
                ->where('id_producto_original = ?', $codigo)
                ->where('eliminado = ?', 0);
        	}
           

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

    public function getProductoById($id){
    	try{
            $query = $this->select()->setIntegrityCheck(false)
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





}