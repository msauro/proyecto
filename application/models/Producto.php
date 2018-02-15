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

    public function getProductosByParams($descripcion, $nombre, $id_marca){
        try{
            $query = $this->select()
                ->from($this, array('*'))
                ->where('id_marca = ?', $id_marca)
                ->where('eliminado = ?', 0)
                ->where('id_marca = ?', $id_marca)
                ->where('nombre = ?', $nombre);

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

     public function getFullList($id_cliente,$search=NULL,$paginate=NULL){
        $days = 15;
        $fecha = date('Y-m-j');
        $nuevafecha = strtotime ( "-$days day" , strtotime ( $fecha ) ) ;
        $nuevafecha = date ( 'Y-m-j' , $nuevafecha );

        $search['search'] = $search['search'];
        $query = $this->select()->setIntegrityCheck(false)
            ->from($this, array('*'))
            ->join('estados', 'estados.id = clientes.estado', array('estados.nombre as nom_estado'))
            ->where('clientes.eliminado = 0')
            ->where("(clientes.id LIKE '%{$search['search']}%' OR clientes.nombre LIKE '%{$search['search']}%' OR apellido LIKE '%{$search['search']}%' OR email LIKE '%{$search['search']}%' OR cuit LIKE '%{$search['search']}%' OR CONCAT(clientes.nombre,' ', apellido) LIKE '%{$search['search']}%')")
            ->group('clientes.id');

            // if(!$order){
            //     $query->order('id DESC');
            // }else{
            //     $query->order( $order );
            // }

            if ($paginate)
                $query->limit($paginate['per_page'],$paginate['start_from']);
            // switch ($search["filter"]) {
            //     case 'auth':
            //         $query->where('recommendation_allowed = 1');
            //         break;
            //     case 'notauth':
            //         $query->where('recommendation_allowed = 0');
            //         break;
            //     case 'active':
            //         $query->where('status = 1');
            //         break;
            //     case 'inactive':
            //         $query->where('status = 0');
            //         break;
            //     case 'new':
            //         $fecha = date('Y-m-j');
            //         $nuevafecha = strtotime ( "-$days day" , strtotime ( $fecha ) ) ;
            //         $nuevafecha = date ( 'Y-m-j' , $nuevafecha );

            //         $query = $this->select()->setIntegrityCheck(false)
            //         ->from($this, array('*'))
            //         ->where('eliminado = 0')
            //         // ->where("created_at >= '$nuevafecha'")
            //         ->group('id');

            //         // $query->where("created_at >= '$nuevafecha'");
            //         break;
            //     case 'expired':
            //         $query->where('expire_recommendation_id <= ?',date('Y-m-d'));
            //         break;
            //     case 'dontbuy30days':
            //         $query = $this->select()->setIntegrityCheck(false)
            //             ->from($this, '*')
            //             ->where('id_patient NOT IN (SELECT donations.id_patient FROM donations WHERE (date >= (NOW() - INTERVAL 30 DAY)) GROUP BY `donations`.`id_patient`)')
            //             ->where('is_deleted = 0')
            //             ->where('status = 1')
            //             ->group('patients.id_patient');
            //         break;                        
            //     default:
            //         break;
            // }    
        $rows = $this->fetchAll($query);

        return $rows->toArray();
    }
   
}
