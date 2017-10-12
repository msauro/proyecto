<?php

class Application_Model_Product extends Application_Model_Base
{

	protected $_name = 'products';

	public function getList(){
    	$query = $this->select()
            ->from($this, array('*'))
            ->where('isDeleted = ?', 0);

        $rows = $this->fetchAll($query);

        return $rows->toArray();
    }
}