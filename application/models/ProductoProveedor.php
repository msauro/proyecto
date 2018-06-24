<?php

class Application_Model_ProductoProveedor extends Application_Model_Base
{
	protected $_name = 'producto_proveedor';

	public function getProductosByParams($codigo){
        try{
            $query = $this->select()
                ->from($this, array('*'))
                ->where('codigo_producto_proveedor = ?', $codigo);
                // ->where('eliminado = ?', 0);

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

    public function getProductos(){
        try{
            $query = $this->select()
                ->from($this, array('codigo_producto_proveedor as codigo'));

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

    public function getList(){
        $query = $this->select()->setIntegrityCheck(false)
            ->from($this, array('*'))
            ->join('productos', 'producto_proveedor.codigo_producto = productos.codigo', array('*'))
            ->join('marcas', 'marcas.id = productos.id_marca', array('nombre as nom_marca'))
            ->joinLeft('producto_proveedor_precio', 'producto_proveedor_precio.id_proveedor = producto_proveedor.id_proveedor && producto_proveedor_precio.codigo_producto_proveedor = producto_proveedor.codigo_producto_proveedor', array('precio AS precio_costo', 'descuento'))
            ->join('proveedores', 'proveedores.id = producto_proveedor.id_proveedor', array('proveedores.razon_social AS razon_social'));
        $rows = $this->fetchAll($query);
        return $rows->toArray();
    }

    public function getCosto(){
        $query = $this->select()->setIntegrityCheck(false)
            ->from($this, array('*'))
            ->join('productos', 'producto_proveedor.codigo_producto = productos.codigo', array('*'))
            ->join('marcas', 'marcas.id = productos.id_marca', array('nombre as nom_marca'))
            ->joinLeft('producto_proveedor_precio', 'producto_proveedor_precio.id_proveedor = producto_proveedor.id_proveedor && producto_proveedor_precio.codigo_producto_proveedor = producto_proveedor.codigo_producto_proveedor', array('precio AS precio_costo', 'descuento'))
            ->join('proveedores', 'proveedores.id = producto_proveedor.id_proveedor', array('proveedores.razon_social AS razon_social'));
        $rows = $this->fetchAll($query);
        return $rows->toArray();
    }
    //HACER FUNC. PARA COMPARAR PRECIOS DE DISTINTOS PROVEEDORES
    public function compararPrecios(){
        $query = $this->select()->setIntegrityCheck(false)
            ->from($this, array('*'))
            ->join('producto_proveedor p1', 'producto_proveedor.cod_producto = p1.cod_producto', array('*'))
            ->join('productos', 'producto_proveedor.cod_producto = productos.codigo', array('*'))
            ->join('marcas', 'marcas.id = productos.id_marca', array('marcas.nombre AS nom_marca'))
            ->join('precio_proveedor', 'precio_proveedor.id_prod_prov = producto_proveedor.id', array('precio AS precio_costo'))
            ->join('proveedores', 'proveedores.id = producto_proveedor.id_proveedor', array('proveedores.razon_social AS razon_social'));

        $rows = $this->fetchAll($query);
        return $rows->toArray();
    }

    public function getListFiltered($search,$paginate=NULL,$id_proveedor=NULL){
        // $days = 15;
        // $fecha = date('Y-m-j');
        // $nuevafecha = strtotime ( "-$days day" , strtotime ( $fecha ) ) ;
        // $nuevafecha = date ( 'Y-m-j' , $nuevafecha );
        $search= $search['search'];
        if ($id_proveedor) {
             $query = 
                "SELECT `productos`.`id` as `id_prod`,`productos`.*, `producto_proveedor`.*, `marcas`.`nombre` AS `nom_marca`, `existencias`.`cantidad`, `producto_proveedor_precio`.`codigo_producto_proveedor`, `producto_proveedor_precio`.`precio`
                FROM `producto_proveedor`
                INNER JOIN `productos` ON productos.codigo = producto_proveedor.codigo_producto
                INNER JOIN `marcas` ON marcas.id = productos.id_marca
                INNER JOIN (
                    SELECT MAX(id) AS maxid, id_producto
                        FROM existencias
                        GROUP BY id_producto
                ) AS t2 ON t2.id_producto = productos.id
                INNER JOIN `existencias` ON existencias.id = t2.maxid
                INNER JOIN (
                    SELECT MAX(id) AS maxidprecio, id, codigo_producto_proveedor
                        FROM producto_proveedor_precio
                        GROUP BY codigo_producto_proveedor
                ) AS maxpre ON maxpre.codigo_producto_proveedor =  producto_proveedor.codigo_producto_proveedor
                INNER JOIN `producto_proveedor_precio` ON producto_proveedor_precio.id = maxpre.maxidprecio
                -- INNER JOIN `producto_proveedor` ON producto_proveedor_precio.id_proveedor = producto_proveedor.id_proveedor
                WHERE (producto_proveedor.id_proveedor = '$id_proveedor')
                AND (existencias.eliminado = 0) 
                AND (productos.eliminado = 0) 
                AND (marcas.eliminado = 0) 
                GROUP BY `productos`.`id`";
        }else{
            $query = 
                "SELECT `productos`.*,  `marcas`.`nombre` AS `nom_marca`, `existencias`.`cantidad`
                FROM `producto_proveedor`
                INNER JOIN `productos` ON productos.codigo = producto_proveedor.codigo_producto
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
                -- AND ('productos.codigo LIKE %$search% OR productos.nombre LIKE %$search% OR productos.descripcion LIKE %$search%')
                GROUP BY `productos`.`id`";
        }
        if ($paginate)
            $query.= "LIMIT ".$paginate['per_page']." OFFSET ". $paginate['start_from'];

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $stmt = $db->query($query);
        $productos =  $stmt->fetchAll();

        return $productos;
        
    }

    public function getProductosByProveedor($id_proveedor){
        try{
            $query = $this->select()
                ->from($this, array('codigo_producto_proveedor as codigo_proveedor'))
                ->where('producto_proveedor.id_proveedor = ?', $id_proveedor);

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