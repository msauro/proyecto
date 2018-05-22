<?php

class Application_Model_ProductoOriginal extends Application_Model_Base
{

	protected $_name = 'producto_original';

	public function getList(){
    	$query = $this->select()
            ->from($this, array('*'))
            ->where('eliminado = ?', 0);

        $rows = $this->fetchAll($query);

        return $rows->toArray();
    }









}