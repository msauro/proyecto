<?php

class Application_Model_Usuario extends Application_Model_Base
{

	protected $_name = 'usuarios';

	public function isValidLogin($params){
		$email	= $params['email'];
		$password	= base64_encode(pack("H*",sha1(utf8_encode($params["password"]))));

		try {
			$query = $this->select()
				->from($this, '*')
				->where('email = ?', $email)
				->where('password = ?', $password)
				->where('eliminado = ?', 0);

			$row = $this->fetchRow($query);

			if (!$row) {
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
            ->from($this, array('id','email','nombre','apellido','administrador'))
            ->where('eliminado = ?', 0);

        $rows = $this->fetchAll($query);

        return $rows->toArray();
    }

	public function getUserById($id){
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

	public function getUserByEmail($email){		
		try{
            $query = $this->select()
	            ->from($this, array('*'))
	            ->where('email = ?', $email);

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


    public function isEmailAvailable($email){
    	
    	try {
			$query = $this->select()
				->from($this, array('email'))
				->where('email = ?', $email)
				->where('eliminado = ?', 0);

			$row = $this->fetchRow($query);

			if (!$row) {
				return true;
			}
			
			return false;
		}
		catch(Exception $e){
			return $e;
		}
	}
}
