<?php

class Application_Model_Producto extends Application_Model_Base
{
    // protected $_db = Zend_Db_Table_Abstract::getDefaultAdapter();
	protected $_name = 'productos';

	public function getList(){
    	$query = $this->select()->setIntegrityCheck(false)
            ->from($this, array('*'))
            ->join('marcas', 'marcas.id = productos.id_marca', array('marcas.nombre AS nom_marca'))
            ->where('productos.eliminado = ?', 0);
        $rows = $this->fetchAll($query);
        return $rows->toArray();
    }

    public function getProductoById($id){
    	try{
            $query = $this->select()->setIntegrityCheck(false)
	            ->from($this, array('*'))
                ->join('existencias', 'productos.id = existencias.id_producto', array('*'))
	            ->where('productos.id = ?', $id);

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

    public function getProductoEquivalenteById($codigo){

        $query = "SELECT ep.id_original, ep2.id_producto AS id_producto_equivalente FROM producto_equivalente AS ep
                LEFT JOIN producto_equivalente AS ep2 ON ep.id_original = ep2.id_original
                WHERE ep.id_producto = '$codigo'";
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $stmt = $db->query($query);
        $cantVentas =  $stmt->fetchAll();
        
        return $cantVentas;
    }

    public function getListPrecios($tipoCliente,$hoy){
        $query = $this->select()->setIntegrityCheck(false)
            ->from($this, array('*'))
            ->join('marcas', 'marcas.id = productos.id_marca', array('marcas.nombre AS nom_marca'))
            ->join('precios', 'productos.id = precios.id_producto', array('precio'))
            ->join('listas_precios', 'listas_precios.id = precios.id_lista_precio', array('precio', MAX('fecha_vigencia as fecha_vigencia')))
            ->where('productos.eliminado = ?', 0)
            ->where('listas_precios.tipo_cliente = ?', $tipoCliente)
            ->where('listas_precios.fecha_vigencia <= ', $hoy);

        $rows = $this->fetchAll($query);
        return $rows->toArray();
    }

    public function getProductosByParams($codigo, $id_marca, $id_producto = null){
        try{
            $query = $this->select()
                ->from($this, array('*'))
                ->where('id_marca = ?', $id_marca)
                ->where('codigo = ?', $codigo)
                ->where('eliminado = ?', 0);
            if ($id_producto) {
                $query ->where('id != ?', $id_producto);
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

    public function getListFiltered($search,$paginate=NULL){
        $days = 15;
        $fecha = date('Y-m-j');
        $nuevafecha = strtotime ( "-$days day" , strtotime ( $fecha ) ) ;
        $nuevafecha = date ( 'Y-m-j' , $nuevafecha );

        $search= $search['search'];
        $query = 
                "SELECT `productos`.*, `precios`.`precio`, `marcas`.`nombre` AS `nom_marca`, `existencias`.`cantidad`
                FROM `productos`
                INNER JOIN `precios` ON precios.id_producto = productos.id
                INNER JOIN `marcas` ON marcas.id = productos.id_marca
                INNER JOIN (
                    SELECT MAX(id) AS maxid, id_producto
                        FROM existencias
                        GROUP BY id_producto
                ) AS t2 ON t2.id_producto = productos.id
                INNER JOIN `existencias` ON existencias.id = t2.maxid
                WHERE (existencias.eliminado = 0) 
                AND (productos.eliminado = 0) 
                AND (marcas.eliminado = 0) 
                AND (precios.eliminado = 0) 
                -- AND ('productos.codigo LIKE %$search% OR productos.nombre LIKE %$search% OR productos.descripcion LIKE %$search%')
                GROUP BY `productos`.`id`";
        
        if ($paginate)
            $query.= "LIMIT ".$paginate['per_page']." OFFSET ". $paginate['start_from'];
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $stmt = $db->query($query);
        $productos =  $stmt->fetchAll();
        return $productos;
        
    }

    public function editEquivalente($codigo){

        $query = "SELECT ep.id_original, ep2.id_producto AS id_producto_equivalente FROM producto_equivalente AS ep
                LEFT JOIN producto_equivalente AS ep2 ON ep.id_original = ep2.id_original
                WHERE ep.id_producto = '$codigo'";
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $stmt = $db->query($query);
        $cantVentas =  $stmt->fetchAll();
        
        return $cantVentas;
    }



    // SELECT ep.id_original, ep2.id_producto AS id_producto_equivalente FROM equivalencias_productos AS ep
    // LEFT JOIN equivalencias_productos AS ep2 ON ep.id_original = ep2.id_original
    // WHERE ep.id_producto = 960

    // es mas, incluso le podes sacar del select el ep.id_original y al otro lo pones un “DISTINCT()”
    // para q no te traiga repetidos
    // y so vo


}
