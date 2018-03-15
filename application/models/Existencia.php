<?php

class Application_Model_Existencia extends Application_Model_Base
{
    protected $_name = 'existencias';

    //Descontar en $cant la cantidad almacenada en la db
    public function editarStock($id, $cant){
    	try {
	    	$query = 'UPDATE existencias
			SET cantidad = cantidad-'.$cant.'
			WHERE id_producto = '.$id;
			
			//$db =Zend_Db_Table_Abstract::getDefaultAdapter();
	    	//$stmt = new Zend_Db_Statement_Mysqli($db, $sql);
	    	 
	    	//$stmt->execute();

	    	$db = Zend_Db_Table_Abstract::getDefaultAdapter();
        
        	$stmt = $db->query($query);
       		$existencia =  $stmt->fetchRow();

	    	return true;
    	} catch (Exception $e) {
    		return false;
    	}
    }


    public function getExistenciaById($id){
    	try{
            $query = $this->select()
	            ->from($this, array('*'))
	            ->where('id_producto = ?', $id);

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

