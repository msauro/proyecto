<?php

class Application_Model_Marca extends Application_Model_Base
{

	protected $_name = 'marcas';

	public function getList(){
    	$query = $this->select()
            ->from($this, array('*'))
            ->where('eliminado = ?', 0);

        $rows = $this->fetchAll($query);

        return $rows->toArray();
    }

    public function getMarcaById($id){
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

     public function getMarcaByName($nombre){
        try{
            $query = $this->select()
                ->from($this, array('*'))
                ->where('nombre = ?', $nombre)
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

    public function getListFiltered($search,$paginate=NULL){
        $search['search'] = $search['search'];
        $query = $this->select()->setIntegrityCheck(false)
            ->from($this, array('*'))
            ->where('marcas.eliminado = 0')
            ->where("(marcas.id LIKE '%{$search['search']}%' OR marcas.nombre LIKE '%{$search['search']}%')")
            ->group('marcas.id');

            if ($paginate)
                $query->limit($paginate['per_page'],$paginate['start_from']);
           
        $rows = $this->fetchAll($query);

        return $rows->toArray();
    }

   
}
