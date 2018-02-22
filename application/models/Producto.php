<?php

class Application_Model_Producto extends Application_Model_Base
{

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

        $search['search'] = $search['search'];
        $query = $this->select()->setIntegrityCheck(false)
            ->from($this, array('*'))
            ->join('precios', 'precios.id_producto = productos.id', array('precios.precio'))
            ->join('marcas', 'marcas.id = productos.id_marca', array('marcas.nombre as nom_marca'))
            ->where('productos.eliminado = 0')
            ->where('marcas.eliminado = 0')
            ->where('precios.eliminado = 0')
            ->where("(productos.codigo LIKE '%{$search['search']}%' OR productos.nombre LIKE '%{$search['search']}%' OR descripcion LIKE '%{$search['search']}%')")
            ->group('productos.nombre');
            // if(!$order){
            //     $query->order('id DESC');
            // }else{
            //     $query->order( $order );
            // }

        if ($paginate)
            $query->limit($paginate['per_page'],$paginate['start_from']);
             
        $rows = $this->fetchAll($query);

        return $rows->toArray();
    }

}
