<?php

class Application_Model_Settings extends Application_Model_Base
{

	protected $_name = 'settings';

	public function getSettings($id){
		try{
            $query = $this->select()
	            ->from($this, array('*'))
	            ->where('id_setting = ?',$id);


            $row = $this->fetchRow($query);

			if (!$row) {
				return null;
			}

			return $row->toArray();
			
        }catch(Exception $e){
            return $e;
        }
	}
	
}