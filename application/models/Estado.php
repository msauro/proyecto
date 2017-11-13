<?php

class Application_Model_Estado extends Application_Model_Base
{

	protected $_name = 'estados';

	public function getList(){
    	$query = $this->select()
            ->from($this, array('*'))
            ->where('eliminado = ?', 0);

        $rows = $this->fetchAll($query);

        return $rows->toArray();
    }

    public function getEstadoById($id){
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

   
}
