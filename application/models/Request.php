<?php

class Application_Model_Request extends Application_Model_Base
{

	protected $_name = 'requests';


	public function add(Array $params){

		if($params['user'] != 'Guest'){

			$userParams = $params['user'];
			// Users table needs "id_user" instead of "id"
			$userParams['id_user'] = $userParams['id'];
			unset($userParams['id']);
			
			// Requests table just needs user's id
			unset($params['user']);
			$params['id_user'] = $userParams['id_user'];

			$user = new Application_Model_User();

			try{

				$user->insert($userParams); // If user is already registered it'll throw an exception
				
				return $this->insert($params);

			} catch (Exception $e) {
				// If user is already registered (Error code 23000)
				if($e->getCode() == 23000){
					// Create a new request for that user
					return $this->insert($params);
				} else{
					// Throw exception
					return $e;
				}
			}
		}else{
			try{
				unset($params['user']);
				return $this->insert($params);

			} catch (Exception $e) {
				return $e;
			}
		}		
	}


	public function getOne($id){
		try{
			
			$query = $this->select()->setIntegrityCheck(false)
			->from($this, array('id' => 'id_request','type','status','id_user','place', 'date', 'time'))
			->joinLeft('users','requests.id_user = users.id_user')
			->where('id_request = ?', $id);
			
			$row = $this->fetchRow($query);

			if(!$row) {
				return null;
			}
			
			$row = $row->toArray();

			// Build data to return
			if(isset($row['id_user'])){
				$data['user']['id_user'] = $row['id_user'];
				$data['user']['first_name'] = $row['first_name'];
				$data['user']['last_name'] = $row['last_name'];
				$data['user']['email'] = $row['email'];
				$data['user']['iecode'] = $row['iecode'];
				$data['user']['avatar_medium'] = $row['avatar_medium'];
			}else{
				$data['user'] = 'Guest';
			}
			
			$data['id'] = $row['id'];
			$data['type'] = $row['type'];
			$data['status'] = $row['status'];
			$data['place'] = $row['place'];
			$data['date'] = $row['date'] . ' ' . $row['time'];   
			
			return $data;
		}
		catch(Exception $e){
			return $e;
		}
	}


	public function getList(){
		$query = $this->select()->setIntegrityCheck(false)
		->from($this, array('id' => 'id_request','type','status','id_user','place', 'date', 'time', 'pushToken', 'waitingSince'))
		->joinLeft('users','requests.id_user = users.id_user');

		$rows = $this->fetchAll($query);
		$rows = $rows->toArray();

		if(count($rows) > 0){
			for ($i=0; $i < count($rows) ; $i++) { 
				
				// Build data to return
				if(isset($rows[$i]['id_user'])){
					$data['requests'][$i]['user']['id_user'] = $rows[$i]['id_user'];
					$data['requests'][$i]['user']['first_name'] = $rows[$i]['first_name'];
					$data['requests'][$i]['user']['last_name'] = $rows[$i]['last_name'];
					$data['requests'][$i]['user']['email'] = $rows[$i]['email'];
					$data['requests'][$i]['user']['iecode'] = $rows[$i]['iecode'];
					$data['requests'][$i]['user']['avatar_medium'] = $rows[$i]['avatar_medium'];
				}else{
					$data['requests'][$i]['user'] = 'Guest';
				}

				$data['requests'][$i]['id'] = $rows[$i]['id'];
				$data['requests'][$i]['type'] = $rows[$i]['type'];
				$data['requests'][$i]['status'] = $rows[$i]['status'];
				$data['requests'][$i]['place'] = $rows[$i]['place'];
				$data['requests'][$i]['date'] = $rows[$i]['date'] . ' ' . $rows[$i]['time'];
				$data['requests'][$i]['pushToken'] = $rows[$i]['pushToken'];
				$data['requests'][$i]['waitingSince'] = $rows[$i]['waitingSince'];
			}
		}else{
			$data['requests'] = [];
		}
		
		return $data;
	}


	public function getListByUser($id_user){
		$query = $this->select()->setIntegrityCheck(false)
		->from($this, array('id' => 'id_request','type','status','id_user','place', 'date', 'time'))
		->join('users','requests.id_user = users.id_user')
		->where('users.id_user = ?', $id_user);

		$rows = $this->fetchAll($query);
		$rows = $rows->toArray();
		
		if(count($rows) > 0){

			for ($i=0; $i < count($rows) ; $i++) { 
				
				// Build data to return
				$data['requests'][$i]['user']['id_user'] = $rows[$i]['id_user'];
				$data['requests'][$i]['user']['first_name'] = $rows[$i]['first_name'];
				$data['requests'][$i]['user']['last_name'] = $rows[$i]['last_name'];
				$data['requests'][$i]['user']['email'] = $rows[$i]['email'];
				$data['requests'][$i]['user']['iecode'] = $rows[$i]['iecode'];
				$data['requests'][$i]['user']['avatar_medium'] = $rows[$i]['avatar_medium'];
				
				$data['requests'][$i]['id'] = $rows[$i]['id'];
				$data['requests'][$i]['type'] = $rows[$i]['type'];
				$data['requests'][$i]['status'] = $rows[$i]['status'];
				$data['requests'][$i]['place'] = $rows[$i]['place'];
				$data['requests'][$i]['date'] = $rows[$i]['date'] . ' ' . $rows[$i]['time'];
			}
		}else{
			$data['requests'] = [];
		}

		return $data;
	}


	public function getListByGuest($id_guest){
		$query = $this->select()->setIntegrityCheck(false)
		->from($this, array('id' => 'id_request','type','status','place', 'date', 'id_guest', 'time'))
		->where('id_guest = ?', $id_guest);

		$rows = $this->fetchAll($query);
		$rows = $rows->toArray();

		if(count($rows) > 0){

			for ($i=0; $i < count($rows) ; $i++) { 
				
				
				$data['requests'][$i]['user'] = 'Guest';

				$data['requests'][$i]['id'] = $rows[$i]['id'];
				$data['requests'][$i]['type'] = $rows[$i]['type'];
				$data['requests'][$i]['status'] = $rows[$i]['status'];
				$data['requests'][$i]['place'] = $rows[$i]['place'];
				$data['requests'][$i]['date'] = $rows[$i]['date'] . ' ' . $rows[$i]['time'];

			}
		}else{
			$data['requests'] = [];
		}

		return $data;
	}


	public function getUser($id){
		try{
			$query = $this->select()->setIntegrityCheck(false)
			->from($this, 'id_request')
			->joinLeft('users','requests.id_user = users.id_user')
			->where('id_request = ?', $id);
			
			$row = $this->fetchRow($query);

			if(!$row) {
				return null;
			}
			
			$row = $row->toArray();

			// Build data to return
			if(isset($row['id_user'])){

				$user['first_name'] = $row['first_name'];
				$user['last_name'] = $row['last_name'];

			}else{
				$user = 'Guest';
			}

			return $user;
			
		}catch(Exception $e){
			return $e;
		}
	}
}

?>