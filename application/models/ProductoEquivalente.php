<?php

class Application_Model_ProductoEquivalente extends Application_Model_Base
{

	protected $_name = 'producto_equivalente';

	// public function getList(){
 //    	$query = $this->select()
 //            ->from($this, array('*'))
 //            ->where('eliminado = ?', 0);

 //        $rows = $this->fetchAll($query);

 //        return $rows->toArray();
 //    }

    public function getIdOriginal($id_producto){
    	try{
            $query = $this->select()->setIntegrityCheck(false)
	            ->from($this, array('*'))
                ->join('ventas', 'ventas.id_cliente = clientes.id', array('id'))
	            ->where('id = ?', $id);

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

//////////////////////////////

    public function getClientesMasVentas($mesActualDesde,$mesActualHasta){

        $query = "SELECT * FROM clientes c
                    INNER JOIN ( 
                        SELECT COUNT(id) AS cant_compras, id_cliente, fecha
                            FROM ventas 
                            WHERE fecha > '$mesActualDesde' AND fecha < '$mesActualHasta'

                            GROUP BY id_cliente
                            ORDER BY cant_compras DESC
                        ) AS t2 
                    WHERE id_cliente = c.id
                    ORDER BY cant_compras DESC 
                    LIMIT 5
                    ";
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $stmt = $db->query($query);

        $clientesMasVentas =  $stmt->fetchAll();
        return $clientesMasVentas;
    }
//////////////////////////////

    public function getEquivalentes($id_producto){
        
        $query = "SELECT DISTINCT pe.id_original, pe2.id_producto AS id_producto_equivalente
                FROM producto_equivalente AS pe
                LEFT JOIN producto_equivalente AS pe2 
                ON pe.id_original = pe2.id_original
                WHERE pe.id_producto = '$id_producto'";
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $stmt = $db->query($query);

        $productosEquivalentes =  $stmt->fetchAll();
        return $productosEquivalentes;
            
    }

    public function eliminarEquivalentes($id){
        $query = "DELETE from producto_equivalente WHERE id_producto = '$id'";
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $stmt = $db->query($query);
        return true;

    }

}
