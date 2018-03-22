<?php

class Application_Model_Existencia extends Application_Model_Base
{
    protected $_name = 'existencias';

    //Descontar en $cant la cantidad almacenada en la db
    public function movimientoStock($id, $cant, $operacion=NULL){
    	try {
    		if ($operacion == 'venta') {
    			$cant = $cant * -1;
    		}
	    	$query = 
                'UPDATE existencias e
				INNER JOIN (
			                    SELECT MAX(id) AS maxid, id_producto, cantidad
			                        FROM existencias ex
			                        where eliminado = 0
			                        GROUP BY ex.id_producto
			                ) AS t2 ON t2.id_producto = productos.id
			        SET t2.cantidad = t2.cantidad+'.$cant.'
			        WHERE e.id_producto = '.$id;

			
			//$db =Zend_Db_Table_Abstract::getDefaultAdapter();
	    	//$stmt = new Zend_Db_Statement_Mysqli($db, $sql);
	    	 
	    	//$stmt->execute();
die($query);
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

    public function getUltimaExistencia($id_producto){
    	try{
            $query = "SELECT *  FROM existencias e
					INNER JOIN (
	                    SELECT MAX(id) AS maxid, id_producto
	                        FROM existencias
	                        GROUP BY id_producto
	                	) AS t2 ON t2.maxid = e.id
					WHERE  e.id_producto = $id_producto
                    GROUP BY  e.id_producto";
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
       		$stmt = $db->query($query);
        	$productos =  $stmt->fetch();
        	
        	return $productos;

        }
		catch(Exception $e){
            return $e;
        }
    }




}

