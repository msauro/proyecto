<?php

class Application_Model_User extends Application_Model_Base
{

	protected $_name = 'users';


	public function getOne($id){
		try{
            $query = $this->select()
	            ->from($this, array('id' => 'id_request','type','status','user','place', 'date'))
	            ->where('id_request = ?', $id);

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


	public function getList(){
    	$query = $this->select()
            ->from($this, array('id' => 'id_request','type','status','user','place', 'date'));

        $rows = $this->fetchAll($query);

        return $rows->toArray();
    }
}

?>