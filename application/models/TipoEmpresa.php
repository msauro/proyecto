<?php

class Application_Model_TipoEmpresa extends Application_Model_Base
{

	protected $_name = 'tipo_empresa';

	public function getList(){
    	$query = $this->select()
            ->from($this, array('*'))
            ->where('eliminado = ?', 0);

        $rows = $this->fetchAll($query);
        return $rows->toArray();
    }

    public function getTipoEmpresaByName($nombre){
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

   
}
