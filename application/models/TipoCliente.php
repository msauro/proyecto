<?php

class Application_Model_TipoCliente extends Application_Model_Base
{

	protected $_name = 'tipo_cliente';

	public function getList(){
    	$query = $this->select()->setIntegrityCheck(false)
            ->from($this, array('*'))
            ->where('eliminado = ?', 0);

        $rows = $this->fetchAll($query);
        return $rows->toArray();
    }

    public function getTipoClienteById($id){
    	try{
            $query = $this->select()
	            ->from($this, array('*'))
                ->where('id = ?', $id)
	            ->where('eliminado = ?', 0);

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

    public function getListClientes(){
        try{
            $query = $this->select()
                ->from($this, array('*'))
                ->where('eliminado = ?', 0);

            $row = $this->fetchAll($query);

            return $row->toArray();
        }
        catch(Exception $e){
            return $e;
        }
    }

   
}
